<?php

global $cp_project;
global $cp_task_list;
global $cp_bp_integration;

// Add Task
if ( isset( $_POST['cp-add-task'] ) && isset($_POST['cp-task']) ) :

	check_admin_referer('cp-add-task');

	$task_id = cp_insert_task( array( $_POST['cp-task'] ) );

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

endif;

// Edit Task
if ( isset( $_POST['cp-edit-task'] ) && isset($_POST['cp-edit-task-id']) ) :

	check_admin_referer('cp-edit-task');

    //verify user has permission to edit tasks
    if ( cp_check_permissions( 'settings_user_role' ) ) {

	// The ID
	$taskID =  absint($_POST['cp-edit-task-id']);

	$task = array();
	$task['ID'] = $taskID;
	$task['post_title'] = esc_html($_POST['cp-task']);
	wp_update_post( $task );
	update_post_meta( $taskID, '_cp-task-due', esc_html($_POST['cp-task-due']) );
	update_post_meta( $taskID, '_cp-task-assign', absint($_POST['cp-task-assign']) );
	update_post_meta( $taskID, '_cp-task-priority', strip_tags($_POST['cp-task-priority']) );

	// Add Activity
	cp_add_activity(__('updated', 'collabpress'), __('task', 'collabpress'), $current_user->ID, $taskID);

	do_action( 'cp_task_edited', $taskID );

    }

endif;

// Complete Task
if ( isset( $_GET['cp-complete-task-id'] ) ) :

    check_admin_referer('cp-complete-task');

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

    }elseif ($taskID ) {

	//open the task
	update_post_meta( $taskID, '_cp-task-status', 'open' );

	// Add Activity
	cp_add_activity(__('opened', 'collabpress'), __('task', 'collabpress'), $current_user->ID, $taskID );

	do_action( 'cp_task_reopened', $taskID );

    }

endif;

// Delete Task
if ( isset( $_GET['cp-delete-task-id'] ) ) :

    check_admin_referer( 'cp-action-delete_task' );

    //verify user has permission to delete tasks
    if ( cp_check_permissions( 'settings_user_role' ) ) {

	$cp_task_id = absint( $_GET['cp-delete-task-id'] );

	//delete the task
	wp_trash_post( $cp_task_id, true );

	//add activity log
	cp_add_activity(__('deleted', 'collabpress'), __('task', 'collabpress'), $current_user->ID, $cp_task_id );

	do_action( 'cp_task_deleted', $cp_task_id );

    }

endif;