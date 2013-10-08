<?php
/**
* @package   HeadwayRocket Framework
* @author    HeadwayRocket http://headwayrocket.com
*/

/* we don't load the framework if the butler framework isn't loaded */
if ( !defined('BUTLER_VERSION') )
	return;
	
do_action('butler_load_dependencies', array('register' => 'global'));

$component = array(
	'version' => '1.0.3',
	'id' => 'headwayrocket',
	'path' => trailingslashit(dirname(__FILE__))
);

do_action('butler_register', $component);

/* since butler_load doesn't load twice the same files, we don't bother including files from multiple components */
butler_load(array(HEADWAYROCKET_PATH . 'admin/admin' => 'HwrAdmin'));