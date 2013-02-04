<?php global $cp; ?>
<div class="collabpress">
	<?php cp_get_sidebar(); ?>
	<div class="collabpress-content" style="border: dashed 1px black; width: 75%; margin-left: 5px;min-height: 400px; padding: 5px; float: left">
		<div class="project-links" style="float: right;">
			<?php cp_project_links(); ?>
		</div>
		<?php echo cp_project_title(); ?>
		<div class="tasks">
			<h3>Tasks</h3>
			<?php if ( cp_has_tasks() ) : ?>
				<?php while( cp_tasks() ) : cp_the_task(); ?>
					<div class="collabpress-task">
						<a href="<?php cp_task_permalink(); ?>"><?php the_title(); ?></a> <a href="javascript:void(0);" class="delete-task" data-id="<?php echo get_the_ID(); ?>">delete</a>
					</div>
				<?php endwhile; ?>
			<?php endif; ?>
			<a href="#inline_content" class="add-new-task">Add new task</a>
		</div>
	</div>
	<div style='display:none'>
		<div id='inline_content' style='padding:10px; background:#fff;'>
			<h2>Add Task</h2>
			<input type="hidden" id="add_new_task_nonce" value="<?php echo wp_create_nonce( 'add_new_task' ); ?>">
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
	$('.delete-task').click(function(i, el) {
		var confirm_delete = confirm('Are you sure you want to delete this task?');
		var task_el = $(this);
		if ( ! confirm_delete )
			return;

		var data = {
			task_id: task_el.data( 'id' )
		};

		// todo: add nonce
		$.post(
			ajaxurl,
			{
				action: 'cp_delete_task',
				data: data
			},
			function( response ) {
				task_el.parent('.collabpress-task').hide();
			}
		);
	});
	$(document).ready(function() {
		$('.add-new-task').colorbox(
			{
				inline: true, 
				width: '50%',
				onLoad: function() {}
			}
		);
	});
	$('.submit').click(function() {

		var data = { 
			post_title: $('#cp-task').val(),
			project_id: $('#cp-project-id').val(),
			task_description: $('#cp-task').val(),
			task_due_date: $('#cp-task-due-date').val(),
			task_assigned_to: $('#cp-task-assign').val(),
			task_priority: $('#cp-task-priority').val(),
			send_email_notification: $('#notify').val(),
		};
		data.nonce = $('#add_new_task_nonce').val();
		$('#inline_content .spinner').show();
		$.post(
			ajaxurl,
			{
				action: 'cp_add_new_task',
				data: data
			}, function( response ) {
				console.log( response );
				$('#inline_content .spinner').hide();
				window.location = response.data.redirect;
			}
		);
	});
})(jQuery);
</script>