<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/

butler_register_actions('butler_register', array('call' => array('butlerRegister', 'init')));	

class butlerRegister {

	public static function init($args = null) {
		
		if ( !$args || !is_array($args) )
			return;
						
		$version = $args['version'];
		$component = $args['id'];
		$component_cap = strtoupper($component);
		$path = $args['path'];
		$butler_versions = get_option('butler_versions');
		
		if ( !isset($butler_versions[$component]) || $version > $butler_versions[$component]['version'] || ( $butler_versions[$component]['path'] == $path && $version < $butler_versions[$component]['version'] ) || realpath($butler_versions[$component]['path']) == '' ) {
						
			$butler_versions[$component] = array(
				'version' => $version,
				'path' => $path
			);
		
			update_option('butler_versions', $butler_versions);
				
		} else {
		
			$version = $butler_versions[$component]['version'];
			$path = $butler_versions[$component]['path'];
		
		}
		
		if ( !defined($component_cap . '_VERSION') ) {
				
			define($component_cap . '_VERSION', $version);
			define($component_cap . '_PATH', $path);
			define($component_cap . '_URL', butler_path_to_url(constant($component_cap . '_PATH')));
						
			if ( isset($args['callback']) && is_callable($args['callback']) ) 
			    call_user_func($args['callback']);
			
		}
						
	}
	
}