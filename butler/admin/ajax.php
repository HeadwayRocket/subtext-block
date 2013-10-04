<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/

add_action( 'wp_ajax_butler_admin_save_options', 'butler_ajax_admin_save_options' );

function butler_ajax_admin_save_options() {
	
	/* we call the butler options since it wasn't globally loaded */
	do_action('butler_load_dependencies', array('options' => 'admin'));
	
	/* we set a delay simply because it is saving to fast and user can't see what is going on */
	sleep(1);
		
	butlerAdminOptions::save_options($_POST['entries']);
	
	exit;

}