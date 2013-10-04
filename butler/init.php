<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/

$version = '1.0.1';
$component = 'butler';
$path = trailingslashit(dirname(__FILE__));
$butler_versions = get_option('butler_versions');
$minify_assets = WP_DEBUG ? false : true;
				
if ( !isset($butler_versions[$component]) || $version > $butler_versions[$component]['version'] || ( $butler_versions[$component]['path'] == $path && $version < $butler_versions[$component]['version'] ) || realpath($butler_versions[$component]['path']) == '' ) {
	
	$butler_versions[$component] = array(
		'version' => $version,
		'path' => $path
	);

	update_option('butler_versions', $butler_versions);
		
} else {

	$version = $butler_versions[$component]['version'];
	$path = $butler_versions[$component]['path'];

}

if ( !class_exists('butlerFramework') ) {

	define('BUTLER_VERSION', $version);
	define('BUTLER_PATH', $path);
	define('BUTLER_MIN_CSS', $minify_assets ? '.min' : '');
	define('BUTLER_MIN_JS', $minify_assets ? '.min' : '');

	include_once BUTLER_PATH . 'butler.php';
		
	do_action('butler_load_framework');
	
}