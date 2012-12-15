<?php

// Get Page Title
function cp_get_page_title() {

	global $cp_dashboard_page;
	global $cp_project_page;
	global $cp_task_list_page;
	global $cp_task_page;
	global $cp_user_page;
	global $cp_calendar_page;
	global $cp_view_projects;

	global $cp_project;
	global $cp_task_list;
	global $cp_task;
	global $cp_user;

	// Task
	if ( $cp_task_page ) :

		// Edit Task
		if ( isset($_GET['view']) && $_GET['view'] == 'edit' ) :
			$dashboardTitle = '<h2>'.cp_screen_icon('collabpress').get_the_title($cp_task->id).' - <a title="'.__('Back', 'collabpress').'" href="'.CP_DASHBOARD.'&project='.$cp_project->id.'&task='.$cp_task->id.'">'.__('Back', 'collabpress').'</a></h2>';

		// Normal Task
		else :
			$dashboardTitle = '<h2>'.cp_screen_icon('collabpress').get_the_title($cp_task->id);

			//check if user can view edit/delete links
			if ( cp_check_permissions( 'settings_user_role' ) ) {
			    $dashboardTitle .= ' - <a title="'.__('Edit', 'collabpress').'" href="'.CP_DASHBOARD.'&project='.$cp_project->id.'&task='.$cp_task->id.'&view=edit">'.__('Edit', 'collabpress').'</a>';
			}

			$dashboardTitle .= '</h2>';
		endif;

	// Task List
	elseif ( $cp_task_list_page ) :
		$task_list_desc = get_post_meta( $cp_task_list->id, '_cp-task-list-description', true );

		// Edit Task List
		if ( isset($_GET['view']) && $_GET['view'] == 'edit' ) :
			$dashboardTitle = '<h2>'.cp_screen_icon('collabpress').get_the_title($cp_task_list->id).' - <a title="'.__('Back', 'collabpress').'" href="'.CP_DASHBOARD.'&project='.$cp_project->id.'&task-list='.$cp_task_list->id.'">'.__('Back', 'collabpress').'</a></h2>';

		// Normal Task List
		else :
			$dashboardTitle = '<h2>'.cp_screen_icon('collabpress').get_the_title($cp_task_list->id);

		    //check if user can view edit/delete links
		    if ( cp_check_permissions( 'settings_user_role' ) ) {
			$dashboardTitle .= ' - <a title="'.__('Edit', 'collabpress').'" href="'.CP_DASHBOARD.'&project='.$cp_project->id.'&task-list='.$cp_task_list->id.'&view=edit">'.__('Edit', 'collabpress').'</a>';
		    }

		    $dashboardTitle .= '</h2>';

		endif;

		if ($task_list_desc) : $dashboardTitle .= '<p class="description">'.$task_list_desc.'</p>'; endif;

	// Project
	elseif ( $cp_project_page ) :
		$project_desc = get_post_meta( $cp_project->id, '_cp-project-description', true );

		// Edit Project
		if ( isset($_GET['view']) && $_GET['view'] == 'edit' ) :
			$dashboardTitle = '<h2>'.cp_screen_icon('collabpress').get_the_title($cp_project->id).' - <a title="'.__('Back', 'collabpress').'" href="'.CP_DASHBOARD.'&project='.$cp_project->id.'">'.__('Back', 'collabpress').'</a></h2>';

		// Normal Project
		else :
			//generate delete project link
			$cp_del_link = CP_DASHBOARD .'&cp-delete-project-id='.$cp_project->id;
			$cp_del_link = ( function_exists('wp_nonce_url') ) ? wp_nonce_url( $cp_del_link, 'cp-action-delete_project' ) : $cp_del_link;

			$dashboardTitle = '<h2><a title="'.__('CollabPress Dashboard', 'collabpress').'" href="'.CP_DASHBOARD.'">'.cp_screen_icon('collabpress').'</a>'.get_the_title($cp_project->id);

			//check if user can view edit/delete links
			if ( cp_check_permissions( 'settings_user_role' ) ) {
				$dashboardTitle .= ' - <a title="'.__('Edit', 'collabpress').'" href="'.CP_DASHBOARD.'&project='.$cp_project->id.'&view=edit">'.__('Edit', 'collabpress').'</a>&middot; <a href="'. $cp_del_link. '" style="color:red;" onclick="javascript:check=confirm(\'' . __('WARNING: This will delete the selected project, including ALL task lists and tasks in the project.\n\nChoose [Cancel] to Stop, [OK] to delete.\n', 'collabpress' ) .'\');if(check==false) return false;">'.__( 'Delete', 'collabpress' ). '</a>';
			}

			$dashboardTitle .= '</h2>';

		endif;

		if ($project_desc) : $dashboardTitle .= '<p class="description">'.$project_desc.'</p>'; endif;

	// User
	elseif ( $cp_user_page ) :
		$userdata = get_userdata($cp_user->id);
		$dashboardTitle = '<h2>'.cp_screen_icon('collabpress').$userdata->display_name.'</h2>';

	// Calendar
	elseif ( $cp_calendar_page ) :
		$dashboardTitle = '<h2>'.cp_screen_icon('collabpress').__('Calendar', 'collabpress').'</h2>';

	// View All Projects
	elseif ( $cp_view_projects ) :
		$dashboardTitle = '<h2>'.cp_screen_icon('collabpress').__('View All Projects', 'collabpress').'</h2>';

	// Dashboard
	else :
		$dashboardTitle = '<h2><a title="'.__('CollabPress Dashboard', 'collabpress').'" href="'.CP_DASHBOARD.'">'.cp_screen_icon('collabpress').__('CollabPress Dashboard', 'collabpress').'</a></h2>';
	endif;

	return $dashboardTitle;
}

// Create breadcrumb
function cp_get_breadcrumb() {

	global $cp_dashboard_page;
	global $cp_project_page;
	global $cp_task_list_page;
	global $cp_task_page;
	global $cp_user_page;
	global $cp_calendar_page;
	global $cp_view_projects;

	global $cp_project;
	global $cp_task_list;
	global $cp_task;
	global $cp_user;


	// Task page
	echo '<div id="cp_breadcrumb">';
		echo '<ul>';
		
		if ( $cp_calendar_page || $cp_view_projects ) :
			
			echo '<li class="dash-crumb"><a href="' .CP_DASHBOARD. '">'.__('Dashboard', 'collabpress').'</a></li>';
		
		elseif ( $cp_project_page ) :

			echo '<li class="dash-crumb"><a href="' .CP_DASHBOARD. '">'.__('Dashboard', 'collabpress').'</a></li><li class="proj-crumb"><span>' .get_the_title( $cp_project->id ) .'</span></li>';

		elseif ( $cp_task_list_page ) :
			//load the project ID for this task list
			$cp_project_id = get_post_meta( $cp_task_list->id, '_cp-project-id', true );

			echo '<li class="dash-crumb"><a href="' .CP_DASHBOARD. '">'.__('Dashboard', 'collabpress').'</a></li><li class="proj-crumb"><a href="'.CP_DASHBOARD.'&project='.$cp_project_id.'">' .get_the_title($cp_project_id). '</a></li><li class="list-crumb"><span>' .get_the_title($cp_task_list->id).'</span></li>';

		elseif ( $cp_task_page ) :
			//load the project ID for this task list
			$cp_project_id = get_post_meta( $cp_task->id, '_cp-project-id', true );

			//load the task list ID for this task
			$cp_task_list_id = get_post_meta( $cp_task->id, '_cp-task-list-id', true );

			echo '<li class="dash-crumb"><a href="' .CP_DASHBOARD. '">'.__('Dashboard', 'collabpress').'</a></li><li class="proj-crumb"><a href="'.CP_DASHBOARD.'&project='.$cp_project_id.'">' .get_the_title($cp_project_id). '</a></li><li class="list-crumb"><a href="'.CP_DASHBOARD.'&project='.$cp_project_id.'&task-list='.$cp_task_list_id.'">' .get_the_title( $cp_task_list_id ). '</a></li><li class="task-crumb"><span>' .cp_limit_length( get_the_title( $cp_task->id ), 50 ).'</span></li>';

		else :

			echo '<li class="dash-crumb"><span>'.__('Dashboard', 'collabpress').'</span></li>';

		endif;
		
		echo '</ul>';
	echo '</div>';
}

// Send Emails from CollabPress
function cp_send_email( $to, $subject, $message ) {

    // Load plugin options
    $cp_options = get_option( 'cp_options' );

    // Check if email notifications are enabled - default to enabled
    $cp_email_notify = ( $cp_options['email_notifications'] == 'disabled' ) ? false : true;

    // If email notifications are enabled proceed
    if ( $cp_email_notify ) {

	// Set email variables
	$cp_email = ( is_email( $to ) ) ? $to : null;
	$cp_subject = ( isset( $subject ) ) ? $subject : '';
	$cp_footer = __('Powered by ', 'collabpress').'<a href="http://collabpress.org">'.__( 'CollabPress.org', 'collabpress' ).'</a>.';
	$cp_message = $message . "\n\n" .$cp_footer;

	// Send Away
	wp_mail( $cp_email, $cp_subject, $cp_message );
    }
}

// User Notice
function cp_user_notice($data) {

	// Project Added
	if ( isset( $data['cp-add-project'] ) )
		echo '<div class="updated fade"><p><strong>'.__('Project Added', 'collabpress').'</strong></p></div>';

	// Project Updated
	if ( isset( $data['cp-edit-project'] ) )
		echo '<div class="updated fade"><p><strong>'.__('Project Updated', 'collabpress').'</strong></p></div>';

	// Project Deleted
	if ( isset( $data['cp-delete-project-id'] ) )
		echo '<div class="error fade"><p><strong>'.__('Project Deleted', 'collabpress').'</strong></p></div>';

	// Task List Added
	if ( isset( $data['cp-add-task-list'] ) )
		echo '<div class="updated fade"><p><strong>'.__('Task List Added', 'collabpress').'</strong></p></div>';

	// Task List Added
	if ( isset( $data['cp-edit-task-list'] ) )
		echo '<div class="updated fade"><p><strong>'.__('Task List Updated', 'collabpress').'</strong></p></div>';

	// Task List Deleted
	if ( isset( $data['cp-delete-task-list-id'] ) )
		echo '<div class="error fade"><p><strong>'.__('Task List Deleted', 'collabpress').'</strong></p></div>';

	// Task Added
	if ( isset( $data['cp-add-task'] ) )
		echo '<div class="updated fade"><p><strong>'.__('Task Added', 'collabpress').'</strong></p></div>';

	// Task Updated
	if ( isset( $data['cp-edit-task-id'] ) )
		echo '<div class="updated fade"><p><strong>'.__('Task Updated', 'collabpress').'</strong></p></div>';

	// Task Deleted
	if ( isset( $data['cp-delete-task-id'] ) )
		echo '<div class="error fade"><p><strong>'.__('Task Deleted', 'collabpress').'</strong></p></div>';

	// Comment Added
	if ( isset( $data['cp-add-comment'] ) )
		echo '<div class="updated fade"><p><strong>'.__('Comment Added', 'collabpress').'</strong></p></div>';

	// Activity log cleared
	if ( isset( $data['cp_clear_activity'] ) )
		echo '<div class="updated fade"><p><strong>' .__( 'Acitivity Log Has Been Cleared', 'collabpress' ) .'</strong></p></div>';

}

// Add Activity
function cp_add_activity( $action = NULL, $type = NULL, $author = NULL, $ID = NULL ) {
	$add_activity = array(
						'post_title' => __( 'Activity', 'collabpress' ),
						'post_status' => 'publish',
						'post_type' => 'cp-meta-data'
						);
	$activity_id = wp_insert_post( $add_activity );
	update_post_meta( $activity_id, '_cp-meta-type', 'activity');

	// Action
	if ( $action )
		update_post_meta( $activity_id, '_cp-activity-action', $action );
	// Type
	if ( $type )
		update_post_meta( $activity_id, '_cp-activity-type', $type );
	// Author
	if ( $author )
		update_post_meta( $activity_id, '_cp-activity-author', $author );
	// ID
	if ( $ID )
		update_post_meta( $activity_id, '_cp-activity-ID', $ID );

	do_action( 'cp_add_activity', $action, $type, $author, $ID, $activity_id );
}

// Display Calendar Link
function cp_calendar() {
	echo '<p><a title="'.__('View Calendar', 'collabpress').'" href="'.CP_DASHBOARD.'&calendar=1">'.__('View Calendar', 'collabpress').'</a></p>';
}

// List CollabPress Projects
function cp_projects() {

	// Get Current User
	global $current_user;
	get_currentuserinfo();

	// Get Projects
	$projects_args = array( 'post_type' => 'cp-projects', 'showposts' => '-1' );
	$projects_query = new WP_Query( $projects_args );

	// WP_Query();
	if ( $projects_query->have_posts() ) :
	    while( $projects_query->have_posts() ) : $projects_query->the_post();

		//verify logged in user has access to this project
		if ( cp_check_project_permissions( $current_user->ID, get_the_ID() ) ) {

		    //generate delete project link
		    $cp_del_link = CP_DASHBOARD .'&cp-delete-project-id='.get_the_ID();
		    $cp_del_link = ( function_exists('wp_nonce_url') ) ? wp_nonce_url( $cp_del_link, 'cp-action-delete_project' ) : $cp_del_link;

		    //generate edit project link
		    $cp_edit_link = CP_DASHBOARD.'&project='.get_the_ID().'&view=edit';

		    echo '<p><a href="'.CP_DASHBOARD.'&project='.get_the_ID().'">'.get_the_title().'</a>';

		    //check if user can view edit/delete links
		    if ( cp_check_permissions( 'settings_user_role' ) ) {
			echo ' - <a href="' .$cp_edit_link. '">' .__( 'edit', 'collabpress'). '</a> &middot; <a href="' .$cp_del_link. '" style="color:red;" onclick="javascript:check=confirm(\'' . __('WARNING: This will delete the selected project, including ALL task lists and tasks in the project.\n\nChoose [Cancel] to Stop, [OK] to delete.\n', 'collabpress' ) .'\');if(check==false) return false;">'.__('delete', 'collabpress').'</a></p>';
		    }

		}

	    endwhile;
	    wp_reset_query();

	// No Results
	else :
		echo '<p>'.__( 'No Projects...', 'collabpress' ).'</p>';
	endif;

	echo '<p><a class="button" title="'.__('View All Projects', 'collabpress').'" href="'.CP_DASHBOARD.'&view-projects=1">'.__('View All Projects', 'collabpress').'</a></p>';

}

// List CollabPress Users
function cp_users( $limit='yes' ) {

	//using a custom query for now
	//will update with WP_User_Query when WP 3.1 is released
	//http://wpdevel.wordpress.com/2010/10/07/wp_user_search-has-been-replaced-by-wp_u/
	$users = get_users();
	$cp_users_count = count( $users );

	//load num users setting
	$options = get_option( 'cp_options' );
	$cp_num_users_display = ( isset( $options['num_users_display'] ) ) ? $options['num_users_display'] : '10';

	$avatarCount = 1;
	foreach ($users as $user) {
		$userdata = get_userdata( $user->ID );

		if ( ($avatarCount % 4) == 0 ) {
			$last = 'class="last"';
		} else {
			$last = '';
		}

		echo '<a '.$last.' title="'.$userdata->display_name.'" href="'.CP_DASHBOARD.'&user='.$userdata->ID.'">'.get_avatar($userdata->ID, '64').'</a>';
		$avatarCount++;

		//display users based on setting value
		if ( $avatarCount > $cp_num_users_display && $limit == 'yes' ) break;

	}

	// Get Current User
	global $current_user;
	get_currentuserinfo();

	if ( $cp_users_count > $cp_num_users_display ) :
	    echo '<div style="clear:both;"></div>';
	    echo '<p><a title="' .__( 'View All Users', 'collabpress' ) .'" href="' .CP_DASHBOARD .'&allusers=1">' .__( 'View All Users', 'collabpress' ) .'</a></p>';
	endif;

}

// Show Overview
function cp_overview() {

	echo '<div class="cp-overview">';

	// Project Count
	$projectCount = wp_count_posts('cp-projects');
	$projectCount = $projectCount->publish;
	echo '<p><span class="overview-count">'.$projectCount.'</span> '.__('Project', 'collabpress').(($projectCount == 1) ? '' : 's').'</p>';

	// Task Lists Count
	$taskListCount = wp_count_posts('cp-task-lists');
	$taskListCount = $taskListCount->publish;
	echo '<p><span class="overview-count">'.$taskListCount.'</span> '.__('Task List', 'collabpress').(($taskListCount == 1) ? '' : 's').'</p>';

	// Tasks Count
	$taskCount = wp_count_posts('cp-tasks');
	$taskCount = $taskCount->publish;
	echo '<p><span class="overview-count">'.$taskCount.'</span> '.__('Task', 'collabpress').(($taskCount == 1) ? '' : 's').'</p>';

	// User Count
	$result = count_users();
	echo '<p><span class="overview-count">' .$result['total_users'] .'</span> '.__('User', 'collabpress') .( ( $result['total_users'] == 1 ) ? '' : 's' ) .'</p>';

	echo '</div>';
}

// Thumnnail
function cp_files($id = NULL) {
	?>
	<form id="upload_image" name="upload_image">
	<input class="cp-featured-id" type="hidden" value="<?php echo $id; ?>" />
	<input id="upload_image_button" type="button" value="<?php _e('Click to Upload', 'collabpress'); ?>" />
	<?php

	echo '<div id="collabpress-uploaded-files">';

		$args = array(
			'post_type' => 'attachment',
			'numberposts' => null,
			'post_status' => null,
			'post_parent' => $id
		);
		$attachments = get_posts($args);
		if ($attachments) {
			echo '<ul>';
			foreach ($attachments as $attachment) {
				if ( strpos($attachment->post_mime_type, 'image') !== false ) {
					$attachment_class = 'class="cp_grouped_elements" rel="group-'.$id.'"';
				} else {
					$attachment_class = '';
				}
				echo '<li><p><a '.$attachment_class.' href="'.wp_get_attachment_url($attachment->ID).'" title="'.$attachment->post_title.'">'.$attachment->post_title.'</a> - '.$attachment->post_mime_type.'</p></li>';
			}
			echo '</ul>';
		} else {
			echo '<p>'.__('No file attachments...', 'collabpress') . '</p>';
		}

	echo '</div>';

	echo '</form>';
}

// Show Recent Activity
function cp_recent_activity($data = NULL) {

	// Get Current User
	global $current_user;
	get_currentuserinfo();

	// Get Activities
	$paged = (isset($_GET['paged'])) ? esc_html($_GET['paged']) : 1;

	// Load plugin options
	$cp_options = get_option( 'cp_options' );

	// Check number of recent items to display
	$cp_num_recent = ( isset( $cp_options['num_recent_activity'] ) ) ? absint( $cp_options['num_recent_activity'] ) : 4;

	$activities_args = array( 'post_type' => 'cp-meta-data', 'showposts' => $cp_num_recent, 'paged' => $paged );
	$activities_query = new WP_Query( $activities_args );

	echo '<div class="cp-activity-list">';

	// WP_Query();
	if ( $activities_query->have_posts() ) :
	$activityCount = 1;
	while( $activities_query->have_posts() ) : $activities_query->the_post();
		    global $post;

		    if ( ($activityCount % 2) == 0 ) {
			    $row = " even";
		    } else {
			    $row = " odd";
		    }

		    // Avatar
		    $activityUser = get_post_meta($post->ID, '_cp-activity-author', true);
		    $activityUser = get_userdata($activityUser);
		    $activityAction = get_post_meta($post->ID, '_cp-activity-action', true);
		    $activityType = get_post_meta($post->ID, '_cp-activity-type', true);
		    $activityID = get_post_meta($post->ID, '_cp-activity-ID', true);

		    if ( $activityUser ) :
		    ?>

		    <div class="cp-activity-row <?php echo $row ?>">
			    <a class="cp-activity-author" title="<?php $activityUser->display_name ?>" href="<?php echo CP_DASHBOARD; ?>&user=<?php echo $activityUser->ID ?>"><?php echo get_avatar($activityUser->ID, 32) ?></a>
			    <div class="cp-activity-wrap">
			    <p class="cp-activity-description"><?php echo $activityUser->display_name . ' ' . $activityAction . ' ' . __('a', 'collabpress') . ' '. $activityType ?>: <a href="<?php echo cp_get_url( $activityID, $activityType ); ?>"><?php echo get_the_title( $activityID ); ?></a></p>
			    </div>
		    </div>

		    <?php
		    endif;
		    $activityCount++;
	endwhile;
	wp_reset_query();
	else :
		echo '<p>'.__( 'No Activities...', 'collabpress' ).'</p>';
	endif;

	// Pagination
	if ( $activities_query->max_num_pages > 1 ) {
		echo '<p class="cp_pagination">';
	    for ( $i = 1; $i <= $activities_query->max_num_pages; $i++ ) {
	        echo '<a href="'.CP_DASHBOARD.'&paged='.$i.'" '.(($paged == $i) ? 'class="active"' : '' ).'>'.$i.'</a> ';
	    }
	    echo '</p>';
	} ?>

	<style type="text/css">
		.cp-activity-list {
		    position: relative;
		}
		.cp-activity-row {
		    margin: 0;
		    overflow: hidden;
		    padding: 2px 10px;
		}
		.cp-activity-list .even {
		    background-color: #FFFFE0;
		}
		.cp-activity-list .cp-activity-author {
		    float: left;
		    margin: 5px 0;
		}
		.cp-activity-list .cp-activity-wrap {
		    margin: 6px 0;
		    overflow: hidden;
		    word-wrap: break-word;
		}
		.cp-activity-list p {
		    font-size: 11px;
		    margin: 6px 6px 8px;
		}
	</style>

	<?php echo '</div>';
}

// Add Task
function cp_add_task($data = NULL) {

    global $cp_project;
    global $cp_task_list;

    // Get Project
    $task_list_args = array ( 'post_type' => 'cp-task-lists',
					    'showposts' => 1
				    );
    $task_list_query = new WP_Query( $task_list_args );

    //check if email option is enabled
    $options = get_option('cp_options');
    $checked = ( $options['email_notifications'] == 'enabled' ) ? 'checked="checked"' : null;

    // WP_Query();
    while( $task_list_query->have_posts() ) : $task_list_query->the_post();

	//CHECK IF USER CAN ADD TASKS CAPABILITY
	if ( current_user_can( 'cp_add_task' ) ) {

		echo '<form action="'.cp_clean_querystring().'" method="post">';
			wp_nonce_field('cp-add-task');
			?>
			<h2>Add Task</h2>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e('Description: ', 'collabpress') ?></th>
						<td><fieldset><legend class="screen-reader-text"><span></span></legend>
							<p><label for="cp-task"></label></p>
							<p>
								<textarea class="large-text code" id="cp-task" cols="30" rows="10" name="cp-task"></textarea>
							</p>
						</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="cp-task-due"><?php _e('Due: ', 'collabpress') ?></label></th>
						<td><p><input name="cp-task-due" id="datepicker" class="regular-text" type="text" value=<?php echo date('n/j/Y') ?> /></p></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="cp-task-assign"><?php _e('Assigned to: ', 'collabpress') ?></label></th>
						<td>
							<p>
                                <?php
								$wp_user_search = new WP_User_Query( array( 'fields' => 'all' ) );
								$wp_users = $wp_user_search->get_results();

								$user_list = '<select name="cp-task-assign" id="cp-task-assign">';

								foreach ( $wp_users as $wp_user ) {
									//verify user has access to this project
									if ( cp_check_project_permissions ( $wp_user->ID, $cp_project->id ) ) {
										$user_list .= '<option value="' . $wp_user->ID . '">' . $wp_user->user_login . '</option>';
									}
								}
								$user_list .= '</select>';

                                				$user_list = apply_filters( 'cp_task_user_list_html', $user_list, false );

                                				echo $user_list
                                ?>
							</p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="cp-task-priority"><?php _e('Priority: ', 'collabpress') ?></label></th>
						<td>
							<select name="cp-task-priority" id="cp-task-priority">
								<option value="Urgent">Urgent</option>
								<option value="High">High</option>
								<option value="Normal">Normal</option>
								<option value="Low">Low</option>
								<option value="Very Low">Very Low</option>
								<option value="None" selected="selected">None</option>
							</select>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="cp-task-due"><?php _e('Notify via Email? ', 'collabpress') ?></label></th>
						<td align="left"><p><input name="notify" id="notify" type="checkbox" <?php echo $checked; ?> /></p></td>
					</tr>
				</tbody>
			</table>
			<?php
			echo '<p class="submit"><input class="button-primary" type="submit" name="cp-add-task" value="'.__( 'Submit', 'collabpress' ).'"/></p>';

		echo '</form>';

	}

    endwhile;
	wp_reset_query();
}

// Edit Task
function cp_edit_task() {

	global $cp_task, $cp_project;

	//CHECK IF USER CAN EDIT TASKS CAPABILITY
	if ( current_user_can( 'cp_edit_task' ) ) {

		echo '<form action="" method="post">';
			wp_nonce_field('cp-edit-task');
			?>
			<input type="hidden" name="cp-edit-task-id" value="<?php echo $cp_task->id ?>" />
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e('Description: ', 'collabpress') ?></th>
						<td><fieldset><legend class="screen-reader-text"><span></span></legend>
							<p><label for="cp-task"></label></p>
							<p>
								<textarea class="large-text code" id="cp-task" cols="30" rows="10" name="cp-task"><?php echo get_the_title($cp_task->id) ?></textarea>
							</p>
						</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="cp-task-due"><?php _e('Due: ', 'collabpress') ?></label></th>
						<td><p><input name="cp-task-due" id="datepicker" class="regular-text" type="text" value="<?php echo get_post_meta($cp_task->id, '_cp-task-due', true) ?>"/></p></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="cp-task-assign"><?php _e('Assigned to: ', 'collabpress') ?></label></th>
						<td>
							<p>
								<?php
								$selected = get_post_meta( $cp_task->id, '_cp-task-assign', true );

								$wp_user_search = new WP_User_Query( array( 'fields' => 'all' ) );
								$wp_users = $wp_user_search->get_results();

								$users = '<select name="cp-task-assign" id="cp-task-assign">';
								foreach ( $wp_users as $wp_user ) {
									//verify user has access to this project
									if ( cp_check_project_permissions ( $wp_user->ID, $cp_project->id ) ) {
										$users .= '<option value="' . $wp_user->ID . '" ' . selected( $wp_user->ID, $selected, false ) . '>' . $wp_user->user_login . '</option>';
									}
								}
								$users .= '</select>';

								$users = apply_filters( 'cp_task_user_list_html', $users, $selected );

								echo $users;
								?>
							</p>
						</td>
					</tr>
						<tr valign="top">
							<th scope="row"><label for="cp-task-priority"><?php _e('Priority: ', 'collabpress') ?></label></th>
							<td>
								<?php
								$task_priority = get_post_meta( $cp_task->id, '_cp-task-priority', true );
								?>
								<select name="cp-task-priority" id="cp-task-priority">
									<option value="Urgent" <?php selected( $task_priority, 'Urgent' ); ?>>Urgent</option>
									<option value="High" <?php selected( $task_priority, 'High' ); ?>>High</option>
									<option value="Normal" <?php selected( $task_priority, 'Normal' ); ?>>Normal</option>
									<option value="Low" <?php selected( $task_priority, 'Low' ); ?>>Low</option>
									<option value="Very Low" <?php selected( $task_priority, 'Very Low' ); ?>>Very Low</option>
									<option value="None" <?php selected( $task_priority, 'None' ); ?>>None</option>
								</select>
							</td>
						</tr>
				</tbody>
			</table>
			<?php
			echo '<p class="submit"><input class="button-primary" type="submit" name="cp-edit-task" value="'.__( 'Submit', 'collabpress' ).'"/></p>';

		echo '</form>';

	}

}

// Task
function cp_task() {

	    global $cp_project;
	    global $cp_task_list;
	    global $post;

	    //get open tasks
	    $tasks_query = cp_get_tasks( $cp_task_list->id, 'open' );

	    echo '<h4>' . __( 'Current Tasks', 'collabpress' ) . '</h4>';

	    if ($tasks_query) :
	    foreach ($tasks_query as $post):
		setup_postdata($post);

			$user = get_post_meta( get_the_ID(), '_cp-task-assign', true);
			$user = get_userdata($user);

			//get task due date
			$task_due_date = get_post_meta( get_the_ID(), '_cp-task-due', true );

			//get task priority
			$task_priority = get_post_meta( get_the_ID(), '_cp-task-priority', true );

			//get comment count
			$num_comments = get_comments_number();

			//get user ID assigned the task
			$task_user_id = get_post_meta( get_the_ID(), '_cp-task-assign', true );

			//generate complete task link
			$link = CP_DASHBOARD .'&project=' .$cp_project->id .'&task-list=' .$cp_task_list->id .'&cp-complete-task-id=' .get_the_ID();
			$link = ( function_exists( 'wp_nonce_url' ) ) ? wp_nonce_url( $link, 'cp-complete-task' ) : $link;

			//generate delete task link
			$cp_del_link = CP_DASHBOARD .'&project='.$cp_project->id.'&task-list=' .$cp_task_list->id .'&cp-delete-task-id='.get_the_ID();
			$cp_del_link = ( function_exists('wp_nonce_url') ) ? wp_nonce_url( $cp_del_link, 'cp-action-delete_task' ) : $cp_del_link;

			//generate edit task link
			$cp_edit_link = add_query_arg( 'view', 'edit', apply_filters( 'cp_task_link', CP_DASHBOARD . '&project=' . $cp_project->id . '&task=' . get_the_ID(), get_the_ID(), $cp_project->id ) );

			//check task status
			$task_status = get_post_meta( get_the_ID(), '_cp-task-status', true );

			echo '<div class="cp_task_summary">';
			echo '<div id="cp-gravatar">' .get_avatar( $task_user_id, 32 ). '</div><p><input type="checkbox" name="" value="0" onclick="window.location=\''. $link. '\'; return true;"  /> ';

			echo '<a href="' . apply_filters( 'cp_task_link', CP_DASHBOARD . '&project=' . $cp_project->id . '&task=' . get_the_ID(), get_the_ID(), $cp_project->id ) .'">' .cp_limit_length( get_the_title(), 125 ).'</a> - '.__('Due: ', 'collabpress') .$task_due_date;

			//check if user can view edit/delete links
			if ( cp_check_permissions( 'settings_user_role' ) ) {
			    echo '  <a href="'.$cp_edit_link.'">' .__('edit', 'collabpress'). '</a> &middot; <a href="'. $cp_del_link .'" style="color:red;" onclick="javascript:check=confirm(\'' . __('WARNING: This will delete the selected task.\n\nChoose [Cancel] to Stop, [OK] to delete.\n', 'collabpress' ) .'\');if(check==false) return false;">'.__( 'delete', 'collabpress' ). '</a>';
			}

			echo '</p>';

			echo '<p>Priority: ' .esc_html( $task_priority );

			echo ' - ' .$num_comments. ' comments';
			echo '</p>';
			echo '</div>';

		endforeach;

	    else :
		    echo '<p>'.__( 'No Tasks...', 'collabpress' ).'</p>';
	    endif;

	    echo '<div style="clear:both;"></div>';

	    //get completed tasks
	    $tasks_query = cp_get_tasks( $cp_task_list->id, 'complete' );

	    echo '<h4>' . __( 'Completed Tasks', 'collabpress' ) . '</h4>';

	    if ($tasks_query) :
	    foreach ($tasks_query as $post):
		setup_postdata($post);

			$user = get_post_meta(get_the_ID(), '_cp-task-assign', true);
			$user = get_userdata($user);

			//get due date
			$task_due_date = get_post_meta( get_the_ID(), '_cp-task-due', true );

			//get comment count
			$num_comments = get_comments_number();

			//get user ID assigned the task
			$task_user_id = get_post_meta( get_the_ID(), '_cp-task-assign', true );

			//generate complete task link
			$link = CP_DASHBOARD .'&project=' .$cp_project->id .'&task-list=' .$cp_task_list->id .'&cp-complete-task-id=' .get_the_ID();
			$link = ( function_exists( 'wp_nonce_url' ) ) ? wp_nonce_url( $link, 'cp-complete-task' ) : $link;

			//check task status
			$task_status = get_post_meta( get_the_ID(), '_cp-task-status', true );

			echo '<div class="cp_task_summary">';
			echo '<div id="cp-gravatar">' .get_avatar( $task_user_id, 32 ). '</div><p><input type="checkbox" name="" value="0" onclick="window.location=\''. $link. '\'; return true;"  /> ';

			if ( $task_status == 'complete') {
			    echo '<span style="text-decoration:line-through">';
			}

			echo '<a href="'.CP_DASHBOARD.'&project='.$cp_project->id.'&task='.get_the_ID().'">'.get_the_title().'</a> - '.__('Due: ', 'collabpress') .$task_due_date;

			if ( $task_status == 'complete') {
			    echo '</span>';
			}

			echo '</p>';

			echo $num_comments. ' comments';
			echo '</div>';

		endforeach;

	    else :
		    echo '<p>'.__( 'No Tasks Completed...', 'collabpress' ).'</p>';
	    endif;

	    echo '<div style="clear:both;"></div>';
}

// Add Task list
function cp_add_task_list() {

	global $cp_project;

	// Get Project
	$project_args = array (
		'post_type' => 'cp-projects',
		'posts_per_page' => 1
		);
	$project_query = new WP_Query( $project_args );

	// WP_Query();
    while( $project_query->have_posts() ) : $project_query->the_post();

	//CHECK IF USER CAN ADD TASK LISTS CAPABILITY
	if ( current_user_can( 'cp_add_task_lists' ) ) {

		echo '<form action="'.cp_clean_querystring().'" method="post">';
			wp_nonce_field('cp-add-task-list');
			?>
			<h2><?php _e( 'Add Task List', 'collabpress' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label for="cp-task-list"><?php _e('Name: ', 'collabpress') ?></label></th>
						<td><p><input type="text" class="regular-text" value="" id="blogname" name="cp-task-list" /></p></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Description: ', 'collabpress') ?></th>
						<td><fieldset><legend class="screen-reader-text"><span></span></legend>
							<p><label for="cp-task-list-description"></label></p>
							<p>
								<textarea class="large-text code" id="cp-task-list-description" cols="30" rows="10" name="cp-task-list-description"></textarea>
							</p>
						</fieldset></td>
					</tr>
					<?php do_action( 'cp_add_task_list_extra_fields' ); ?>
				</tbody>
			</table>
			<?php
			echo '<p class="submit"><input class="button-primary" type="submit" name="cp-add-task-list" value="'.__( 'Submit', 'collabpress' ).'"/></p>';

		echo '</form>';

	}

    endwhile;
	wp_reset_query();
}

// Edit Task List
function cp_edit_task_list() {

	global $cp_task_list;

	//CHECK IF USER CAN EDIT TASK LISTS CAPABILITY
	if ( current_user_can( 'cp_edit_task_lists' ) ) {

		echo '<form action="" method="post">';
			wp_nonce_field('cp-edit-task-list');
			?>
			<input type="hidden" name="cp-edit-task-list-id" value="<?php echo $cp_task_list->id ?>" />
			<h2><?php _e( 'Edit Task List', 'collabpress' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label for="cp-task-list"><?php _e('Name: ', 'collabpress') ?></label></th>
						<td><p><input type="text" class="regular-text" value="<?php echo get_the_title($cp_task_list->id) ?>" id="blogname" name="cp-task-list" /></p></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Description: ', 'collabpress') ?></th>
						<td><fieldset><legend class="screen-reader-text"><span></span></legend>
							<p><label for="cp-task-list-description"></label></p>
							<p>
								<textarea class="large-text code" id="cp-task-list-description" cols="30" rows="10" name="cp-task-list-description"><?php echo get_post_meta($cp_task_list->id, '_cp-task-list-description', true) ?></textarea>
							</p>
						</fieldset></td>
					</tr>
					<?php do_action( 'cp_edit_task_list_extra_fields', $cp_task_list->id ); ?>
				</tbody>
			</table>
			<?php
			echo '<p class="submit"><input class="button-primary" type="submit" name="cp-edit-task-list" value="'.__( 'Submit', 'collabpress' ).'"/></p>';

		echo '</form>';

	}

}

// Task List
function cp_task_list() {

	global $cp_project;

	// Get Task Lists
	$task_lists_args = array(
						'post_type' => 'cp-task-lists',
						'meta_key' => '_cp-project-id',
						'meta_value' => $cp_project->id,
						'showposts' => '-1'
						);
	$task_lists_query = new WP_Query( $task_lists_args );

	echo '<p>' . __('Tasks Lists', 'collabpress') . '</p>';

    // WP_Query();
    if ( $task_lists_query->have_posts() ) :
    while( $task_lists_query->have_posts() ) : $task_lists_query->the_post();

	//generate delete task list link
	$cp_del_link = CP_DASHBOARD .'&project='.$cp_project->id.'&cp-delete-task-list-id='.get_the_ID();
	$cp_del_link = ( function_exists('wp_nonce_url') ) ? wp_nonce_url( $cp_del_link, 'cp-action-delete_task_list' ) : $cp_del_link;

	//generate edit task list link
	$cp_edit_link = add_query_arg( 'view', 'edit', apply_filters( 'cp_task_list_link', CP_DASHBOARD . '&project=' . $cp_project->id . '&task-list=' . get_the_ID(), get_the_ID(), $cp_project->id ) );

	echo '<p><a href="'. apply_filters( 'cp_task_list_link', CP_DASHBOARD . '&project=' . $cp_project->id . '&task-list=' . get_the_ID(), get_the_ID(), $cp_project->id ) . '">'.get_the_title().'</a>';

	//check if user can view edit/delete links
	if ( cp_check_permissions( 'settings_user_role' ) ) {
	    echo '  - <a href="' .$cp_edit_link. '">edit</a> &middot; <a href="' .$cp_del_link. '" style="color:red;" onclick="javascript:check=confirm(\'' . __('WARNING: This will delete the selected task list and all tasks in the list.\n\nChoose [Cancel] to Stop, [OK] to delete.\n', 'collabpress' ) .'\');if(check==false) return false;">'.__('delete', 'collabpress').'</a></p>';
	}

    endwhile;
    wp_reset_query();
    else :
	    echo '<p class="description">'.__( 'No Task Lists...', 'collabpress' ).'</p>';
    endif;
}

// Add Project
function cp_add_project() {

	// Get Current User
	global $current_user;
	get_currentuserinfo();

	//CHECK IF USER CAN ADD PROJECTS CAPABILITY
	if ( current_user_can( 'cp_add_projects' ) ) {

		// Add Project Form
		echo '<form action="'.cp_clean_querystring().'" method="post" name="new_project_form">';
			wp_nonce_field('cp-add-project');
			?>
			<h2><?php _e( 'Add Project', 'collabpress' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label for="cp-project"><?php _e( 'Name: ', 'collabpress' ) ?></label></th>
						<td><p><input type="text" class="regular-text" value="" id="blogname" name="cp-project" /></p></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Description: ', 'collabpress' ) ?></th>
						<td><fieldset><legend class="screen-reader-text"><span></span></legend>
							<p><label for="cp-project-description"></label></p>
							<p>
							<textarea class="large-text code" id="cp-project-description" cols="30" rows="10" name="cp-project-description"></textarea>
							</p>
						</fieldset></td>
					</tr>

					<?php if ( !function_exists( 'bp_is_active' ) || !bp_is_active( 'groups' ) || !bp_is_group() ) : ?>
					<tr valign="top">
						<th scope="row"><label for="cp-project-users"><?php _e( 'Users: ', 'collabpress' ) ?></label></th>
						<td>
						<p>
							<input type="button" name="CheckAll" value="<?php _e( 'Check All', 'collabpress' ); ?>" onClick="checkAll(document.new_project_form['cp_project_users[]'])" />
							<input type="button" name="UnCheckAll" value="<?php _e( 'Uncheck All', 'collabpress' ); ?>" onClick="uncheckAll(document.new_project_form['cp_project_users[]'])" />
						</p>
						<?php
						//check if user is subscriber
						if ( !current_user_can( 'manage_options' ) ) {
							//if not admin, assign project to logged in user
							echo '<input type="checkbox" name="cp_project_users[]" value="'.$current_user->ID .'" checked="checked" />&nbsp;' .$current_user->user_login .'<br />';
						}else{
							// @todo This fails on huge userbases
							$wp_user_search = new WP_User_Query( array( 'fields' => 'all' ) );
							$wp_users = $wp_user_search->get_results();

							foreach ( $wp_users as $wp_user ) {
								echo '<input type="checkbox" name="cp_project_users[]" value="'.$wp_user->ID .'" checked="checked" />&nbsp;' .$wp_user->user_login .'<br />';
							}
						}
						?>
						</td>
					</tr>
					<?php endif ?>
				</tbody>
			</table>
			<?php
			echo '<p class="submit"><input class="button-primary" type="submit" name="cp-add-project" value="'.__( 'Submit', 'collabpress' ).'"/></p>';

		echo '</form>';

	}
}

// Edit Project
function cp_edit_project() {

	global $cp_project, $bp;

	// Get Current User
	global $current_user;
	get_currentuserinfo();

	$wp_user_search = new WP_User_Query( array( 'fields' => 'all' ) );
	$wp_users = $wp_user_search->get_results();

	//CHECK IF USER CAN EDIT PROJECTS CAPABILITY
	if ( current_user_can( 'cp_edit_projects' ) ) {

		// Add Project Form
		echo '<form action="" method="post" name="edit_project_form">';
			wp_nonce_field('cp-edit-project');
			?>
			<input type="hidden" name="cp-edit-project-id" value="<?php echo $cp_project->id ?>" />
			<h2><?php _e( 'Edit Project', 'collabpress' ); ?></h2>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><label for="cp-project"><?php _e('Name: ', 'collabpress') ?></label></th>
						<td><p><input type="text" class="regular-text" value="<?php echo get_the_title($cp_project->id) ?>" id="blogname" name="cp-project" /></p></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Description: ', 'collabpress') ?></th>
						<td><fieldset><legend class="screen-reader-text"><span></span></legend>
							<p><label for="cp-project-description"></label></p>
							<p>
								<textarea class="large-text code" id="cp-project-description" cols="30" rows="10" name="cp-project-description"><?php echo get_post_meta($cp_project->id, '_cp-project-description', true) ?></textarea>
							</p>
						</fieldset></td>
					</tr>

					<?php /* Don't show this on BP group tabs */ ?>
					<?php if ( empty( $bp->groups->current_group ) ) : ?>
					<tr valign="top">
						<th scope="row"><label for="cp-project-users"><?php _e( 'Users: ', 'collabpress' ) ?></label></th>
						<td>
						<p>
							<input type="button" name="CheckAll" value="<?php _e( 'Check All', 'collabpress' ); ?>" onClick="checkAll(document.edit_project_form['cp_project_users[]'])" />
							<input type="button" name="UnCheckAll" value="<?php _e( 'Uncheck All', 'collabpress' ); ?>" onClick="uncheckAll(document.edit_project_form['cp_project_users[]'])" />
						</p>
						<?php
						//get existing project users
						$cp_project_users = get_post_meta( $cp_project->id, '_cp-project-users', true );

						foreach ( $wp_users as $wp_user ) {
							if ( is_array( $cp_project_users ) ) {
							$checked = ( in_array( $wp_user->ID, $cp_project_users ) ) ? 'checked="checked"' : '';
							}else{
							$checked='';
							}
							if ( !current_user_can( 'manage_options' ) ) {
								//if not admin, assign project to logged in user
								echo '<input type="checkbox" name="cp_project_users[]" value="'.$current_user->ID .'" checked="checked" />&nbsp;' .$current_user->user_login .'<br />';
							}else{
								echo '<input type="checkbox" name="cp_project_users[]" value="'.$wp_user->ID .'" '.$checked.'>&nbsp;' .$wp_user->user_login .'<br />';
							}
						}
						?>
						</td>
					</tr>
					<?php endif ?>
				</tbody>
			</table>
			<?php
			echo '<p class="submit"><input class="button-primary" type="submit" name="cp-edit-project" value="'.__( 'Submit', 'collabpress' ).'"/></p>';

		echo '</form>';

	}
}

// Task Comments
function cp_task_comments() {
	global $cp_task;
	global $cp_project;

	// Get Current User
	global $current_user;
	get_currentuserinfo();

	$comments = get_comments('post_id='.$cp_task->id);

	echo '<div id="cp_task_comments_wrap">';

	if ($comments) :
		$commentCount = 1;
		// Display each comment
		foreach( $comments as $comm ) :

			if ( ($commentCount % 2) == 0 ) {
				$row = " even";
			} else {
				$row = " odd";
			}

		?>
			<div class="cp_task_comment<?php echo $row ?>">
				<a class="avatar" title="<?php echo $comm->comment_author ?>" href="<?php echo CP_DASHBOARD; ?>&user=<?php echo $comm->user_id ?>"><?php echo get_avatar($comm->user_id, 64) ?></a>
				<div class="cp_task_comment_content">
					<p class="cp_comment_author"><a title="<?php echo $comm->comment_author ?>" href="<?php echo CP_DASHBOARD; ?>&user=<?php echo $comm->user_id ?>"><?php echo $comm->comment_author ?></a>
					<?php
					//generate delete comment link
					$cp_del_link = CP_DASHBOARD .'&project='.$cp_project->id.'&task='.$cp_task->id.'&cp-delete-comment-id='.$comm->comment_ID;
					$cp_del_link = ( function_exists('wp_nonce_url') ) ? wp_nonce_url( $cp_del_link, 'cp-action-delete_comment' ) : $cp_del_link;

					if ( $current_user->ID == $comm->user_id || current_user_can( 'manage_options' ) )
						echo ' - <a href="'.$cp_del_link.'" style="color:red;" onclick="javascript:check=confirm(\'' . __('WARNING: This will delete the selected comment.\n\nChoose [Cancel] to Stop, [OK] to delete.\n', 'collabpress' ) .'\');if(check==false) return false;">'.__( 'delete', 'collabpress' ). '</a>';
					?>
					</p>
					<p class="cp_comment_content"><?php echo $comm->comment_content ?></p>
				</div>
			</div>
		<?php
			$commentCount++;
		endforeach;
	// No Comments
	else:
		echo '<div class="cp_task_comment"><p>'.__('No comments...', 'collabpress').'</p></div>';
	endif;

	echo '</div>';

        //check if email option is enabled
	$options = get_option('cp_options');
        $checked = ( $options['email_notifications'] == 'enabled' ) ? 'checked="checked"' : null;

	echo '<form action="'.cp_clean_querystring().'" method="post">';
		wp_nonce_field('cp-add-comment');
		?>
		<p><label for="cp-comment-content"><?php _e('Leave a Comment: ', 'collabpress') ?></label></p>
		<p><textarea class="large-text code" id="cp-comment-content" cols="30" rows="10" name="cp-comment-content"></textarea></p>
		<p><?php _e('Notify via Email?', 'collabpress'); ?> <input type="checkbox" name="notify" <?php echo $checked; ?> /></p>
		<?php
		echo '<p class="submit"><input class="button-primary" type="submit" name="cp-add-comment" value="'.__( 'Submit', 'collabpress' ).'"/></p>';

	echo '</form>';
}

// User Page
function cp_user_page() {
	global $post;
	global $cp_user;
	$userdata = get_userdata($cp_user->id);

	?>

	<div id="cp_user_page_wrap">

		<?php echo get_avatar($userdata->ID, 128) ?>

		<div class="cp_user_page_right">
			<h3><?php _e('Recent Activity', 'collabpress') ?></h3>
			<?php
			// Get Task Lists
		    $tasks_query = get_posts( array(
		    	'post_type' => 'cp-meta-data',
		    	'meta_query' => array(
		    		array( 
		    			'key' => '_cp-meta-type',
		    			'value' => 'activity',
		    		),
		    		array( 
		    			'key' => '_cp-activity-author',
		    			'value' => $cp_user->id,
		    		)	
		    	)
		    ) );

			// WP_Query();
		    if ($tasks_query) :
		    foreach ($tasks_query as $post):
				setup_postdata($post);

				$activityUser = get_post_meta($post->ID, '_cp-activity-author', true);
				$activityUser = get_userdata($activityUser);
				$activityAction = get_post_meta($post->ID, '_cp-activity-action', true);
				$activityType = get_post_meta($post->ID, '_cp-activity-type', true);
				$activityID = get_post_meta($post->ID, '_cp-activity-ID', true);
				?>

					<p><?php echo $activityUser->display_name . ' ' . $activityAction . ' a ' . $activityType ?>: <a href="<?php echo cp_get_url( $activityID, $activityType ); ?>"><?php echo get_the_title( $activityID ); ?></a></p>

				<?php

			endforeach;
		    else :
			    echo '<p>'.__( 'No Recent Activity...', 'collabpress' ).'</p>';
			endif;
			?>
			<h3><?php _e('Current Tasks', 'collabpress') ?></h3>
			<?php
			// Get Task Lists
			$tasks_query = get_posts( array(
				'post_type' => 'cp-tasks',
				'meta_query' => array(
					array( 
						'key' => '_cp-task-assign',
						'value' => $cp_user->id,
					),
					array( 
						'key' => '_cp-task-status',
						'value' => 'open',
					)
				)
			) );

			// WP_Query();
		    if ($tasks_query) :
		    foreach ($tasks_query as $post):
				setup_postdata($post);
				echo '<p><a title="'.get_the_title().'" href="'.cp_get_url($post->ID, 'task').'">'.get_the_title().__(' - View', 'collabpress').'</a></p>';
			endforeach;
		    else :
			    echo '<p>'.__( 'No Tasks...', 'collabpress' ).'</p>';
			endif;
			?>
		</div>

	</div>

	<?php
}


// CollabPress Calendar
function cp_draw_calendar($month = NULL, $year = NULL) {

	echo '<div id="cp-calendar-wrap">';

	if ( !isset($_GET['month']) && !isset($_GET['day']) ) :
		$month = date('n');
		$year = date('Y');
	else :
		$month = absint($_GET['month']);
		$year = absint($_GET['year']);
	endif;

	$monthName= date("F",mktime(0,0,0,$month,1,2000));
	echo '<h3 style="clear:both; text-align: center">'.$monthName.' - '.$year.'</h3>';

	// Previous Link
	if ($month == 1) :
		$previousMonth = 12;
		$previousYear = $year - 1;
	else :
		$previousMonth = $month - 1;
		$previousYear = $year;
	endif;
	$previousmonthName= date("F",mktime(0,0,0,$previousMonth,1,2000)) . ', ' . $previousYear;
	echo '<a title="" class="cp_previous_month" href="'.CP_DASHBOARD.'&calendar=1&month='.$previousMonth.'&year='.$previousYear.'">'.$previousmonthName.'</a>';

	// Next Link
	if ($month == 12) :
		$nextMonth = 1;
		$nextYear = $year + 1;
	else :
		$nextMonth = $month + 1;
		$nextYear = $year;
	endif;
	$nextmonthName= date("F",mktime(0,0,0,$nextMonth,1,2000)) . ', ' . $nextYear;
	echo '<a title="" class="cp_next_month" href="'.CP_DASHBOARD.'&calendar=1&month='.$nextMonth.'&year='.$nextYear.'">'.$nextmonthName.'</a>';

	/* draw table */
	$calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

	/* table headings */
	$headings = array(__('Sunday', 'collabpress'), __('Monday', 'collabpress'), __('Tuesday', 'collabpress'), __('Wednesday', 'collabpress'), __('Thursday', 'collabpress'), __('Friday', 'collabpress'), __('Saturday', 'collabpress'));
	$calendar.= '<tr class="calendar-row" valign="top"><td class="calendar-day-head">'.implode('</td><td class="calendar-day-head">',$headings).'</td></tr>';

	/* days and weeks vars now ... */
	$running_day = date('w',mktime(0,0,0,$month,1,$year));
	$days_in_month = date('t',mktime(0,0,0,$month,1,$year));
	$days_in_this_week = 1;
	$day_counter = 0;
	$dates_array = array();

	/* row for week one */
	$calendar.= '<tr class="calendar-row" valign="top">';

	/* print "blank" days until the first of the current week */
	for($x = 0; $x < $running_day; $x++):
		$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
		$days_in_this_week++;
	endfor;

	/* keep going with days.... */
	for($list_day = 1; $list_day <= $days_in_month; $list_day++):
		$calendar.= '<td class="calendar-day">';
			/* add in the day number */
			$calendar.= '<div class="day-number">'.$list_day.'</div>';
			$formatDate = $month.'/'.$list_day.'/'.$year;

			// Get Task Lists
			$tasks_args = apply_filters( 'cp_calendar_tasks_args', array(
								'post_type' => 'cp-tasks',
								'meta_key' => '_cp-task-due',
								'meta_value' => $formatDate,
								'showposts' => '-1'
								) );
			$tasks_query = new WP_Query( $tasks_args );

			// WP_Query();
			if ( $tasks_query->have_posts() ) :
		    while( $tasks_query->have_posts() ) : $tasks_query->the_post();

				// Project ID
				$projectID = get_post_meta(get_the_ID(), '_cp-project-id', true);
				$task_user_id = get_post_meta(get_the_ID(), '_cp-task-assign', true);
				$task_status = get_post_meta (get_the_ID(), '_cp-task-status', true);

				if ($task_status == 'open') :
					$calendar .= '<p><a href="'.cp_get_url(get_the_ID(), 'task').'">'.get_avatar($task_user_id, 32).' '.get_the_title().'</a></p>';
				endif;

		    endwhile;
			wp_reset_query();
			else :
			endif;

			$calendar.= str_repeat('<p>&nbsp;</p>',2);

		$calendar.= '</td>';
		if($running_day == 6):
			$calendar.= '</tr>';
			if(($day_counter+1) != $days_in_month):
				$calendar.= '<tr class="calendar-row" valign="top">';
			endif;
			$running_day = -1;
			$days_in_this_week = 0;
		endif;
		$days_in_this_week++; $running_day++; $day_counter++;
	endfor;

	/* finish the rest of the days in the week */
	if($days_in_this_week < 8):
		for($x = 1; $x <= (8 - $days_in_this_week); $x++):
			$calendar.= '<td class="calendar-day-np">&nbsp;</td>';
		endfor;
	endif;

	/* final row */
	$calendar.= '</tr>';

	/* end the table */
	$calendar.= '</table>';

	/* all done, return result */
	echo $calendar;

	echo '</div>';
}

// View All Projects
function cp_view_all_projects() {

	// Get Current User
	global $current_user;
	get_currentuserinfo();

	echo '<h4 class="cp-no-margin">'.__('All Projects', 'collabpress').'</h4>';

	// Get Projects
	$projects_args = array( 'post_type' => 'cp-projects', 'showposts' => '-1' );
	$projects_query = new WP_Query( $projects_args );

	// WP_Query();
	if ( $projects_query->have_posts() ) :
	    while( $projects_query->have_posts() ) : $projects_query->the_post();

		//verify user has permission to view this project
		if ( cp_check_project_permissions( $current_user->ID, get_the_ID() ) ) {

		    //generate delete project link
		    $cp_del_link = CP_DASHBOARD .'&cp-delete-project-id='.get_the_ID();
		    $cp_del_link = ( function_exists('wp_nonce_url') ) ? wp_nonce_url( $cp_del_link, 'cp-action-delete_project' ) : $cp_del_link;

		    //generate edit project link
		    $cp_edit_link = CP_DASHBOARD.'&project='.get_the_ID().'&view=edit';

		    echo '<p><a href="'.CP_DASHBOARD.'&project='.get_the_ID().'">'.get_the_title().'</a>';

		    //check if user can view edit/delete links
		    if ( cp_check_permissions( 'settings_user_role' ) ) {
			echo ' - <a href="' .$cp_edit_link. '">' .__( 'edit', 'collabpress'). '</a> &middot; <a href="' .$cp_del_link. '" style="color:red;" onclick="javascript:check=confirm(\'' . __('WARNING: This will delete the selected project, including ALL task lists and tasks in the project.\n\nChoose [Cancel] to Stop, [OK] to delete.\n', 'collabpress' ) .'\');if(check==false) return false;">'.__('delete', 'collabpress').'</a></p>';
		    }

		}

	    endwhile;
	    wp_reset_query();

	// No Results
	else :
		echo '<p>'.__( 'No Projects...', 'collabpress' ).'</p>';
	endif;

}

// Display Icon
function cp_screen_icon($screen = '') {
	global $current_screen, $typenow;

	if ( empty($screen) )
		$screen = $current_screen;
	elseif ( is_string($screen) )
		$name = $screen;

	$class = 'icon32';

	if ( empty($name) ) {
		if ( !empty($screen->parent_base) )
			$name = $screen->parent_base;
		else
			$name = $screen->base;

		if ( 'edit' == $name && isset($screen->post_type) && 'page' == $screen->post_type )
			$name = 'edit-pages';

		$post_type = '';
		if ( isset( $screen->post_type ) )
			$post_type = $screen->post_type;
		elseif ( $current_screen == $screen )
			$post_type = $typenow;
		if ( $post_type )
			$class .= ' ' . sanitize_html_class( 'icon32-posts-' . $post_type );
	}
	return '<span id="icon-'.$name.'" class="'.$class.'"></span>';
}

// Get URL
function cp_get_url( $ID = NULL, $type = NULL ) {
    if ( $type == 'task' || $type == 'comment' ) :
		$cp_project_id = get_post_meta( $ID, '_cp-project-id', true );
		$cp_url = CP_DASHBOARD .'&project=' .$cp_project_id .'&task=' .absint( $ID );
	elseif( $type =="task list" ) :
		$cp_project_id = get_post_meta( $ID, '_cp-project-id', true );
		$cp_url = CP_DASHBOARD .'&project=' .$cp_project_id .'&task-list=' .absint( $ID );
    elseif ( $type == 'project' ) :
		$cp_url = CP_DASHBOARD.'&project=' .absint( $ID );
    endif;

    // Constructs a custom filter for each type. Annoying, but this is how the CP-BP filters work
    $filter_name = 'task list' == $type ? 'cp_task_list_link' : 'cp_' . $type . '_link';

    return apply_filters( $filter_name, $cp_url, $ID );
}

// Retrieve all tasks in a task list with a specific status
function cp_get_tasks( $task_list_id, $status ) {
	$task_list_id = absint( $task_list_id );
	$status = esc_attr( $status );
    if ( $task_list_id && $status ) {
		$tasks = get_posts( array( 
			'post_type' => 'cp-tasks',
			'meta_query' => array( 
				array( 
					'key' => '_cp-task-list-id',
					'value' => $task_list_id,
				),
				array( 
					'key' => '_cp-task-status',
					'value' => $status,
				)
			)
		) );
		return $tasks;
    }
}

// Validate Date
function cp_validate_date( $value = NULL ) {
	return preg_match( '`^\d{1,2}/\d{1,2}/\d{4}$`' , $value );
}

// Check user permissions
function cp_check_permissions( $type = NULL ) {

    //load settings user role
    $options = get_option( 'cp_options' );
    $cp_settings_user_role = ( isset( $options[$type] ) ) ? esc_attr( $options[$type] ) : 'manage_options';

    // Filter so that BP-compatibility (and other plugins) can modify
    $cp_settings_user_role = apply_filters( 'cp_settings_user_role', $cp_settings_user_role, $type );

    if ( $cp_settings_user_role == 'all' ) :

        return true;

    else :

        if ( current_user_can( $cp_settings_user_role ) ) :

            return true;

        endif;

    endif;

    return false;

}

// Clean Querystring
function cp_clean_querystring() {
	$cp_cleaned_querystring = $_SERVER['REQUEST_URI'];
	parse_str($_SERVER['QUERY_STRING'], $cp_querystring);
	foreach ($cp_querystring as $key => $cp_cleaned_query_key) {
		$cp_clean_query_array = array(
			'cp-delete-project-id',
			'cp-delete-task-list-id',
			'cp-delete-task-id',
			'cp-delete-comment-id',
			'_wpnonce'
		);
		if (in_array($key, $cp_clean_query_array))
			$cp_cleaned_querystring = remove_query_arg($key, $cp_cleaned_querystring);
	}
	return $cp_cleaned_querystring;
}

//verify user has access to view a project
function cp_check_project_permissions( $user_id=1, $project_id=1 ) {

    $cp_project_users = get_post_meta( $project_id, '_cp-project-users', true );
    $has_access = false;

    if ( is_array( $cp_project_users ) ) {
	if ( in_array( $user_id, $cp_project_users ) ) {
	    $has_access = true;
	}
    }else{
	//old projects don't have users set so allow access
	$has_access = true;
    }

    return apply_filters( 'cp_check_project_permissions', $has_access, $user_id, $project_id, $cp_project_users );
}

/**
 * Utility function for calling up CP's options array, providing any necessary defaults
 *
 * @package CollabPress
 * @since 1.2
 *
 * @return array $options
 */
function cp_get_options() {
	$options = get_option('cp_options');

	$saved_options = $options;

	// BP settings
	if ( function_exists( 'bp_is_active' ) ) {
		// Set some defaults if necessary
		if ( empty( $options['bp'] ) ) {
			$options['bp'] = array();
		}

		// Default group settings
		if ( bp_is_active( 'groups' ) ) {
			$groups_defaults = array(
				'groups_enabled' 		=> 'enabled',
				'groups_admins_can_disable' 	=> 'allow',
				'groups_admins_can_customize' 	=> 'allow',
				'groups_default_tab_name' 	=> __( 'Projects', 'collabpress' ),
				'groups_default_tab_slug' 	=> sanitize_title( __( 'Projects', 'collabpress' ) )
			);

			foreach ( $groups_defaults as $key => $value ) {
				if ( !isset( $options['bp'][$key] ) )
					$options['bp'][$key] = $value;
			}
		}
	}

	return apply_filters( 'cp_get_options', $options, $saved_options );
}

//limit string length function
function cp_limit_length( $strtolimit=null, $limit=50 ) {

	if ( strlen( $strtolimit ) > $limit ) {
		$strtolimit = substr( $strtolimit, 0, $limit ) .'...';
	}

	return $strtolimit;
}
