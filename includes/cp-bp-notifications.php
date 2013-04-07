<?php

/**
 * This file contains functions related to various BP notification types, broadly construed:
 *   - BP activity streams (todo)
 *   - BP admin bar notifications (todo)
 *   - BP template notices
 *
 * @package CollabPress
 * @since 1.2
 */


/*********************************************/
/* TEMPLATE NOTICES                          */
/*********************************************/

/* TASKS */
function bp_cp_tn_task_added() {
	bp_core_add_message( __( 'Task added!', 'collabpress' ) );
}
add_action( 'cp_task_added', 'bp_cp_tn_task_added' );

function bp_cp_tn_task_edited() {
	bp_core_add_message( __( 'Task edited!', 'collabpress' ) );
}
add_action( 'cp_task_edited', 'bp_cp_tn_task_edited' );

function bp_cp_tn_task_completed() {
	bp_core_add_message( __( 'Task completed!', 'collabpress' ) );
}
add_action( 'cp_task_completed', 'bp_cp_tn_task_completed' );

function bp_cp_tn_task_reopened() {
	bp_core_add_message( __( 'Task reopened!', 'collabpress' ) );
}
add_action( 'cp_task_reopened', 'bp_cp_tn_task_reopened' );

function bp_cp_tn_task_deleted() {
	bp_core_add_message( __( 'Task deleted!', 'collabpress' ) );
}
add_action( 'cp_task_deleted', 'bp_cp_tn_task_deleted' );

/* TASK LISTS */
function bp_cp_tn_task_list_added() {
	bp_core_add_message( __( 'Task list added!', 'collabpress' ) );
}
add_action( 'cp_task_list_added', 'bp_cp_tn_task_list_added' );

function bp_cp_tn_task_list_edited() {
	bp_core_add_message( __( 'Task list edited!', 'collabpress' ) );
}
add_action( 'cp_task_list_edited', 'bp_cp_tn_task_list_edited' );

function bp_cp_tn_task_list_deleted() {
	bp_core_add_message( __( 'Task list deleted!', 'collabpress' ) );
}
add_action( 'cp_task_list_deleted', 'bp_cp_tn_task_list_deleted' );

/* PROJECTS */
function bp_cp_tn_project_added() {
	bp_core_add_message( __( 'Project added!', 'collabpress' ) );
}
add_action( 'cp_project_added', 'bp_cp_tn_project_added' );

function bp_cp_tn_project_edited() {
	bp_core_add_message( __( 'Project edited!', 'collabpress' ) );
}
add_action( 'cp_project_edited', 'bp_cp_tn_project_edited' );

function bp_cp_tn_project_deleted() {
	bp_core_add_message( __( 'Project deleted!', 'collabpress' ) );
}
add_action( 'cp_project_deleted', 'bp_cp_tn_project_deleted' );

/*********************************************/
/* BP ACTIVITY STREAM                        */
/*********************************************/

/**
 * Hooks the BP activity poster.
 */
add_action( 'cp_add_activity', 'cp_bp_post_activity_do', 10, 4 );

/**
 * Posts a BP activity item
 *
 * @package CollabPress
 * @since 1.2
 *
 * @param str $action 'updated', 'created', etc
 * @param str $type 'project', 'task-list', or 'task'
 * @param int $author The author ID
 * @param int $cp_post_id The ID of the CP post
 */
function cp_bp_post_activity_do( $action, $type, $author, $cp_post_id ) {
	global $bp;

	// This hyphen is getting stripped somewhere
	if ( 'task list' == $type )
		$type = 'task-list';

	$bp_activity_add_args = array(
		'component'	    => 'collabpress',
		'user_id'           => $author,
		'secondary_item_id' => $cp_post_id
	);

	// Get the user display name
	$display_name = bp_core_get_user_displayname( $author );

	switch( $type ) {
		case 'task' :
			$task_link = cp_bp_get_task_permalink( $cp_post_id );
			$bp_activity_add_args['primary_link'] = $task_link;

			$task_obj  = get_post( $cp_post_id );
			$task_name = $task_obj->post_title;

			switch( $action ) {
				case 'updated' :
					$bp_activity_add_args['type']   = 'cp_task_updated';
					$bp_activity_add_args['action'] = sprintf( __( '%1$s updated the task "%2$s"', 'collabpress' ), bp_core_get_userlink( $author ), '<a href="' . $task_link . '">' . $task_name . '</a>' );
					break;

				case 'opened' :
					$bp_activity_add_args['type']   = 'cp_task_opened';
					$bp_activity_add_args['action'] = sprintf( __( '%1$s reopened the task "%2$s"', 'collabpress' ), bp_core_get_userlink( $author ), '<a href="' . $task_link . '">' . $task_name . '</a>' );
					break;

				case 'deleted' :
					$bp_activity_add_args['type']   = 'cp_task_deleted';
					$bp_activity_add_args['action'] = sprintf( __( '%1$s deleted the task "%2$s"', 'collabpress' ), bp_core_get_userlink( $author ), '<a href="' . $task_link . '">' . $task_name . '</a>' );
					break;

				case 'edited' :
					$bp_activity_add_args['type']   = 'cp_task_edited';
					$bp_activity_add_args['action'] = sprintf( __( '%1$s edited the task "%2$s"', 'collabpress' ), bp_core_get_userlink( $author ), '<a href="' . $task_link . '">' . $task_name . '</a>' );
					break;

				case 'added' :
					$bp_activity_add_args['type']   = 'cp_task_added';
					$bp_activity_add_args['action'] = sprintf( __( '%1$s added the task "%2$s"', 'collabpress' ), bp_core_get_userlink( $author ), '<a href="' . $task_link . '">' . $task_name . '</a>' );
					break;

				case 'completed' :
					$bp_activity_add_args['type']   = 'cp_task_completed';
					$bp_activity_add_args['action'] = sprintf( __( '%1$s completed the task "%2$s"', 'collabpress' ), bp_core_get_userlink( $author ), '<a href="' . $task_link . '">' . $task_name . '</a>' );
					break;

				case 'commented' :
					$bp_activity_add_args['type']   = 'cp_task_commented';
					$bp_activity_add_args['action'] = sprintf( __( '%1$s commented on the task "%2$s"', 'collabpress' ), bp_core_get_userlink( $author ), '<a href="' . $task_link . '">' . $task_name . '</a>' );
					break;
			}
			break;

		case 'task-list' :
			$task_list_link = cp_bp_get_task_list_permalink( $cp_post_id );
			$bp_activity_add_args['primary_link'] = $task_list_link;

			$task_list_obj  = get_post( $cp_post_id );
			$task_list_name = $task_list_obj->post_title;

			switch( $action ) {
				case 'deleted' :
					$bp_activity_add_args['type']   = 'cp_task_list_deleted';
					$bp_activity_add_args['action'] = sprintf( __( '%1$s deleted the task list "%2$s"', 'collabpress' ), bp_core_get_userlink( $author ), '<a href="' . $task_list_link . '">' . $task_list_name . '</a>' );
					break;

				case 'edited' :
					$bp_activity_add_args['type']   = 'cp_task_list_edited';
					$bp_activity_add_args['action'] = sprintf( __( '%1$s edited the task list "%2$s"', 'collabpress' ), bp_core_get_userlink( $author ), '<a href="' . $task_list_link . '">' . $task_list_name . '</a>' );
					break;

				case 'added' :
					$bp_activity_add_args['type']   = 'cp_task_list_added';
					$bp_activity_add_args['action'] = sprintf( __( '%1$s added the task list "%2$s"', 'collabpress' ), bp_core_get_userlink( $author ), '<a href="' . $task_list_link . '">' . $task_list_name . '</a>' );
					break;
			}
			break;

		case 'project' :
			$project_link = cp_bp_get_project_permalink( $cp_post_id );
			$bp_activity_add_args['primary_link'] = $project_link;

			$project_obj  = get_post( $cp_post_id );
			$project_name = $project_obj->post_title;

			switch( $action ) {
				case 'deleted' :
					$bp_activity_add_args['type']   = 'cp_project_deleted';
					$bp_activity_add_args['action'] = sprintf( __( '%1$s deleted the project "%2$s"', 'collabpress' ), bp_core_get_userlink( $author ), '<a href="' . $project_link . '">' . $project_name . '</a>' );
					break;

				case 'edited' :
					$bp_activity_add_args['type']   = 'cp_project_edited';
					$bp_activity_add_args['action'] = sprintf( __( '%1$s edited the project "%2$s"', 'collabpress' ), bp_core_get_userlink( $author ), '<a href="' . $project_link . '">' . $project_name . '</a>' );
					break;

				case 'added' :
					$bp_activity_add_args['type']   = 'cp_project_added';
					$bp_activity_add_args['action'] = sprintf( __( '%1$s added the project "%2$s"', 'collabpress' ), bp_core_get_userlink( $author ), '<a href="' . $project_link . '">' . $project_name . '</a>' );
					break;
			}
			break;
	}

	if ( bp_is_active( 'groups' ) && bp_is_group() ) {
		$bp_activity_add_args['component'] = 'groups';
		$bp_activity_add_args['item_id']   = bp_get_current_group_id();
		$bp_activity_add_args['hide_sitewide'] = 'public' != $bp->groups->current_group->status;

		$bp_activity_add_args['action'] .= sprintf( __( ' in the group %s', 'collabpress' ), '<a href="' . bp_get_group_permalink( groups_get_current_group() ) . '">' . bp_get_current_group_name() . '</a>' );

	}

	bp_activity_add( $bp_activity_add_args );
}

?>