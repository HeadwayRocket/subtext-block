<?php

class HeadwaySubtextBlock extends HeadwayBlockAPI {
	
	
	public $id = 'hwr-subtext';
	
	public $name = 'Subtext';
	
	public $options_class = 'HeadwaySubtextBlockOptions';

	public $fixed_height = false;

	public $html_tag = 'nav';

	protected $show_content_in_grid = false;

	static public $block = null;

	function __construct() {
		add_action('init', array(__CLASS__, 'set_options_defaults'));
	}
	
	function init_action($block_id, $block = false) {
	
		if ( !$block )
			$block = HeadwayBlocksData::get_block($block_id);
		$name = HeadwayBlocksData::get_block_name($block) . ' &mdash; ' . 'Layout: ' . HeadwayLayout::get_name($block['layout']);
		register_nav_menu('subtext_block_' . $block_id, $name);
		wp_register_script('jquery-hoverintent', headway_url() . '/library/media/js/jquery.hoverintent.js', array('jquery'));
		
	}
	
	function enqueue_action($block_id) {
	
		if ( !self::does_menu_have_subs('subtext_block_' . $block_id) )
			return false;
		$dependencies = array('jquery');
		
		if ( parent::get_setting($block_id, 'hover-intent', true) )
		
			$dependencies[] = 'jquery-hoverintent';
			
		if (version_compare('3.4', HEADWAY_VERSION, '>'))
			wp_enqueue_script('headway-superfish', headway_url() . '/library/blocks/core/navigation/js/jquery.superfish.js', $dependencies);
		
		else
			wp_enqueue_script('headway-superfish', headway_url() . '/library/blocks/navigation/js/jquery.superfish.js', $dependencies);		
		
	}
	
	function is_menu_assigned($block) {
		
		self::$block = $block;
		$enable_subtext = parent::get_setting($block, 'enable-subtext', true);
		
		$get_location	  = get_nav_menu_locations();
		
		$menu_assignement = isset($get_location['subtext_block_' . $block['id']]) ? $get_location['subtext_block_' . $block['id']] : '';
		
		if (($enable_subtext) && ($menu_assignement != 0)) {
			return true;
		}
		
		return false;
	}
	
	function content($block) {
		
		self::$block = $block;
		
		add_filter('wp_nav_menu_items', array(__CLASS__, 'home_link_filter'));
		add_filter('wp_list_pages', array(__CLASS__, 'home_link_filter'));
		add_filter('wp_page_menu', array(__CLASS__, 'fix_legacy_nav'));
		
		$vertical = parent::get_setting($block, 'vert-nav-box', false);
		$alignment = parent::get_setting($block, 'alignment', 'left');
		$search = parent::get_setting($block, 'enable-nav-search', false);
		$search_position = parent::get_setting($block, 'nav-search-position', 'right');
		$assignment_notice = '<div class="alert alert-yellow"><p>No menu has been assigned. To add items to this subtext navigation menu, go to <a href="' . admin_url('nav-menus.php') . '" target="_blank">WordPress Admin &raquo; Appearance &raquo; Menus</a>.  Then, create a menu and assign it to <em>' . HeadwayBlocksData::get_block_name($block) . '</em> in the <strong>Theme Locations</strong> box.</p></div>';
		
		$nav_classes = array();
		
		$nav_classes[] = $vertical ? 'nav-vertical' : 'nav-horizontal';
		$nav_classes[] = 'nav-align-' . $alignment;
		
		if ( $search && !$vertical ) {
			
			$nav_classes[] = 'nav-search-active';
			$nav_classes[] = 'nav-search-position-' . $search_position;
			
		}
			
		$nav_classes = trim(implode(' ', array_unique($nav_classes)));
		$nav_location = 'subtext_block_' . $block['id'];
		
		if (self::is_menu_assigned($block) == false) {
			echo $assignment_notice;
		}
		
		echo '<div id="nav-' . $block['id'] . '" class="block-type-navigation"><div class="' . $nav_classes . '">';
				
				if (self::is_menu_assigned($block) == true) {
					$nav_menu_args = array(
						'theme_location' => $nav_location,
						'container' => false,
						'walker' => new subtext_walker()
					);
				} else {
					$nav_menu_args = array(
						'theme_location' => $nav_location,
						'container' => false,
					);
				}
				
				if ( HeadwayRoute::is_grid() || Headway_get('ve-live-content-query', $block) ) {
					
					$nav_menu_args['link_before'] = '<span>';
					$nav_menu_args['link_after'] = '</span>';
					
				}				
				
				wp_nav_menu(apply_filters('headway_subtext_block_query_args', $nav_menu_args, $block));
				
				if ( $search && !$vertical ) {
				
					echo '<div class="nav-search">';
						if (version_compare('3.4', HEADWAY_VERSION, '>'))
							echo get_search_form();
						
						else
							echo headway_get_search_form(parent::get_setting($block, 'nav-search-placeholder', null));
					echo '</div>';
					
				}
		
		echo '</div></div><!-- .' . $nav_classes . ' -->';		
				
		remove_filter('wp_nav_menu_items', array(__CLASS__, 'home_link_filter'));
		remove_filter('wp_list_pages', array(__CLASS__, 'home_link_filter'));
		remove_filter('wp_page_menu', array(__CLASS__, 'fix_legacy_nav'));
		
	}
	
	
	function dynamic_css($block_id, $block, $original_block = null) {
		
		$block          			= HeadwayBlocksData::get_block($block_id);
		$block_height   			= HeadwayBlocksData::get_block_height($block);
		$enable_nav_search 			= parent::get_setting($block, 'enable-nav-search', true);
		$nav_search_top_margin 		= parent::get_setting($block, 'nav-search-top-margin', 12);
		$nav_search_right_margin 	= parent::get_setting($block, 'nav-search-right-margin', 0);
		$subtext_margin 			= parent::get_setting($block, 'subtext-margin', 3);
		$css            			= '';

		if (self::is_menu_assigned($block) == true) {
			$css .= '#block-'.$block_id.' .nav-horizontal ul.menu { margin: 0; padding: 0; }';
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

		if (($enable_nav_search == true) && (self::is_menu_assigned($block) == true)) {
			$css .= '
			#block-' . $block_id . ' .nav-search { 
				margin-top: ' . $nav_search_top_margin . 'px; 
				margin-right: ' . $nav_search_right_margin . 'px; 
			}';
		}
		
		return $css;
		
	}
	
	
	function dynamic_js($block_id) {
		
		if ( !self::does_menu_have_subs('subtext_block_' . $block_id) )
			return null;
		
		switch ( parent::get_setting($block, 'effect', 'fade') ) {
			case 'none':
				$animation = '{height:"show"}';
				$speed = '0';
			break;

			case 'fade':
				$animation = '{opacity:"show"}';
				$speed = "'fast'";
			break;

			case 'slide':
				$animation = '{height:"show"}';
				$speed = "'fast'";
			break;
		}

		return 'jQuery(document).ready(function(){ 
			jQuery("#block-' . $block_id . '").find("ul.menu").superfish({
				delay: 200,
				animation: ' . $animation . ',
				speed: ' . $speed . ',
				onBeforeShow: function() {
					var parent = jQuery(this).parent();
					
					var subMenuParentLink = jQuery(this).siblings(\'a\');
					var subMenuParents = jQuery(this).parents(\'.sub-menu\');
	
					if ( subMenuParents.length > 0 || jQuery(this).parents(\'.nav-vertical\').length > 0 ) {
						jQuery(this).css(\'marginLeft\',  parent.outerWidth());
						jQuery(this).css(\'marginTop\',  -subMenuParentLink.outerHeight());
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
		$subtext = HeadwayBlocksData::get_blocks_by_type('hwr-subtext');
		$return = '';

		if ( !isset($subtext) || !is_array($subtext) )
			return $return;
		
		foreach ($subtext as $block_id => $layout_id) {


			$this->register_block_element(array(
				'id' => 'menu-' . $block_id,
				'name' => 'Menu',
				'selector' => '#block-' . $block_id . ' ul.menu',
				'properties' => array('background', 'borders', 'padding', 'rounded-corners', 'box-shadow')
			));
			$this->register_block_element(array(
				'id' => 'menu-item-' . $block_id,
				'name' => 'Menu Item',
				'selector' => '#nav-' . $block_id . ' ul.menu li a',
				'properties' => array('fonts' => array('font-family', 'font-size', 'color', 'font-styling', 'capitalization', 'letter-spacing', 'text-decoration'), 'background', 'borders', 'padding', 'rounded-corners', 'box-shadow', 'text-shadow'),
				'states' => array(
					'Selected' => 'ul.menu li.current_page_item > a, 
								   ul.menu li.current_page_parent > a, 
								   ul.menu li.current_page_ancestor > a, 
								   ul.menu li.current_page_item > a:hover, 
								   ul.menu li.current_page_parent > a:hover, 
								   ul.menu li.current_page_ancestor > a:hover', 
					'Hover' => 'ul.menu li > a:hover', 
					'Clicked' => 'ul.menu li > a:active'
				)
			));
			$this->register_block_element(array(
				'id' => 'menu-item-active-' . $block_id,
				'name' => 'Active Menu Item',
				'selector' => '#nav-' . $block_id . ' ul.menu li.current_page_item a',
				'properties' => array('fonts' => array('font-family', 'font-size', 'color', 'font-styling', 'capitalization', 'letter-spacing', 'text-decoration'), 'background', 'borders', 'padding', 'rounded-corners', 'box-shadow', 'text-shadow'),
				'states' => array(
					'Selected' => 'ul.menu li.current_page_item > a, 
								   ul.menu li.current_page_parent > a, 
								   ul.menu li.current_page_ancestor > a, 
								   ul.menu li.current_page_item > a:hover, 
								   ul.menu li.current_page_parent > a:hover, 
								   ul.menu li.current_page_ancestor > a:hover', 
					'Hover' => 'ul.menu li > a:hover', 
					'Clicked' => 'ul.menu li > a:active'
				)
			));
			$this->register_block_element(array(
				'id' => 'item-subtext-' . $block_id,
				'name' => 'Menu Item Subtext',
				'selector' => '#nav-' . $block_id . ' ul.menu li a span.subtext',
				'properties' => array('fonts' => array('font-family', 'font-size', 'color', 'font-styling', 'capitalization', 'letter-spacing', 'text-decoration'), 'background', 'borders', 'rounded-corners', 'box-shadow', 'text-shadow'),
				'states' => array(
					'Selected' => 'ul.menu li a:focus span.subtext', 
					'Hover' => 'ul.menu li a:hover span.subtext', 
					'Clicked' => 'ul.menu li a:active span.subtext'
				)
			));
			$this->register_block_element(array(
				'id' => 'sub-nav-menu-' . $block_id,
				'name' => 'Sub Menu',
				'selector' => '#nav-' . $block_id . ' ul.sub-menu',
				'properties' => array('background', 'borders', 'padding', 'rounded-corners', 'box-shadow')
			));
			$this->register_block_element(array(
				'id' => 'sub-menu-item-' . $block_id,
				'name' => 'Sub Menu Item',
				'selector' => '#nav-' . $block_id . ' ul.sub-menu li > a',
				'properties' => array(
					'fonts' => array('font-family', 'font-size', 'color', 'font-styling', 'capitalization', 'letter-spacing', 'text-decoration'), 
					'background', 
					'borders', 
					'padding', 
					'rounded-corners', 
					'box-shadow', 
					'text-shadow'
				),
				'states' => array(
					'Selected' => '
						#block-' . $block_id . ' ul.sub-menu li.current_page_item > a, 
						#block-' . $block_id . ' ul.sub-menu li.current_page_parent > a, 
						#block-' . $block_id . ' ul.sub-menu li.current_page_ancestor > a, 
						#block-' . $block_id . ' ul.sub-menu li.current_page_item > a:hover, 
						#block-' . $block_id . ' ul.sub-menu li.current_page_parent > a:hover, 
						#block-' . $block_id . ' ul.sub-menu li.current_page_ancestor > a:hover
					', 
					'Hover' => '#block-' . $block_id . ' ul.sub-menu li > a:hover', 
					'Selected' => 'ul.menu li.current_page_item a', 
					'Clicked' => '#block-' . $block_id . ' ul.sub-menu li > a:active'
				),
				'inherit-location' => 'block-navigation-menu-item'
			));
		}
	}
	
	function set_options_defaults() {
		$block = self::$block;
				
		global $headway_default_element_data;
		
		$subtext_blocks = HeadwayBlocksData::get_blocks_by_type('hwr-subtext');
		
		if ( !isset($subtext_blocks) || !is_array($subtext_blocks) )
			return isset($return);
			
		$new_headway_default_element_data = array();
		
		foreach ($subtext_blocks as $block_id => $layout_id) {
			$new_headway_default_element_data['block-hwr-subtext-menu-item-' . $block_id] = array(
				'properties' => array(
					'font-size' => '17',
					'capitalization' => 'none',
					'text-decoration' => 'none',
					'color' => '555555'
				),
				'special-element-state' => array(
					'selected' => array(
						'color' => 'C25B00'
					),
					'hover' => array(
						'color' => 'C25B00'
					)
				)
			);
			$new_headway_default_element_data['block-hwr-subtext-menu-item-active-' . $block_id] = array(
				'properties' => array(
					'color' => 'C25B00'
				)
			);
			$new_headway_default_element_data['block-hwr-subtext-item-subtext-' . $block_id] = array(
				'properties' => array(
					'font-size' => '12',
					'color' => '999999'
				)
			);
		}
		$headway_default_element_data = array_merge($headway_default_element_data, $new_headway_default_element_data);
	}
	

	function home_link_filter($menu) {
		
		$block = self::$block;
		
		if ( parent::get_setting($block, 'hide-home-link') )
			return $menu;
		
		if ( get_option('show_on_front') == 'posts' ) {

			$current = (is_home() || is_front_page()) ? ' current_page_item' : null;
			$home_text = ( parent::get_setting($block, 'home-link-text') ) ? parent::get_setting($block, 'home-link-text') : 'Home';
			$home_text_html = '<span class="text"> ' . $home_text . '</span>';
			$home_description = ( parent::get_setting($block, 'home-link-description') ) ? parent::get_setting($block, 'home-link-description') : 'Back home';
			$home_description_html = '<span class="subtext"> ' . $home_description . '</span>';
			
			if(self::is_menu_assigned($block) == true) {
				$menu_item_text = $home_text_html . $home_description_html;
			} else {
				$menu_item_text = $home_text_html;
			}

			if ( !HeadwayRoute::is_grid() && !headway_get('ve-live-content-query', $block) )
				$home_link = '<li class="menu-item-home' . $current . '"><a href="' . home_url() . '" class="top-level">' . $menu_item_text . '</a></li>';
			
			else
				$home_link = '<li class="menu-item-home' . $current . '"><a href="' . home_url() . '" class="top-level">' . $menu_item_text . '</a></li>';
			
		} else {
			
			$home_link = null;
			
		}

		return $home_link . $menu;
		
	}
	
	
	function fix_legacy_nav($menu) {
		
		$menu = preg_replace('/<ul class=[\'"]children[\'"]/', '<ul class="sub-menu"', trim($menu)); 
		$menu = preg_replace('/<div class=[\'"]menu[\'"]>/', '', $menu, 1); 
		$menu = str_replace('<ul>', '<ul class="menu">', $menu); 
						
		return substr(trim($menu), 0, -6);
				
	}
	
	public static function is_subtext() {
		$block = self::$block;
		return parent::get_setting($block, 'enable-subtext', true);
	}
	
}

class subtext_walker extends Walker_Nav_Menu {
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
		
		$description  = ! empty( $item->description ) ? '<span class="subtext">'.esc_attr( $item->description ).'</span>' : '';
		
		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';
		$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
		
		$item_output .= $description.$args->link_after;
		
		$item_output .= '</a>';
		$item_output .= $args->after;
		
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		
	}
}