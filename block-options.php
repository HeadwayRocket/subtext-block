<?php

/* This class must be included in another file and included later so we don't get an error about HeadwayBlockOptionsAPI class not existing. */

class HeadwaySubtextNavigationBlockOptions extends HeadwayBlockOptionsAPI {
	
	public $tabs = array(
		'navigation-content' => 'Content',
		'home-link' => 'Home Link',
		'subtext' => 'Subtext',
		'search' => 'Search',
		'orientation' => 'Orientation'
	);

	public $inputs = array(
		'home-link' => array(
			'enable-home-link' => array(
				'type' => 'checkbox',
				'name' => 'enable-home-link',
				'label' => 'Enable Home Link',
				'default' => true
			),
			'home-link-text' => array(
				'name' => 'home-link-text',
				'label' => 'Home Link Text',
				'type' => 'text',
				'tooltip' => 'If you would like the link to your homepage to say something other than <em>Home</em>, enter it here!',
				'default' => 'Home'
			),
			'home-link-description' => array(
				'name' => 'home-link-description',
				'label' => 'Home Link Description',
				'type' => 'text',
				'tooltip' => 'This will add a subtext description for the home menu item.',
				'default' => 'Back home'
			)
		),
		'subtext' => array(
			'enable-subtext' => array(
				'type' => 'checkbox',
				'name' => 'enable-subtext',
				'label' => 'Enable Subtext',
				'tooltip' => 'This will enable subtext on your menu items that have descriptions.',
				'default' => true,
				'callback' => '
					id = $(input).attr("block_id");
					if(value == false) {
						stylesheet.update_rule("#block-" + id + " ul li a span.subtext", {"display": "none", "line-height": "40"});
					} else {
						stylesheet.update_rule("#block-" + id + " .nav-horizontal ul.menu > li > a", {"height": "55"});
						stylesheet.update_rule("#block-" + id + " ul li a span.subtext", {"display": "block", "line-height": "1"});
					}'
			),
			'subtext-margin' => array(
				'type' => 'slider',
				'name' => 'subtext-margin',
				'label' => 'Subtext Top Margin',
				'default' => 3,
				'slider-min' => -15,
				'slider-max' => 15,
				'slider-interval' => 1,
				'unit' => 'px',
				'callback' => '
					id = $(input).attr("block_id");
					stylesheet.update_rule("#block-" + id + " ul li a span.subtext", {"margin-top": $(input).attr("value") + "px"});
				'
			)
		),
		'search' => array(
			'enable-nav-search' => array(
				'type' => 'checkbox',
				'name' => 'enable-nav-search',
				'label' => 'Enable Menu Search',
				'default' => true,
				'tooltip' => 'If you wish to have a simple search form in the menu bar, then check this box.  <em><strong>Note:</strong> the search form will not show if the Vertical Menu option is enabled for this block.</em>'
			),
			'nav-search-position' => array(
				'type' => 'select',
				'name' => 'nav-search-position',
				'label' => 'Search Position',
				'default' => 'right',
				'options' => array(
					'left' => 'Left',
					'right' => 'Right'),
			),
			'nav-search-top-margin' => array(
				'type' => 'slider',
				'name' => 'nav-search-top-margin',
				'label' => 'Search Top Margin',
				'default' => 12,
				'slider-min' => -15,
				'slider-max' => 15,
				'slider-interval' => 1,
				'unit' => 'px',
				'callback' => '
					id = $(input).attr("block_id");
					stylesheet.update_rule("#block-" + id + " .nav-search", {"margin-top": $(input).attr("value") + "px"});
				'
			),
			'nav-search-right-margin' => array(
				'type' => 'slider',
				'name' => 'nav-search-right-margin',
				'label' => 'Search Right Margin',
				'default' => -9,
				'slider-min' => -15,
				'slider-max' => 15,
				'slider-interval' => 1,
				'unit' => 'px',
				'callback' => '
					id = $(input).attr("block_id");
					stylesheet.update_rule("#block-" + id + " .nav-search", {"margin-right": $(input).attr("value") + "px"});
				'			
			),
		),
		'orientation' => array(
			'alignment' => array(
				'type' => 'select',
				'name' => 'alignment',
				'label' => 'Alignment',
				'default' => 'left',
				'options' => array(
					'left' => 'Left',
					'right' => 'Right',
					'center' => 'Center'
				)
			),
			'vert-nav-box' => array(
				'type' => 'checkbox',
				'name' => 'vert-nav-box',
				'label' => 'Vertical Navigation',
				'default' => false,
				'tooltip' => 'Instead of showing menu horizontally, you can make it show vertically.  <em><strong>Note:</strong> You may have to resize the block to make the menu items fit correctly.</em>'
			)
		)
	);
	
	
	function modify_arguments($args) {
		
		$this->tab_notices['navigation-content'] = 'To add items to this menu, go to <a href="' . admin_url('nav-menus.php') . '" target="_blank">WordPress Admin &raquo; Appearance &raquo; Menus</a>.  Then, create a menu and assign it to <em>' . HeadwayBlocksData::get_block_name($args['block_id']) . '</em> in the <strong>Theme Locations</strong> box.';
		
	}
	
}