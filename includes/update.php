<?php
add_action( 'init', 'cp_update' );

function cp_update() {
	global $wpdb;
	$installed_version = get_option( 'CP_VERSION' );

	if ( $installed_version != COLLABPRESS_VERSION ) {
		// 1.3 specific upgrades
		if ( version_compare( $installed_version, '1.3-dev', '<' ) ) {
			$tablename = $wpdb->prefix . 'cp_project_users';

			// Add project_users table
			$sql = "CREATE TABLE $tablename (
				project_member_id bigint(20) NOT NULL AUTO_INCREMENT,
				project_id bigint(20) NOT NULL,
				user_id bigint(20) NOT NULL,
				UNIQUE KEY project_member_id (project_member_id)
			);";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			// Move old user relationships into new user table
			$projects = get_posts(
				array(
					'post_type' => 'cp-projects',
					'posts_per_page' => -1
				)
			);
			foreach ( $projects as $project ) {
				$users = get_post_meta( $project->ID, '_cp-project-users', true );
				foreach ( $users as $user_id ) {
					cp_add_user_to_project( $project->ID, $user_id );
				}
			}

		}

		// 1.4 specific upgrades
		if ( version_compare( $installed_version, '1.4-dev', '<' ) ) {

			// Change task due date storage format from m/d/yy to mysql formatted date
			$tasks = get_posts( array( 'post_type' => 'cp-tasks', 'posts_per_page' => -1 ) );
			foreach ( $tasks as $task ) {
				$due_date = cp_get_task_due_date_mysql( $task->ID );
				$unix_timestamp = strtotime( $due_date );
				$formatted_date  = gmdate( 'Y-m-d H:i:s', ( $unix_timestamp ) );
				cp_update_task( array( 'ID' => $task->ID, 'task_due_date' => $formatted_date ) );
			}

			$cp_options = get_option( 'cp_options' );
			if ( empty( $cp_options['date_format'] ) ) {
				$cp_options['date_format'] = 'F j, Y';
				update_option( 'cp_options', $cp_options );
			}
		}

		update_option( 'CP_VERSION', COLLABPRESS_VERSION );
	}
}
