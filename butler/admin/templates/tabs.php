<?php
/**
* @package   Butler Framework
* @author    ThemeButler http://themebulter.com
*/
?>

<div id="btr-admin-page-tabs" class="btr-tabs <?php echo $tabs['layout']; ?>-layout">
	<ul class="<?php echo $tabs['layout']; ?>-tabs">
		<?php $i = 1; foreach ( $tabs['tabs'] as $id => $tab ) : ?>
		
			<li class="<?php if ( $i == 1 ) echo 'first'; elseif ( $i == count($tabs['tabs']) ) echo 'last'; ?>">
				<a href="#tab-<?php echo str_replace('_', '-', $id); ?>"><?php echo $tab['label']; ?></a>
				<div class="btr-pointer"><div class="btr-pointer-inner"></div></div>
			</li>
			
			<?php $i++; ?>
					
		<?php endforeach; ?>
	</ul>
	<div class="<?php echo $tabs['layout']; ?>-panels">
		<?php foreach ( $tabs['tabs'] as $id => $tab ) : ?>
			
			<div id="tab-<?php echo str_replace('_', '-', $id); ?>">
				
				<?php
					$args = isset($tab['args']) ? $tab['args'] : array();
					
					/* we accept template path or function to call a function */
					if ( is_array($tab['content']) )
						echo call_user_func($tab['content'], $args);
					else		
						echo butler_render($tab['content'], $args);
				?>
			</div>
			
		<?php endforeach; ?>
	</div>
</div>