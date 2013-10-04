<?php
/**
* @package   HeadwayRocket Framework
* @author    HeadwayRocket http://headwayrocket.com
*/

$products = $args;

do_action('hwr_dashboard_alert');

if ( !$products )
	return;

?>

<div class="btr-grid">
	<div class="btr-width-large-2-3 btr-panel installed-add-ons">
		<h4 class="btr-panel-title">Your installed add-ons</h4>
		<div class="btr-panel-content">
			<table class="btr-panel-table">
				<thead>
					<tr>
						<th class="hwr-title">Title</th>
						<th class="hwr-installed" title="Your Installed Version">Installed Version</th>
						<th class="hwr-current" title="Current Available Version">Current Version</th>
						<th class="hwr-resources">Resources</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $products['add-ons'] as $type => $addon ) : ?>
					
						<?php if ( !isset($addon['installed']) ) continue; ?>
						<?php $update = $addon['installed']['version'] < $addon['version'] ? true : false; ?>
					
						<tr class="hwr-first">
							<td><?php echo $addon['name']; ?></td>
							<td>
								<span class="hwr-version-indicator-<?php echo $update ? 'orange' : 'green'; ?>"></span><?php echo $addon['installed']['version']; ?>
								<?php if ( $update ) : ?>
									<a href="<?php echo admin_url(); ?>update-core.php" class="hwr-update-link">Update</a>
								<?php endif; ?>
							</td>
							<td><?php echo $addon['version']; ?></td>
							<td>
								<a href="<?php echo $addon['links']['guide']; ?>" target="_blank" title="<?php echo $addon['name']; ?> guide">Guide</a>
								<a href="<?php echo $addon['links']['support']; ?>" class="hwr-separator-left" target="_blank" title="<?php echo $addon['name']; ?> support">Support</a>
								<?php if ( isset($addon['links']['settings']) ) : ?>
									<a href="<?php echo $addon['links']['settings']; ?>" class="hwr-separator-left" target="_blank" title="<?php echo $addon['name']; ?> settings">Settings</a>
								<?php endif; ?>
							</td>
						</tr>
																	
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
	<div class="btr-width-large-1-3 btr-panel sign-up">
		<h4 class="btr-panel-title">Get email updates</h4>
		<div class="btr-panel-content">
			<p>Sign up to get notified when we release new Headway goodies and product updates.</p>
			<form action="http://headwayrocket.us5.list-manage.com/subscribe/post?u=5634a864e7429fd035bd71b21&amp;id=3a9b303fe3" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank">
				<input type="email" name="EMAIL" class="required input email" id="mce-EMAIL" value="Enter your email address" onblur="if(this.value=='') this.value='Enter your email address';" onfocus="if(this.value=='Enter your email address') this.value='';">
				<input type="submit" value="Subscribe" name="subscribe" id="mc-embedded-subscribe" class="button">
			</form>
		</div>
	</div>
</div>
<div class="hwr-add-ons-title-wrap">
	<h3 class="subtitle">Add-ons</h3>
	<ul class="subsubsub hwr-add-ons-filters">
		<li class="all"><a href="<?php echo HEADWAYROCKET_DASHBOARD_URL; ?>" class="<?php if ( !butler_get('addons_filter') ) echo 'current';?>">All <span class="count">(<?php echo count($products['add-ons']); ?>)</span></a></li>
		<li class="inactive"><a href="<?php echo add_query_arg(array('addons_filter' => 'installed'), HEADWAYROCKET_DASHBOARD_URL); ?>" class="hwr-separator-left<?php if ( butler_get('addons_filter') == 'installed' ) echo ' active';?>">Installed <span class="count">(<?php echo count($products['add-ons-installed']); ?>)</span></a></li>
		<li class="active"><a href="<?php echo add_query_arg(array('addons_filter' => 'active'), HEADWAYROCKET_DASHBOARD_URL); ?>" class="hwr-separator-left<?php if ( butler_get('addons_filter') == 'active' ) echo ' active';?>">Active <span class="count">(<?php echo count($products['add-ons-active']); ?>)</span></a></li>
	</ul>
</div>

<?php 
	/* we apply the filters on the add-ons array */
	if ( butler_get('addons_filter') == 'active' )
		$products['add-ons'] = array_intersect_key($products['add-ons'], array_flip($products['add-ons-active']));
	elseif ( butler_get('addons_filter') == 'installed' )
		$products['add-ons'] = array_intersect_key($products['add-ons'], array_flip($products['add-ons-installed']));
?>

<ul class="hwr-add-ons btr-grid">
	<?php $i = 0; foreach ( $products['add-ons'] as $type => $addon ) : ?>
	
		<li class="btr-width-1-3 btr-panel">				
			<h4 class="btr-panel-title"><?php echo $addon['name']; ?></h4>
			
			<?php if ( isset($addon['installed']) ) : ?>
				<?php if ( $addon['installed']['is_active'] ) : ?>
					<span class="btr-panel-badge btr-badge-green">Active</span>
				<?php else : ?>
					<span class="btr-panel-badge btr-badge-orange">Installed</span>
				<?php endif; ?>
				
			<?php else : ?>
				
				<span class="btr-panel-badge btr-badge">$<?php echo $addon['price']; ?></span>
			
			<?php endif; ?>
			<div class="btr-panel-content">
				
				<img src="<?php echo $addon['links']['image-path-90x90']; ?>" onerror="this.src='<?php echo HEADWAYROCKET_URL . 'admin/assets/images/offline-image.png'; ?>'" alt="<?php echo $addon['name']; ?>">
					
				<p><?php echo $addon['description']; ?></p>
				
				<div>
					<a href="<?php echo $addon['links']['details']; ?>" class="hwr-demo" target="_blank" title="<?php echo $addon['name']; ?> details">Details</a>
					<a href="<?php echo $addon['links']['demo']; ?>" class="hwr-demo hwr-separator-left" target="_blank" title="<?php echo $addon['name']; ?> demo">Demo</a>
					
					<?php if ( !isset($addon['installed']) ) : ?>
						
						<?php if ( $addon['price'] == 0 ) : ?>
							<a class="button-secondary" href="<?php echo $addon['links']['download']; ?>" title="Downlaod <?php echo $addon['name']; ?>">Download</a>
						<?php else : ?>
							<a class="button-secondary" href="<?php echo $addon['links']['buy']; ?>" target="_blank" title="Buy <?php echo $addon['name']; ?>">Buy</a>
						<?php endif; ?>		
									
					<?php elseif ( $addon['installed']['is_active'] == false ) : ?>
						
						<a class="button-secondary" href="<?php echo $addon['installed']['activation_url']; ?>" title="Activate <?php echo $addon['name']; ?>">Activate</a>
						
					<?php endif; ?>
				</div>	
				
			</div>
		</li>
						
		<?php $i++; ?>
													
	<?php endforeach; ?>
</ul>
