<?php
/**
* @package   HeadwayRocket Framework
* @author    HeadwayRocket http://headwayrocket.com
*/

$options = array(
	'hwr_framework' => array(
		'menu_title' => array(
			'label' => 'Menu',
			'type' => 'header'
		),
		'admin_bar_display_menu' => array(
			'label' => 'Admin Bar Menu',
			'type' => 'checkbox',
			'default' => true,
			'checkbox-label' => 'Show HeadwayRocket Menu',
			'description' => 'Set whether you would like to show the HeadwayRocket menu in the top admin bar.',
		)
	)
);
	
do_action('butler_register_options', $options);