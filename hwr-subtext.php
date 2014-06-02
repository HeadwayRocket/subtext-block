<?php
/*
Plugin Name: Headway Subtext Block
Plugin URI: http://www.headwayrocket.com/subtext
Description: A subtext block for Headway that makes it easy to add subtext to your WordPress menu items.
Version: 1.2.1
Author: HeadwayRocket
Author URI: http://www.headwayrocket.com
License: GNU GPL v2
*/
 
define('HWR_SUBTEXT_VERSION', '1.2.1');
define('HWR_SUBTEXT_PATH', plugin_dir_path(__FILE__));
define('HWR_SUBTEXT_URL', plugin_dir_url(__FILE__));

add_action('after_setup_theme', 'subtext_block');

function subtext_block() {

	if ( !class_exists('Headway') )
		return;

	require_once 'block.php';
	require_once 'block-options.php';
	require_once 'maintenance.php';

	return headway_register_block('HeadwaySubtextBlock', plugins_url(false, __FILE__));

}

