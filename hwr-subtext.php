<?php
/*
Plugin Name: Headway Subtext Block
Plugin URI: http://www.headwayrocket.com/subtext
Description: A subtext block for Headway that makes it easy to add subtext to your WordPress menu items.
Version: 1.1.0
Author: HeadwayRocket
Author URI: http://www.headwayrocket.com
License: GNU GPL v2
*/
 
define('HWR_SUBTEXT_VERSION', '1.0.3');
define('HWR_SUBTEXT_PATH', plugin_dir_path(__FILE__));
define('HWR_SUBTEXT_URL', plugin_dir_url(__FILE__));

/* we call the Butler framework */
include(HWR_SUBTEXT_PATH . 'butler/init.php');

/* we call the HeadwayRocket framework and toolkit admin */
butler_load(HWR_SUBTEXT_PATH . 'headwayrocket/headwayrocket');

function subtext_block() {
	if ( !class_exists('Headway') )
		return;
	require_once 'block.php';
	require_once 'block-options.php';
	return headway_register_block('HeadwaySubtextBlock', plugins_url(false, __FILE__));

}
add_action('after_setup_theme', 'subtext_block');