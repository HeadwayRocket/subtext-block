<?php

$option_group = 'hwr_subtext_upgrade';
$option = get_option($option_group);
$default_skin = defined('HEADWAY_DEFAULT_SKIN') ? HEADWAY_DEFAULT_SKIN : 'base';
$active_skin = HeadwayOption::get('current-skin', 'general', $default_skin);
$task = 'elements_mapping';

if ( empty($option) )
	$option = array();

if ( empty($option[$task]) )
	$option[$task] = array();
	
if ( isset($option[$task][$active_skin]) && $option[$task][$active_skin] ) {

	return;
	
} else {

	$block_type = 'hwr-subtext';
	
	$elements = array(
		'menu',
		'menu-item',
		'menu-item-active',
		'item-subtext',
		'sub-nav-menu',
		'sub-menu-item'
	);
		
	if ( version_compare(HEADWAY_VERSION, '3.4.5', '>=') ) {
		
	    /* we get all the blocks for this type */
	    $blocks = HeadwayBlocksData::get_blocks_by_type($block_type);
	    
	    if ( $blocks ) {
		    
		    $block_instance_elements = array();
		    
		    $all_elements = HeadwayElementsData::get_all_elements();
		    
		    /* we build the array with all the elements registered with the ID */
		    foreach ( $blocks as $block_id => $layout )
		    	foreach ( $elements as $element_id )
		    		$block_instance_elements[] = 'block-' . $block_type . '-' . $element_id . '-' . $block_id;
		    	    		
			/* we loop trough all the elements registered with the ID */
			foreach ( $block_instance_elements as $element ) {
				
				if ( !isset($all_elements[$element]) )
					continue;
			    	    
			    $instance_id = end(explode('-', $element));
			    
			    $element_with_no_instance = str_replace('-' . $instance_id, '', $element);
		
			    $instance_to_register = $element_with_no_instance . '-block-' . $instance_id;
			    
			    /* we map the element properties to the correct headway instances */
			    if ( isset($all_elements[$element]['properties']) )
				    foreach ( $all_elements[$element]['properties'] as $property => $property_value )
				        HeadwayElementsData::set_special_element_property('blocks', $element_with_no_instance, 'instance', $instance_to_register, $property, $property_value);
				
				/* we map or overwrite the properties if it was set for a layout since the "edit for current layout" was previously only styling the instance rather that all the block for the layout */
				if ( isset($all_elements[$element]['special-element-layout']) )
				    foreach ( $all_elements[$element]['special-element-layout'] as $layout => $properties )
				    	foreach ( $properties as $property => $property_value )
				        	HeadwayElementsData::set_special_element_property('blocks', $element_with_no_instance, 'instance', $instance_to_register, $property, $property_value);
				
			}
			
		}
	    
	    /* we finally set a flag in the db to say that the job is done */
	    $option[$task][$active_skin] = true;
	    
	    update_option($option_group, $option);
	
	}
	
}