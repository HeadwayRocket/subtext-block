<?php
/*
Plugin Name: Subtext Navigation Block
Plugin URI: http://www.headwaylabs.com
Description: A subtext navigation block for Headway that makes it easy to add subtext to your WordPress menu items.
Author URI: http://www.headwaylabs.com
License: GNU GPL v2
*/

/**
 * Everything runs at the after_setup_theme action to insure that 
 * all of Headway's classes and functions are loaded.
 **/

function register_block() {
	
	/* Make sure that Headway is activated, otherwise don't register the block because errors will be thrown. */
	if ( !class_exists('Headway') )
		return;
	require_once 'block.php';
	require_once 'block-options.php';

	return headway_register_block('HeadwaySubtextNavigationBlock', substr(WP_PLUGIN_URL . '/' . str_replace(basename(__FILE__), '', plugin_basename(__FILE__)), 0, -1));

}
add_action('after_setup_theme', 'register_block');