<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/
?>

<input type="hidden" name="btr_admin_options_nonce" value="<?php echo wp_create_nonce('btr-admin-options-nonce'); ?>" />

<?php if ( isset($args) ) : ?>

	<?php foreach ( butler_maybe_array($args) as $group ) : ?>
	
		<input type="hidden" name="btr_options_group[]" value="<?php echo $group; ?>">
	
	<?php endforeach; ?>

<?php else : ?>

	<input type="hidden" name="btr_options_group" value="<?php echo butlerAdminOptions::$undefined_group; ?>">

<?php endif; ?>

<p class="submit"><input type="submit" name="btr_submit" value="Save" class="button-primary btr-save-options"></p>