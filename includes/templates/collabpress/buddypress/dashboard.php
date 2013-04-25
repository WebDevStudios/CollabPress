<div class="collabpress">
	<div class="projects">
		<?php
		?>
		<?php if( cp_has_projects( cp_bp_projects_query_args() ) ) : ?>
			<?php while( cp_projects() ) : cp_the_project(); ?>
			<div class="collabpress-project">
				<h2>
					<a href="<?php the_permalink() ?>"><?php the_title() ?></a>
				</h2>
			</div>
			<?php endwhile; ?>
		<?php endif; ?>
		<?php if ( cp_check_permissions( 'settings_user_role' ) ) { ?>

		<div class="collabpress-project new">
			<a href="#inline_content" class="add-new-project">
				<div class="plus-sign">+</div>
				<h2><?php _e( 'Add new project', 'collabpress' ); ?></h2>
			</a>
		</div>
		<?php } ?>
		<div style="clear: both"></div>
		<div style='display:none'>
			<div id='inline_content' style='padding:10px; background:#fff;'>
			<h2><?php _e( 'Add Project', 'collabpress' ); ?></h2>
			<input type="hidden" id="add_project_nonce" value="<?php echo wp_create_nonce( 'add-new-project' ); ?>">
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><label for="cp-project"><?php _e( 'Name: ', 'collabpress' ) ?></label></th>
							<td><p><input type="text" class="regular-text" value="" id="cp-project" name="cp-project" /></p></td>
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
									echo '<input type="checkbox" name="cp_project_users[]" class="cp_project_user" value="'.$wp_user->ID .'" />&nbsp;' .$wp_user->user_login .'<br />';
								}
							}
							?>
							</td>
						</tr>
						<?php endif ?>
					</tbody>
				</table>
				<p class="submit"><input class="button-primary" type="submit" name="cp-add-project" value="<?php _e( 'Submit', 'collabpress' ); ?>"/><span class="spinner" style="float: left"></span></p>
			</div>
		</div>
	</div>
</div>
<script>
(function($) {
	$(document).ready(function() {
		$('.add-new-project').colorbox(
			{
				inline: true,
				width: '50%',
				onLoad: function() {
					$('#cp-project').val( '' );
					$('#cp-project-description').val( '' );
					$('.cp_project_user').each(function( i, el ) {
						$(el).attr( 'checked', false );
					});
				}
			}
		);
	});

	// Add new Project submit handler
	$('.submit').click(function() {
		var project_name = $('#cp-project').val();
		if ( ! project_name ) {
			alert( '<?php _e( 'Please enter a name for this project.', 'collabpress' ); ?>' );
			return;
		}
		var data = {
			project_name: project_name,
			project_description: $('#cp-project-description').val(),
			users: [],
			group_id: <?php echo bp_get_current_group_id(); ?>,
			collabpress_ajax_request_origin: '<?php echo ( is_admin() ? 'admin' : 'frontend' ); ?>',
		};
		$('.cp_project_user').each(function( i, el ){
			if ( $(el).is(':checked') ) {
				data.users.push( $(el).val() );
			}
		});
		data.nonce = $('#add-project-nonce').val();
		$('#inline_content .spinner').show();
		$.post(
			ajaxurl,
			{
				action: 'cp_add_project',
				data: data,
				nonce: jQuery( '#add_project_nonce' ).val()
			}, function( response ) {
				$('#inline_content .spinner').hide();
				window.location = response.data.redirect;
			}
		);
	});
})(jQuery);
</script>