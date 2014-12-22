<?php

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

	cp_set_project_description( $project_id, $project_description );

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

/*********************/
/** TASK MANAGEMENT **/
/*********************/

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

	// check if email notification is checked, and a user is assigned to a project
	if( $send_email_notification && $task_assigned_to ) {

	    // send email
	    $task_author_data = get_userdata( $task_assigned_to );
	    $author_email = $task_author_data->user_email;

	    $subject = sprintf( __('You have been assigned the task %s.', 'collabpress'),
	    	esc_attr( get_the_title( $task_id ) )
	    );

	    $message = sprintf( __('You have been assigned the task %s.', 'collabpress'),
	    	esc_attr( get_the_title( $task_id ) )
	    );

	    cp_send_email( $author_email, $subject, $message );

	}
}


/**
 * Update existing CollabPress task.
 *
 */
function cp_update_task( $args = array() ) {
	if ( empty( $args['ID'] ) )
		return false;
	extract( $args );
	if ( ! empty( $priority ) )
		update_post_meta( $ID, '_cp-task-priority', $priority );
	if ( ! empty( $task_assigned_to ) )
		update_post_meta( $ID, '_cp-task-assign', $task_assigned_to );
	if ( ! empty( $task_due_date ) )
		update_post_meta( $ID, '_cp-task-due', $task_due_date );
	return wp_update_post( $args );
}


/**************************/
/** TASK LIST MANAGEMENT **/
/**************************/


/**
 * Create new CollabPress task list
 *
 */
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


/************************/
/** COMMENT MANAGEMENT **/
/************************/

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
	    'comment_author'           => $current_user->display_name,
	    'comment_author_email'     => $current_user->user_email,
	    'comment_author_url'       => $current_user->user_email,
	    'comment_type'             => 'collabpress',
	    'comment_parent'           => 0,
	    'user_id'                  => $current_user->ID,
	    'comment_author_IP'        => preg_replace( '/[^0-9a-fA-F:., ]/', '',$_SERVER['REMOTE_ADDR'] ),
	    'comment_agent'            => substr( $_SERVER['HTTP_USER_AGENT'], 0, 254 ),
	    'comment_date'             => $time,
	    'comment_approved'         => 1,
	    'send_email_notification' => true,
	);

	// $cp may not be defined, check here
	if ( isset( $cp->task->ID ) )
		$defaults['comment_post_ID'] = $cp->task->ID;
	if ( isset( $_POST['cp-comment-content'] ) )
	 	$defaults['comment_content'] = wp_kses_post( $_POST['cp-comment-content'] );

	$args = wp_parse_args( $args, $defaults );

	wp_insert_comment( $args );

	//check if email notification is checked
	if ( $args['send_email_notification'] ) {

	    $task_author_id = get_post_meta( $args['comment_post_ID'], '_cp-task-assign', true );
	    $task_author_data = get_userdata( $task_author_id );

	    // Add user assigned to the task to the email list.
	    $to[] = $task_author_data->user_email;

	    // Add all users that have commented on the task to the email list.

	    $comments = get_comments( array(
			'post_id' => $args['comment_post_ID'],
			'order' => 'ASC',
		) );

	    foreach ( $comments as $comment ) {
	    	$to[] = $comment->comment_author_email;
	    }

	    // Remove duplicates from the email list.
	    array_unique( $to );

	    $subject = __('New comment on task ', 'collabpress') .get_the_title( $args['comment_post_ID'] );


	    $subject = apply_filters( 'cp_new_comment_email_subject', $subject );

	    $message = sprintf( __("There is a new comment on the task %s from %s", "collabpress"),
	    	get_the_title( $args['comment_post_ID'] ),
	    	$current_user->display_name
	    );

	    $message .= "\n\n";
	    $message .= __("Comment:", "collabpress") . "\n";
	    $message .= esc_html( $args['comment_content'] );
		$message = apply_filters( 'cp_new_comment_email_body', $message );

	    cp_send_email( $to, $subject, $message );
	}
	// Add Activity
	cp_add_activity( __('commented', 'collabpress'), __('task', 'collabpress'), $args['user_id'], $args['comment_post_ID'] );

}
