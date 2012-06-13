<?php

class HeadwaySubtextNavigationBlock extends HeadwayBlockAPI {
	
	public $id = 'subtext-navigation';

	public $name = 'Subtext Navigation';

	public $options_class = 'HeadwaySubtextNavigationBlockOptions';

	public $fixed_height = false;

	public $html_tag = 'nav';

	protected $show_content_in_grid = true;

	static public $block = null;

	function __construct() {
		add_action('init', array(__CLASS__, 'set_options_defaults'));
	}

	function add_subtext_navigation_css($general_css_fragments) {
		$general_css_fragments[] = dirname(__FILE__).'/css/menus.css';
		return $general_css_fragments;
	}

	function init_action($block_id) {
		$block = HeadwayBlocksData::get_block($block_id);			
		$name = HeadwayBlocksData::get_block_name($block) . ' &mdash; ' . 'Layout: ' . HeadwayLayout::get_name($block['layout']);
		register_nav_menu('subtext_navigation_block_' . $block_id, $name);
		
	}
	
	function enqueue_action($block_id) {
		add_filter('headaway_general_css', array(__CLASS__, 'add_subtext_navigation_css'));
		// If there are no sub menus in the navigation, then do not enqueue all of the JS.
		if ( !self::does_menu_have_subs('subtext_navigation_block_' . $block_id) )
			return false;
		wp_register_script('jquery-hoverintent', HEADWAY_URL . '/library/media/js/jquery.hoverintent.js', array('jquery'));
		wp_enqueue_script('headway-superfish', HEADWAY_URL . '/library/blocks/core/navigation/js/jquery.superfish.js', array('jquery-hoverintent'));
	}
	
	function content($block) {
		
		self::$block = $block;
		
		/* Add filter to add home link */
		add_filter('wp_nav_menu_items', array(__CLASS__, 'home_link_filter'));
		add_filter('wp_list_pages', array(__CLASS__, 'home_link_filter'));
		add_filter('wp_page_menu', array(__CLASS__, 'fix_legacy_nav'));
		
		/* Variables */
		$vertical = parent::get_setting($block, 'vert-nav-box', false);
		$alignment = parent::get_setting($block, 'alignment', 'left');
		
		$search = parent::get_setting($block, 'enable-nav-search', true);
		$search_position = parent::get_setting($block, 'nav-search-position', 'right');
		
		/* Classes */
		$nav_classes = array();
		
		$nav_classes[] = $vertical ? 'nav-vertical' : 'nav-horizontal';
		$nav_classes[] = 'nav-align-' . $alignment;
		
		if ( $search && !$vertical ) {
			
			$nav_classes[] = 'nav-search-active';
			$nav_classes[] = 'nav-search-position-' . $search_position;
			
		}
			
		$nav_classes = trim(implode(' ', array_unique($nav_classes)));
		$nav_location = 'subtext_navigation_block_' . $block['id'];
		
		echo '<div class="' . $nav_classes . '">';
		
				$nav_menu_args = array(
					'theme_location' => $nav_location,
					'container' => false,
					'walker' => new subtext_navigation_walker()
				);
				
				if ( HeadwayRoute::is_grid() || Headway::get('ve-live-content-query', $block) ) {
					
					$nav_menu_args['link_before'] = '<span>';
					$nav_menu_args['link_after'] = '</span>';
					
				}
			
				wp_nav_menu(apply_filters('headway_subtext_navigation_block_query_args', $nav_menu_args, $block));
				
				if ( $search && !$vertical ) {
				
					echo '<div class="nav-search">';
						echo get_search_form();
					echo '</div>';
					
				}
		
		echo '</div><!-- .' . $nav_classes . ' -->';
				
		/* Remove filter for home link so other non subtext menu blocks are modified */
		remove_filter('wp_nav_menu_items', array(__CLASS__, 'home_link_filter'));
		remove_filter('wp_list_pages', array(__CLASS__, 'home_link_filter'));
		remove_filter('wp_page_menu', array(__CLASS__, 'fix_legacy_nav'));
		
	}
	
	
	function dynamic_css($block_id) {
		
		$block          			= HeadwayBlocksData::get_block($block_id);
		$block_height   			= HeadwayBlocksData::get_block_height($block);
		$enable_nav_search 			= parent::get_setting($block, 'enable-nav-search', true);
		$nav_search_top_margin 		= parent::get_setting($block, 'nav-search-top-margin', 12);
		$nav_search_right_margin 	= parent::get_setting($block, 'nav-search-right-margin', -9);
		$enable_subtext 			= parent::get_setting($block, 'enable-subtext', true);
		$subtext_margin 			= parent::get_setting($block, 'subtext-margin', 3);
		$css            			= '';

		if ($enable_subtext) {
			$css .= '#block-'.$block_id.' .nav-horizontal ul.menu > li > a { height: 55px; line-height: 1; padding-top: 13px; }';
			$css .= '#block-'.$block_id.' ul li a span.subtext { display: block; line-height: 1; margin-top: ' . $subtext_margin . 'px; }';
		} else {
			$css .= '
			#block-' . $block_id . ' .nav-horizontal ul.menu > li > a, 
			#block-' . $block_id . ' .nav-search { 
				height: ' . $block_height . 'px; 
				line-height: ' . $block_height . 'px; 
			}';
		}

		if ($enable_nav_search == true) {
			$css .= '
			#block-' . $block_id . ' .nav-search { 
				margin-top: ' . $nav_search_top_margin . 'px; 
				margin-right: ' . $nav_search_right_margin . 'px; 
			}';
		}
		
		return $css;
	}

	
	function dynamic_js($block_id) {
		
		//If there are no sub menus in the navigation, then do not output the Superfish JS.
		if ( !self::does_menu_have_subs('navigation_block_' . $block_id) )
			return false;
		
		return 'jQuery(document).ready(function(){ 
		jQuery("#block-' . $block_id . '").find("ul.menu").superfish({
			delay: 200,
			speed: \'fast\',
			onBeforeShow: function() {
				var parent = jQuery(this).parent();
				
				var subMenuParentLink = jQuery(this).siblings(\'a\');
				var subMenuParents = jQuery(this).parents(\'.sub-menu\');

				if ( subMenuParents.length > 0 || jQuery(this).parents(\'.nav-vertical\').length > 0 ) {
					jQuery(this).css(\'marginLeft\',  parent.width());
					jQuery(this).css(\'marginTop\',  -subMenuParentLink.height());
				}
			}
		});		
});' . "\n\n";
		
	}
	
	
	function does_menu_have_subs($location) {
		
		$menu = wp_nav_menu(array(
			'theme_location' => $location,
			'echo' => false
		));	
				
		if ( preg_match('/class=[\'"]sub-menu[\'"]/', $menu) || preg_match('/class=[\'"]children[\'"]/', $menu) )
			return true;
			
		return false;
		
	}
	
	
	function setup_elements() {

		$this->register_block_element(array(
			'id' => 'container',
			'name' => 'Container',
			'selector' => '.block-content',
			'properties' => array('background', 'borders', 'rounded-corners', 'box-shadow')
		));

		$this->register_block_element(array(
			'id' => 'item',
			'name' => 'Menu Item',
			'selector' => 'ul.menu li a',
			'properties' => array('fonts' => array('font-family', 'font-size', 'color', 'font-styling', 'capitalization', 'letter-spacing', 'text-decoration'), 'background', 'borders', 'rounded-corners', 'box-shadow', 'text-shadow'),
			'states' => array(
				'Selected' => 'ul.menu li.current_page_item a', 
				'Hover' => 'ul.menu li a:hover', 
				'Clicked' => 'ul.menu li a:active'
			)
		));

		$this->register_block_element(array(
			'id' => 'item-subtext',
			'name' => 'Menu Item Subtext',
			'selector' => 'ul.menu li a span.subtext',
			'properties' => array('fonts' => array('font-family', 'font-size', 'color', 'font-styling', 'capitalization', 'letter-spacing', 'text-decoration'), 'background', 'borders', 'rounded-corners', 'box-shadow', 'text-shadow'),
			'states' => array(
				'Selected' => 'ul.menu li a:focus span.subtext', 
				'Hover' => 'ul.menu li a:hover span.subtext', 
				'Clicked' => 'ul.menu li a:active span.subtext'
			)
		));

		$this->register_block_element(array(
            'id' => 'search-input',
            'name' => 'Search Input',
            'selector' => '#searchform input',
            'properties' => array('fonts', 'text-shadow','background','borders','box-shadow', 'rounded-corners'),
            'states' => array(
				'Focus' => '#searchform input:focus', 
				'Hover' => '#searchform input:hover'
			)
        ));
		
	}
	

	function home_link_filter($menu) {
		
		$block = self::$block;
		
		if ( parent::get_setting($block, 'enable-home-link') )
			return $menu;
		
		if ( get_option('show_on_front') == 'posts' ) {

			$current = (is_home() || is_front_page()) ? ' current_page_item' : null;
			$enable_subtext = parent::get_setting($block, 'enable-subtext', true);
			$home_text = ( parent::get_setting($block, 'home-link-text') ) ? parent::get_setting($block, 'home-link-text') : 'Home';
			$home_text_html .= '<span class="text"> ' . $home_text . '</span>';
			$home_description = ( parent::get_setting($block, 'home-link-description') ) ? parent::get_setting($block, 'home-link-description') : 'Back home';
			$home_description_html .= '<span class="subtext"> ' . $home_description . '</span>';
			
			if($enable_subtext == true) {
				$menu_item_text = $home_text_html . $home_description_html;
			} else {
				$menu_item_text = $home_text_html;
			}

			/* If it's not the grid, then do not add the extra <span>'s */
			if ( !HeadwayRoute::is_grid() && !Headway::get('ve-live-content-query', $block) )
				$home_link = '<li class="menu-item-home' . $current . '"><a href="' . home_url() . '" class="top-level">' . $menu_item_text . '</a></li>';
			
			/* If it IS the grid, add extra <span>'s so it can be automatically vertically aligned */
			else
				$home_link = '<li class="menu-item-home' . $current . '"><a href="' . home_url() . '" class="top-level">' . $menu_item_text . '</a></li>';
			
		} else {
			
			$home_link = null;
			
		}

		return $home_link . $menu;
		
	}
	
	
	function fix_legacy_nav($menu) {
		
		$menu = preg_replace('/<ul class=[\'"]children[\'"]/', '<ul class="sub-menu"', trim($menu)); //Change sub menu class
		$menu = preg_replace('/<div class=[\'"]menu[\'"]>/', '', $menu, 1); //Remove opening <div>
		$menu = str_replace('<ul>', '<ul class="menu">', $menu); //Add menu class to main <ul>
				
		return substr(trim($menu), 0, -6); //Remove the closing </div>
		
	}
	
	//a helper method to get a value to use in other block files
	public static function is_subtext($block, $setting, $default = null) {
		$block = self::$block;
		return parent::get_setting($block, 'enable-subtext', true);
	}

	/* set defaults for any options we created in block-options.php */
	function set_options_defaults() {
		$block = self::$block;
				
		global $headway_default_element_data;
		
		$subtext_navigation_blocks = HeadwayBlocksData::get_blocks_by_type('subtext-navigation');
		
		/* return if there are no blocks for this type.. else do the foreach */
		if ( !isset($subtext_navigation_blocks) || !is_array($subtext_navigation_blocks) )
			return $return;
			
		$new_headway_default_element_data = array();
		
		foreach ($subtext_navigation_blocks as $block_id => $layout_id) {
			
			$new_headway_default_element_data['block-subtext-navigation-container'] = array(
				'properties' => array(
					'border-top-width' => '1',
					'border-bottom-width' => '1',
					'border-color' => 'eeeeee',
					'border-style' => 'solid'
				)
			);		
			$new_headway_default_element_data['block-subtext-navigation-item'] = array(
				'properties' => array(
					'font-size' => '16',
					'font-family' => 'palatino',
					'text-decoration' => 'none',
					'color' => '555555'
				)
			);
			$new_headway_default_element_data['block-subtext-navigation-item-subtext'] = array(
				'properties' => array(
					'font-size' => '12',
					'color' => '999999'
				)
			);	
		}
		$headway_default_element_data = array_merge($headway_default_element_data, $new_headway_default_element_data);
	}

}

/**
 * Subtext menu walker class extends wordpress menu
 **/
class subtext_navigation_walker extends Walker_Nav_Menu
{
      function start_el(&$output, $item, $depth, $args) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

        $output .= $indent . '<li id="item-'. $item->ID . '"' . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';
		$attributes .= $depth == 0        			? ' class="top-level"' : '';

		if (HeadwaySubtextNavigationBlock::is_subtext())
        $description  = ! empty( $item->description ) ? '<span class="subtext">'.esc_attr( $item->description ).'</span>' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;

		if (HeadwaySubtextNavigationBlock::is_subtext())
		$item_output .= $description.$args->link_after;
		
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
