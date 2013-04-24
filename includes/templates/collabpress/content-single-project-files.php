<?php global $cp; ?>
<div class="collabpress">
	<div class="project-links" style="float: right;">
		<?php cp_project_links(); ?>
	</div>
	<?php echo cp_project_title(); ?>
	<div class="files">
		<h3><?php _e( 'Files', 'collabpress' ); ?></h3>
		<?php if ( cp_has_files() ) : ?>
			<?php while( cp_files() ) : cp_the_file(); ?>
				<div class="collabpress-task">
					<a href="<?php echo wp_get_attachment_url( get_the_ID() ); ?>"><?php the_title(); ?></a>
				</div>
			<?php endwhile; ?>
		<?php endif; ?>
		<a href="#inline_content" class="add-new-task"><?php _e( 'Upload file', 'collabpress' ); ?></a>
	</div>
	<div style='display:none'>
		<div id='inline_content' style='padding:10px; background:#fff;'>
			<h2><?php _e( 'Add File', 'collabpress' ); ?></h2>
			<input type="hidden" id="cp_add_new_file_nonce" value="<?php echo wp_create_nonce( 'cp_add_new_file' ); ?>">
			<input type="hidden" id="cp-project-id" value="<?php echo cp_get_project_id() ?>">
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
						<td><p><input name="cp-task-due" class="cp-task-due-date" id="cp-task-due-date" id="datepicker" class="regular-text" type="text" value=<?php echo date('n/j/Y') ?> /></p></td>
					</tr>
					<tr valign="top">
						<th scope="row"><label for="cp-task-assign"><?php _e('Assigned to: ', 'collabpress') ?></label></th>
						<td>
							<p>
														<?php
								$user_list = '<select name="cp-task-assign" id="cp-task-assign">';
								foreach ( cp_get_project_users() as $wp_user )
									$user_list .= '<option value="' . $wp_user->ID . '">' . $wp_user->user_login . '</option>';
								$user_list .= '</select>';
												$user_list = apply_filters( 'cp_task_user_list_html', $user_list, false );
												echo $user_list;
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
			<p class="submit">
				<input class="button-primary" type="submit" name="cp-add-task" value="<?php _e( 'Submit', 'collabpress' ); ?>"/>
				<span class="spinner" style="float: left"></span></p>
			</p>
		</div>
	</div>
</div>

<script>
(function($) {
	// Uploading files
var file_frame;

	jQuery('.add-new-task').live('click', function( event ){

		event.preventDefault();

		// If the media frame already exists, reopen it.
		if ( file_frame ) {
			file_frame.open();
			return;
		}

		// Create the media frame.
		file_frame = wp.media.frames.file_frame = wp.media({
			title: 'Select a file',
			button: {
				text: 'Attach file to project',
			},
			multiple: false	// Set to true to allow multiple files to be selected
		});



		// When an image is selected, run a callback.
		file_frame.on( 'select', function() {
			// We set multiple to false so only get one image from the uploader
			attachment = file_frame.state().get('selection').first().toJSON();
 			$.post(
 				ajaxurl,
 				{
 					action: 'cp_attach_new_file',
 					nonce: jQuery( '#cp_add_new_file_nonce' ).val(),
 					data: {
 						project_id: <?php echo cp_get_project_id(); ?>,
 						attachment_id: attachment.id
 					}
				},
				function( response ) {
					location.reload();
				}
			);
			// Do something with attachment.id and/or attachment.url here
		});

		// Finally, open the modal
		file_frame.open();

	});
})(jQuery);
</script>