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

// Add Translation
add_action( 'init', 'cp_translation' );
function cp_translation() {
	load_plugin_textdomain( 'collabpress', false, basename( dirname( dirname( __FILE__ ) ) ) . '/languages' );
}

// Frontend Init
add_action( 'init', 'cp_frontend_init' );
function cp_frontend_init() {
	if ( !is_admin() ) :
		// Register Styles
		wp_register_style('cp_jquery-ui', CP_PLUGIN_URL . 'includes/css/jquery-ui/jquery-ui-1.8.16.custom.css');

		// Register Scripts
		wp_register_script('cp_frontend', CP_PLUGIN_URL . 'includes/js/frontend.js', array('jquery'));
	endif;
}

// Print Styles
add_action( 'wp_print_styles', 'collabpress_frontend_styles' );
function collabpress_frontend_styles() {
	wp_enqueue_style('cp_jquery-ui');
}

// Print Scripts
add_action( 'wp_print_scripts', 'collabpress_frontend_scripts' );
function collabpress_frontend_scripts() {
	wp_enqueue_script('jquery-ui');
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_script('cp_frontend');
	?>
	<script language="JavaScript">

	function checkAll(field)
	{
	for (i = 0; i < field.length; i++)
		field[i].checked = true ;
	}

	function uncheckAll(field)
	{
	for (i = 0; i < field.length; i++)
		field[i].checked = false ;
	}
	</script>
<?php
}

/**
 * CollabPress Init
 * 
 * Register Custom Post Types
 */
add_action( 'init', 'collabpress_init', 5 );
function collabpress_init() {

	// Load plugin options
	$cp_options = get_option( 'cp_options' );

	// Check if debug mode is enabled
	$cp_debug_mode = ( $cp_options['debug_mode'] == 'enabled' ) ? true : false;

	// Register Custom Post Types

	// Projects
	$args_projects = array(
		'label' => __( 'Projects', 'collabpress' ),
		'description' => __( 'Custom Post Type for CollabPress Projects', 'collabpress' ),
		'public' => $cp_debug_mode,
		'supports' => array( 'title', 'author', 'thumbnail', 'comments', 'custom-fields' ),
		'exclude_from_search' => true
	);
	// Register Projects Custom Post Type
	register_post_type( 'cp-projects', $args_projects );

	// Task Lists
	$args_task_lists = array('label' => __('Task Lists', 'collabpress'),
		'description' => __('Custom Post Type for CollabPress Task Lists', 'collabpress'),
		'public' => $cp_debug_mode,
		'supports' => array( 'title', 'author', 'thumbnail', 'comments', 'custom-fields' ),
		'exclude_from_search' => true
		);
	// Register Task List Custom Post Type
	register_post_type( 'cp-task-lists', $args_task_lists );

	// Tasks
	$args_tasks = array('label' => __('Tasks', 'collabpress'),
		'description' => __('Custom Post Type for CollabPress Tasks', 'collabpress'),
		'public' => $cp_debug_mode,
		'supports' => array( 'title', 'author', 'thumbnail', 'comments', 'custom-fields' ),
		'exclude_from_search' => true
		);
	// Register Tasks Custom Post Type
	register_post_type( 'cp-tasks', $args_tasks );

	// Meta Data
	$args_tasks = array('label' => __('Meta Data', 'collabpress'),
		'description' => __('Custom Post Type for CollabPress Meta Data', 'collabpress' ),
		'public' => $cp_debug_mode,
		'supports' => array( 'title', 'author', 'thumbnail', 'comments', 'custom-fields' ),
		'exclude_from_search' => true
	);
	// Register CollabPress Meta Data
	register_post_type( 'cp-meta-data', $args_tasks );

	// Let other plugins (and the BuddyPress compatibility module) know that we've registered
	do_action( 'cp_registered_post_types' );

}

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
add_action( 'admin_init', 'cp_dismiss_admin_notice' );
