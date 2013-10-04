<?php
/**
* @package   HeadwayRocket Framework
* @author    HeadwayRocket http://headwayrocket.com
*/

/* we set our dashboard page tabs */
$tabs = array(
    'layout' => 'horizontal',
    'tabs' => array(
        'dashboard' => array(
                'label' => 'Dashboard',
                'content' => HEADWAYROCKET_PATH . 'admin/templates/tab-dashboard.php',
                'args' => $products,
        ),
        'settings' => array(
                'label' => 'Settings',
                'content' => HEADWAYROCKET_PATH . 'admin/templates/tab-settings.php'
        )
    )
);

?>

<div class="wrap hwr-dashboard">
	<h2 class="hwr-dashboard-title">Dashboard <a href="<?php echo add_query_arg(array('refresh' => 'true'), HEADWAYROCKET_DASHBOARD_URL); ?>" class="add-new-h2">Refresh data</a><span class="hwr-last-update">Last update: <?php echo isset($products['last-update']) ? $products['last-update'] : 'Unavailable'; ?></span></h2>
    <?php do_action('butler_admin_display_save_options_message', 'Settings saved successfully.'); ?>
    <?php do_action('butler_admin_tabs', $tabs); ?>
</div>