<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/

butler_register_actions('butler_admin_tabs', array('call' => array('butlerAdminTabs', 'display')));	

class butlerAdminTabs {
	
	public static function init() {
		
		add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));		
			
	}
	
	
	public static function enqueue_assets() {
					
		/* we enqueue our stylesheets */
		wp_enqueue_style('btr-tabs', BUTLER_URL . 'admin/assets/css/tabs' . BUTLER_MIN_CSS . '.css', false, BUTLER_VERSION);
		
		/* we enqueue our js files */
		wp_enqueue_script('btr-tabs', BUTLER_URL . 'admin/assets/js/tabs' . BUTLER_MIN_JS . '.js', array('jquery'), BUTLER_VERSION);
		
	}
	
	
	public static function display($args = array()) {
		
		echo butler_render(BUTLER_PATH . 'admin/templates/tabs.php', array('tabs' => $args));
					
	}	

}