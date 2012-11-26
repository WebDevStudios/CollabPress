<?php

// Core
do_action( 'cp_core' );

// Install CollabPress
register_activation_hook( __FILE__, 'cp_install' );

// Check for Pro Version
if ( file_exists( CP_PLUGIN_DIR . '/collabpress-pro.php' ) )
	require_once( CP_PLUGIN_DIR . '/collabpress-pro.php' );

// CollabPress Admin Init Functions
require_once( 'admin_init.php' );
do_action( 'cp_after_admin_init' );

// CollabPress Functions
require_once( 'functions.php' );
do_action( 'cp_after_functions' );

// Administration Menus
require_once( 'menus.php' );
do_action( 'cp_after_menus' );

// CollabPress shortcode support
require_once( 'shortcode.php' );
do_action( 'cp_after_shortcode' );

// CollabPress widgets
require_once( 'cp-widgets.php' );
do_action( 'cp_after_widgets' );

// Load BuddyPress integration, if BP is enabled
add_action( 'bp_include', 'cp_load_bp_functions' );

// Add "View CollabPress Dashboard" link on plugins page
add_filter( 'plugin_action_links_' . CP_BASENAME, 'filter_plugin_actions' );

function filter_plugin_actions ( $links ) {
	$settings_link = '<a href="'.CP_DASHBOARD.'">'.__('View Dashboard', 'collabpress').'</a>';
	array_unshift ( $links, $settings_link );
	return $links;
}

// Show Dashboard Meta Box
add_action( 'wp_dashboard_setup', 'cp_wp_add_dashboard_widgets' );
function cp_wp_add_dashboard_widgets() {

    //check if dashboard widget is enabled
    $options = get_option('cp_options');
    if ( $options['dashboard_meta_box'] == 'enabled' ) {
	wp_add_dashboard_widget('cp_wp_dashboard_widget', __('CollabPress - Recent Activity', 'collabpress'), 'cp_wp_dashboard_widget_function');
    }

}
function cp_wp_dashboard_widget_function() {
	cp_recent_activity();
}

function cp_load_bp_functions() {
	// A rough-n-ready check to enforce that BP files aren't loaded if you're not running at
	// least BuddyPress 1.5.
	if ( function_exists( 'bp_get_current_group_id' ) ) {
		require_once( CP_PLUGIN_DIR . 'includes/cp-bp.php' );
	}else{
		//BuddyPress is older than 1.5, display a notice
		add_action( 'admin_notices', create_function( '', 'echo \'<div class="updated fade"><p>CollabPress BuddyPress integration requires v1.5+ of BuddyPress to work.  You can download a copy on the <a href="http://buddypress.org/blog/" target="_blank">BuddyPress site</a>.</p><p>Not ready to upgrade to BuddyPress 1.5? No problem. You can continue to use CollabPress - you just won&#39;t get any BuddyPress integration yet.</p></div>\';' ) );
	}
}

// End
do_action( 'cp_end' );
