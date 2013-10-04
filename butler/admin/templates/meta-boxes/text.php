<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/
?>

<input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="<?php if ( $meta ) echo $meta; else echo $field['default']; ?>" size="30" />

<span class="btr-field-description"><?php echo $field['desc']; ?></span>