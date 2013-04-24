<?php global $cp; ?>
<div class="collabpress">
	<div class="overall-links" style="float: right">
		<?php cp_overall_links(); ?>
	</div>
	<div class="clear"></div>
	<div class="activity">
		<h3><?php _e( 'Activity', 'collabpress' ); ?></h3>
		<?php
		// Get Current User
		global $current_user, $cp;
		get_currentuserinfo();

		// Get Activities
		$paged = ( ! empty( $_GET['activity_page'] ) ) ? esc_html( $_GET['activity_page'] ) : 1;

		// Load plugin options
		$cp_options = get_option( 'cp_options' );

		$activities_args = array( 'paged' => $paged );

		echo '<div class="cp-activity-list">';

		if ( cp_has_activities( $activities_args ) ) :
			$activityCount = 1;
			while( cp_activities() ) : cp_the_activity();
				    global $post;

				    $row_class = ($activityCount % 2) ? 'even' : 'odd';

				    // Avatar
				    $activity_user = get_post_meta( get_the_ID(), '_cp-activity-author', true);
				    $activity_user = get_userdata( $activity_user );
				    $activity_action = get_post_meta(get_the_ID(), '_cp-activity-action', true);
				    $activity_type = get_post_meta(get_the_ID(), '_cp-activity-type', true);
				    $activity_id = get_post_meta(get_the_ID(), '_cp-activity-ID', true);

				    if ( $activity_user ) : ?>
				    <div class="cp-activity-row <?php echo $row_class ?>">
					    <a class="cp-activity-author" title="<?php $activity_user->display_name ?>" href="<?php echo CP_DASHBOARD; ?>&user=<?php echo $activity_user->ID ?>"><?php echo get_avatar($activity_user->ID, 32) ?></a>
					    <div class="cp-activity-wrap">
					    <p class="cp-activity-description"><?php echo $activity_user->display_name . ' ' . $activity_action . ' ' . __('a', 'collabpress') . ' '. $activity_type ?>: <a href="<?php echo cp_get_url( $activity_id, $activity_type ); ?>"><?php echo get_the_title( $activity_id ); ?></a></p>
					    </div>
				    </div>
				    <?php endif;
				    $activityCount++;
			endwhile;
		else :
			echo '<p>'.__( 'No Activities...', 'collabpress' ).'</p>';
		endif;

		// Pagination
		if ( $cp->activities->max_num_pages > 1 ) {
			echo '<p class="cp_pagination">';
		    for ( $i = 1; $i <= $cp->activities->max_num_pages; $i++ ) {
		        echo '<a href="' . CP_DASHBOARD . '&view=activity&activity_page=' . $i . '" '.( ( $paged == $i ) ? 'class="active"' : '' ) . '>' . $i . '</a> ';
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

		<?php echo '</div>'; ?>
	</div>

</div>