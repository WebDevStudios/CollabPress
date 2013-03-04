<?php

// Add Project
if ( isset( $_POST['cp-add-project'] ) && isset($_POST['cp-project']) ) {

	//check nonce for security
	check_admin_referer( 'cp-add-project' );

	$add_project = array(
					'post_title' => sanitize_text_field( $_POST['cp-project'] ),
					'post_status' => 'publish',
					'post_type' => 'cp-projects'
					);
	$project_id = wp_insert_post( $add_project );
	
	update_post_meta( $project_id, '_cp-project-description', esc_html($_POST['cp-project-description']) );

	);
	cp_insert_project( $args );
	
}

// Edit Project
if ( isset( $_POST['cp-edit-project'] ) && $_POST['cp-edit-project-id'] ) :

	//check nonce for security
	check_admin_referer( 'cp-edit-project' .absint( $_POST['cp-edit-project-id'] ) );

	//verify user has permission to edit projects and post ID is a project CPT
	if ( cp_check_permissions( 'settings_user_role' ) && 'cp-projects' === get_post_type( absint( $_POST['cp-edit-project-id'] ) ) ) {

	    // The ID
	    $projectID = absint( $_POST['cp-edit-project-id'] );

	    $project = array();
	    $project['ID'] = $projectID;
	    $project['post_title'] = sanitize_text_field( $_POST['cp-project'] );
	    wp_update_post( $project );

	    update_post_meta( $projectID, '_cp-project-description', esc_html( $_POST['cp-project-description'] ) );

	    $cp_project_users = ( !empty($_POST['cp_project_users']) ) ? array_map( 'absint', $_POST['cp_project_users'] ) : array( 1 );
	    update_post_meta( $projectID, '_cp-project-users', $cp_project_users );

	    // Add Activity
	    cp_add_activity(__('updated', 'collabpress'), __('project', 'collabpress'), $current_user->ID, $projectID);

	    do_action( 'cp_project_edited', $projectID );
	}

endif;

// Delete Project
if ( isset( $_GET['cp-delete-project-id'] ) ) :

	//check nonce for security
    check_admin_referer( 'cp-action-delete_project' .absint( $_GET['cp-delete-project-id'] ) );

    //verify user has permission to delete projects and post ID is a project CPT
    if ( cp_check_permissions( 'settings_user_role' ) && 'cp-projects' === get_post_type( absint( $_GET['cp-delete-project-id'] ) ) ) {

		$cp_project_id = absint( $_GET['cp-delete-project-id'] );

		//delete the project
		wp_delete_post( $cp_project_id, true );

		//delete all task lists assigned to this project
		$tasks_args = array(
					'post_type' => 'cp-task-lists',
					'meta_key' => '_cp-project-id',
					'meta_value' => $cp_project_id,
					'showposts' => '-1'
					);
		$tasks_query = new WP_Query( $tasks_args );

		// WP_Query();
		if ( $tasks_query->have_posts() ) :
			while( $tasks_query->have_posts() ) : $tasks_query->the_post();

			//delete the task
			wp_delete_post( get_the_ID(), true );

			endwhile;
		endif;

		//delete all tasks assigned to this project
		$tasks_args = array(
					'post_type' => 'cp-tasks',
					'meta_key' => '_cp-project-id',
					'meta_value' => $cp_project_id,
					'showposts' => '-1'
					);
		$tasks_query = new WP_Query( $tasks_args );

		// WP_Query();
		if ( $tasks_query->have_posts() ) :
			while( $tasks_query->have_posts() ) : $tasks_query->the_post();

			//delete the task
			wp_delete_post( get_the_ID(), true );

			endwhile;
		endif;

		//add activity log
		cp_add_activity(__('deleted', 'collabpress'), __('project', 'collabpress'), $current_user->ID, $cp_project_id );

		do_action( 'cp_project_deleted', $cp_project_id );

    }

endif;
