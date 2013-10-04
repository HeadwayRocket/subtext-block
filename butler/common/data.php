<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/

butler_register_actions('butler_register_options', array('call' => array('butlerData', 'register_options')));	

class butlerData {

	public static $undefined_group = 'undefined_options_group';
	
	public static $options = array();
	
	public static function get_option($option = null, $group_name = false) {
					
		if ( $option === null )
			return false;
							
		if ( !$group_name ) 
			$group_name = self::$undefined_group;
		
		$group_data = self::get_options_group($group_name, false);
		
		/* we return the default value if the group or the option doesn't exist */
		if ( !$group_data || !isset($group_data[$option]) )	{
			
			$option = self::$options[$group_name][$option];
			
			if ( !isset($option['default']) )
				return false;
			
			return $option['default'];
			
		}
		
		return butler_clean_data($group_data[$option]);
		
	}
	
	
	public static function get_options_group($group_name = null, $default = true) {
	
		global $current_site;
		
		if ( $group_name === null )
			return false;
						
		if ( !is_multisite() )
			$group_data = get_option($group_name);
		else
			$group_data = get_blog_option($current_site->blog_id, $group_name);
			
		/* we return default value if the group doesn't exist */
		if ( !isset($group_data) && isset(self::$options[$group_name]) && $default ) {
			
			$group_data = array();
			
			$options = self::$options[$group_name];
			
			foreach ( $options as $id => $option ) {
			
				$option = isset($option['default']) ? $option['default'] : false;
							
				$group_data[$id] = $option;
			
			}
			
		}

		return $group_data;

	}
	
	
	public static function set_option($option = null, $value = null, $group_name = false) {
			
		if ( $option === null || $value === null )
			return false;
				
		if ( !$group_name ) 
			$group_name = self::$undefined_group;
		
		$group_data = get_option($group_name);
				
		$group_data[$option] = $value;
		
		update_option($group_name, $group_data);
		
		return true;
		
	}
	
	
	public static function delete_option($option = null, $group_name = false) {
			
		if ( $option === null )
			return false;
				
		if ( !$group_name ) 
			$group_name = self::$undefined_group;
			
		$group_data = self::get_options_group($group_name, false);
				
		/* we unset the options from the var */
		unset(self::$options[$group_name][$option]);
		
		/* we unset the options from the group data before we update the it */
		unset($group_data[$option]);
		
		/* we finally update the group without the option removed */
		update_option($group_name, $group_data);
		
		return true;
		
	}
	
	
	public static function delete_options_group($group_name = false) {
						
		if ( !$group_name ) 
			$group_name = self::$undefined_group;
			
		/* we remove the group from the var */
		unset(self::$options[$group_name]);
		
		/* we delete the group */	
		delete_option($group_name);
					
		return true;
		
	}

	
	public static function register_options($args) {
			
		if ( !isset($args) || ( !is_array($args) && !is_callable($args) ) )
			return false;			
		
		/* we determine if the groups is in a function and call it if it is the case */
		if ( is_callable($args) ) {
		
			$groups = call_user_func($args);
			$is_function = true;
		
		} else {
		
			$groups = $args;
			$is_function = false;
			
		}
										
		foreach ( $groups as $group_id => $group ) {			
			
			$rebuilt = array();
			
			/* we rebuild the group with the activations -> improvement: try to insert activation after option instead of rebuilding the array */
			foreach ( $group as $id => $option ) {
				
				if ( isset($option['activation']) ) {
				
					$activation_default = isset($option['activation']['default']) ? $option['activation']['default'] : true;
									
					$rebuilt[$id . '_activation'] = array(
						'type' => 'activation',
						'default' => $activation_default,
					);
				
				}
					
				$rebuilt[$id] = $option;
															
			}
			
			self::$options[$group_id] = $rebuilt;
			
			/* we set a callback if the argument passed is a function */
			self::$options[$group_id . '_callback'] = $is_function ? $args : false;
		
		}
			
	}

}