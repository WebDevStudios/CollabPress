<?php
//verify user has permission to view settings
if ( cp_check_permissions( 'settings_user_role' ) ) {
?>
	<div id="collabpress-wrap" class="wrap">
	<?php echo cp_screen_icon( 'collabpress' ); ?>
		<h2><?php _e( 'CollabPress Settings', 'collabpress' ); ?></h2>
			<?php
			// User Notice
			$sent_data = ( $_POST ) ? $_POST : $_GET;
			cp_user_notice( $sent_data );
			?>
			<form method="post" action="options.php">

			<?php settings_fields('cp_options_group'); ?>
			<?php $options = get_option('cp_options'); ?>

			<?php
			//clear activity log
			if ( isset( $_POST['cp_clear_activity'] ) && $_POST['cp_clear_activity'] == 'Delete Log' ) :
				//delete all activity log posts
				$tasks_args = array(
					'post_type' => 'cp-meta-data',
					'showposts' => '-1'
					);
				$activity_query = new WP_Query( $tasks_args );

				// WP_Query();
				if ( $activity_query->have_posts() ) :
					while( $activity_query->have_posts() ) : $activity_query->the_post();

						//delete the activity
						wp_delete_post( get_the_ID(), true );

					endwhile;
				endif;

			endif;

			//load option values
			$cp_rss_feed_num = ( isset( $options['num_recent_activity'] ) ) ? absint( $options['num_recent_activity'] ) : 4;

			//load option values
			$num_users_display = ( isset( $options['num_users_display'] ) ) ? absint( $options['num_users_display'] ) : 10;

			//load minimum user role
			$cp_user_role = ( isset( $options['user_role'] ) ) ? esc_attr( $options['user_role'] ) : 'manage_options';

			//load settings user role
			$cp_settings_user_role = ( isset( $options['settings_user_role'] ) ) ? esc_attr( $options['settings_user_role'] ) : 'manage_options';

			//load shortcode user role
			$cp_shortcode_user_role = ( isset( $options['shortcode_user_role'] ) ) ? esc_attr( $options['shortcode_user_role'] ) : '';

			//load presstrends
            $cp_presstrends = ( isset( $options['presstrends'] ) ) ? $options['presstrends'] : 'no';

            // Output pate format
            $date_format = ( isset( $options['date_format'] ) ) ? $options['date_format'] : 'F j, Y';


			?>
			<table class="form-table">
				<tr>
					<td colspan="2"><h3><?php _e( 'General', 'collabpress' ); ?></h3><hr /></td>
				</tr>
				<tr>
					<th scope="row"><label for="dashboard"><?php _e( 'Dashboard Meta Box', 'collabpress' ); ?></label></th>
					<td>
						<select name="cp_options[dashboard_meta_box]">
							<option value="disabled" <?php selected( $options['dashboard_meta_box'], 'disabled' ); ?>><?php _e('Disabled', 'collabpress') ?></option>
							<option value="enabled" <?php selected( $options['dashboard_meta_box'], 'enabled' ); ?>><?php _e('Enabled', 'collabpress') ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="email"><?php _e( 'Email Notifications', 'collabpress' ); ?></label></th>
					<td>
						<select name="cp_options[email_notifications]">
							<option value="enabled" <?php selected( $options['email_notifications'], 'enabled' ); ?>><?php _e('Enabled', 'collabpress') ?></option>
							<option value="disabled" <?php selected( $options['email_notifications'], 'disabled' ); ?>><?php _e('Disabled', 'collabpress') ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="recent_activity"><?php _e( 'Recent Activites Per Page', 'collabpress' ); ?></label></th>
					<td>
						<select name="cp_options[num_recent_activity]">
							<option value="1" <?php selected( $cp_rss_feed_num, '1' ); ?>>1</option>
							<option value="2" <?php selected( $cp_rss_feed_num, '2' ); ?>>2</option>
							<option value="3" <?php selected( $cp_rss_feed_num, '3' ); ?>>3</option>
							<option value="4" <?php selected( $cp_rss_feed_num, '4' ); ?>>4</option>
							<option value="5" <?php selected( $cp_rss_feed_num, '5' ); ?>>5</option>
							<option value="6" <?php selected( $cp_rss_feed_num, '6' ); ?>>6</option>
							<option value="7" <?php selected( $cp_rss_feed_num, '7' ); ?>>7</option>
							<option value="8" <?php selected( $cp_rss_feed_num, '8' ); ?>>8</option>
							<option value="9" <?php selected( $cp_rss_feed_num, '9' ); ?>>9</option>
							<option value="10" <?php selected( $cp_rss_feed_num, '10' ); ?>>10</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="recent_activity"><?php _e( 'Number of Users to Display', 'collabpress' ); ?></label></th>
					<td>
						<select name="cp_options[num_users_display]">
				<?php
				for ( $counter = 1; $counter <= 50; $counter++ ) {
				echo '<option value="' .$counter .'" ' .selected( $num_users_display, $counter ) .'>' .$counter .'</option>';
				}
				?>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="cp_options[date_format]"><?php _e( 'Date format', 'collabpress' ); ?></label></th>
					<td>
						<?php
						$date_formats = array_unique( apply_filters( 'date_formats', array(
							__('F j, Y'),
							'Y/m/d',
							'm/d/Y',
							'd/m/Y',
						) ) );

						$custom = true;

						foreach ( $date_formats as $format ) {
							echo "\t<label title='" . esc_attr($format) . "'><input type='radio' name='cp_options[date_format]' value='" . esc_attr($format) . "'";
							if ( $date_format === $format ) { // checked() uses "==" rather than "==="
								echo " checked='checked'";
								$custom = false;
							}
							echo ' /> <span>' . date_i18n( $format ) . "</span></label><br />\n";
						}

						echo '	<label><input type="radio" name="cp_options[date_format]" id="date_format_custom_radio" value="\c\u\s\t\o\m"';
						checked( $custom );
						echo '/> ' . __('Custom:') . ' </label><input type="text" name="cp_options[date_format]" value="';
						if ( $date_format == '\c\u\s\t\o\m' )
							echo esc_attr( $date_format );
						echo '" class="small-text" /> <span class="example"> ' . date_i18n( $date_format ) . "</span> <span class='spinner'></span>\n";

						echo "\t<p>" . __('<a href="http://codex.wordpress.org/Formatting_Date_and_Time">Documentation on date and time formatting</a>.') . "</p>\n";
						?>
					</td>
				</tr>
				<tr>
					<td colspan="2"><h3><?php _e( 'Permissions', 'collabpress' ); ?></h3><hr /></td>
				</tr>
				<tr>
					<th scope="row"><label for="user_role"><?php _e( 'Minimum User Role for Access', 'collabpress' ); ?></label></th>
					<td>
						<select name="cp_options[user_role]">
							<option value="manage_options" <?php selected( $cp_user_role, 'manage_options' ); ?>><?php _e('Administrator', 'collabpress'); ?></option>
							<option value="delete_others_posts" <?php selected( $cp_user_role, 'delete_others_posts' ); ?>><?php _e('Editor', 'collabpress'); ?></option>
							<option value="publish_posts" <?php selected( $cp_user_role, 'publish_posts' ); ?>><?php _e('Author', 'collabpress'); ?></option>
							<option value="edit_posts" <?php selected( $cp_user_role, 'edit_posts' ); ?>><?php _e('Contributor', 'collabpress'); ?></option>
							<option value="read" <?php selected( $cp_user_role, 'read' ); ?>><?php _e('Subscriber', 'collabpress'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="settings_user_role"><?php _e( 'Minimum User Role to change Settings, Edit/Delete data, Enable/View Debug, etc', 'collabpress' ); ?></label></th>
					<td>
						<select name="cp_options[settings_user_role]">
							<option value="manage_options" <?php selected( $cp_settings_user_role, 'manage_options' ); ?>><?php _e('Administrator', 'collabpress'); ?></option>
							<option value="delete_others_posts" <?php selected( $cp_settings_user_role, 'delete_others_posts' ); ?>><?php _e('Editor', 'collabpress'); ?></option>
							<option value="publish_posts" <?php selected( $cp_settings_user_role, 'publish_posts' ); ?>><?php _e('Author', 'collabpress'); ?></option>
							<option value="edit_posts" <?php selected( $cp_settings_user_role, 'edit_posts' ); ?>><?php _e('Contributor', 'collabpress'); ?></option>
							<option value="read" <?php selected( $cp_settings_user_role, 'read' ); ?>><?php _e('Subscriber', 'collabpress'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="shortcode_user_role"><?php _e( 'Minimum User Role to view [collabpress] shortcode front-end', 'collabpress' ); ?></label></th>
					<td>
						<select name="cp_options[shortcode_user_role]">
							<option value="all" <?php selected( $cp_shortcode_user_role, 'all' ); ?>><?php _e('Anyone', 'collabpress'); ?></option>
							<option value="manage_options" <?php selected( $cp_shortcode_user_role, 'manage_options' ); ?>><?php _e('Administrator', 'collabpress'); ?></option>
							<option value="delete_others_posts" <?php selected( $cp_shortcode_user_role, 'delete_others_posts' ); ?>><?php _e('Editor', 'collabpress'); ?></option>
							<option value="publish_posts" <?php selected( $cp_shortcode_user_role, 'publish_posts' ); ?>><?php _e('Author', 'collabpress'); ?></option>
							<option value="edit_posts" <?php selected( $cp_shortcode_user_role, 'edit_posts' ); ?>><?php _e('Contributor', 'collabpress'); ?></option>
							<option value="read" <?php selected( $cp_shortcode_user_role, 'read' ); ?>><?php _e('Subscriber', 'collabpress'); ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<td colspan="2"><h3><?php _e( 'Advanced', 'collabpress' ); ?></h3><hr /></td>
				</tr>
				<tr>
					<th scope="row"><label for="debug"><?php _e( 'Debug Mode', 'collabpress' ); ?></label></th>
					<td>
						<select name="cp_options[debug_mode]">
							<option value="disabled" <?php selected( $options['debug_mode'], 'disabled' ); ?>><?php _e('Disabled', 'collabpress') ?></option>
							<option value="enabled" <?php selected( $options['debug_mode'], 'enabled' ); ?>><?php _e('Enabled', 'collabpress') ?></option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="presstrends"><?php _e( 'Opt-out of PressTrends Tracking', 'collabpress' ); ?></label></th>
					<td>
						<select name="cp_options[presstrends]">
							<option value="no" <?php selected( $cp_presstrends, 'no' ); ?>><?php _e('No', 'collabpress') ?></option>
							<option value="yes" <?php selected( $cp_presstrends, 'yes' ); ?>><?php _e('Yes', 'collabpress') ?></option>
						</select>
					</td>
				</tr>

				<?php do_action( 'cp_after_advanced_settings' ) ?>

				<tr>
					<td colspan="2"><input type="submit" name="Save" value="<?php _e( 'Save Settings', 'collabpress' ) ?>" class="button-primary" /></td>
				</tr>
			</table>
		</form>
		<form method="post">
			<table class="form-table">
				<tr>
					<td colspan="2"><h3><?php _e( 'Optimize', 'collabpress' ); ?></h3><hr /></td>
				</tr>
				<?php
				//count log activity entries
				$cp_count_activity_log = wp_count_posts( 'cp-meta-data' );
				$cp_activity_count = absint( $cp_count_activity_log->publish ); //checky checky, you better be a positive int
				?>
				 <tr>
					<th scope="row"><label for="debug"><?php _e( 'Clear Activity Log', 'collabpress' ); ?><br />( <?php echo $cp_activity_count .' entries total )'; ?></label></th>
					<td valign="top"><?php echo '<input type="submit" name="cp_clear_activity" value="'.__('Delete Log', 'collabpress').'" class="button-secondary" onclick="javascript:check=confirm(\'' . __( 'WARNING: This will delete ALL activity logs.  Once logs have been deleted they can NOT be restored.\n\nChoose [Cancel] to Stop, [OK] to delete logs.\n', 'collabpress' ) .'\' );if(check==false) return false;" />';?></td>
				</tr>
				<tr>
					<td colspan="2"></td>
				</tr>
			</table>
		</form>
		<?php include "settings-addons.php";?>
	</div>

	<?php cp_footer(); ?>
<?php } ?>