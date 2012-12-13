<?php

//create [collabpress $id ] shortcode
//calls cp_sc_projects( $id)
//$id = project ID (option) : if not set all projects listed
add_shortcode( 'collabpress', 'cp_project_short' );

function cp_project_short( $atts ) {
    extract( shortcode_atts( array(
        "id"    =>  null
    ), $atts ) );

    //verify user has permission to view shortcode
    if ( cp_check_permissions( 'shortcode_user_role' ) ) :

        cp_sc_projects( $id );

    endif;

}

// Checks to see if the current post/page is using
// CollabPress shortcode - if so adds front end css
add_action('the_posts', 'cp_using_shortcode');

function cp_using_shortcode($posts) {

    if ( empty($posts) )
        return $posts;
	
    $foundsc = false;

    foreach ($posts as $post) {
        if ( stripos($post->post_content, '[collabpress') )
            $foundsc = true;
            break;
        }

    if ($foundsc = true) {
    $css_src = CP_PLUGIN_URL . 'includes/css/front.css';
    $js_src = CP_PLUGIN_URL . 'includes/js/frontend.js';
   
    wp_register_style('cp_frontend_css', $css_src );
    wp_enqueue_style('cp_frontend_css');
    
    wp_register_script('cp_frontend_js', $js_src );
	wp_enqueue_script('jquery');  
    wp_enqueue_script('cp_frontend_js');    
	}
	
    return $posts;
}

//SHORTCODE: List CollabPress Projects
//$id = project id, if not set all projects are listed
//these comments suck, will expand later :)
function cp_sc_projects( $id ) {

    global $cp_dashboard_page;
    global $cp_project_page;
    global $cp_task_list_page;
    global $cp_task_page;

    // Get Current User
    global $current_user;
    global $cp_project;
    global $cp_task_list;
    global $cp_task;
    global $cp_user;

    get_currentuserinfo();

    $cp_project_page = false;
    $cp_task_list_page = false;
    $cp_task_page = false;

    if ( isset( $_GET['project'] ) ) :

		//verify user has permission to access the project
		if ( cp_check_project_permissions( $current_user->ID, absint( $_GET['project'] ) ) ) {
		
			//store the project ID
			$cp_project = new CP_Project();
			$cp_project->id = absint( $_GET['project'] );

			// Task Page
			if ( isset( $_GET['task'] ) ) :

				// Set Task List ID
				$cp_task = new CP_Task();
				$cp_task->id = absint( $_GET['task'] );
				$cp_task_page = true;

			// Task List Page
			elseif ( isset( $_GET['task-list'] ) ) :

				// Set Task List ID
				$cp_task_list = new CP_TaskList();
				$cp_task_list->id = absint( $_GET['task-list'] );
				$cp_task_list_page = true;

			// Project Page
			else:
				$cp_project_page = true;
			endif;

		}
		
    endif;

    require_once('menus/isset/project.php');
    require_once('menus/isset/task-list.php');
    require_once('menus/isset/task.php');
    require_once('menus/isset/comment.php');

    // Title
    echo '<div id="collabpress">';
 
     // User Notice
    $sent_data = ( $_POST ) ? $_POST : $_GET;
    cp_user_notice( $sent_data );
    
    //show the breadcrumb navigation
    cp_get_breadcrumb();

	echo '<div id="page_header">';
		// logic for title output. On single task page output <p> instead of <h2>
		if ( $cp_task_page ) :
			echo str_replace( 'h2', 'p', cp_get_page_title() );
		else :
			echo cp_get_page_title();
		endif;
   	echo '</div>';
   	
    if ( $cp_project_page ) :

        //load project task lists
        if ( isset( $_GET['view'] ) ) :

            //display edit project form
            cp_edit_project();

        else :

            //display task lists for the project
            cp_sc_task_list();


            //display add task list form
            cp_add_task_list();

        endif;

    elseif ( $cp_task_list_page ) :

        //load task list tasks
        if( isset( $_GET['view'] ) ) :

            //edit the task list
            cp_edit_task_list();

        else:

            //display all open/completed tasks
            cp_sc_task();

            //display add task form
            cp_add_task();

        endif;

    elseif ( $cp_task_page ) :

        //load single task
        if( isset( $_GET['view'] ) ) :

            //edit task form
            cp_edit_task();

        else :

            //single task detail page
            cp_task_comments();

        endif;

    else :

    // Get Projects
    $projects_args = array( 'post_type' => 'cp-projects', 'p' => absint( $id ), 'showposts' => '-1' );
    $projects_query = new WP_Query( $projects_args );

    // WP_Query();
    if ( $projects_query->have_posts() ) :
    echo '<div id="project-list">';
      echo '<h4 class="cp_tl_title">' . __('Projects', 'collabpress') . '</h4>';

        while( $projects_query->have_posts() ) : $projects_query->the_post();

		//verify user has permission to view this project
		if ( cp_check_project_permissions( $current_user->ID, get_the_ID() ) ) {

			//generate delete project link
			$cp_del_link = CP_DASHBOARD .'&cp-delete-project-id='.get_the_ID();
			$cp_del_link = ( function_exists('wp_nonce_url') ) ? wp_nonce_url( $cp_del_link, 'cp-action-delete_project' ) : $cp_del_link;

			//generate edit project link
			$cp_edit_link = CP_DASHBOARD.'&project='.get_the_ID().'&view=edit';

			echo '<div class="cp-project-item"><a class="cp_project_name" href="'.CP_DASHBOARD.'&project='.get_the_ID().'">'.get_the_title().'</a>';

			//check if user can view edit/delete links
			if ( cp_check_permissions( 'settings_user_role' ) ) {
				echo '<span class="edit-del-links"><a class="cp_project_edit" href="' .$cp_edit_link. '">' .__( 'edit', 'collabpress'). '</a> &middot; <a class="cp_project_del" href="' .$cp_del_link. '" style="color:red;" onclick="javascript:check=confirm(\'' . __('WARNING: This will delete the selected project, including ALL task lists and tasks in the project.\n\nChoose [Cancel] to Stop, [OK] to delete.\n', 'collabpress' ) .'\');if(check==false) return false;">' .__('delete', 'collabpress'). '</a></span>';
			}
			echo '</div>';

		}
    ?>

    <?php
        endwhile;
        wp_reset_query();

    //display add project form
    cp_add_project();

    // No Results
    else :
        echo '<p class="cp_none">' .__( 'No Projects...', 'collabpress' ). '</p>';
       
    endif;
		echo '</div>';
   	endif;
   	
	echo '</div>'; // <!-- end div#collabpress -->

}

//display a project's task lists
function cp_sc_task_list() {

    global $cp_project;
	global $current_user;
	
	get_currentuserinfo();

	//verify user has permission to access the project
	if ( cp_check_project_permissions( $current_user->ID, $cp_project->id ) ) {
	
		// Get Task Lists
		$task_lists_args = array(
				'post_type' => 'cp-task-lists',
				'meta_key' => '_cp-project-id',
				'meta_value' => $cp_project->id,
				'showposts' => '-1'
				);
		$task_lists_query = new WP_Query( $task_lists_args );

		echo '<h4 class="cp_tl_title">' . __('Tasks Lists', 'collabpress') . '</h4>';

		// WP_Query();
		echo '<ul class="cp_tl">';
		if ( $task_lists_query->have_posts() ) :
		while( $task_lists_query->have_posts() ) : $task_lists_query->the_post();

			//generate delete task list link
			$cp_del_link = CP_DASHBOARD .'&project='.$cp_project->id.'&cp-delete-task-list-id='.get_the_ID();
			$cp_del_link = ( function_exists('wp_nonce_url') ) ? wp_nonce_url( $cp_del_link, 'cp-action-delete_task_list' ) : $cp_del_link;

			//generate edit task list link
			$cp_edit_link = CP_DASHBOARD .'&project='.$cp_project->id.'&task-list='.get_the_ID().'&view=edit';

			echo '<li><a class="cp_tl_name" href="'.CP_DASHBOARD.'&project='.$cp_project->id.'&task-list='.get_the_ID().'">'.get_the_title().'</a>';

			//check if user can view edit/delete links
			if ( cp_check_permissions( 'settings_user_role' ) ) {
			echo '<span class="edit-del-links"><a class="cp_tl_edit" href="' .$cp_edit_link. '">edit</a> &middot; <a class="cp_tl_del" href="' .$cp_del_link. '" style="color:red;" onclick="javascript:check=confirm(\'' . __('WARNING: This will delete the selected task list and all tasks in the list.\n\nChoose [Cancel] to Stop, [OK] to delete.\n', 'collabpress' ) .'\');if(check==false) return false;">' .__('delete', 'collabpress'). '</a></span></li>';
			}

		endwhile;
		wp_reset_query();
		else :
			echo '<p class="description cp_none">'.__( 'No Task Lists...', 'collabpress' ).'</p>';
		endif;
		echo '</ul>';
	
	}
}

//display open and completed tasks for a task list
function cp_sc_task() {

        global $cp_project;
        global $cp_task_list;
        global $post;
		global $current_user;

		get_currentuserinfo();
		
		//verify user has permission to access the project
		if ( cp_check_project_permissions( $current_user->ID, $cp_project->id ) ) {

			//get open tasks
			$tasks_query = cp_get_tasks( $cp_task_list->id, 'open' );
			$cp_count = 0;
			echo '<h4 class="cp_tl_title">' . __( 'Current Tasks', 'collabpress' ) . '</h4>';

			if ($tasks_query) :
			foreach ($tasks_query as $post):
			setup_postdata($post);

			$cp_count++;
				// adds odd or even to class of task summary
				if ( $cp_count % 2 ) { $oddeven_class = "odd"; }
				else { $oddeven_class = "even"; }

				$user = get_post_meta(get_the_ID(), '_cp-task-assign', true);
				$user = get_userdata($user);

				// get user's name resorts to nickname if not entered
				$user_name = $user->first_name;
				$user_name .= " " . $user->last_name;
				if ( $user->first_name == "" && $user->last_name == "" ) {
				$user_name = $user->nickname;
				}

				//get due date
				$task_due_date = get_post_meta( get_the_ID(), '_cp-task-due', true );

				//get comment count
				$num_comments = get_comments_number();
				if ( $num_comments == 1 ) $num_comments .= " comment"; // if only one comment
				elseif ( $num_comments > 1 ) $num_comments .= " comments"; // if more than one comment
				elseif ( $num_comments == 0 ) $num_comments = ""; // if more than one comment

				//get user ID assigned the task
				$task_user_id = get_post_meta( get_the_ID(), '_cp-task-assign', true );

				//generate complete task link
				$link = CP_DASHBOARD .'&project=' .$cp_project->id .'&task-list=' .$cp_task_list->id .'&cp-complete-task-id=' .get_the_ID();
				$link = ( function_exists( 'wp_nonce_url' ) ) ? wp_nonce_url( $link, 'cp-complete-task' ) : $link;

				//generate delete task link
				$cp_del_link = CP_DASHBOARD .'&project='.$cp_project->id.'&task-list=' .$cp_task_list->id .'&cp-delete-task-id='.get_the_ID();
				$cp_del_link = ( function_exists('wp_nonce_url') ) ? wp_nonce_url( $cp_del_link, 'cp-action-delete_task' ) : $cp_del_link;

				//generate edit task link
				$cp_edit_link = CP_DASHBOARD.'&project='.$cp_project->id.'&task='.get_the_ID().'&view=edit';

				//check task status
				$task_status = get_post_meta( get_the_ID(), '_cp-task-status', true );

				//check task priority
				$task_priority = get_post_meta( get_the_ID(), '_cp-task-priority', true );

				echo '<div class="cp_task_summary '. $oddeven_class . '">';

					echo '<input type="checkbox" name="" value="0" onclick="window.location=\''. $link. '\'; return true;"  /> ';
					echo '<a class="cp_task_name" href="'.CP_DASHBOARD.'&project='.$cp_project->id.'&task='.get_the_ID().'">' .cp_limit_length( get_the_title(), 125 ). '</a>';

					echo '<div class="cp_task_meta">';

						echo '<span class="cp_assign_due">'.__( 'Assigned to:', 'collabpress' ) .'' .get_avatar( $task_user_id, 16 ). '' . $user_name . ' - ' .__('Due: ', 'collabpress') . $task_due_date .' - ' .__('Priority: ', 'collabpress') .$task_priority.'</span>';
						if ( $num_comments > 0 ) echo '<span class="cp_task_comm">'.$num_comments. '</span>';
						
						//check if user can view edit/delete links
						if ( cp_check_permissions( 'settings_user_role' ) ) {
							echo '<span class="edit-del-links"><a class="cp_task_edit" href="'.$cp_edit_link.'">' .__('edit', 'collabpress'). '</a> &middot; <a class="cp_task_del" href="'. $cp_del_link .'" style="color:red;" onclick="javascript:check=confirm(\'' . __('WARNING: This will delete the selected task.\n\nChoose [Cancel] to Stop, [OK] to delete.\n', 'collabpress' ) .'\');if(check==false) return false;">' .__( 'delete', 'collabpress' ). '</a></span>';
						}

					echo '</div>';

				echo '</div>';

			endforeach;

			else :
				echo '<p class="cp_none">'.__( 'No Tasks...', 'collabpress' ).'</p>';
			endif;

			echo '<div style="clear:both;"></div>';

			//get completed tasks
			$tasks_query = cp_get_tasks( $cp_task_list->id, 'complete' );
			$cp_count = 0;
			echo '<h4 class="cp_tl_title">' . __( 'Completed Tasks', 'collabpress' ) . '</h4>';

			if ($tasks_query) :
			foreach ($tasks_query as $post):
			setup_postdata($post);

			$cp_count++;
				// adds odd or even to class of task summary
				if ( $cp_count % 2 ) { $oddeven_class = "odd"; }
				else { $oddeven_class = "even"; }

				$user = get_post_meta(get_the_ID(), '_cp-task-assign', true);
				$user = get_userdata($user);

				// get user's name resorts to nickname if not entered
				$user_name = $user->first_name;
				$user_name .= " " . $user->last_name;
				if ( $user->first_name == "" || $user->last_name == "" ) {
				$user_name = $user->nickname;
				}

				//get due date
				$task_due_date = get_post_meta( get_the_ID(), '_cp-task-due', true );

				//get comment count
				$num_comments = get_comments_number();
				if ( $num_comments == 1 ) $num_comments .= " comment"; // if only one comment
				elseif ( $num_comments > 1 ) $num_comments .= " comments"; // if more than one comment

				//get user ID assigned the task
				$task_user_id = get_post_meta( get_the_ID(), '_cp-task-assign', true );

				//generate complete task link
				$link = CP_DASHBOARD .'&project=' .$cp_project->id .'&task-list=' .$cp_task_list->id .'&cp-complete-task-id=' .get_the_ID();
				$link = ( function_exists( 'wp_nonce_url' ) ) ? wp_nonce_url( $link, 'cp-complete-task' ) : $link;

				//check task status
				$task_status = get_post_meta( get_the_ID(), '_cp-task-status', true );

				//check task priority
				$task_priority = get_post_meta( get_the_ID(), '_cp-task-priority', true );

				echo '<div class="cp_task_summary ' . $oddeven_class . '">';

					echo '<input type="checkbox" name="" value="0" onclick="window.location=\''. $link. '\'; return true;"  /> ';

					if ( $task_status == 'complete') {
						echo '<span style="text-decoration:line-through">';
					}

					echo '<a class="cp_task_name" href="'.CP_DASHBOARD.'&project='.$cp_project->id.'&task='.get_the_ID().'">'.cp_limit_length( get_the_title(), 125 ).'</a>';

					if ( $task_status == 'complete') {
						echo '</span>';
					}

					echo '<div class="cp_task_meta">';
						echo '<span class="cp_assign_due">'.__( 'Assigned to:', 'collabpress' ) .'' .get_avatar( $task_user_id, 16 ). '' . $user_name . ' - ' .__('Due: ', 'collabpress') . $task_due_date .' - ' .__('Priority: ', 'collabpress') .$task_priority.'</span>';
						echo '<span class="cp_task_comm">'.$num_comments. '</span>';
					echo '</div>';

				echo '</div>';

			endforeach;

			else :
				echo '<p class="cp_none">'.__( 'No Tasks Completed...', 'collabpress' ).'</p>';
			endif;

			echo '<div style="clear:both;"></div>';
			
		}
}
?>