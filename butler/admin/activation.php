<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/

butler_register_actions('butler_admin_activation', array('call' => array('butlerAdminActivation', 'init'), 'nb_agrs' => 4));

class butlerAdminActivation {

	public static $file;
	public static $redirect;
	public static $callback;
	public static $args;
	
	public static function init($file, $redirect = false, $callback = false, $args = null) {
	
		$name = explode('/', $file);
				
		self::$file = preg_replace('/[^a-zA-z0-9]/s', '_', end($name));
		self::$redirect = $redirect;
		self::$callback = $callback;
		self::$args = butler_maybe_array($args);
		
		register_activation_hook($file, array(__CLASS__, 'register_activation'));			
		
		add_action('admin_init', array(__CLASS__, 'action'));
		
	}
	
	public static function register_activation() {
					
		add_option(self::$file, true);
				
	}
		
	public static function action() {
	
	    if ( get_option(self::$file, false) ) {
	    
	        delete_option(self::$file);
	        
	        if ( self::$callback && is_callable(self::$callback) ) 
	            call_user_func(self::$callback, self::$args);
	        
	        if ( !isset($_GET['activate-multi']) && self::$redirect ) 
	            wp_redirect(self::$redirect);
	            
	    }
		
	}
	
}