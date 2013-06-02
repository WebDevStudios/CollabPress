<?php


add_action( 'admin_init', 'cp_admin_init' );

// Initialize CollabPress settings
function cp_admin_init() {

	// Register CollabPress options
	register_setting( 'cp_options_group', 'cp_options' );

	//add CP user capabilities to the built in accounts
	global $wp_roles;

	// add capabilities to Client user role
	// TODO: use get_role() instead of global $wp_roles
	// TODO: review roles and capabilities in CP. Why should a subscriber be able to add projects

	$wp_roles->add_cap( 'administrator', 'cp_add_projects' );
	$wp_roles->add_cap( 'administrator', 'cp_edit_projects' );
	$wp_roles->add_cap( 'administrator', 'cp_add_task_lists' );
	$wp_roles->add_cap( 'administrator', 'cp_edit_task_lists' );
	$wp_roles->add_cap( 'administrator', 'cp_add_task' );
	$wp_roles->add_cap( 'administrator', 'cp_edit_task' );

	$wp_roles->add_cap( 'editor', 'cp_add_projects' );
	$wp_roles->add_cap( 'editor', 'cp_edit_projects' );
	$wp_roles->add_cap( 'editor', 'cp_add_task_lists' );
	$wp_roles->add_cap( 'editor', 'cp_edit_task_lists' );
	$wp_roles->add_cap( 'editor', 'cp_add_task' );
	$wp_roles->add_cap( 'editor', 'cp_edit_task' );

	$wp_roles->add_cap( 'author', 'cp_add_projects' );
	$wp_roles->add_cap( 'author', 'cp_edit_projects' );
	$wp_roles->add_cap( 'author', 'cp_add_task_lists' );
	$wp_roles->add_cap( 'author', 'cp_edit_task_lists' );
	$wp_roles->add_cap( 'author', 'cp_add_task' );
	$wp_roles->add_cap( 'author', 'cp_edit_task' );

	$wp_roles->add_cap( 'contributor', 'cp_add_projects' );
	$wp_roles->add_cap( 'contributor', 'cp_edit_projects' );
	$wp_roles->add_cap( 'contributor', 'cp_add_task_lists' );
	$wp_roles->add_cap( 'contributor', 'cp_edit_task_lists' );
	$wp_roles->add_cap( 'contributor', 'cp_add_task' );
	$wp_roles->add_cap( 'contributor', 'cp_edit_task' );

	$wp_roles->add_cap( 'subscriber', 'cp_add_projects' );
	$wp_roles->add_cap( 'subscriber', 'cp_edit_projects' );
	$wp_roles->add_cap( 'subscriber', 'cp_add_task_lists' );
	$wp_roles->add_cap( 'subscriber', 'cp_edit_task_lists' );
	$wp_roles->add_cap( 'subscriber', 'cp_add_task' );
	$wp_roles->add_cap( 'subscriber', 'cp_edit_task' );

}

add_action( 'admin_init', 'cp_dismiss_admin_notice' );

/**
 * Dismiss a CP warning admin notice
 *
 * @since 1.3
 */
function cp_dismiss_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Sanitize
	$notices = array( 'no_bp_groups', 'no_bp_15' );
	if ( ! isset( $_GET['cp_dismiss'] ) || ! in_array( $_GET['cp_dismiss'], $notices ) ) {
		return;
	}

	check_admin_referer( 'cp_dismiss_notice' );

	$dismissed = get_option( 'cp_dismissed_messages' );
	if ( ! $dismissed ) {
		$dismissed = array();
	}

	$dismissed[ $_GET['cp_dismiss'] ] = '1';

	update_option( 'cp_dismissed_messages', $dismissed );

	$redirect = remove_query_arg( array( '_wpnonce', 'cp_dismiss' ), wp_get_referer() );
	wp_safe_redirect( $redirect );
}