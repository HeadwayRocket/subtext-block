<?php
/**
* @package   HeadwayRocket Framework
* @author    HeadwayRocket http://headwayrocket.com
*/

class HwrAdmin {

	public static $dashoard_extension = 'hwr-dashboard';
	public static $api_products_request_code = null;

	public static function init() {
		
		define('HEADWAYROCKET_DASHBOARD_URL', admin_url('admin.php?page=' . self::$dashoard_extension));
		
		$dependencies = array(
			'tabs' => array('page' => 'hwr-dashboard'),
			'options' => array('page' => 'hwr-dashboard'),
		);
					
		do_action('butler_load_dependencies', $dependencies);
		
		/* we register the options so that it is accessible as soon as possible, in fact, from this point */
		butler_load(HEADWAYROCKET_PATH . 'admin/options');
		
		if ( butler_get_option('admin_bar_display_menu', 'hwr_framework') )
			add_action('admin_bar_menu', array(__CLASS__, 'add_admin_hwr'), 76);
		
		/* we prevent all the admin stuff from loading in the frontend */
		if ( !is_admin() )
			return;
		
		butler_load(HEADWAYROCKET_PATH . 'hwr-api-wp');
				
		add_action('admin_menu', array('HwrAdmin', 'admin_menu'));
		add_action('admin_head', array(__CLASS__, 'hwr_wp_head_script' ));
		add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
		add_action('hwr_dashboard_alert', array(__CLASS__, 'dashboard_alert'));
		
	}
	
	
	public static function enqueue_assets() {
	
		wp_enqueue_style('hwr-global', HEADWAYROCKET_URL . 'admin/assets/css/global' . BUTLER_MIN_CSS . '.css', false, HEADWAYROCKET_VERSION);
		
		/* we load the butler ui css for the dashboard */
		if ( isset($_GET['page']) && $_GET['page'] == self::$dashoard_extension ) {
		
			wp_enqueue_style( 'hwr-dashboard', HEADWAYROCKET_URL . 'admin/assets/css/dashboard' . BUTLER_MIN_CSS . '.css', false, HEADWAYROCKET_VERSION);
		
		}
		
	}
	
	
	public static function add_admin_hwr() {
	
		global $wp_admin_bar;
		
		$wp_admin_bar->add_menu(array(
			'id' => 'headwayrocket', 
			'title' => 'HeadwayRocket', 
			'href' => HEADWAYROCKET_DASHBOARD_URL
		));
		
	}
		
	public static function hwr_wp_head_script() {
	
		echo '<script type="text/javascript">';
			echo 'hwr_is_admin = "' .  is_admin() . '";';
			echo 'hwr_admin_url = "' .  admin_url() . '";';
			echo 'hwr_loader_grey = "' . HEADWAYROCKET_URL . '/assets/images/loader-grey.gif' . '";';
		echo '</script>';
					
	}
	
	
	public static function parent_menu() {
			
		return array(
			'page-title' => 'HeadwayRocket',
			'menu-title' => 'HeadwayRocket',
			'menu-slug' => self::$dashoard_extension,
			'function' => array('HwrAdmin', 'display_dashboard')
		);
		
	}
	
	
	public static function add_admin_separator($position){
				
		global $menu;
				
		$menu[$position] = array('', 'read', 'separator-headwayrocket', '', 'wp-menu-separator headwayrocket-separator');
	
		ksort($menu);
		
	}
	
	
	public static function admin_menu() {
	
		$parent_menu = self::parent_menu();
		
		self::add_admin_separator(51);
		
		/* we add the main menu */
		add_menu_page($parent_menu['page-title'],$parent_menu['menu-title'], 'manage_options', $parent_menu['menu-slug'], $parent_menu['function'], false, 52);
		
		/* we add the submenus */
		add_submenu_page($parent_menu['menu-slug'], 'Dashboard', 'Dashboard', 'manage_options', self::$dashoard_extension, array('HwrAdmin', 'display_dashboard'));
	
	}
	
	
	public static function display_dashboard() {
				
		if ( butler_get('refresh') == true )
			$products = self::get_products('all', true);
		else
			$products = self::get_products();	
					
		echo butler_render(HEADWAYROCKET_PATH . 'admin/templates/dashboard.php', array('products' => $products));
		
	}
	
	
	public static function dashboard_alert() {
	
		/* we display a notice if we could not get or refresh the api data */	
		if ( self::$api_products_request_code ) {
		
			echo '<div class="updated">';
				echo '<p>Whoops! ';
					 
					if ( self::$api_products_request_code == 1 )
						echo 'It would seem that you are not connected to the internet. Please check your internet connection and try again.';
					else
						echo 'Our update server seems to be offline. It’s likely that we’re doing maintainance on the server, so please try again later.';

					echo '<a href="' . add_query_arg(array('refresh' => 'true'), HEADWAYROCKET_DASHBOARD_URL) . '" class="button-secondary">Try again</a>';
				echo '</p>';
			echo '</div>';
		
		}		

	}
		
		
	public static function get_products($filter = 'all', $refresh = false) {
	
		$hwr_api = new HWRAPI();
		
		if ( $refresh )
			$api_request_products = $hwr_api->refresh('products');
		else
			$api_request_products = $hwr_api->request('products', 60*60*7);
		
		/* we set an error code if our api call fails */						
		if ( isset($api_request_products['response']['error']) ) {
		
			self::$api_products_request_code = $api_request_products['code'];
			
			return null;
			
		}
		
		/* we set a notice code if our api call fails on refresh */	
		if ( $refresh && $api_request_products['call'] == 'database' )
			self::$api_products_request_code = $api_request_products['code'];
					
		$hwr_products = $api_request_products['response']['data'];
				
		$hwr_products['last-update'] = date("d M y - h:i A (e)", $api_request_products['response']['last-update']);
		
		$get_plugins = get_plugins();
		
		$hwr_products['add-ons-installed'] = array();
		
		foreach ( $get_plugins as $key => $plugin ) {
			
			/* we only take our plugins */
			if ( !isset($plugin['AuthorName']) || $plugin['AuthorName'] != 'HeadwayRocket' )
				continue;
			
			$is_active = is_plugin_active($key);
			
			$type = explode('/', $plugin['PluginURI']);
			
			$hwr_products['add-ons-installed'][] = end($type);
			
			if ( $is_active )
				$hwr_products['add-ons-active'][] = end($type);
	    		
		    $hwr_products['add-ons'][end($type)]['installed'] = array(
		        'plugin' => $key, 
		        'version' => $plugin['Version'],
		        'name' => $plugin['Name'], 
		        'is_active' => $is_active,
		        'activation_url' => $is_active ? '' : wp_nonce_url('plugins.php?action=activate&plugin=' . $key, 'activate-plugin_' . $key)
		    );
		    				
		}
		
		return $hwr_products;
	
	}
			
}