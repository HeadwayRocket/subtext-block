<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/

function butler_render($template, $args = array()) {
			
	extract($args);
	ob_start();
	
		include($template);
    
    return ob_get_clean();

}


function butler_path_to_url($path) {
				
    $root = str_replace('/wp-content/themes', '', get_theme_root());
    	
    $clean_path = str_replace($root, '', $path);
    	
    return home_url() . $clean_path;

}


function butler_version_control($component, $version) {

	if ( !$component || !$version )
		return false;

	$butler_versions = get_option('butler_versions');
					
	if ( !isset($butler_versions[$component]) ) {
		
		$butler_versions[$component] = $version;
	
		update_option('butler_versions', $butler_versions);
	
	} elseif ( $butler_versions[$component] > $version ) {
	
		return false;
	
	}
	
	return true;
	
}


function butler_clean_data($data) {
	
	if ( is_numeric($data) ) {
		
		if ( floatval($data) == intval($data) )
			return (int)$data;
		else
			return (float)$data;
		
	} elseif ( $data === 'true' || $data === 'on' ) {
		
	 	return true;
		
	} elseif ( $data === 'false' ) {
		
	 	return false;
		
	} elseif ( $data === '' || $data === 'null' ) {
		
		return null;
		
	} else {

		$data = maybe_unserialize($data);
		
		if ( !is_array($data) ) {
			return stripslashes($data);
			
		} else {
			
			return array_map('maybe_unserialize', $data);
			
		}
		
	}
	
}


function butler_maybe_array($element) {
	
	if ( is_array($element) )
		return $element;
		
	return array($element);

}


function butler_get($name, $array = false, $default = null) {
	
	if ( $array === false )
		$array = $_GET;
	
	if ( (is_string($name) || is_numeric($name)) && !is_float($name) ) {

		if ( is_array($array) && isset($array[$name]) )
			return $array[$name];
		elseif ( is_object($array) && isset($array->$name) )
			return $array->$name;

	}
		
	return $default;	
		
}


function butler_post($name, $data = null) {

	if ( $data )
		$_POST = $data;
	
	return butler_get($name, $_POST);
	
}


function butler_get_post_types($exclude = false, $ids = false) {

	$return = array();
	
	$post_types = get_post_types(false, 'objects');
		
	foreach ( $post_types as $post_type_id => $post_type ) {
		
		/* we make sure the post type is not an excluded post type. */
		if ( in_array($post_type_id, array('revision', 'nav_menu_item')) || in_array($post_type_id, butler_maybe_array($exclude)) ) 
			continue;
		
		if ( $ids )
			$return[] = $post_type_id;
		else
			$return[$post_type_id] = $post_type->labels->name;	
		
	
	}
	
	return $return;

}


function butler_get_post_items($post_type, $extra = array()) {
		
	$args = array(
	    'posts_per_page' => -1,
	    'post_type' => $post_type,
	    'post_status' => 'publish',
	    'suppress_filters' => true
	);
	    
	$post_type_query = get_posts($args);
	
	$items = $extra;
			
	foreach ( $post_type_query as $item )
		$items[$item->ID] = $item->post_title;
					
	return $items;

}


function butler_load($files, $init = false) {
		
	return butlerFramework::load($files, $init = false);

}


function butler_get_int($string) {

	preg_match("/([0-9]+[\.,]?)+/", $string, $matches);
	
	if ( !isset($matches[0]) ) 
		return false;
	
	return $matches[0];
	
}


function butler_troncate($content, $tag = '<!--plus-->', $more_text = 'More...') {
		
	if ( preg_match('/' . $tag . '/', $content, $matches) ) {
	
		list($main, $extended) = explode($matches[0], $content, 2);
		
	} else {
	
		$main = $content;
		$extended = '';
		$more_text = '';
		
	}

	/* we scandirtrip leading and trailing whitespace */
	$main = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $main);
	$extended = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $extended);
	$more_text = preg_replace('/^[\s]*(.*)[\s]*$/', '\\1', $more_text);

	return array(
		'main' => $main,
		'extended' => $extended,
		'more_text' => $more_text
	);
		
}


function butler_get_option($option = null, $group_name = null) {

	return butlerData::get_option($option, $group_name);
	
}


function butler_register_actions($actions, $args = null) {

	if ( !is_array($actions) )
		$actions = array($actions => $args);

	foreach ( $actions as $tag => $params ) {
		
		$priority = isset($params['call']) ? $params['call'] : null;
		$priority = isset($params['priority']) ? $params['priority'] : 10;
		$nb_agrs = isset($params['nb_agrs']) ? $params['nb_agrs'] : 1;
				
		if ( is_array($params['call']) && is_callable($params['call']) )
			add_action($tag, $params['call'], $priority, $nb_agrs);
		
	}

}