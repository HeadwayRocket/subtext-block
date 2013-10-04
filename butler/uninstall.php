<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/

/* we exit if uninstall not called from WordPress exit */
if ( !defined('WP_UNINSTALL_PLUGIN') )
	exit();

/* we remove the versions registered in the db */
delete_option('butler_versions');
