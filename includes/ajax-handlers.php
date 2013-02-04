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
