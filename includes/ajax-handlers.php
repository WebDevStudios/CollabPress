<?php

add_action( 'wp_ajax_cp_add_project', 'cp_add_project_ajax_handler' );

function cp_add_project_ajax_handler() {
	$data = $_REQUEST['data'];

	if ( ! wp_verify_nonce( $data['nonce'], 'add-new-project' ) ) {
		echo 'bad nonce';
		die;
	}
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
	
	$data = $_REQUEST['data'];
	extract( $data );

	if ( ! wp_verify_nonce( $nonce, 'modify_project_users' ) ) {
		echo 'bad nonce';
		die;
	}

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
	
	$data = $_REQUEST['data'];
	extract( $data );
	if ( ! wp_verify_nonce( $nonce, 'add_new_task' ) ) {
		echo 'bad nonce';
		die;
	}
	cp_insert_task( $data );
	
	$permalink = cp_get_project_tasks_permalink( $project_id );

	wp_send_json_success( array( 'redirect' => $permalink ) );
}


add_action( 'wp_ajax_cp_delete_task', 'cp_delete_task_handler' );

function cp_delete_task_handler() {
	global $wpdb, $cp;
	
	$data = $_REQUEST['data'];
	extract( $data );
	// todo fix nonce
	// if ( ! wp_verify_nonce( $nonce, 'add_new_task' ) ) {
	// 	echo 'bad nonce';
	// 	die;
	// }
	wp_delete_post( $task_id, true );

	wp_send_json_success();
}

add_action( 'wp_ajax_cp_add_new_task_list', 'cp_add_new_task_list_handler' );

function cp_add_new_task_list_handler() {
	global $wpdb, $cp;
	
	$data = $_REQUEST['data'];
	extract( $data );
	
	// todo: fix nonces
	// if ( ! wp_verify_nonce( $nonce, 'add_new_task' ) ) {
	// 	echo 'bad nonce';
	// 	die;
	// }
	cp_insert_task_list( $data );
	
	$permalink = cp_get_project_tasks_permalink( $project_id );
	
	wp_send_json_success( array( 'redirect' => $permalink ) );
}

add_action( 'wp_ajax_cp_attach_new_file', 'cp_attach_new_file_handler' );

function cp_attach_new_file_handler() {
	global $wpdb, $cp;
	
	$data = $_REQUEST['data'];
	extract( $data );
	// todo fix nonce
	$attachment = get_post( $attachment_id );

	wp_insert_attachment( $attachment, '', $project_id );

	wp_send_json_success();
}

add_action( 'wp_ajax_cp_save_task_list_order', 'cp_save_task_list_order' );

function cp_save_task_list_order() {
	global $wpdb, $cp;

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
	$task_status = ( $task_status == 'on' ) ? 'complete' : 'open';
	cp_update_task_status( $task_id, $task_status );
	wp_send_json_success();
}

add_action( 'wp_ajax_cp_add_comment_to_task', 'cp_add_comment_to_task_handler' );

function cp_add_comment_to_task_handler() {
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

