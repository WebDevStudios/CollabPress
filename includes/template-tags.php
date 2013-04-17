<?php

/**
 * CollabPress Template Tags
 *
 * @package CollabPress
 * @subpackage TemplateTags
 */


function cp_has_projects( $args = array() ) {
	global $cp;

	$defaults = array(
		'post_type' => 'cp-projects',
		'posts_per_page' => -1,
		'project_id' => NULL,
		'task_list_id' => NULL,
		'status' => 'any',
		'projects_logged_in_user_has_access' => false, //todo fix this
	);

	$args = wp_parse_args( $args, $defaults );

	if ( $args['projects_logged_in_user_has_access'] ) {
		// Add filters to only grab projects logged-in user has access to.
		add_filter( 'posts_join_paged', 'cp_add_join_for_project_users_table' );
		add_filter( 'posts_where_paged', 'cp_add_where_for_current_user' );
	}

	$cp->projects = new WP_Query( $args );

	if ( $args['projects_logged_in_user_has_access'] ) {
		remove_filter( 'posts_join_paged', 'cp_add_join_for_project_users_table' );
		remove_filter( 'posts_where_paged', 'cp_add_where_for_current_user' );
	}

	return $cp->projects->have_posts();
}

function cp_add_join_for_project_users_table( $sql ) {
	global $cp, $wpdb;
	$sql .= "LEFT JOIN {$cp->tables->project_users} AS cppu ON cppu.project_id = {$wpdb->posts}.ID";
	return $sql;
}

function cp_add_where_for_current_user( $sql ) {
	global $wpdb;
	$current_user = wp_get_current_user();
	if ( empty( $current_user ) || ! $current_user )
		return;
	$sql .= $wpdb->prepare(
		" AND cppu.user_id = %d ",
		$current_user->ID
	);
	return $sql;
}

function cp_projects() {
	global $cp;
	// Put into variable to check against next
	$have_posts = $cp->projects->have_posts();

	// Reset the post data when finished
	if ( empty( $have_posts ) )
		wp_reset_postdata();

	return $have_posts;
}

function cp_the_project() {
	global $cp;

	return $cp->projects->the_post();
}

function cp_project_title() {
	global $cp;
	echo '<h2>' . $cp->project->post_title . '</h2>';
}

function cp_get_the_project_title() {
	global $cp;
	if ( ! empty( $cp->project->post_title ) )
		return $cp->project->post_title;
	else
		return false;
}

/**
 * Return current project ID in $cp global
 */
function cp_get_project_id() {
	global $cp;
	if( ! empty( $cp->project->ID ) )
		return $cp->project->ID;
	else
		return false;
}

function cp_project_permalink( $project_id = 0 ) {
	if ( ! $project_id )
		$project_id = cp_get_project_id();
	echo cp_get_project_permalink( $project_id );
}

function cp_get_project_tasks_permalink( $project_id = 0 ) {
	if ( ! $project_id ) {
		global $cp;
		$project_id = $cp->project->ID;
	}
	return add_query_arg( array( 'project' => $project_id, 'view' => 'tasks' ), CP_DASHBOARD );
}

	function cp_project_tasks_permalink( $project_id = 0 ) {
		echo cp_get_project_tasks_permalink( $project_id );
	}

function cp_get_project_calendar_permalink( $project_id = 0 ) {
	if ( ! $project_id ) {
		global $cp;
		$project_id = $cp->project->ID;
	}
	return add_query_arg( array( 'project' => $project_id, 'view' => 'calendar' ), CP_DASHBOARD );
}

	function cp_project_calendar_permalink( $project_id = 0 ) {
		echo cp_get_project_calendar_permalink( $project_id );
	}

function cp_get_project_files_permalink( $project_id = 0 ) {
	if ( ! $project_id ) {
		global $cp;
		$project_id = $cp->project->ID;
	}
	return add_query_arg( array( 'project' => $project_id, 'view' => 'files' ), CP_DASHBOARD );
}

	function cp_project_files_permalink( $project_id = 0 ) {
		echo cp_get_project_files_permalink( $project_id );
	}

function cp_get_project_users_permalink( $project_id = 0 ) {
	if ( ! $project_id ) {
		global $cp;
		$project_id = $cp->project->ID;
	}
	return add_query_arg( array( 'project' => $project_id, 'view' => 'users' ), CP_DASHBOARD );
}

	function cp_project_users_permalink( $project_id = 0 ) {
		echo cp_get_project_users_permalink( $project_id );
	}

/**
 * Fetch some tasks
 *
 * Note that this uses some WP defaults for fetching posts. In addition to
 * CP-specific arguments, you can also pass any argument accepted by
 * get_posts() or WP_Query.
 *
 * 'posts_only' is a special param that allows you to control whether the
 * function returns just a list of the posts matching a query, or the WP_Query
 * object. You might want this latter option when you need the total found
 * rows, as when building pagination.
 *
 * @param array $args See default definition below
 */
function cp_get_tasks( $args = array() ) {

	// Backward compatibility for old argument style
	if ( ! is_array( $args ) ) {
		$new_args = array( 'task_list_id' => $args );

		if ( func_get_arg( 1 ) && is_string( func_get_arg( 1 ) ) ) {
			$new_args['status'] = func_get_arg( 1 );
		}

		$args = $new_args;
	}

	$defaults = array(
		'posts_only'       => true,
		'post_type'        => 'cp-tasks',
		'posts_per_page'   => -1,
		'project_id'       => NULL,
		'task_list_id'     => NULL,
		'status'           => 'any',
		'assigned_user_id' => null,
	);

	$args = wp_parse_args( $args, $defaults );

	// Sanitize and convert
	foreach ( $args as $key => $value ) {
		if ( ! is_null( $value ) ) {
			switch ( $key ) {
				case 'task_list_id' :
				case 'user_id' :
					$args[ $key ] = absint( $value );
				break;
				case 'status' :
					$args[ $key ] = esc_attr( $value );
				break;
				case 'orderby' :
					if ( 'status' == $value ) {
						$args['meta_key'] = '_cp-task-status';
						$args['orderby'] = 'meta_value';
					}
				break;
			}
		}
	}

	if ( $args['task_list_id'] ) {
		$args['meta_query'][] = array(
			'key' => '_cp-task-list-id',
			'value' => $args['task_list_id'],
		);
	}

	if ( $args['project_id'] ) {
		$args['meta_query'][] = array(
			'key' => '_cp-project-id',
			'value' => $args['project_id'],
		);
	}

	if ( $args['status'] != 'any' ) {
		$args['meta_query'][] = array(
			'key' => '_cp-task-status',
			'value' => $args['status'],
		);
	}

	if ( ! empty( $args['assigned_user_id'] ) && $args['assigned_user_id'] ) {
		$args['meta_query'][] = array(
			'key' => '_cp-task-assign',
			'value' => $args['assigned_user_id'],
		);
	}

	if ( $args['posts_only'] )
		return get_posts( $args );
	else
		return new WP_Query( $args );
}

function cp_task_title() {
	global $cp;
	echo '<h2>' . $cp->task->post_title . '</h2>';
}

	function cp_get_task_title() {
		global $cp;
		return $cp->task->post_title;
	}

function cp_get_task_id() {
	global $cp;
	return $cp->task->ID;
}

function cp_task_content() {
	global $cp;
	echo $cp->task->post_content;
}

/**
 * The main tasks loop.
 *
 * @since 1.2
 *
 * @param mixed $args All the arguments supported by {@link WP_Query},
 * 	as well as a few custom arguments specific to CollabPress:
 *		'task_list_id' - a task list
 * 		'project_id' - a project
 * 		'status' - a task status
 * @uses WP_Query To make query and get the tasks
 * @return object Multidimensional array of forum information
 */
function cp_has_tasks( $args = array() ) {
	global $cp;

	$defaults = array(
		'post_type' => 'cp-tasks',
		'posts_per_page' => -1,
		'project_id' => NULL,
		'task_list_id' => NULL,
		'status' => 'any',
	);

	$args = wp_parse_args( $args, $defaults );

	extract( $args );

	if ( $task_list_id ) {
		$args['meta_query'][] = array(
			'key' => '_cp-task-list-id',
			'value' => $task_list_id,
		);
	}

	if ( $project_id ) {
		$args['meta_query'][] = array(
			'key' => '_cp-project-id',
			'value' => $project_id,
		);
	} else if ( ! empty( $cp->project ) ) {
		$args['meta_query'][] = array(
			'key' => '_cp-project-id',
			'value' => $cp->project->ID,
		);
	}

	if ( $status != 'any' ) {
		$args['meta_query'][] = array(
			'key' => '_cp-task-status',
			'value' => $status,
		);
	}

	$cp->tasks = new WP_Query( $args );

	return $cp->tasks->have_posts();
}

function cp_tasks() {
	global $cp;
	// Put into variable to check against next
	$have_posts = $cp->tasks->have_posts();

	// Reset the post data when finished
	if ( empty( $have_posts ) )
		wp_reset_postdata();

	return $have_posts;
}

function cp_the_task() {
	global $cp;
	return $cp->tasks->the_post();
}

function cp_task_permalink() {
	global $cp, $post;
	$permalink = add_query_arg(
		array(
			'project' => $cp->project->ID,
			'task' => $post->ID,
			),
		CP_DASHBOARD
	);
	echo $permalink;
}

function cp_get_project_for_task( $task_id ) {
	return get_post_meta( $task_id, '_cp-project-id', true );
}

function cp_project_links() {
	global $cp;

	?>
	<a class="<?php echo ( is_collabpress_page( 'dashboard' ) ? 'current' : '' ); ?>" href="<?php cp_permalink(); ?>">Dashboard</a>
	<a class="<?php echo ( is_collabpress_page( 'project-overview' ) ? 'current' : '' ); ?>" href="<?php cp_project_permalink(); ?>">Project Overview</a>
	<a class="<?php echo ( is_collabpress_page( 'project-calendar' ) ? 'current' : '' ); ?>" href="<?php cp_project_calendar_permalink(); ?>">Calendar</a>
	<a class="<?php echo ( is_collabpress_page( 'project-tasks' ) || is_collabpress_page( 'task' ) ? 'current' : '' ); ?>" href="<?php cp_project_tasks_permalink(); ?>">Tasks</a>
	<a class="<?php echo ( is_collabpress_page( 'project-files' ) ? 'current' : '' ); ?>" href="<?php cp_project_files_permalink(); ?>">Files</a>
	<a class="<?php echo ( is_collabpress_page( 'project-users' ) ? 'current' : '' ); ?>" href="<?php cp_project_users_permalink(); ?>">Users</a><?php
}

function cp_overall_links() {
	?>
	<a class="<?php echo ( is_collabpress_page( 'dashboard' ) ? 'current' : '' ); ?>" href="<?php cp_permalink(); ?>">Dashboard</a>
	<a class="<?php echo ( is_collabpress_page( 'activity' ) ? 'current' : '' ); ?>" href="<?php cp_activity_permalink(); ?>">Activity</a>
	<a class="<?php echo ( is_collabpress_page( 'calendar' ) ? 'current' : '' ); ?>" href="<?php cp_calendar_permalink(); ?>">Calendar</a><?php
}

function cp_permalink() {
	echo CP_DASHBOARD;
}
function cp_get_sidebar() {
	?>
	<div class="collabpress-sidebar" style="border: dashed 1px black; width: 20%; max-width: 200px; min-height: 400px; padding: 5px; float: left">
		<div style="border: dashed 1px black; height: 200px; padding: 5px">calendar</div>
		<div style="border: dashed 1px black; height: 200px; padding: 5px">recent activity</div>
	</div>
	<?php
}

/**
 * The main files loop.
 *
 * @since 1.2
 *
 * @param mixed $args All the arguments supported by {@link WP_Query},
 * 	as well as a few custom arguments specific to CollabPress:
 * 		'project_id' - a project
 * @uses WP_Query To make query and get the tasks
 * @return object Multidimensional array of forum information
 */
function cp_has_files( $args = array() ) {
	global $cp;

	$defaults = array(
		'post_type' => 'attachment',
		'posts_per_page' => -1,
		'project_id' => NULL,
		'post_status' => 'inherit'
	);

	$args = wp_parse_args( $args, $defaults );

	extract( $args );

	if ( $project_id ) {
		$args['post_parent'] = $project_id;
	} else if ( ! empty( $cp->project ) ) {
		$args['post_parent'] = $cp->project->ID;
	}

	$cp->files = new WP_Query( $args );
	return $cp->files->have_posts();
}

function cp_files() {
	global $cp;
	// Put into variable to check against next
	$have_posts = $cp->files->have_posts();

	// Reset the post data when finished
	if ( empty( $have_posts ) )
		wp_reset_postdata();

	return $have_posts;
}

function cp_the_file() {
	global $cp;
	return $cp->files->the_post();
}


/**
 * The main CollabPress activities loop.
 *
 * @since 1.2
 *
 * @param mixed $args All the arguments supported by {@link WP_Query},
 * 	as well as a few custom arguments specific to CollabPress:
 * @uses WP_Query To make query and get the tasks
 * @return object Multidimensional array of forum information
 */
function cp_has_activities( $args = array() ) {
	global $cp;

	$defaults = array(
		'post_type' => 'cp-meta-data',
		'posts_per_page' => 10
	);

	$args = wp_parse_args( $args, $defaults );

	extract( $args );

	$cp->activities = new WP_Query( $args );

	return $cp->activities->have_posts();
}

function cp_activities() {
	global $cp;
	// Put into variable to check against next
	$have_posts = $cp->activities->have_posts();

	// Reset the post data when finished
	if ( empty( $have_posts ) )
		wp_reset_postdata();

	return $have_posts;
}

function cp_the_activity() {
	global $cp;
	return $cp->activities->the_post();
}
