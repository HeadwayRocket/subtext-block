<?php

/* This class must be included in another file and included later so we don't get an error about HeadwayBlockOptionsAPI class not existing. */

class HeadwaySubtextBlockOptions extends HeadwayBlockOptionsAPI {

	function modify_arguments($args) {
		
		$this->tab_notices['nav-menu-content'] = 'To add items to this subtext navigation menu, go to <a href="' . admin_url('nav-menus.php') . '" target="_blank">WordPress Admin &raquo; Appearance &raquo; Menus</a>.  Then, create a menu and assign it to <em>' . HeadwayBlocksData::get_block_name($args['block_id']) . '</em> in the <strong>Theme Locations</strong> box.';
		
	}
	
	public $tabs = array(
		'nav-menu-content' => 'Content',
		'search' => 'Search',
		'home-link' => 'Home Link',
		'subtext' => 'Subtext',
		'orientation' => 'Orientation',
		'effects' => 'Effects'
	);

	public $inputs = array(
		'search' => array(
			'enable-nav-search' => array(
				'type' => 'checkbox',
				'name' => 'enable-nav-search',
				'label' => 'Enable Navigation Search',
				'default' => false,
				'tooltip' => 'If you wish to have a simple search form in the navigation bar, then check this box.  <em><strong>Note:</strong> the search form will not show if the Vertical Navigation option is enabled for this block.</em>'
			),
			
			'nav-search-position' => array(
				'type' => 'select',
				'name' => 'nav-search-position',
				'label' => 'Search Position',
				'default' => 'right',
				'options' => array(
					'left' => 'Left',
					'right' => 'Right'
				),
				'tooltip' => 'If you would like the navigation search input to snap to the left instead of the right, you can use this option.'
			),
			'nav-search-placeholder' => array(
				'type' => 'text',
				'name' => 'nav-search-placeholder',
				'label' => 'Search Placeholder',
				'default' => 'Type to search, then press enter',
				'tooltip' => 'This will be the text inside the search input telling the visitor how to interact with the search input.'
			),
			'nav-search-top-margin' => array(
				'type' => 'slider',
				'name' => 'nav-search-top-margin',
				'label' => 'Search Top Margin',
				'default' => 12,
				'slider-min' => -30,
				'slider-max' => 30,
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
				'default' => 0,
				'slider-min' => -30,
				'slider-max' => 30,
				'slider-interval' => 1,
				'unit' => 'px',
				'callback' => '
					id = $(input).attr("block_id");
					stylesheet.update_rule("#block-" + id + " .nav-search", {"margin-right": $(input).attr("value") + "px"});
				'			
			)
		),
		
		'home-link' => array(
			'hide-home-link' => array(
				'type' => 'checkbox',
				'name' => 'hide-home-link',
				'label' => 'Hide Home Link',
				'default' => false
			),
			
			'home-link-text' => array(
				'name' => 'home-link-text',
				'label' => 'Home Link Text',
				'type' => 'text',
				'tooltip' => 'If you would like the link to your homepage to say something other than <em>Home</em>, enter it here!',
				'default' => 'Home'
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
				'tooltip' => 'Instead of showing navigation horizontally, you can make the navigation show vertically.  <em><strong>Note:</strong> You may have to resize the block to make the navigation items fit correctly.</em>'
			)
		),

		'effects' => array(
			'effect' => array(
				'type' => 'select',
				'name' => 'effect',
				'label' => 'Drop Down Effect',
				'default' => 'fade',
				'options' => array(
					'none' => 'No Effect',
					'fade' => 'Fade',
					'slide' => 'Slide'
				),
				'tooltip' => 'This is the effect that will be used when the drop downs are shown and hidden.'
			),

			'hover-intent' => array(
				'type' => 'checkbox',
				'name' => 'hover-intent',
				'label' => 'Hover Intent',
				'default' => true,
				'tooltip' => 'Hover Intent makes it so if a navigation item with a drop down is hovered then the drop down will only be shown if the visitor has their mouse over the item for more than a split second.<br /><br />This reduces drop-downs from sporatically showing if the visitor makes fast movements over the navigation.'
			)
		)
	);
	
}