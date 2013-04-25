<div class="collabpress">
	<div class="collabpress-task">
		<?php $task_status = cp_get_task_status( cp_get_the_task_ID() ); ?>
		<?php $title_class = $task_status; ?>
		<h1 id="task-title" class="<?php echo $title_class; ?>"><input id="item-completed" type="checkbox" <?php checked( 'complete', $task_status ); ?>><?php echo cp_get_task_title(); ?></h1>
		<input type="hidden" id="item-complete-status-change-nonce_<?php echo cp_get_task_id(); ?>" value="<?php echo wp_create_nonce( 'item-complete-status-change_' . cp_get_task_id() ) ?>" />
		<a class="edit-task" href="#edit_task_inline_content">Edit</a><BR>
		<?php if ( $due_date = cp_get_the_task_due_date() ) {
			echo '<div>Due date: ' . $due_date . '</div>';
		} ?>
		<?php if ( $priority = cp_get_the_task_priority() ) {
			echo '<div>Priority: ' . $priority . '</div>';
		} ?>
		<?php
		$user_assigned = cp_get_user_assigned_to_task();
		echo '<div>Assigned to: ' . get_avatar( $user_assigned->ID ) . '</div>'; ?>
		<?php cp_task_comments(); ?>
	</div>
	<div style='display:none'>
		<div id='edit_task_inline_content' style='padding:10px; background:#fff;'>
			<form id="edit-task-form">
				<h2><?php _e( 'Edit Task', 'collabpress' ); ?></h2>
				<input type="hidden" id="edit_task_nonce" value="<?php echo wp_create_nonce( 'edit-task' ); ?>" />
				<input type="hidden" id="cp-project-id" value="<?php echo cp_get_project_id() ?>" />
				<input type="hidden" id="cp-task-id" value="<?php echo cp_get_task_id() ?>" />
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><?php _e('Description: ', 'collabpress') ?></th>
							<td><fieldset><legend class="screen-reader-text"><span></span></legend>
								<p><label for="cp-task"></label></p>
								<p>
									<textarea class="large-text code" id="cp-task" cols="30" rows="10" name="cp-task"><?php echo cp_get_the_task_description(); ?></textarea>
								</p>
							</fieldset></td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="cp-task-due"><?php _e('Due: ', 'collabpress') ?></label></th>
							<td><p><input name="cp-task-due" class="cp-task-due-date" id="cp-task-due-date" class="regular-text" type="text" value=<?php echo cp_get_the_task_due_date(); ?> /></p></td>
						</tr>
						<tr valign="top">
							<th scope="row"><label for="cp-task-assign"><?php _e('Assigned to: ', 'collabpress') ?></label></th>
							<td>
								<p>
			                        <?php
									$user_list = '<select name="cp-task-assign" id="cp-task-assign">';
									foreach ( cp_get_project_users() as $wp_user )
										$user_list .= '<option ' . selected( $user_assigned->ID, $wp_user->ID, false ) . ' value="' . $wp_user->ID . '">' . $wp_user->user_login . '</option>';
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
									<option <?php selected(cp_get_the_task_priority(), 'Urgent' ); ?> value="Urgent"><?php _e( 'Urgent', 'collabpress' ); ?></option>
									<option <?php selected(cp_get_the_task_priority(), 'High' ); ?> value="High"><?php _e( 'High', 'collabpress' ); ?></option>
									<option <?php selected(cp_get_the_task_priority(), 'Normal' ); ?> value="Normal"><?php _e( 'Normal', 'collabpress' ); ?></option>
									<option <?php selected(cp_get_the_task_priority(), 'Low' ); ?> value="Low"><?php _e( 'Low', 'collabpress' ); ?></option>
									<option <?php selected(cp_get_the_task_priority(), 'Very Low' ); ?> value="Very Low"><?php _e( 'Very Low', 'collabpress' ); ?></option>
									<option <?php selected(cp_get_the_task_priority(), 'None' ); ?> value="None" selected="selected"><?php _e( 'None', 'collabpress' ); ?></option>
								</select>
							</td>
						</tr>
					</tbody>
				</table>
				<p class="submit">
					<input class="button-primary" type="submit" class="add-task-button" name="cp-add-task" value="<?php _e( 'Submit', 'collabpress' ); ?>"/>
					<span class="spinner" style="float: left"></span></p>
				</p>
			</form>
		</div>
	</div>
</div>
<script>
(function($) {
	$(document).ready(function() {
		jQuery('#cp-task-due-date').datepicker( {dateFormat: 'm/d/yy'} ); // init the datepicker

		// Handle checkbox change for a task
		$('#item-completed').change( function(event) {
			var task_id = $('#cp-task-id').val();
			var data = {
				task_id: task_id,
				task_status: ( $(this).is(':checked') ? 'complete' : 'open' ),
				collabpress_ajax_request_origin: '<?php echo ( is_admin() ? 'admin' : 'frontend' ); ?>',
			};
			var nonce = jQuery( '#item-complete-status-change-nonce_' + task_id ).val();

			if ( $(this).is(':checked') )
				$('#task-title').css('text-decoration', 'line-through' );
			else
				$('#task-title').css('text-decoration', 'none' );

			$.post(
				ajaxurl,
				{
					action: 'cp_update_task_status',
					data: data,
					nonce: nonce
				}, function( response ) { }
			);
		});

		// Init colorbox on edit task modal
		$('.edit-task').colorbox(
			{
				inline: true,
				width: '50%'
			}
		);

		// On Edit task form submit send AJAX request
		$('#edit-task-form').submit( function() {
			var data = {
				ID: $('#cp-task-id').val(),
				post_title: $('#cp-task').val(),
				task_assigned_to: $('#cp-task-assign').val(),
				priority: $('#cp-task-priority').val(),
				task_due_date: $('#cp-task-due-date').val(),
				collabpress_ajax_request_origin: '<?php echo ( is_admin() ? 'admin' : 'frontend' ); ?>'
			};
			var nonce = $( '#edit_task_nonce' ).val();
			$.post(
				ajaxurl,
				{
					action: 'cp_edit_task',
					data: data,
					nonce: nonce
				}, function( response ) {
					if ( response.data.redirect )
						window.location = response.data.redirect;
				}
			);
			return false;
		});

		// On comment form submit send AJAX request
		$('.collabpress #task-comment-form').submit(function() {
			var data = {
				task_id: $('#cp-task-id').val(),
				user_id: <?php echo wp_get_current_user()->ID; ?>,
				collabpress_ajax_request_origin: '<?php echo ( is_admin() ? 'admin' : 'frontend' ); ?>',
				comment_content: $('#cp-comment-content').val()
			};
			$.post(
				ajaxurl,
				{
					action: 'cp_add_comment_to_task',
					data: data,
					nonce: jQuery( '#add_task_comment_nonce' ).val()
				}, function( response ) {
					if ( response.data.redirect )
						window.location = response.data.redirect;
				}
			);
			return false;
		});
			// On comment delete click send AJAX request
		$('.delete-comment-link').click( function() {

			if ( window.confirm( '<?php _e( 'Are you sure you want to delete this comment?', 'collabpress' ); ?>' ) ) {
				var that = this;

				var comment_id = $(this).data('comment-id'),
					data = {
						comment_id: comment_id,
						collabpress_ajax_request_origin: '<?php echo ( is_admin() ? 'admin' : 'frontend' ); ?>'
					},
					nonce = jQuery( '#delete_comment_nonce_' + comment_id ).val();

				$.post(
					ajaxurl,
					{
						action: 'cp_delete_comment',
						data: data,
						nonce: nonce
					}, function( response ) {
						if ( response.success )
							jQuery(that).parents('.cp_task_comment').hide();
					}
				);
			}
		});
	});
})(jQuery);
</script>
