<?php

cp_update();

function cp_update() {
	global $wpdb;
	$installed_version = get_option( 'cp_version' );

	if ( $installed_version != CP_VERSION ) {
		// 1.3 specific upgrades
		if ( version_compare( $installed_version, '1.3', '<' ) ) {
			$tablename = $wpdb->prefix . 'cp_project_users';

			// Add project_users table
			$sql = "CREATE TABLE $tablename (
				project_member_id bigint(20) NOT NULL AUTO_INCREMENT,
				project_id bigint(20) NOT NULL,
				user_id bigint(20) NOT NULL,
				UNIQUE KEY project_member_id (project_member_id)
			);";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta( $sql );
		}
		update_option( 'cp_version', CP_VERSION );
	}
}
