<?php

// Send Emails from CollabPress
function cp_send_email( $to, $subject, $message ) {

    // Load plugin options
    $cp_options = get_option( 'cp_options' );

    // Check if email notifications are enabled - default to enabled
    $cp_email_notify = ( $cp_options['email_notifications'] == 'disabled' ) ? false : true;

    // If email notifications are enabled proceed
    if ( $cp_email_notify ) {

		// Set email variables
		$cp_email = ( is_email( $to ) ) ? $to : null;
		$cp_subject = ( isset( $subject ) ) ? $subject : '';
		$cp_message = $message;

		// Send Away
		wp_mail( $cp_email, $cp_subject, $cp_message );
    }
}

// User Notice
function cp_user_notice($data) {

	// Project Added
	if ( isset( $data['cp-add-project'] ) )
		echo '<div class="updated fade"><p><strong>'.__('Project Added', 'collabpress').'</strong></p></div>';

	// Project Updated
	if ( isset( $data['cp-edit-project'] ) )
		echo '<div class="updated fade"><p><strong>'.__('Project Updated', 'collabpress').'</strong></p></div>';

	// Project Deleted
	if ( isset( $data['cp-delete-project-id'] ) )
		echo '<div class="error fade"><p><strong>'.__('Project Deleted', 'collabpress').'</strong></p></div>';

	// Task List Added
	if ( isset( $data['cp-add-task-list'] ) )
		echo '<div class="updated fade"><p><strong>'.__('Task List Added', 'collabpress').'</strong></p></div>';

	// Task List Added
	if ( isset( $data['cp-edit-task-list'] ) )
		echo '<div class="updated fade"><p><strong>'.__('Task List Updated', 'collabpress').'</strong></p></div>';

	// Task List Deleted
	if ( isset( $data['cp-delete-task-list-id'] ) )
		echo '<div class="error fade"><p><strong>'.__('Task List Deleted', 'collabpress').'</strong></p></div>';

	// Task Added
	if ( isset( $data['cp-add-task'] ) )
		echo '<div class="updated fade"><p><strong>'.__('Task Added', 'collabpress').'</strong></p></div>';

	// Task Updated
	if ( isset( $data['cp-edit-task-id'] ) )
		echo '<div class="updated fade"><p><strong>'.__('Task Updated', 'collabpress').'</strong></p></div>';

	// Task Deleted
	if ( isset( $data['cp-delete-task-id'] ) )
		echo '<div class="error fade"><p><strong>'.__('Task Deleted', 'collabpress').'</strong></p></div>';

	// Comment Added
	if ( isset( $data['cp-add-comment'] ) )
		echo '<div class="updated fade"><p><strong>'.__('Comment Added', 'collabpress').'</strong></p></div>';

	// Activity log cleared
	if ( isset( $data['cp_clear_activity'] ) )
		echo '<div class="updated fade"><p><strong>' .__( 'Acitivity Log Has Been Cleared', 'collabpress' ) .'</strong></p></div>';

}

/**
 * Create a new CollabPress activity post.
 *
 */
function cp_add_activity( $action = NULL, $type = NULL, $author = NULL, $ID = NULL ) {
	$add_activity = array(
		'post_title' => __( 'Activity', 'collabpress' ),
		'post_status' => 'publish',
		'post_type' => 'cp-meta-data'
		);
	$activity_id = wp_insert_post( $add_activity );
	update_post_meta( $activity_id, '_cp-meta-type', 'activity' );

	// Action
	if ( $action )
		update_post_meta( $activity_id, '_cp-activity-action', $action );
	// Type
	if ( $type )
		update_post_meta( $activity_id, '_cp-activity-type', $type );
	// Author
	if ( $author )
		update_post_meta( $activity_id, '_cp-activity-author', $author );
	// ID
	if ( $ID )
		update_post_meta( $activity_id, '_cp-activity-ID', $ID );

	do_action( 'cp_add_activity', $action, $type, $author, $ID, $activity_id );
}

/**
 * Outputs all comments on displayed task.
 */
function cp_task_comments() {
	global $cp;
	global $cp_task;
	global $cp_project;

	// Get Current User
	global $current_user;
	get_currentuserinfo();

	$comments = get_comments( array(
		'post_id' => $cp->task->ID,
		'order' => 'ASC',
	) );

	echo '<div id="cp_task_comments_wrap">';

	if ($comments) :
		$commentCount = 1;
		// Display each comment
		foreach( $comments as $comm ) :

			if ( ($commentCount % 2) == 0 ) {
				$row = " even";
			} else {
				$row = " odd";
			}

		?>
			<div class="cp_task_comment<?php echo $row ?>">
				<a class="avatar" title="<?php echo $comm->comment_author ?>" href="<?php echo CP_DASHBOARD; ?>&user=<?php echo $comm->user_id ?>"><?php echo get_avatar($comm->user_id, 64) ?></a>
				<div class="cp_task_comment_content">
					<p class="cp_comment_author"><a title="<?php echo $comm->comment_author ?>" href="<?php echo CP_DASHBOARD; ?>&user=<?php echo $comm->user_id ?>"><?php echo $comm->comment_author ?></a>
					<?php
					//generate delete comment link

					$cp_del_link = CP_DASHBOARD .'&project='.$cp->project->ID.'&task='.$cp->task->ID.'&cp-delete-comment-id='.$comm->comment_ID;
					$cp_del_link = ( function_exists('wp_nonce_url') ) ? wp_nonce_url( $cp_del_link, 'cp-action-delete_comment' ) : $cp_del_link;

					if ( $current_user->ID == $comm->user_id || current_user_can( 'manage_options' ) )
						echo ' - <a href="'.$cp_del_link.'" style="color:red;" onclick="javascript:check=confirm(\'' . __('WARNING: This will delete the selected comment.\n\nChoose [Cancel] to Stop, [OK] to delete.\n', 'collabpress' ) .'\');if(check==false) return false;">'.__( 'delete', 'collabpress' ). '</a>';
					?>
					</p>
					<p class="cp_comment_content"><?php echo $comm->comment_content ?></p>
				</div>
			</div>
		<?php
			$commentCount++;
		endforeach;
	// No Comments
	else:
		echo '<div class="cp_task_comment"><p>'.__('No comments...', 'collabpress').'</p></div>';
	endif;

	echo '</div>';

	//check if email option is enabled
	$options = get_option('cp_options');

	echo '<form action="'.cp_clean_querystring().'" method="post">';
		wp_nonce_field( 'cp-add-comment' .absint( $cp->task->ID ) );
		?>
		<p><label for="cp-comment-content"><?php _e('Leave a Comment: ', 'collabpress') ?></label></p>
		<p><textarea class="large-text code" id="cp-comment-content" cols="30" rows="10" name="cp-comment-content"></textarea></p>
		<p><?php _e('Notify via Email?', 'collabpress'); ?> <input type="checkbox" name="notify" <?php checked( $options['email_notifications'], 'enabled' ); ?> /></p>
		<?php
		echo '<p class="submit"><input class="button-primary" type="submit" name="cp-add-comment" value="'.__( 'Submit', 'collabpress' ).'"/></p>';

	echo '</form>';
}

function cp_get_projects_for_user( $user_id ) {
	global $cp, $wpdb;
	$projects = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT project_id
			FROM {$cp->tables->project_users}
			WHERE user_id = %d",
			$user_id
		)
	);
	return $projects;
}

function cp_get_projects_for_current_user() {
	$current_user = wp_get_current_user();
	return cp_get_projects_for_user( $current_user->ID );
}
// CollabPress Calendar
function cp_draw_calendar( $args = array() ) {
	global $cp;

	$defaults = array(
		'month' => NULL,
		'year' => NULL,
		'project' => NULL
	);

	$args = wp_parse_args( $args, $defaults );
	extract( $args );

	// If no project was given, only show projects
	// user has access to
	if ( ! $project ) {
		$project = cp_get_projects_for_current_user();
	}

	echo '<div id="cp-calendar-wrap">';

	if ( ! $month && ! isset( $_GET['day'] ) ) :
		$month = date('n');
		$year = date('Y');
	else :
		$month = absint($_GET['month']);
		$year = absint($_GET['year']);
	endif;

	$monthName= date("F",mktime(0,0,0,$month,1,2000));
	echo '<h3 style="clear:both; text-align: center">'.$monthName.' - '.$year.'</h3>';

	// Previous Link
	if ($month == 1) :
		$previousMonth = 12;
		$previousYear = $year - 1;
	else :
		$previousMonth = $month - 1;
		$previousYear = $year;
	endif;
	$previousmonthName= date("F",mktime(0,0,0,$previousMonth,1,2000)) . ', ' . $previousYear;
	$calendar_previous_month_link = cp_get_calendar_permalink(
		array(
			'project' => $project,
			'month' => $previousMonth,
			'year' => $previousYear,
		)
	);

	echo '<a title="" class="cp_previous_month" href="' . $calendar_previous_month_link . '">'.$previousmonthName.'</a>';

	// Next Link
	if ($month == 12) :
		$nextMonth = 1;
		$nextYear = $year + 1;
	else :
		$nextMonth = $month + 1;
		$nextYear = $year;
	endif;
	$nextmonthName= date("F",mktime(0,0,0,$nextMonth,1,2000)) . ', ' . $nextYear;
	$calendar_next_month_link = cp_get_calendar_permalink(
		array(
			'project' => $project,
			'month' => $nextMonth,
			'year' => $nextYear,
		)
	);
	echo '<a title="" class="cp_next_month" href="' . $calendar_next_month_link . '">'.$nextmonthName.'</a>';

	/* draw table */
	$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

	/* table headings */
	$headings = array( __('Sunday', 'collabpress'),
		__('Monday', 'collabpress'),
		__('Tuesday', 'collabpress'),
		__('Wednesday', 'collabpress'),
		__('Thursday', 'collabpress'),
		__('Friday', 'collabpress'),
		__('Saturday', 'collabpress')
	);

	$calendar .= '<tr class="calendar-row" valign="top"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date(
		'w',
		mktime( 0, 0, 0, $month, 1, $year )
	);
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar .= '<tr class="calendar-row" valign="top">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$calendar .= '<td class="calendar-day-np">&nbsp;</td>';
		$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for ($list_day = 1; $list_day <= $days_in_month; $list_day++ ):
		$calendar .= '<td class="calendar-day">';
			/* add in the day number */
			$calendar .= '<div class="day-number">'.$list_day.'</div>';
			$formatDate = $month.'/'.$list_day.'/'.$year;

			// Get Task Lists
			$tasks_args = apply_filters(
				'cp_calendar_tasks_args',
				array(
					'post_type' => 'cp-tasks',
					'meta_query' => array(
						array(
							 'key' => '_cp-task-due',
							 'value' => $formatDate,
						)
					),
					'showposts' => '-1'
				)
			);

			if ( $project ) {
				$tasks_args['meta_query'][] = array(
					'key' => '_cp-project-id',
				 	'value' => $project,
				);
			}

			$tasks_query = new WP_Query( $tasks_args );

			// WP_Query();
			if ( $tasks_query->have_posts() ) :
		    while( $tasks_query->have_posts() ) : $tasks_query->the_post();

				// Project ID
				$projectID = get_post_meta( get_the_ID(), '_cp-project-id', true );
				$task_user_id = get_post_meta( get_the_ID(), '_cp-task-assign', true );
				$task_status = get_post_meta( get_the_ID(), '_cp-task-status', true );

				if ($task_status == 'open') :
					$calendar .= '<p><a href="'.cp_get_url(get_the_ID(), 'task').'">'.get_avatar($task_user_id, 32).' '.get_the_title().'</a></p>';
				endif;

		    endwhile;
			wp_reset_query();
			else :
			endif;

			$calendar .= str_repeat('<p>&nbsp;</p>',2);

		$calendar .= '</td>';
		if($running_day == 6):
			$calendar .= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar .= '<tr class="calendar-row" valign="top">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar .= '<td class="calendar-day-np">&nbsp;</td>';
		endfor;
	endif;

	/* final row */
	$calendar .= '</tr>';

	/* end the table */
	$calendar .= '</table>';

	/* all done, return result */
	echo $calendar;

	echo '</div>';
}


function cp_get_calendar_permalink( $args = array() ) {
	$defaults = array(
		'project' => NULL,
		'month' => NULL,
		'year' => NULL
	);

	$args = wp_parse_args( $args, $defaults );

	extract( $args );

	$link = CP_DASHBOARD;
	$link = add_query_arg( array( 'view' => 'calendar' ), $link );

	if ( $project )
		$link = add_query_arg( array( 'project' => $project ), $link );

	if ( $month )
		$link = add_query_arg( array( 'month' => $month ), $link );

	if ( $year )
		$link = add_query_arg( array( 'year' => $year ), $link );

	return $link;
}

	function cp_calendar_permalink( $args = array() ) {
		echo cp_get_calendar_permalink( $args );
	}

// View All Projects
function cp_view_all_projects() {

	// Get Current User
	global $current_user;
	get_currentuserinfo();

	echo '<h4 class="cp-no-margin">'.__('All Projects', 'collabpress').'</h4>';

	// Get Projects
	$projects_args = array( 'post_type' => 'cp-projects', 'showposts' => '-1' );
	$projects_query = new WP_Query( $projects_args );

	// WP_Query();
	if ( $projects_query->have_posts() ) :
	    while( $projects_query->have_posts() ) : $projects_query->the_post();

		//verify user has permission to view this project
		if ( cp_check_project_permissions( $current_user->ID, get_the_ID() ) ) {

		    //generate delete project link
		    $cp_del_link = CP_DASHBOARD .'&cp-delete-project-id='.get_the_ID();
		    $cp_del_link = ( function_exists('wp_nonce_url') ) ? wp_nonce_url( $cp_del_link, 'cp-action-delete_project' .get_the_ID() ) : $cp_del_link;

		    //generate edit project link
		    $cp_edit_link = CP_DASHBOARD.'&project='.get_the_ID().'&view=edit';

		    echo '<p><a href="'.CP_DASHBOARD.'&project='.get_the_ID().'">'.get_the_title().'</a>';

		    //check if user can view edit/delete links
		    if ( cp_check_permissions( 'settings_user_role' ) ) {
			echo ' - <a href="' .$cp_edit_link. '">' .__( 'edit', 'collabpress'). '</a> &middot; <a href="' .$cp_del_link. '" style="color:red;" onclick="javascript:check=confirm(\'' . __('WARNING: This will delete the selected project, including ALL task lists and tasks in the project.\n\nChoose [Cancel] to Stop, [OK] to delete.\n', 'collabpress' ) .'\');if(check==false) return false;">'.__('delete', 'collabpress').'</a></p>';
		    }

		}

	    endwhile;
	    wp_reset_query();

	// No Results
	else :
		echo '<p>'.__( 'No Projects...', 'collabpress' ).'</p>';
	endif;

}

// Display Icon
function cp_screen_icon($screen = '') {
	global $current_screen, $typenow;

	if ( empty($screen) )
		$screen = $current_screen;
	elseif ( is_string($screen) )
		$name = $screen;

	$class = 'icon32';

	if ( empty($name) ) {
		if ( !empty($screen->parent_base) )
			$name = $screen->parent_base;
		else
			$name = $screen->base;

		if ( 'edit' == $name && isset($screen->post_type) && 'page' == $screen->post_type )
			$name = 'edit-pages';

		$post_type = '';
		if ( isset( $screen->post_type ) )
			$post_type = $screen->post_type;
		elseif ( $current_screen == $screen )
			$post_type = $typenow;
		if ( $post_type )
			$class .= ' ' . sanitize_html_class( 'icon32-posts-' . $post_type );
	}
	return '<span id="icon-'.$name.'" class="'.$class.'"></span>';
}

// Get URL
function cp_get_url( $ID = NULL, $type = NULL ) {
    if ( $type == 'task' || $type == 'comment' ) :
		$cp_project_id = get_post_meta( $ID, '_cp-project-id', true );
		$cp_url = CP_DASHBOARD .'&project=' .$cp_project_id .'&task=' .absint( $ID );
	elseif( $type =="task list" ) :
		$cp_project_id = get_post_meta( $ID, '_cp-project-id', true );
		$cp_url = CP_DASHBOARD .'&project=' .$cp_project_id .'&task-list=' .absint( $ID );
    elseif ( $type == 'project' ) :
		$cp_url = CP_DASHBOARD.'&project=' .absint( $ID );
    endif;

    // Constructs a custom filter for each type. Annoying, but this is how the CP-BP filters work
    $filter_name = 'task list' == $type ? 'cp_task_list_link' : 'cp_' . $type . '_link';

    return apply_filters( $filter_name, $cp_url, $ID );
}

// Validate Date
function cp_validate_date( $value = NULL ) {
	return preg_match( '`^\d{1,2}/\d{1,2}/\d{4}$`' , $value );
}

/**
 * Check user permissions
 */
function cp_check_permissions( $type = NULL ) {
    $options = get_option( 'cp_options' ); //load settings user role
    $cp_settings_user_role = ( isset( $options[$type] ) ) ? esc_attr( $options[$type] ) : 'manage_options';

    // Filter so that BP-compatibility (and other plugins) can modify
    $cp_settings_user_role = apply_filters( 'cp_settings_user_role', $cp_settings_user_role, $type );
    if ( $cp_settings_user_role == 'all' )
        return true;
	else if ( current_user_can( $cp_settings_user_role ) )
        return true;
    return false;
}

// Clean Querystring
function cp_clean_querystring() {
	$cp_cleaned_querystring = $_SERVER['REQUEST_URI'];
	parse_str($_SERVER['QUERY_STRING'], $cp_querystring);
	foreach ($cp_querystring as $key => $cp_cleaned_query_key) {
		$cp_clean_query_array = array(
			'cp-delete-project-id',
			'cp-delete-task-list-id',
			'cp-delete-task-id',
			'cp-delete-comment-id',
			'_wpnonce'
		);
		if (in_array($key, $cp_clean_query_array))
			$cp_cleaned_querystring = remove_query_arg($key, $cp_cleaned_querystring);
	}
	return $cp_cleaned_querystring;
}

//verify user has access to view a project
function cp_check_project_permissions( $user_id=1, $project_id=1 ) {

    $cp_project_users = get_post_meta( $project_id, '_cp-project-users', true );
    $has_access = false;

    if ( is_array( $cp_project_users ) ) {
	if ( in_array( $user_id, $cp_project_users ) ) {
	    $has_access = true;
	}
    }else{
	//old projects don't have users set so allow access
	$has_access = true;
    }

    return apply_filters( 'cp_check_project_permissions', $has_access, $user_id, $project_id, $cp_project_users );
}

/**
 * Get a task's project ID
 *
 * @since 1.3
 * @todo If and when task lists are made into optional taxonomies, this'll have
 *   to be reworked
 *
 * @param int $task_id
 * @return int|bool Project id if one is found, otherwise false
 */
function cp_get_task_project_id( $task_id = 0 ) {
	$tasklist_id = cp_get_task_tasklist_id( $task_id );
	$project_id = cp_get_tasklist_project_id( $tasklist_id );

	return $project_id;
}

/**
 * Get a task's task list ID
 *
 * @since 1.3
 * @todo If and when task lists are made into optional taxonomies, this'll have
 *   to be reworked
 *
 * @param int $task_id
 * @return int|bool Task list id if one is found, otherwise false
 */
function cp_get_tasklist_project_id( $tasklist_id = 0 ) {
	$tasklist_id = absint( $tasklist_id );
	$project_id = get_post_meta( $tasklist_id, '_cp-project-id', true );

	if ( $project_id ) {
		$project_id = absint( $project_id );
	} else {
		$project_id = false;
	}

	return $project_id;
}

/**
 * Get a task list's project ID
 *
 * @since 1.3
 * @todo If and when task lists are made into optional taxonomies, this'll have
 *   to be reworked
 *
 * @param int $task_list_id
 * @return int|bool Project id if one is found, otherwise false
 */
function cp_get_task_tasklist_id( $task_id = 0 ) {
	$task_id = absint( $task_id );
	$tasklist_id = get_post_meta( $task_id, '_cp-task-list-id', true );

	if ( $tasklist_id ) {
		$tasklist_id = absint( $tasklist_id );
	} else {
		$tasklist_id = false;
	}

	return $tasklist_id;
}

/**
 * Utility function for calling up CP's options array, providing any necessary defaults
 *
 * @package CollabPress
 * @since 1.2
 *
 * @return array $options
 */
function cp_get_options() {
	$options = get_option('cp_options');

	$saved_options = $options;

	// BP settings
	if ( function_exists( 'bp_is_active' ) ) {
		// Set some defaults if necessary
		if ( empty( $options['bp'] ) ) {
			$options['bp'] = array();
		}

		// Default group settings
		if ( bp_is_active( 'groups' ) ) {
			$groups_defaults = array(
				'groups_enabled' 		=> 'enabled',
				'groups_admins_can_disable' 	=> 'allow',
				'groups_admins_can_customize' 	=> 'allow',
				'groups_default_tab_name' 	=> __( 'Projects', 'collabpress' ),
				'groups_default_tab_slug' 	=> sanitize_title( __( 'Projects', 'collabpress' ) )
			);

			foreach ( $groups_defaults as $key => $value ) {
				if ( !isset( $options['bp'][$key] ) )
					$options['bp'][$key] = $value;
			}
		}
	}

	return apply_filters( 'cp_get_options', $options, $saved_options );
}

//limit string length function
function cp_limit_length( $strtolimit=null, $limit=50 ) {

	if ( strlen( $strtolimit ) > $limit ) {
		$strtolimit = substr( $strtolimit, 0, $limit ) .'...';
	}

	return $strtolimit;
}

require_once( CP_PLUGIN_DIR . 'includes/template-tags.php' );

add_action( 'wp_enqueue_scripts', 'cp_maybe_enqueue_style' );
add_action( 'admin_enqueue_scripts', 'cp_maybe_enqueue_style' );


function cp_maybe_enqueue_style() {
	if ( is_collabpress_page() ) {
		wp_enqueue_style( 'collabpress-new', CP_PLUGIN_URL . 'includes/css/new.css' );
		wp_enqueue_style( 'collabpress-fonts', 'http://fonts.googleapis.com/css?family=Roboto+Condensed:300italic,400italic,700italic,300,400,700' );
		wp_enqueue_style( 'cp_admin', CP_PLUGIN_URL . 'includes/css/admin.css' );

		wp_enqueue_style( 'colorbox-css', CP_PLUGIN_URL . 'includes/css/colorbox.css' );
		wp_enqueue_script( 'colorbox', CP_PLUGIN_URL . 'includes/js/jquery.colorbox-min.js', array( 'jquery') );

		wp_enqueue_script( 'cp-task-list', CP_PLUGIN_URL . 'includes/js/task_list.js', array( 'jquery', 'jquery-ui-sortable' ) );
	}

}

/**
 * Check if we're on a CollabPress page.
 *
 * If a slug is included, check if we're on a specific CollabPress page.
 */
function is_collabpress_page( $slug = '' ) {
	global $post;

	// If the page is not set
	if ( (
		empty( $_REQUEST['page'] )
	  	||
	  	( ! empty( $_REQUEST['page'] )
	  		&& $_REQUEST['page'] != 'collabpress-dashboard' )
	  	)
	  	&&
	  	(
	  		! empty( $post->post_content )
	  		&&
	  		strpos( $post->post_content, '[collabpress]' ) === FALSE
  		)


	   )
		return false;

	// Default, if we're just checking that we're on a CollabPress page
	if ( ! $slug )
		return true;

	if ( ! empty( $_REQUEST['project'] ) ) {
		if ( ! empty( $_REQUEST['view'] ) ) {
			if ( $slug == 'project-calendar'
			  && $_REQUEST['view'] == 'calendar' )
				return true;
			if ( $slug == 'project-tasks'
			  && $_REQUEST['view'] == 'tasks' )
				return true;
			if ( $slug == 'project-files'
			  && $_REQUEST['view'] == 'files' )
				return true;
			if ( $slug == 'project-users'
			  && $_REQUEST['view'] == 'users' )
				return true;
		} else {
			if ( ! empty( $_REQUEST['task'] ) ) {
				if ( $slug == 'task' )
					return true;
			} else {
				if ( $slug == 'project-overview' )
					return true;
			}
		}
	} else {
		if ( ! empty( $_REQUEST['view'] ) ) {
			if ( $slug == 'calendar' && $_REQUEST['view'] == 'calendar' )
				return true;
		} else {
			if ( $slug == 'dashboard' )
				return true;
		}
	}
	return false;
}

function cp_task_due_date() {
	global $cp;
	if ( $due_date = get_post_meta( $cp->task->ID, '_cp-task-due', true ) ) {
		return $due_date;
	} else
		return false;
}

function cp_task_priority() {
	global $cp;
	if ( ( $priority = get_post_meta( $cp->task->ID, '_cp-task-priority', true ) != 'None' ) ) {
		return $priority;
	} else
		return false;
}

/**
 * Create new CollabPress project.
 *
 */
function cp_insert_project( $args ) {
	$defaults = array(
		'post_title' => 'New Project',
		'post_status' => 'publish',
		'post_type' => 'cp-projects',
		'project_description' => '',
		'project_users' => array( 1 ),
	);

	$args = wp_parse_args( $args, $defaults );

	extract( $args );

	$project_id = wp_insert_post( $args );

	// Project description
	update_post_meta(
		$project_id,
		'_cp-project-description',
		esc_html( $project_description )
	);

	// Project users
	update_post_meta(
		$project_id,
		'_cp-project-users',
		$project_users
	);

	$current_user = wp_get_current_user();
	if ( ! empty( $current_user ) ) {
		// Add CollabPress Activity entry
		cp_add_activity(
			__('added', 'collabpress'),
			__('project', 'collabpress'),
			$current_user->ID,
			$project_id
		);
	}

	$project_users[] = $current_user->ID;
	$project_users = array_unique( $project_users );
	foreach ( $project_users as $user_id )
		cp_add_user_to_project( $project_id, $user_id );

	do_action( 'cp_project_added', $project_id );

	return $project_id;
}

function cp_add_user_to_project( $project_id, $user_id ) {
	global $wpdb, $cp;
	$row_exists = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*)
			FROM {$cp->tables->project_users}
			WHERE project_id = %d
			AND user_id = %d",
			$project_id,
			$user_id
		)
	);
	if ( $row_exists )
		return true;

	return $wpdb->insert(
		$cp->tables->project_users,
		array(
			'project_id' => $project_id,
			'user_id' => $user_id,
		),
		array(
			'%d',
			'%d',
		)
	);
}

function cp_remove_user_from_project( $project_id, $user_id ) {
	global $wpdb, $cp;
	$row_exists = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*)
			FROM {$cp->tables->project_users}
			WHERE project_id = %d
			AND user_id = %d",
			$project_id,
			$user_id
		)
	);
	if ( ! $row_exists )
		return true;

	return $wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$cp->tables->project_users}
			WHERE project_id = %d
			AND user_id = %d",
			$project_id,
			$user_id
		)
	);
}

function cp_get_project_users( $project_id = 0 ) {
	global $wpdb, $cp;
	if ( ! $project_id )
		$project_id = $cp->project->ID;

	$users = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT * FROM {$wpdb->users} as users
			LEFT JOIN {$cp->tables->project_users} cp_project_users
				ON users.ID = cp_project_users.user_id
			WHERE cp_project_users.project_id = %d",
			$project_id
		)
	);
	return $users;
}

function cp_user_is_in_project( $project_id, $user_id ) {
	global $wpdb, $cp;
	return $wpdb->get_var(
		$wpdb->prepare(
			"SELECT COUNT(*)
			FROM {$cp->tables->project_users}
			WHERE project_id = %d
			AND user_id = %d",
			$project_id,
			$user_id
		)
	);
}

/**
 * Create new CollabPress task.
 *
 */
function cp_insert_task( $args = array() ) {

	$defaults = array(
		'post_title' => 'New Task',
		'post_status' => 'publish',
		'post_type' => 'cp-tasks',
		'project_id' => NULL,
		'task_due_date' => NULL,
		'task_assigned_to' => NULL,
		'task_priority' => 'None',
		'task_list' => 0,
		'send_email_notification' => true
	);
	$args = wp_parse_args( $args, $defaults );

	extract( $args );
	$task_id = wp_insert_post( $args );

	// Where we get the project_id from depends on whether this is a BP installation
	if ( ! $project_id ) {
		if ( is_object( $cp_bp_integration ) && method_exists( $cp_bp_integration, 'get_current_item_project' ) ) {
			$project_id = $cp_bp_integration->get_current_item_project();
		}
	}

	if ( $project_id )
		update_post_meta( $task_id, '_cp-project-id', $project_id );

	//add task status
	update_post_meta( $task_id, '_cp-task-status', 'open' );

	if ( $task_due_date ) {
		// Validate Date
		if ( cp_validate_date( $task_due_date ) )
			$taskDate = esc_html( $task_due_date );

		update_post_meta( $task_id, '_cp-task-due', $taskDate );
	}

	// update task list
	update_post_meta( $task_id, '_cp-task-list-id', $task_list );

	//save the user assignment
	if ( $task_assigned_to )
		update_post_meta( $task_id, '_cp-task-assign', $task_assigned_to );

	//save the task priority
	if ( $task_priority )
		update_post_meta( $task_id, '_cp-task-priority', $task_priority );

	// Add CollabPress Activity entry
	$current_user = wp_get_current_user();
	cp_add_activity(
		__('added', 'collabpress'),
		__('task', 'collabpress'),
		$current_user->ID,
		$task_id
	);

	do_action( 'cp_task_added', $task_id );

	//check if email notification is checked, and a user is assigned to a project
	if( $send_email_notification && $task_assigned_to ) {

	    //send email
	    $task_author_data = get_userdata( $task_assigned_to );
	    $author_email = $task_author_data->user_email;

	    $subject = __('New task assigned to you: ', 'collabpress') .get_the_title( $task_id );

	    $message = __('There is a new task assigned to you:', 'collabpress') . "\n\n";
	    $message .= $post_title;

	    cp_send_email( $author_email, $subject, $message );

	}
}

function cp_insert_task_list( $args = array() ) {
	global $cp_bp_integration;

	$defaults = array(
		'post_title' => 'New Task List',
		'post_status' => 'publish',
		'post_type' => 'cp-task-lists',
		'project_id' => NULL,
		'task_list_description' => '',
	);
	$args = wp_parse_args( $args, $defaults );

	extract( $args );
	$task_list_id = wp_insert_post( $args );

	if ( $project_id )
		update_post_meta( $task_list_id, '_cp-project-id', $project_id );

	update_post_meta( $task_list_id, '_cp-task-list-description', esc_html( $task_list_description ) );

	// Add CollabPress Activity entry
	$current_user = wp_get_current_user();
	cp_add_activity(
		__('added', 'collabpress'),
		__('task list', 'collabpress'),
		$current_user->ID,
		$task_list_id
	);

	do_action( 'cp_task_list_added', $task_list_id );

}

function cp_add_task_to_task_list( $task_id, $task_list_id ) {
	return update_post_meta( $task_id, '_cp-task-list-id', $task_list_id );
}

add_action( 'wp_head', 'cp_define_ajaxurl' );

function cp_define_ajaxurl() {
	if ( is_collabpress_page() ) {
		?><script>var ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';</script><?php
	}
}

function cp_update_task_status( $task_id, $status = 'open' ) {
	return update_post_meta( $task_id, '_cp-task-status', $status );
}

function cp_get_task_status( $task_id ) {
	return get_post_meta( $task_id, '_cp-task-status', true );
}

/**
 * Create a new commment on a task.
 *
 * @uses wp_insert_comment
 */
function cp_insert_comment_on_task( $args = array() ) {

	global $cp_task, $cp;
	global $current_user;
	get_currentuserinfo();

	$time = current_time( 'mysql' );

	$defaults = array(
	    'comment_author' => $current_user->display_name,
	    'comment_author_email' => $current_user->user_email,
	    'comment_author_url' => $current_user->user_email,
	    'comment_type' => 'collabpress',
	    'comment_parent' => 0,
	    'user_id' => $current_user->ID,
	    'comment_author_IP' => preg_replace( '/[^0-9a-fA-F:., ]/', '',$_SERVER['REMOTE_ADDR'] ),
	    'comment_agent' => substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 ),
	    'comment_date' => $time,
	    'comment_approved' => 1,
	);

	// $cp may not be defined, check here
	if ( isset( $cp->task->ID ) )
		$defaults['comment_post_ID'] = $cp->task->ID;
	if ( isset( $_POST['cp-comment-content'] ) )
	 	$defaults['comment_content'] = nl2br( esc_html( $_POST['cp-comment-content'] ) );

	$args = wp_parse_args( $args, $defaults );

	wp_insert_comment( $args );

	//check if email notification is checked
	if ( isset( $_POST['notify'] ) ) {
	    //send email

	    //get user assigned to the task
	    $task_author_id = get_post_meta( $cp->task->ID, '_cp-task-assign', true );
	    $task_author_data = get_userdata( $task_author_id );
	    $author_email = $task_author_data->user_email;

	    $subject = __('New comment on task ', 'collabpress') .get_the_title( $cp->task->ID );

	    $message = __("There is a new comment on your task from ", "collabpress") .$current_user->display_name. ": " .get_the_title( $cp->task->ID ) ."\n\n";
	    $subject = __('New comment on task ', 'collabpress') .get_the_title( $cp_task->id );
	    $subject = apply_filters( 'cp_new_comment_email_subject', $subject );

	    $message = __("There is a new comment on your task from ", "collabpress") .$current_user->display_name. ": " .get_the_title( $cp_task->id ) ."\n\n";
	    $message .= __("Comment:", "collabpress") . "\n";
	    $message .= esc_html( $_POST['cp-comment-content'] );
		$message = apply_filters( 'cp_new_comment_email_body', $message );

	    cp_send_email( $author_email, $subject, $message );

	}

	// Add Activity
	cp_add_activity(__('added', 'collabpress'), __('comment', 'collabpress'), $args['user_id'], $args['comment_post_ID'] );

}
