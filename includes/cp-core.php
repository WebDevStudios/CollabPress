<?php

// Core
do_action( 'cp_core' );

// Install CollabPress
register_activation_hook( __FILE__, 'cp_install' );

// AJAX handlers
require_once( CP_PLUGIN_DIR . 'includes/ajax-handlers.php' );

// CollabPress Admin Init Functions
require_once( CP_PLUGIN_DIR . 'includes/admin_init.php' );
do_action( 'cp_after_admin_init' );

// CollabPress Functions
require_once( CP_PLUGIN_DIR . 'includes/functions.php' );
do_action( 'cp_after_functions' );

// Administration Menus
require_once( CP_PLUGIN_DIR . 'includes/menus.php' );
do_action( 'cp_after_menus' );

// CollabPress Update
require_once( CP_PLUGIN_DIR . 'includes/update.php' );

// CollabPress shortcode support
require_once( CP_PLUGIN_DIR . 'includes/shortcode.php' );
do_action( 'cp_after_shortcode' );

// CollabPress widgets
require_once( CP_PLUGIN_DIR . 'includes/cp-widgets.php' );
do_action( 'cp_after_widgets' );

// Load BuddyPress integration, if BP is enabled
add_action( 'bp_init', 'cp_load_bp_functions' );

// Add "View CollabPress Dashboard" link on plugins page
add_filter( 'plugin_action_links_' . CP_BASENAME, 'cp_filter_plugin_actions' );

function cp_filter_plugin_actions( $links ) {
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

/**
 * Loads CP's BuddyPress functionality
 *
 * A few things are required for BuddyPress functionality to work:
 * - Must be running at least BP 1.5
 * - Must have the Groups component enabled
 */
function cp_load_bp_functions() {
	$error_message = '';

	if ( ! bp_is_active( 'groups' ) ) {
		$error_type = 'no_bp_groups';
		$error_message = __( 'CollabPress BuddyPress integration requires the BP Groups component to be activated. Not using BP Groups? No problem - you&#8217ll still be able to use CollabPress on the Dashboard.', 'collabpress' );
	} else if ( version_compare( BP_VERSION, '1.5', '<' ) ) {
		$error_type = 'no_bp_15';
		$error_message = __( 'CollabPress BuddyPress integration requires v1.5+ of BuddyPress to work. Download a copy from <a href="http://buddypress.org">buddypress.org</a>. Not ready to upgrade to BuddyPress 1.5? No problem. You can continue to use CollabPress - you just won&#8217t get any BuddyPress integration yet.', 'collabpress' );
	}

	// If an error message has been set, see whether we should throw an
	// an error, then bail
	if ( $error_message && $error_type ) {
		if ( current_user_can( 'activate_plugins' ) ) {
			$dismissed = (array) get_option( 'cp_dismissed_messages' );

			if ( ! isset( $dismissed[ $error_type ] ) ) {
				// Groan
				$dismiss_url = add_query_arg( 'cp_dismiss', $error_type, $_SERVER['REQUEST_URI'] );
				$dismiss_url = wp_nonce_url( $dismiss_url, 'cp_dismiss_notice' );

				$error_message .= ' <span class="description"><a href="' . $dismiss_url . '">' . __( 'Dismiss this message.', 'collabpress' ) . '</a></span>';

				add_action( 'admin_notices', create_function( '', 'echo \'<div class="updated fade"><p>' . $error_message . '</p></div>\';' ) );
			}
		}

		return;
	}

	// Still here? Load BP functionality
	require_once( CP_PLUGIN_DIR . 'includes/cp-bp.php' );
}

// PressTrends Tracking
include ( 'presstrends.php' );

// End
do_action( 'cp_end' );
