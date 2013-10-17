<?php
/**
* @package   HeadwayRocket Framework
* @author    HeadwayRocket http://headwayrocket.com
*/

/* we exit if uninstall not called from WordPress exit */
if ( !defined('WP_UNINSTALL_PLUGIN') )
	exit();

/* we call the butler framework uninstall file */ 
require_once 'butler/uninstall.php';

/* we call the headwayrocket framework uninstall file */
require_once 'headwayrocket/uninstall.php';

/* we remove the upgrade flags from the db */
delete_option('hwr_subtext_upgrade');