<?php
/*
Plugin Name: Headway Subtext Block
Plugin URI: http://www.headwayrocket.com/subtext
Description: A subtext block for Headway that makes it easy to add subtext to your WordPress menu items.
Version: 1.0.2
Author URI: http://www.headwayrocket.com
License: GNU GPL v2
*/
 
define('HWR_SUBTEXT_VERSION', '1.0.2');

function subtext_block() {
	if ( !class_exists('Headway') )
		return;
	require_once 'block.php';
	require_once 'block-options.php';
	return headway_register_block('HeadwaySubtextBlock', plugins_url(false, __FILE__));

}
add_action('after_setup_theme', 'subtext_block');

/* enable auto updates
***************************************************************/
function subtext_extend_updater() {
	if ( !class_exists('HeadwayUpdaterAPI') )
		return;
	$updater = new HeadwayUpdaterAPI(array(
		'slug' => 'hwr-subtext',
		'path' => plugin_basename(__FILE__),
		'name' => 'Subtext',
		'type' => 'block',
		'current_version' => HWR_SUBTEXT_VERSION
	));
}
add_action('init', 'subtext_extend_updater');