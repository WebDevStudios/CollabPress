<?php

global $cp_project;
global $cp_task_list;

// Add Task
if ( isset( $_POST['cp-add-task'] ) && isset( $_POST['cp-task'] ) ) :

	//check nonce for security
	check_admin_referer( 'cp-add-task' );

	$add_task = array(
					'post_title' => sanitize_text_field( $_POST['cp-task'] ),
					'post_status' => 'publish',
					'post_type' => 'cp-tasks'
					);
	$task_id = wp_insert_post( $add_task );

	//add task status
	update_post_meta( $task_id, '_cp-task-status', 'open' );

	$cp_bp_integration = cp_bp();

	// Where we get the project_id from depends on whether this is a BP installation
	if ( isset( $_GET['project'] ) ) {
		$project_id = $cp_project->id;
	} else if ( is_object( $cp_bp_integration ) && method_exists( $cp_bp_integration, 'get_current_item_project' ) ) {
		$project_id = $cp_bp_integration->get_current_item_project();
	} else {
		$project_id = NULL;
	}

	if ( $project_id ) {
		update_post_meta( $task_id, '_cp-project-id', $project_id );
	}

	// Where we get the task list from depends on whether this is a BP installation
	if ( isset( $_GET['task-list'] ) ) {
		$task_list_id = $cp_task_list->id;
	} else if ( is_object( $cp_bp_integration ) && method_exists( $cp_bp_integration, 'get_current_item_task_list' ) ) {
		$task_list_id = $cp_bp_integration->get_current_item_task_list();
	} else {
		$task_list_id = NULL;
	}

	if ( $task_list_id ) {
		update_post_meta( $task_id, '_cp-task-list-id', $task_list_id );
	}

	if ( isset($_POST['cp-task-due']) ) :
		// Validate Date
		if ( cp_validate_date($_POST['cp-task-due']) ) :
			$taskDate = esc_html($_POST['cp-task-due']);
		else :
			$taskDate = date('n/j/Y');
		endif;
		update_post_meta( $task_id, '_cp-task-due', $taskDate );
	endif;

	//save the user assignment
	if ( isset($_POST['cp-task-assign']) )
		update_post_meta( $task_id, '_cp-task-assign', absint($_POST['cp-task-assign']) );

	//save the task priority
	if ( isset($_POST['cp-task-priority']) )
		update_post_meta( $task_id, '_cp-task-priority', strip_tags($_POST['cp-task-priority']) );

	// Add Activity
	cp_add_activity(__('added', 'collabpress'), __('task', 'collabpress'), $current_user->ID, $task_id);

	do_action( 'cp_task_added', $task_id );

	//check if email notification is checked
	if( isset( $_POST['notify'] ) ) {

	    //send email
	    $task_author_data = get_userdata( absint( $_POST['cp-task-assign'] ) );
	    $author_email = $task_author_data->user_email;

		$subject = apply_filters( 'cp_new_task_email_subject', __('New task assigned to you: ', 'collabpress') .get_the_title( $task_id ) );

	    $message = __('There is a new task assigned to you:', 'collabpress') . "\n\n";
	    $message .= esc_html( $_POST['cp-task'] );
		$message = apply_filters( 'cp_new_task_email_body', $message );

	    cp_send_email( $author_email, $subject, $message );

	}

endif;

// Edit Task
if ( isset( $_POST['cp-edit-task'] ) && isset( $_POST['cp-edit-task-id'] ) ) :

	//check nonce for security
	check_admin_referer( 'cp-edit-task' .absint( $_POST['cp-edit-task-id'] ) );

    //verify user has permission to edit tasks and post ID is a task CPT
    if ( cp_check_permissions( 'settings_user_role' ) && 'cp-tasks' === get_post_type( absint( $_POST['cp-edit-task-id'] ) ) ) {

	// The ID
	$taskID =  absint( $_POST['cp-edit-task-id'] );

	$task = array();
	$task['ID'] = $taskID;
	$task['post_title'] = sanitize_text_field( $_POST['cp-task'] );
	wp_update_post( $task );
	update_post_meta( $taskID, '_cp-task-due', sanitize_text_field( $_POST['cp-task-due'] ) );
	update_post_meta( $taskID, '_cp-task-assign', absint( $_POST['cp-task-assign'] ) );
	update_post_meta( $taskID, '_cp-task-priority', sanitize_text_field( $_POST['cp-task-priority'] ) );

	// Add Activity
	cp_add_activity(__('updated', 'collabpress'), __('task', 'collabpress'), $current_user->ID, $taskID);

	do_action( 'cp_task_edited', $taskID );

    }

endif;

// Complete Task
if ( isset( $_GET['cp-complete-task-id'] ) && 'cp-tasks' === get_post_type( absint( $_GET['cp-complete-task-id'] ) ) ) :

	//check nonce for security
    check_admin_referer( 'cp-complete-task' .absint( $_GET['cp-complete-task-id'] ) );

    //task ID to complete
    $taskID = ( isset( $_GET['cp-complete-task-id'] ) ) ? absint( $_GET['cp-complete-task-id'] ) : null;

    //get current task status
    $task_status = get_post_meta( $taskID, '_cp-task-status', true );

    if ( $taskID && $task_status != 'complete' ) {

		//set the task to complete
		update_post_meta( $taskID, '_cp-task-status', 'complete' );

		// Add Activity
		cp_add_activity(__('completed', 'collabpress'), __('task', 'collabpress'), $current_user->ID, $taskID );

		do_action( 'cp_task_completed', $taskID );

    }elseif ( $taskID ) {

		//open the task
		update_post_meta( $taskID, '_cp-task-status', 'open' );

		// Add Activity
		cp_add_activity(__('opened', 'collabpress'), __('task', 'collabpress'), $current_user->ID, $taskID );

		do_action( 'cp_task_reopened', $taskID );

    }

endif;

// Delete Task
if ( isset( $_GET['cp-delete-task-id'] ) ) :

	//check nonce for security
    check_admin_referer( 'cp-action-delete_task' .absint( $_GET['cp-delete-task-id'] ) );

    //verify user has permission to delete tasks and post ID is a task CPT
    if ( cp_check_permissions( 'settings_user_role' ) && 'cp-tasks' === get_post_type( absint( $_GET['cp-delete-task-id'] ) ) ) {

		$cp_task_id = absint( $_GET['cp-delete-task-id'] );

		//delete the task
		wp_trash_post( $cp_task_id, true );

		//add activity log
		cp_add_activity(__('deleted', 'collabpress'), __('task', 'collabpress'), $current_user->ID, $cp_task_id );

		do_action( 'cp_task_deleted', $cp_task_id );

    }

endif;
