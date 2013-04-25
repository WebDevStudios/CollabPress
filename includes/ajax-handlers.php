<?php

add_action( 'wp_ajax_cp_add_project', 'cp_add_project_ajax_handler' );

function cp_add_project_ajax_handler() {
	// Nonce check
	check_admin_referer( 'add-new-project', 'nonce' );

	$data = $_REQUEST['data'];

	$args = array(
		'post_title' => $data['project_name'],
		'project_description' => $data['project_description']
	);

	if ( ! empty( $data['users'] ) )
		$args['project_users'] = $data['users'];

	$project_id = cp_insert_project( $args );

	$permalink = cp_get_project_permalink( $project_id );
	wp_send_json_success( array( 'redirect' => $permalink ) );
}

add_action( 'wp_ajax_cp_modify_project_users', 'cp_modify_project_users_handler' );

function cp_modify_project_users_handler() {
	global $wpdb, $cp;

	// Nonce check
	check_admin_referer( 'modify-project-users', 'nonce' );

	$data = $_REQUEST['data'];
	extract( $data );

	$wpdb->query(
		$wpdb->prepare(
			"DELETE FROM {$cp->tables->project_users}
			WHERE project_id = %d",
			$project_id
		)
	);

	foreach ( $users as $user_id )
		cp_add_user_to_project( $project_id, $user_id );

	$permalink = cp_get_project_users_permalink( $project_id );
	wp_send_json_success( array( 'redirect' => $permalink ) );
}

add_action( 'wp_ajax_cp_add_new_task', 'cp_add_new_task_handler' );

function cp_add_new_task_handler() {
	global $wpdb, $cp;

	// Nonce check
	check_admin_referer( 'add-new-task', 'nonce' );

	$data = $_REQUEST['data'];
	extract( $data );

	cp_insert_task( $data );

	$permalink = cp_get_project_tasks_permalink( $project_id );

	wp_send_json_success( array( 'redirect' => $permalink ) );
}


add_action( 'wp_ajax_cp_delete_task', 'cp_delete_task_handler' );

function cp_delete_task_handler() {
	global $wpdb, $cp;

	$data = $_REQUEST['data'];
	extract( $data );

	// Nonce check
	check_admin_referer( 'delete-task_' . $task_id, 'nonce' );

	$task = get_post( $task_id );

	// If we're deleting a task list, move all tasks related
	// to it up to the project level
	if ( $task->post_type == 'cp-task-lists' ) {
		$task_list_tasks = get_posts( array(
			'posts_per_page' => -1,
			'post_type' => 'cp-tasks',
			'meta_query' => array(
				array(
					'key' => '_cp-task-list-id',
					'value' => $task->ID,
				),
			),
			'orderby' => 'menu_order',
			'order' => 'ASC',
		) );
		foreach ( $task_list_tasks as $task_list_task )
			update_post_meta( $task_list_task->ID, '_cp-task-list-id', '0' );
	}
	wp_delete_post( $task_id, true );

	wp_send_json_success();
}

add_action( 'wp_ajax_cp_add_new_task_list', 'cp_add_new_task_list_handler' );

function cp_add_new_task_list_handler() {
	global $wpdb, $cp;

	// Nonce check
	check_admin_referer( 'add-new-task-list', 'nonce' );

	$data = $_REQUEST['data'];
	extract( $data );

	cp_insert_task_list( $data );

	$permalink = cp_get_project_tasks_permalink( $project_id );

	wp_send_json_success( array( 'redirect' => $permalink ) );
}


add_action( 'wp_ajax_cp_edit_task', 'cp_edit_task_handler' );

function cp_edit_task_handler() {
	global $wpdb, $cp;

	// Nonce check
	check_admin_referer( 'edit-task', 'nonce' );

	$data = $_REQUEST['data'];
	extract( $data );
	cp_update_task( $data );
	$permalink = cp_get_task_permalink( $ID );

	wp_send_json_success( array( 'redirect' => $permalink ) );
}



add_action( 'wp_ajax_cp_attach_new_file', 'cp_attach_new_file_handler' );

function cp_attach_new_file_handler() {
	global $wpdb, $cp;

	// Nonce check
	check_admin_referer( 'cp_add_new_file', 'nonce' );

	$data = $_REQUEST['data'];
	extract( $data );

	$attachment = get_post( $attachment_id );

	wp_insert_attachment( $attachment, '', $project_id );

	wp_send_json_success();
}

add_action( 'wp_ajax_cp_save_task_list_order', 'cp_save_task_list_order' );

function cp_save_task_list_order() {
	global $wpdb, $cp;

	// Nonce check
	check_admin_referer( 'save-task-list-order', 'nonce' );

	$data = $_REQUEST['data'];
	extract( $data );

	foreach ( $items as $item ) {
		$post = get_post( $item['ID'] );
		wp_update_post( $item );
		if ( isset( $item['task_list'] ) ) {
			cp_add_task_to_task_list( $item['ID'], $item['task_list'] );
		} else if ( $post->post_type == 'cp-tasks' )
			update_post_meta( $item['ID'], '_cp-task-list-id', 0 );
	}
	wp_send_json_success();
}

add_action( 'wp_ajax_cp_update_task_status', 'cp_update_task_status_handler' );

function cp_update_task_status_handler() {
	$data = $_REQUEST['data'];
	extract( $data );

	// Nonce check
	check_admin_referer( 'item-complete-status-change_' . $task_id, 'nonce' );

	$task_status = $data['task_status'];
	cp_update_task_status( $task_id, $task_status );
	wp_send_json_success();
}

add_action( 'wp_ajax_cp_add_comment_to_task', 'cp_add_comment_to_task_handler' );

function cp_add_comment_to_task_handler() {
	// Nonce check
	check_admin_referer( 'add-task-comment', 'nonce' );

	$data = $_REQUEST['data'];
	extract( $data );
	cp_insert_comment_on_task(
		array(
			'comment_post_ID' => $task_id,
			'comment_content' => nl2br( esc_html( $comment_content ) )
		)
	);

	$permalink = cp_get_task_permalink( $task_id );
	wp_send_json_success( array( 'redirect' => $permalink ) );
}

add_action( 'wp_ajax_cp_delete_comment', 'cp_delete_comment_handler' );

function cp_delete_comment_handler() {
	$data = $_REQUEST['data'];
	extract( $data );

	// Nonce check
	check_admin_referer( 'delete-task-comment_' . $comment_id, 'nonce' );

	wp_delete_comment( $comment_id, true );
	wp_send_json_success();
}

add_action( 'wp_ajax_cp_edit_project', 'cp_edit_project_handler' );

function cp_edit_project_handler() {
	// Nonce check
	check_admin_referer( 'edit-project', 'nonce' );

	$data = $_REQUEST['data'];
	extract( $data );
	wp_update_post( $data );
	cp_set_project_description( $ID, $project_description );
	$permalink = cp_get_project_permalink( $ID );
	wp_send_json_success( array( 'redirect' => $permalink ) );
}

add_action( 'wp_ajax_cp_delete_project', 'cp_delete_project_handler' );

function cp_delete_project_handler() {
	// Nonce check
	check_admin_referer( 'delete-project', 'nonce' );

	$data = $_REQUEST['data'];
	extract( $data );

	wp_delete_post( $ID, true );
	$permalink = CP_DASHBOARD;
	wp_send_json_success( array( 'redirect' => $permalink ) );
}

add_action( 'wp_ajax_cp_set_user_preferences_for_displaying_completed_tasks', 'cp_set_user_preferences_for_displaying_completed_tasks_handler' );

function cp_set_user_preferences_for_displaying_completed_tasks_handler() {
	// Nonce check
	check_admin_referer( 'toggle-user-preference-view-completed-task', 'nonce' );

	$data = $_REQUEST['data'];
	extract( $data );

	$current_user = wp_get_current_user();
	update_user_option( $current_user->ID, 'display_completed_tasks', $display_completed_tasks );
	wp_send_json_success();
}