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

// Class for Dashboard
class collabpress_dashboard_page {

	// Constructor
	function collabpress_dashboard_page() {
		// Callbacks
		add_action('admin_menu', array(&$this, 'on_admin_menu')); 
		add_action('admin_post_save_howto_metaboxes_general', array(&$this, 'on_save_changes'));
	}
	
	// Add Menus
	function on_admin_menu() {
		$cp_options = get_option( 'cp_options' );
		$cp_debug_mode = ( $cp_options['debug_mode'] == 'enabled' ) ? true : false;
		
		//load user role required for CP
		$cp_user_role = ( isset( $cp_options['user_role'] ) ) ? esc_attr( $cp_options['user_role'] ) : 'manage_options';

		//load settings user role
		$cp_settings_user_role = ( isset( $cp_options['settings_user_role'] ) ) ? esc_attr( $cp_options['settings_user_role'] ) : 'manage_options';

		$this->pagehook = add_menu_page( __('CollabPress Dashboard', 'collabpress'), __('CollabPress', 'collabpress'), $cp_user_role, COLLABPRESS_DASHBOARD_PAGE, array( &$this, 'on_show_page' ), CP_PLUGIN_URL .'includes/images/collabpress-menu-icon.png' );
		// Call Back
		add_action('load-'.$this->pagehook, array(&$this, 'on_load_page'));
		add_action('admin_print_styles-' . $this->pagehook, array(&$this, 'cp_admin_styles'));
		add_action('admin_print_scripts-' . $this->pagehook, array(&$this, 'cp_admin_scripts'));

		//add settings submenu item
		$cp_settings_page_hook = add_submenu_page( COLLABPRESS_DASHBOARD_PAGE, __( 'CollabPress Settings', 'collabpress' ), __( 'Settings', 'collabpress' ), $cp_settings_user_role, 'collabpress-settings', 'cp_settings_page' );
		add_action('admin_print_styles-' . $cp_settings_page_hook, array(&$this, 'cp_admin_styles'));
		add_action('admin_print_scripts-' . $cp_settings_page_hook, array(&$this, 'cp_admin_scripts'));

		//add help submenu item
		$cp_help_page_hook = add_submenu_page( COLLABPRESS_DASHBOARD_PAGE, __( 'CollabPress Help', 'collabpress' ), __( 'Help', 'collabpress' ), $cp_settings_user_role, 'collabpress-help', 'cp_help_page' );
		add_action('admin_print_styles-' . $cp_help_page_hook, array(&$this, 'cp_admin_styles'));
		add_action('admin_print_scripts-' . $cp_help_page_hook, array(&$this, 'cp_admin_scripts'));

		if ( $cp_debug_mode ) :
		    $cp_debug_page_hook = add_submenu_page(COLLABPRESS_DASHBOARD_PAGE, __('Debug', 'collabpress'), __('Debug', 'collabpress'), $cp_settings_user_role, 'collabpress-debug', 'cp_debug_page');
		    add_action('admin_print_styles-' . $cp_debug_page_hook, array(&$this, 'cp_admin_styles'));
		    add_action('admin_print_scripts-' . $cp_debug_page_hook, array(&$this, 'cp_admin_scripts'));
		endif;
	}
	
	function cp_admin_styles() {
		// Register Styles
		wp_register_style('cp_admin', CP_PLUGIN_URL . 'includes/css/admin.css');
		wp_register_style('cp_jquery-ui', CP_PLUGIN_URL . 'includes/css/jquery-ui/jquery-ui-1.8.16.custom.css');
		wp_register_style('cp_fancybox', CP_PLUGIN_URL . 'includes/tools/fancybox/jquery.fancybox-1.3.4.css');
		
		wp_enqueue_style('cp_admin');
		wp_enqueue_style('cp_jquery-ui');
		wp_enqueue_style('thickbox');
		wp_enqueue_style('cp_fancybox');
	}
	
	function cp_admin_scripts() {
		// Register Scripts
		wp_register_script('cp_admin', CP_PLUGIN_URL . 'includes/js/admin.js');
		wp_register_script('cp_upload', CP_PLUGIN_URL . 'includes/js/cp_uploader.js', array('jquery','media-upload','thickbox'));
		wp_register_script('cp_post', CP_PLUGIN_URL . 'includes/js/cp_post.js');
		wp_register_script('cp_fancybox', CP_PLUGIN_URL . 'includes/tools/fancybox/jquery.fancybox-1.3.4.pack.js', array('jquery'));
		
		wp_enqueue_script('jquery');
		wp_enqueue_script('cp_admin');
		wp_enqueue_script('jquery-ui');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('cp_upload');
		wp_enqueue_script('cp_post');
		wp_enqueue_script('cp_fancybox');
	}
	
	// Before Render
	function on_load_page( $args = array() ) {
		
		global $current_user;
		get_currentuserinfo();
		
		global $cp_dashboard_page;
		global $cp_project_page;
		global $cp_task_list_page;
		global $cp_task_page;
		global $cp_user_page;
		global $cp_calendar_page;
		global $cp_view_projects;
		global $cp_all_users_page;
		
		global $cp_project;
		global $cp_task_list;
		global $cp_task;
		global $cp_user;

		// Set up the default keys
		$defaults = array(
			'project' 	 => false,
			'task' 		 => false,
			'task_list' 	 => false,
			'user' 		 => false,
			'all_users' 	 => false,
			'calendar' 	 => false,
			'view_projects'  => false,
			'add_meta_boxes' => true
		);

		// Get values out of $_GET, if available
		foreach ( $defaults as $key => $value ) {
			// $_GET keys have dashes instead of underscores
			$gkey = str_replace( '_', '-', $key);

			if ( isset( $_GET[$gkey] ) ) {
				$defaults[$key] = $_GET[$gkey];
			}
		}
		
		$r = wp_parse_args( $args, $defaults );
		extract( $r, EXTR_SKIP );

		if ( $project ) :
			
			// Set Project ID
			$cp_project = new CP_Project();
			$cp_project->id = absint( $project );
		
			// Task Page
			if ( $task ) :
			
				// Set Task List ID
				$cp_task = new CP_Task();
				$cp_task->id = absint( $task );
				
				$cp_task_page = true;
			
			// Task List Page
			elseif ( $task_list ) :
			
				// Set Task List ID
				$cp_task_list = new CP_TaskList();
				$cp_task_list->id = absint( $task_list );
				
				$cp_task_list_page = true;
			
			// Project Page
			else:
			
				$cp_project_page = true;
				
			endif;
		
		// User Page
		elseif ( $user ) :
		
			// Set User ID
			$cp_user = new CP_User();
			$cp_user->id = absint( $user );
		
			$cp_user_page = true;

		// All Users Page
		elseif ( $all_users ) :
			$cp_all_users_page = true;
			
		// Calendar Page
		elseif ( $calendar ) :
			$cp_calendar_page = true;
			
		// View Projects
		elseif ( $view_projects ) :
			$cp_view_projects = true;
			
		// Dashboard
		else:
			$cp_dashboard_page = true;
		endif;
		
		// Enqueue Scripts
		wp_enqueue_script('common');
		wp_enqueue_script('wp-lists');
		wp_enqueue_script('postbox');

		// Form Submission Includes
		require_once('isset/project.php');
		require_once('isset/task-list.php');
		require_once('isset/task.php');
		require_once('isset/comment.php');

		/* Add Meta Boxes */
		
		if ( $add_meta_boxes ) {
			// Core Sidebar
			add_meta_box('cp-sidebar-meta-box-calendar', __('Calendar', 'collabpress'), array(&$this, 'cp_calendar_meta'), $this->pagehook, 'collabpress-side', 'core');
			add_meta_box('cp-sidebar-meta-box-projects', __('Projects', 'collabpress'), array(&$this, 'cp_projects_meta'), $this->pagehook, 'collabpress-side', 'core');
			add_meta_box('cp-sidebar-meta-box-users', __('Users', 'collabpress'), array(&$this, 'cp_users_meta'), $this->pagehook, 'collabpress-side', 'core');
			add_meta_box('cp-sidebar-meta-box-overview', __('Overview', 'collabpress'), array(&$this, 'cp_overview_meta'), $this->pagehook, 'collabpress-side', 'core');
			
			// Dashboard Landing
			if ($cp_dashboard_page) :
				add_meta_box('cp-recent-activity', __('Recent Activity', 'collabpress'), array(&$this, 'cp_recent_activity_meta'), $this->pagehook, 'collabpress-project', 'core');
				add_meta_box('cp-add-project', __('Add New Project', 'collabpress'), array(&$this, 'cp_add_project_meta'), $this->pagehook, 'collabpress-project', 'core');
				add_meta_box('cp-edit-project', __('Edit Project', 'collabpress'), array(&$this, 'cp_edit_project_meta'), $this->pagehook, 'collabpress-project-edit', 'core');
			endif;
			
			// Project
			if ($cp_project_page) :
	
			    //verify user has permission to view the project
			    if ( cp_check_project_permissions( $current_user->ID, $cp_project->id ) ) {
				// Main
				add_meta_box('cp-add-task-list', __('Add New Task List', 'collabpress'), array(&$this, 'cp_add_task_list_meta'), $this->pagehook, 'collabpress-task-list', 'core');
				add_meta_box('cp-edit-task-list', __('Edit Task List', 'collabpress'), array(&$this, 'cp_edit_task_list_meta'), $this->pagehook, 'collabpress-task-list-edit', 'core');
				add_meta_box('cp-query-task-list', __('Project Overview', 'collabpress'), array(&$this, 'cp_task_list_meta'), $this->pagehook, 'collabpress-task-list-query', 'core');
				// Sidebar
				add_meta_box('cp-sidebar-meta-box-files', __('Files', 'collabpress'), array(&$this, 'cp_files_meta'), $this->pagehook, 'collabpress-files', 'core');
				add_meta_box('cp-edit-project', __('Edit Project', 'collabpress'), array(&$this, 'cp_edit_project_meta'), $this->pagehook, 'collabpress-project-edit', 'core');
			    }else{
				wp_redirect( CP_DASHBOARD );
			    }
			    
			endif;
			
			// Tasks List
			if ($cp_task_list_page) :
				// Main
				add_meta_box('cp-add-task', __('Add New Task', 'collabpress'), array(&$this, 'cp_add_task_meta'), $this->pagehook, 'collabpress-task', 'core');
				add_meta_box('cp-edit-task', __('Edit Task', 'collabpress'), array(&$this, 'cp_edit_task_list_meta'), $this->pagehook, 'collabpress-task-list-edit', 'core');
				add_meta_box('cp-query-task', __('Task List Overview', 'collabpress'), array(&$this, 'cp_task_meta'), $this->pagehook, 'collabpress-task-query', 'core');
				// Sidebar
				add_meta_box('cp-sidebar-meta-box-files', __('Files', 'collabpress'), array(&$this, 'cp_files_meta'), $this->pagehook, 'collabpress-files', 'core');
			endif;
			
			// Task
			if ($cp_task_page) :
				// Main
				add_meta_box('cp-edit-task', __('Edit Task', 'collabpress'), array(&$this, 'cp_edit_task_meta'), $this->pagehook, 'collabpress-task-edit', 'core');
				// Sidebar
				add_meta_box('cp-sidebar-meta-box-files', __('Files', 'collabpress'), array(&$this, 'cp_files_meta'), $this->pagehook, 'collabpress-files', 'core');
			endif;
			
			// Footer
			add_meta_box('cp-footer', __('Credits', 'collabpress'), array(&$this, 'cp_footer_meta'), $this->pagehook, 'collabpress-footer', 'core');
		}
	}
	
	// Render
	function on_show_page() {
		
		// Get Columns
		global $screen_layout_columns;
		
		global $current_user;
		get_currentuserinfo();
		
		?>
		<div id="collabpress-wrap" class="wrap">
		
		<?php
		
		// User Notice
		$sent_data = ( $_POST ) ? $_POST : $_GET;
		cp_user_notice( $sent_data );
		
		// Title
		echo cp_get_page_title();

		cp_get_breadcrumb();
		?>
		
			<?php wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false ); ?>
			<?php wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false ); ?>
			<input type="hidden" name="action" value="save_howto_metaboxes_general" />
		
			<?php $data = array(); ?>
		
			<div id="poststuff" class="metabox-holder has-right-sidebar">
				<div id="side-info-column" class="inner-sidebar">
					<?php
					
					// Show Side Meta Boxes
					require_once('isset/show-side-meta-boxes.php');
					
					?>
				</div>
				<div id="post-body" class="has-sidebar">
					<div id="post-body-content" class="has-sidebar-content">
						<?php
						
						// Show Core Meta Boxes
						require_once('isset/show-core-meta-boxes.php');
						
						?>
					</div>
				</div>
				<br class="clear"/>
			</div>
		</div>
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready( function($) {
			// close postboxes that should be closed
			$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
			// postboxes setup
			postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
			// toggle close specific boxes
			jQuery('#cp-add-task, #cp-add-task-list').addClass('closed');
		});
		//]]>
	</script>
		
		<?php
		
	}

	// On Save
	function on_save_changes() {
		
		// Get Current User
		global $current_user;
		get_currentuserinfo();
		
		if ( !current_user_can('manage_options') )
			wp_die( __('Cheatin&#8217; uh?') );
		check_admin_referer('howto-metaboxes-general');
		wp_redirect($_POST['_wp_http_referer']);		
	}
	
	// List Calendar
	function cp_calendar_meta($data = NULL) {
		cp_calendar();
	}
	
	// List Projects
	function cp_projects_meta($data = NULL) {
		cp_projects();
	}
	
	// CollabPress Users
	function cp_users_meta($data = NULL) {
		cp_users( $limit='yes' );
	}
	
	// CollabPress Overview
	function cp_overview_meta($data = NULL) {
		cp_overview();
	}
	
	// CollabPress Files
	function cp_files_meta($id = NULL) {
		cp_files($id);
	}

	// Recent Activity
	function cp_recent_activity_meta($data = NULL) {
		cp_recent_activity($data);
	}
	
	// Add Task
	function cp_add_task_meta($data = NULL) {
		
		// Get Current User
		global $current_user;
		get_currentuserinfo();

		cp_add_task($data);
	}
	
	// Edit Task
	function cp_edit_task_meta($data = NULL) {
		
		// Get Current User
		global $current_user;
		get_currentuserinfo();

		cp_edit_task();
		
	}
	
	// Task
	function cp_task_meta() {
		
		// Get Current User
		global $current_user;
		get_currentuserinfo();
		
		cp_task();
	}

	// Footer
	function cp_footer_meta() {
		cp_footer();
	}
	
	// Add Task List
	function cp_add_task_list_meta($data = NULL) {
		
		// Get Current User
		global $current_user;
		get_currentuserinfo();

		cp_add_task_list();
	}
	
	// Edit Task List
	function cp_edit_task_list_meta($data = NULL) {
		
		// Get Current User
		global $current_user;
		get_currentuserinfo();

		cp_edit_task_list();
	}
	
	// Task List
	function cp_task_list_meta($data = NULL) {
		
		// Get Current User
		global $current_user;
		get_currentuserinfo();
		
		cp_task_list();
	}
	
	// Add Project
	function cp_add_project_meta($data = NULL) {
		cp_add_project();
	}
	
	// Edit Project
	function cp_edit_project_meta($data = NULL) {
		cp_edit_project();
	}

}

$collabpress_dashboard_page = new collabpress_dashboard_page();