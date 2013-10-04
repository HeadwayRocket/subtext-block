<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/

add_action('butler_load_framework', array('butlerFramework', 'init'), 10, 1);

class butlerFramework {

	public static $loaded = array();
	
	public static function init() {
	
		/* this hook is not added in the hooks file since it is used to load it */
		add_action('butler_load_dependencies', array(__CLASS__, 'load_dependencies'), 10, 1);
		
		/* we load the default components which need to be loaded before the optional ones */
		self::load(array(
			BUTLER_PATH . 'common/functions',
			BUTLER_PATH . 'common/data'
		));
		
		/* we load the default components which need to be loaded after the optional admin ones */
		if ( is_admin() )
			self::load(BUTLER_PATH . 'admin/ajax');
		
		define('BUTLER_URL', butler_path_to_url(BUTLER_PATH));
		
		/* we register the framework assets to make it available for users */
		add_action('admin_enqueue_scripts', array(__CLASS__, 'register_assets'));
				
	}
	
	
	public static function register_assets($dependencies = null) {
	
		wp_register_style('btr-butler-ui', BUTLER_URL . 'admin/assets/css/ui' . BUTLER_MIN_CSS . '.css', false, BUTLER_VERSION);
	
	}
	
	
	public static function load_dependencies($dependencies = null) {
	
 		if ( !$dependencies )
			return false;
						
		/* we declare all the components available in our framework */
		$components = array(
			'tabs' => array(
				BUTLER_PATH . 'admin/tabs' => 'butlerAdminTabs'
			),
			'options' => array(
				BUTLER_PATH . 'admin/options' => 'butlerAdminOptions',
				BUTLER_PATH . 'admin/inputs'
			),
			'meta-boxes' => BUTLER_PATH . 'admin/meta-boxes',
			'activation' => BUTLER_PATH . 'admin/activation',
			'register' => BUTLER_PATH . 'common/register'
		);
		
		$is_admin = is_admin();
		
		/* we load the components requested */
		foreach ( butler_maybe_array($dependencies) as $component => $restriction ) {
			
			/* we make sure the component called is part of the framework */
			if ( !isset($components[$component]) )
				continue;
				
			switch ( $restriction ) {
			
				case 'global':
					self::load($components[$component]);
				break;
				
				case 'admin':
					if ( $is_admin )		
						self::load($components[$component]);
				break;
				
				case 'frontend':
					if ( !$is_admin )		
						self::load($components[$component]);
				break;
				
				case is_array($restriction):
					foreach ( $restriction as $type => $page )
						if ( butler_get($type) == $page )
							self::load($components[$component]);
				break;
			
			}
													
		}
					
		return true;
			
	}
		
		
	public static function load($files, $init = false) {
			
		if ( !is_array($files) )
			$files = array($files => $init);
	
		$classes_to_init = array();
						
		foreach ( $files as $file => $init ) {
					
			/* we set the default init argument if it is not set */
			if ( is_numeric($file) ){
				$file = $init;
				$init = false;
			} 
			
			/* we don't require the file if it has already been loaded */
			if ( in_array($file, self::$loaded) )
				continue;
						
			/* we add the php extension if it is not already */
			if ( strpos($file, '.php') == false )
				require_once $file . '.php';
				
			/* we add the file to the loaded array */
			self::$loaded[] = $file;
			
			/* we figure out what the class name is if init is true, otherwise we use the class provided */
			if ( $init )
				$classes_to_init[] = $init;
						
		}
		
		/* we are ready to initiated classes which require so */
		foreach ( $classes_to_init as $class ) {
			
			if ( method_exists($class, 'init') )
				call_user_func(array($class, 'init'));
			else
				trigger_error($class . '::init is not a valid method', E_USER_WARNING);
			
		}
		
	}
		
}