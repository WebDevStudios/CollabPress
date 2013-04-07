<div class="collabpress">
	<div class="project-links" style="float: right;">
		<?php cp_project_links(); ?>
	</div>
	<?php cp_project_title(); ?>
	<a class="edit-project" href="#edit_project_inline_content">Edit Project</a>
	<h3>Tasks</h3>
	<div class="tasks">
	<?php if ( cp_has_tasks() ) : ?>
		<?php while( cp_tasks() ) : cp_the_task(); ?>
			<div class="collabpress-task">
				<a href="<?php the_permalink() ?>"><?php the_title() ?></a>
			</div>
		<?php endwhile; ?>
	<?php endif; ?>
	</div>
	<a class="view-all-link" href="<?php cp_project_tasks_permalink(); ?>"> View all tasks</a>
	<h3>Users</h3>
	<div class="users">
	<?php foreach ( cp_get_project_users() as $user ) {
		echo get_avatar( $user->ID );
	} ?>
	</div>
	<a class="view-all-link" href="<?php cp_project_users_permalink(); ?>">View all users</a>
	<h3>Files</h3>
	<div class="files">
	<?php if ( cp_has_files() ) : ?>
		<?php while( cp_files() ) : cp_the_file(); ?>
			<div class="collabpress-task">
				<a href="<?php echo wp_get_attachment_url( get_the_ID() ); ?>"><?php the_title(); ?></a>
			</div>
		<?php endwhile; ?>
	<?php endif; ?>
	</div>
	<a class="view-all-link" href="<?php cp_project_files_permalink(); ?>">View all files</a>
</div>
<div style='display:none'>
	<div id='edit_project_inline_content' style='padding:10px; background:#fff;'>
		<form id="edit-project-form">
			<h2>Edit Project</h2>
			<input type="hidden" id="cp_edit_project_nonce" value="<?php echo wp_create_nonce( 'edit-project' ); ?>">
			<input type="hidden" id="cp_delete_project_nonce" value="<?php echo wp_create_nonce( 'delete-project' ); ?>">
			<input type="hidden" id="cp-project-id" value="<?php echo cp_get_project_id() ?>" />
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e('Project Title: ', 'collabpress') ?></th>
						<td><fieldset><legend class="screen-reader-text"><span></span></legend>
							<p><label for="cp-task"></label></p>
							<p>
								<textarea class="large-text code" id="cp-project-title" cols="30" rows="10" name="cp-project-title"><?php echo cp_get_the_project_title(); ?></textarea>
							</p>
						</fieldset></td>
					</tr>
				</tbody>
			</table>
			<p class="submit">
				<input class="button-primary" type="submit" class="edit-project-button" value="<?php _e( 'Submit', 'collabpress' ); ?>"/>
				<input class="button-primary" type="button" id="delete-project-button" value="<?php _e( 'Delete Project', 'collabpress' ); ?>"/>
				<span class="spinner" style="float: left"></span></p>
			</p>
		</form>
	</div>
</div>
<script>
(function($) {
// Init colorbox on edit task modal
	$('.edit-project').colorbox(
		{
			inline: true,
			width: '50%'
		}
	);

	$('#edit-project-form').submit( function() {
		var data = {
			ID: $('#cp-project-id').val(),
			post_title: $('#cp-project-title').val(),
			collabpress_ajax_request_origin: '<?php echo ( is_admin() ? 'admin' : 'frontend' ); ?>'
		};
		$.post(
			ajaxurl,
			{
				action: 'cp_edit_project',
				data: data,
				nonce: jQuery( '#cp_edit_project_nonce' ).val()
			}, function( response ) {
				if ( response.data.redirect )
					window.location = response.data.redirect;
			}
		);
		return false;
	});
	$('#delete-project-button').click( function() {
		if ( ! confirm( '<?php _e('Are you sure you want to delete this project?' ); ?>' ) )
			return false;
		var data = {
			ID: $('#cp-project-id').val(),
			collabpress_ajax_request_origin: '<?php echo ( is_admin() ? 'admin' : 'frontend' ); ?>'
		};
		$.post(
			ajaxurl,
			{
				action: 'cp_delete_project',
				data: data,
				nonce: jQuery( '#cp_delete_project_nonce' ).val()
			}, function( response ) {
				if ( response.data.redirect )
					window.location = response.data.redirect;
			}
		);
		return false;
	});
})(jQuery);

</script>