<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/

$header = false; 

?>

<div class="btr-options btr-grid">

	<?php $i = 1; $wrapper = 1; foreach ( $options as $id => $option ) : ?>
	
		<?php 
		
		/* we exclude activation as we treat it later */
		if ( $option['type'] == 'activation' )
			continue;
		
		$option['id'] = $id;
		$option['group'] = $group;
		
		?>
		
		<?php if ( $option['type'] == 'header' ) : ?>
		
			<?php if ( $wrapper = 1 && $header ) : ?>
				</div> <!-- close wrapper -->
			<?php endif; ?>
			
			<?php $open_wrapper = '<div class="wrapper ' . str_replace('_', '-', $option['id']) . ' btr-width-large-1-2 btr-panel"><!-- open wrapper -->'; ?>
			
			<?php if ( $wrapper = 1 ) : ?>
				<?php echo $open_wrapper; ?> 
				<?php $header = true; ?>
			<?php endif; ?>	
					
			<?php call_user_func('butlerAdminInputs::display', $option); ?>
			
			<?php $wrapper = 0; /* we reset the wrapper */ ?>
		
		<?php else : ?>
		
			<?php if ( !$header ) : /* we add add wrapper if the first option isn't a header */ ?>
				<?php echo $open_wrapper; ?> 
				<?php $header = true; ?>
			<?php endif; ?>
				
			<div class="option<?php if ( $wrapper == 1 ) echo ' first'; ?>">
						
				<div class="field <?php echo str_replace('_', '-', $option['id']); ?>">
					<label><?php echo $option['label']; ?></label>
					<?php call_user_func('butlerAdminInputs::display', $option); ?>
				</div>
				
				<?php if ( isset($option['description']) && $option['description'] ) : ?>
					<div class="info">
						<?php $description = butler_troncate($option['description']); ?>
						<?php echo $description['main']; ?>
						<?php if ( !empty($description['extended']) ) : ?>
							<a class="btr-read-more" href="#"><?php echo $description['more_text']; ?></a>
							<div class="btr-extended-content"><?php echo $description['extended']; ?></div>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			
			</div>
		
		<?php endif; ?>	
				
		<?php if ( $id == end(array_keys($options)) ) : ?>
				</div><!-- close last wrapper -->
		<?php endif; ?>	
		
		<?php $i++; $wrapper++; ?>
		
	<?php endforeach; ?>
	
</div>