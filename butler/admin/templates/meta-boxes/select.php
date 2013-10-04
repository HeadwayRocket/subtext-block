<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/
?>

<select name="<?php echo $field['id']; ?>">
							
	<?php foreach ( $field['options'] as $id => $option ) : ?>
		
		<option value="<?php echo $id; ?>" <?php if ( $meta == $id ) echo 'selected="selected"'; ?> ><?php echo $option; ?></option>
		
	<?php endforeach; ?>
	
</select>