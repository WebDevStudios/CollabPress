<?php


add_action( 'admin_init', 'cp_admin_init' );

// Initialize CollabPress settings
function cp_admin_init() {

	// Register CollabPress options
	register_setting( 'cp_options_group', 'cp_options' );

	//add CP user capabilities to the built in accounts
	global $wp_roles;

	// add capabilities to Client user role
	// TODO: use get_role() instead of global $wp_roles
	// TODO: review roles and capabilities in CP. Why should a subscriber be able to add projects

	$wp_roles->add_cap( 'administrator', 'cp_add_projects' );
	$wp_roles->add_cap( 'administrator', 'cp_edit_projects' );
	$wp_roles->add_cap( 'administrator', 'cp_add_task_lists' );
	$wp_roles->add_cap( 'administrator', 'cp_edit_task_lists' );
	$wp_roles->add_cap( 'administrator', 'cp_add_task' );
	$wp_roles->add_cap( 'administrator', 'cp_edit_task' );

	$wp_roles->add_cap( 'editor', 'cp_add_projects' );
	$wp_roles->add_cap( 'editor', 'cp_edit_projects' );
	$wp_roles->add_cap( 'editor', 'cp_add_task_lists' );
	$wp_roles->add_cap( 'editor', 'cp_edit_task_lists' );
	$wp_roles->add_cap( 'editor', 'cp_add_task' );
	$wp_roles->add_cap( 'editor', 'cp_edit_task' );

	$wp_roles->add_cap( 'author', 'cp_add_projects' );
	$wp_roles->add_cap( 'author', 'cp_edit_projects' );
	$wp_roles->add_cap( 'author', 'cp_add_task_lists' );
	$wp_roles->add_cap( 'author', 'cp_edit_task_lists' );
	$wp_roles->add_cap( 'author', 'cp_add_task' );
	$wp_roles->add_cap( 'author', 'cp_edit_task' );

	$wp_roles->add_cap( 'contributor', 'cp_add_projects' );
	$wp_roles->add_cap( 'contributor', 'cp_edit_projects' );
	$wp_roles->add_cap( 'contributor', 'cp_add_task_lists' );
	$wp_roles->add_cap( 'contributor', 'cp_edit_task_lists' );
	$wp_roles->add_cap( 'contributor', 'cp_add_task' );
	$wp_roles->add_cap( 'contributor', 'cp_edit_task' );

	$wp_roles->add_cap( 'subscriber', 'cp_add_projects' );
	$wp_roles->add_cap( 'subscriber', 'cp_edit_projects' );
	$wp_roles->add_cap( 'subscriber', 'cp_add_task_lists' );
	$wp_roles->add_cap( 'subscriber', 'cp_edit_task_lists' );
	$wp_roles->add_cap( 'subscriber', 'cp_add_task' );
	$wp_roles->add_cap( 'subscriber', 'cp_edit_task' );

}

// Print Scripts
add_action( 'wp_print_scripts', 'collabpress_frontend_scripts' );
function collabpress_frontend_scripts() {
	?>
	<script language="JavaScript">

	function checkAll(field)
	{
	for (i = 0; i < field.length; i++)
		field[i].checked = true ;
	}

	function uncheckAll(field)
	{
	for (i = 0; i < field.length; i++)
		field[i].checked = false ;
	}
	</script>
<?php
}

/**
 * Dismiss a CP warning admin notice
 *
 * @since 1.3
 */
function cp_dismiss_admin_notice() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Sanitize
	$notices = array( 'no_bp_groups', 'no_bp_15' );
	if ( ! isset( $_GET['cp_dismiss'] ) || ! in_array( $_GET['cp_dismiss'], $notices ) ) {
		return;
	}

	check_admin_referer( 'cp_dismiss_notice' );

	$dismissed = get_option( 'cp_dismissed_messages' );
	if ( ! $dismissed ) {
		$dismissed = array();
	}

	$dismissed[ $_GET['cp_dismiss'] ] = '1';

	update_option( 'cp_dismissed_messages', $dismissed );

	$redirect = remove_query_arg( array( '_wpnonce', 'cp_dismiss' ), wp_get_referer() );
	wp_safe_redirect( $redirect );
}
add_action( 'admin_init', 'cp_dismiss_admin_notice' );


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
			    <a class="cp-activity-author" title="<?php $activityUser->display_name ?>" href="<?php echo COLLABPRESS_DASHBOARD; ?>&user=<?php echo $activityUser->ID ?>"><?php echo get_avatar($activityUser->ID, 32) ?></a>
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
	        echo '<a href="'.COLLABPRESS_DASHBOARD.'&paged='.$i.'" '.(($paged == $i) ? 'class="active"' : '' ).'>'.$i.'</a> ';
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
