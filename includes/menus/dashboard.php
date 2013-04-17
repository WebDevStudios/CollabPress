<?php

if (!function_exists ('add_action')) {
		header('Status: 403 Forbidden');
		header('HTTP/1.1 403 Forbidden');
		exit();
}

// CollabPress Pages
$cp_dashboard_page = false;
$cp_project_page = false;
$cp_task_list_page = false;
$cp_task_page = false;
$cp_user_page = false;
$cp_calendar_page = false;
$cp_view_projects = false;
$cp_all_users_page = false;

// CollabPress Objects
class CP_Project {
	public $id;
}
class CP_TaskList {
	public $id;
}
class CP_Task{
	public $id;
}
class CP_User{
	public $id;
}

$cp_project = NULL;
$cp_task_list = NULL;
$cp_task = NULL;
$cp_user = NULL;

define('COLLABPRESS_DASHBOARD_PAGE', 'collabpress-dashboard');

// load the new template instead of the old one on the back-end
add_action( 'admin_menu', 'cp_add_admin_menu_item' );

function cp_add_admin_menu_item() {
	$cp_options = get_option( 'cp_options' );
	$cp_user_role = ( isset( $cp_options['user_role'] ) ) ? esc_attr( $cp_options['user_role'] ) : 'manage_options';

	add_menu_page(
		__('CollabPress Dashboard', 'collabpress'),
		__('CollabPress', 'collabpress'),
		$cp_user_role,
		COLLABPRESS_DASHBOARD_PAGE,
		'cp_admin_menu_page_load',
		CP_PLUGIN_URL .'includes/images/collabpress-menu-icon.png'
	);

	//load settings user role
	$cp_settings_user_role = ( isset( $cp_options['settings_user_role'] ) ) ? esc_attr( $cp_options['settings_user_role'] ) : 'manage_options';

	$cp_settings_page_hook = add_submenu_page( COLLABPRESS_DASHBOARD_PAGE, __( 'CollabPress Settings', 'collabpress' ), __( 'Settings', 'collabpress' ), $cp_settings_user_role, 'collabpress-settings', 'cp_settings_page' );
}

add_action( 'wp', 'cp_setup_cp_global' );
add_action( 'admin_init', 'cp_setup_cp_global' );

/**
 * Setup the $cp PHP global
 */
function cp_setup_cp_global() {
	global $cp, $wpdb;

	$cp = new StdClass;

	// Set custom table names
	$cp->tables = new stdClass;
	$cp->tables->project_users = $wpdb->prefix . 'cp_project_users';

	// If we're not on a CollabPress page, bail.
	if ( ! is_collabpress_page() )
		return;

	// Set up the default keys
	$defaults = array(
		'project'        => false,
		'task'           => false,
		'cp_page'        => false,
		'view'           => false,
	);
	// Parse query string variables and set CollabPress global appropriately
	foreach ( $defaults as $key => $value ) {
		if ( ! empty( $_REQUEST[$key] ) ) {
			switch ( $key ) {
				case 'project':
					$cp->project = get_post( $_REQUEST[$key] );
				break;
				case 'task':
					$cp->task = get_post( $_REQUEST[$key] );
					$cp->project = get_post( cp_get_task_project_id( $cp->task->ID ) );
				break;
				default:
					$cp->$key = $_REQUEST[$key];
				break;
			}
		}
	}

	// Set the view if it's not declared in the query string.
	// We'll use it later for choosing the template to be loaded.
	if ( empty( $cp->view ) ) {
		if ( ! empty( $cp->task ) ) {
			$cp->view = 'task';
		} else if ( ! empty( $cp->project ) ) {
			$cp->view = 'project';
		} else {
			$cp->view = 'dashboard';
		}
	}

	do_action( 'cp_global_setup' );
}

/**
 * Callback for add_menu_page, calls the proper CollabPress template
 *
 */
function cp_admin_menu_page_load() {
	global $cp;
	// Find the template depending on the view.
	if ( ! empty( $cp->project ) ) {
		if ( $cp->view == 'task' ) {
			$template = 'collabpress/content-single-task.php';
		} else if ( $cp->view != 'project' ) {
			if ( $cp->view == 'files' )
				wp_enqueue_media(); // todo: maybe move this to an enqueue function
			$template = 'collabpress/content-single-project-' . $cp->view . '.php';
		} else {
			$template = 'collabpress/content-single-project.php';
		}
	} else {
		if ( $cp->view != 'dashboard' )
			$template = 'collabpress/content-' . $cp->view . '.php';
		else
			$template = 'collabpress/dashboard.php';
	}
	cp_load_template( $template );
}

/**
 * Requires the referenced CollabPress template
 */
function cp_load_template( $template ) {
	if ( ! $located_template = locate_template( $template ) ) {
		// If no template is found, load the one from the plugin
		$located_template = CP_PLUGIN_DIR . 'includes/templates/' . $template;
	}
	require( $located_template );
}