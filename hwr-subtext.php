<?php
/*
Plugin Name: Headway Subtext Block
Plugin URI: http://www.headwayrocket.com/subtext
Description: A subtext block for Headway that makes it easy to add subtext to your WordPress menu items.
Version: 1.0.3
Author URI: http://www.headwayrocket.com
License: GNU GPL v2
*/
 
define('HWR_SUBTEXT_VERSION', '1.0.3');

function subtext_block() {
	if ( !class_exists('Headway') )
		return;
	require_once 'block.php';
	require_once 'block-options.php';
	return headway_register_block('HeadwaySubtextBlock', plugins_url(false, __FILE__));

}
add_action('after_setup_theme', 'subtext_block');