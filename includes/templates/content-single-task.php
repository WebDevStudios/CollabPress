<div class="collabpress">
	<div class="collabpress-task">
		<div class="project-links" style="float: right;">
			<?php cp_project_links(); ?>
		</div>
		<?php cp_project_title(); ?>
		<?php $task_status = cp_get_task_status( cp_get_the_task_ID() ); ?>
		<?php $title_class = $task_status; ?>
		<h1 class="<?php echo $title_class; ?>"><input type="checkbox" <?php checked( 'complete', $task_status ); ?>><?php echo cp_get_task_title(); ?></h1>
		<a class="edit-task" href="javascript:void();">Edit</a><BR>
		<?php if ( $due_date = cp_task_due_date() ) {
			echo 'Due date: ' . $due_date . '<BR>';
		} ?>
		<?php if ( $priority = cp_task_priority() ) {
			echo 'Priority: ' . $priority . '<BR>';
		} ?>
		<?php cp_task_comments(); ?>
	</div>
	<div style='display:none'>
		<div id='inline_content' style='padding:10px; background:#fff;'>
			<h2>Edit Task</h2>
			<input type="hidden" id="edit_task_nonce" value="<?php echo wp_create_nonce( 'edit_task' ); ?>" />
			<input type="hidden" id="cp-project-id" value="<?php echo cp_get_project_id() ?>" />
			<input type="hidden" id="cp-task-id" value="<?php echo cp_get_task_id() ?>" />
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
				<input class="button-primary" type="submit" class="add-task-button" name="cp-add-task" value="<?php _e( 'Submit', 'collabpress' ); ?>"/>
				<span class="spinner" style="float: left"></span></p>
			</p>
		</div>
	</div>
</div>
<script>
(function($) {
	$(document).ready(function() {
		$('.collabpress form').submit(function() {
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
					data: data
				}, function( response ) {
					console.log( response );
					if ( response.data.redirect ) {
						window.location = response.data.redirect;
					}
				}
			);
			return false;
		});
	});
	$('.delete-comment-link').click( function() {
		if ( window.confirm( '<?php _e( 'Are you sure you want to delete this comment?', 'collabpress' ); ?>' ) ) {
			var that = this;
			var data = {
				comment_id: $(this).data('comment-id'),
				collabpress_ajax_request_origin: '<?php echo ( is_admin() ? 'admin' : 'frontend' ); ?>',
			};
			$.post(
				ajaxurl,
				{
					action: 'cp_delete_comment',
					data: data
				}, function( response ) {
					if ( response.success ) {
						jQuery(that).parents('.cp_task_comment').hide();
					}
				});
		}
	});
})(jQuery);
</script>
