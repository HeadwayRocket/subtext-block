<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/

class butlerAdminInputs {

	public static function display($option) {
			
		$default = array(
			'default' => false,
			'activation' => false,
			'checkbox-label' => 'Enable',
		);
		
		$option = array_merge($default, $option);
		$option['name'] = 'name="' . $option['group'] . '[' . $option['id'] . ']"';
		$option['value'] = butler_get_option($option['id'], $option['group']);
		
		if ( $option['activation'] )
			echo call_user_func('butlerAdminInputs::activation', $option);
			
		if ( $option['activation'] )
			echo '<div class="has-activation">';
		
			echo call_user_func('butlerAdminInputs::' . $option['type'], $option);
		
		if ( $option['activation'] )
			echo '</div>';
				
	}
	
	
	public static function activation($option) {
	
		$default = isset($option['activation']['default']) ? $option['activation']['default'] : true;
						
		$value = butler_get_option($option['id'] . '_activation', $option['group'], $default);
		
		$checked = $value == true ? ' checked="checked"' : null;
		
		echo '<input type="hidden" value="0" name="' . $option['group'] . '[' . $option['id'] . '_activation]" />';
		
		echo '<input class="checkbox-field activation on-' . $option['type'] . '" type="checkbox" name="' . $option['group'] . '[' . $option['id'] . '_activation]" value="1" id="' . $option['id'] . '_activation" ' . $checked . ' />';
		
	}
	
	
	public static function text($option) {
	
		echo '<input class="text-field"  type="text" ' . $option['name'] . ' value="' . $option['value'] . '" id="' . $option['id'] . '">';
		
	}
	
	
	public static function textarea($option) {
				
		echo '<textarea class="textarea-field" ' . $option['name'] . ' id="' . $option['id'] . '">' . $option['value'] . '</textarea>';
		
	}
	
	
	public static function radio($option) {
		
		$option['default'] = isset($checkbox['default']) ? $checkbox['default'] : key($option['radios']);
		
		echo '<fieldset>';
				
			foreach ( $option['radios'] as $id => $radio ) {
								
				$checked = $id == $option['value'] ? ' checked="checked"' : null;
								
				echo '<label class="radio-label" for="' . $id . '">';
				
					echo '<input class="radio-field" type="radio" ' . $option['name'] . ' value="' . $id . '" id="' . $id . '" ' . $checked . '/>';
					
				echo $radio . '</label>';
				
			}
					
		echo '</fieldset>';	
		
	}
	
	
	public static function checkbox($option) {
				
		$checked = $option['value'] ? ' checked="checked"' : null;
				
		echo '<label class="checkbox-label" for="' . $option['id'] . '">';
		
			echo '<input type="hidden" value="0" ' . $option['name'] . ' />';
			
			echo '<input class="checkbox-field" type="checkbox" ' . $option['name'] . ' value="1" id="' . $option['id'] . '" ' . $checked . ' />';
			
		echo $option['checkbox-label'] . '</label>';	
		
	}
	
	
	public static function multicheckbox($option) {
				
		echo '<fieldset>';
			
			foreach ( $option['checkboxes'] as $id => $label ) {
												
				$checked = is_array($option['value']) && isset($option['value'][$id]) && $option['value'][$id] == true ? ' checked="checked"' : null;
				
				echo '<label class="checkbox-label" for="' . $id . '">';
				
					echo '<input type="hidden" value="0" name="' . $option['group'] . '[' . $option['id'] .'][' . $id .']" />';
				
					echo '<input class="checkbox-field" type="checkbox" name="' . $option['group'] . '[' . $option['id'] .'][' . $id .']" value="1" id="' . $id . '" ' . $checked . ' />';
										
				echo $label . '</label>';
				
			}
					
		echo '</fieldset>';		
		
	}
	
	
	public static function select($option) {
		
		echo '<select class="select-field" ' . $option['name'] . ' id="' . $option['id'] . '" >';
				
			foreach ( $option['options'] as $value => $label ) {
				
				$selected = $value == $option['value'] ? ' selected="selected"' : null;
		
				echo '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
		
			}
					
		echo '</select>';	
		
	}
	
	
	public static function multiselect($option) {
	
		echo '<input type="hidden" value="0" ' . $option['name'] . ' />';
		
		echo '<select class="multiselect-field" name="' . $option['group'] . '[' . $option['id'] .'][]" multiple="multiple" id="' . $option['id'] . '" >';
				
			foreach ( $option['options'] as $value => $label ) {
				
				$selected = is_array($option['value']) && in_array($value, $option['value']) ? ' selected="selected"' : null;
		
				echo '<option value="' . $value . '"' . $selected . '>' . $label . '</option>';
		
			}
					
		echo '</select>';	
		
	}
	
	
	public static function header($option) {
	
		$default = array(
			'markup' => 'h4',
		);
		
		$option = array_merge($default, $option);
	
		echo '<' . $option['markup'] . ' id="' . $option['id'] . '" class="' . $option['type'] . ' btr-panel-title">' . $option['label'] . '</' .  $option['markup'] . '>';
		
	}
	
}