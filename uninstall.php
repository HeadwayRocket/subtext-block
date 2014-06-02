<?php
/**
* @package   HeadwayRocket Framework
* @author    HeadwayRocket http://headwayrocket.com
*/

/* we exit if uninstall not called from WordPress exit */
if ( !defined('WP_UNINSTALL_PLUGIN') )
	exit();

/* we remove the upgrade flags from the db */
delete_option('hwr_subtext_upgrade');