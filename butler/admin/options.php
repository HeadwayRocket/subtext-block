<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/

$actions = array(
	'butler_admin_display_options' => array(
		'call' => array('butlerAdminOptions', 'display_options')
	),
	'butler_admin_display_save_options' => array(
		'call' => array('butlerAdminOptions', 'display_save_options')
	),
	'butler_admin_display_save_options_message' => array(
		'call' => array('butlerAdminOptions', 'display_save_options_message')
	),
	'butler_admin_save_options' => array(
		'call' => array('butlerAdminOptions', 'save_options')
	)
);

butler_register_actions($actions);

class butlerAdminOptions {
	
	public static function init() {
			
		add_action('init', array(__CLASS__, 'save_options'), 12);
		add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_assets'));
			
	}
	
	
	public static function enqueue_assets() {
					
		/* we enqueue our stylesheets */
		wp_enqueue_style('btr-options', BUTLER_URL . 'admin/assets/css/options' . BUTLER_MIN_CSS . '.css', false, BUTLER_VERSION);
		wp_enqueue_style('btr-butler-ui', BUTLER_URL . 'admin/assets/css/ui' . BUTLER_MIN_CSS . '.css', false, BUTLER_VERSION);
		
		/* we enqueue our js files */
		wp_enqueue_script('btr-options', BUTLER_URL . 'admin/assets/js/options' . BUTLER_MIN_JS . '.js', array('jquery'), BUTLER_VERSION);
		
	}
	
		
	public static function display_options($groups = array()) {
	
		foreach ( butler_maybe_array($groups) as $group ) {
				
			/* we display a notice if no group are specified */
			if ( empty($group) ) {
			
				echo '<div id="message" class="error"><p>The options group(s) need to be specified as an agrument.</p></div>';
			
			}
			/* we display a notice if the option group wasn't register */
			elseif ( !isset(butlerData::$options[$group]) ) {
			
				echo '<div id="message" class="error"><p>This option group "' . $group . '" is not registered.</p></div>';
			
			} else {
				
				/* we refresh the data if the group is in a function */
				if ( butlerData::$options[$group . '_callback'] )
					do_action('butler_register_options', butlerData::$options[$group . '_callback']);
				
				echo butler_render(BUTLER_PATH . 'admin/templates/form/options.php', array('group' => $group, 'options' => butlerData::$options[$group]));
			
			}
					
		}			
		
	}
	
	
	public static function display_save_options($args = array()) {
				
		echo butler_render(BUTLER_PATH . 'admin/templates/form/save-options.php', array('args' => $args));
					
	}
	
		
	public static function save_options($data = null) {
								
		if ( !butler_post('btr_admin_options_nonce', $data) || !wp_verify_nonce(butler_post('btr_admin_options_nonce', $data), 'btr-admin-options-nonce') )
			return false;
			
		/* we save the options if the form contains options */
		if ( butler_post('btr_options_group', $data) ) {
			
			$group_names = butler_post('btr_options_group', $data) ? butler_post('btr_options_group', $data) : butlerData::$undefined_group;
			
			foreach ( butler_maybe_array($group_names) as $group_name ) {
			
				if ( butler_post($group_name, $data) == false )
					continue;
			
				foreach ( butler_post($group_name, $data) as $option => $value )
					butlerData::set_option($option, $value, $group_name);
					
			}
			
		}
				
		return true;
		
	}	
	
	
	public static function display_save_options_message($success = null) {
		
		if ( !butler_post('btr-submit'))
			return false;
		
		if ( !wp_verify_nonce(butler_post('btr-admin-options-nonce'), 'btr-admin-options-nonce') ) {
			
			echo '<div id="message" class="error"><p>Settings could not be saved.</p></div>';
			
			return false;
			
		}
		
		$message = $success ? $success : 'Settings saved.';

		echo '<div class="updated"><p>' . $message . '</p></div>';
		
		return true;
		
	}

}