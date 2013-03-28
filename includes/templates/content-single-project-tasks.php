<?php global $cp;

function cp_compare_tasks_and_task_lists( $a, $b ) {
	if ( $a->menu_order == $b->menu_order )
		return 0;
	else
		return ( $a->menu_order < $b->menu_order ) ? 0 : 1;
}
/**
 * Returns the menu formatted to edit.
 *
 * @since 3.0.0
 *
 * @param string $menu_id The ID of the menu to format.
 * @return string|WP_Error $output The menu formatted to edit or error object on failure.
 */
function cp_output_project_nested_task_lists_and_tasks_html_for_sort( $project_id = 0 ) {
	$tasks_without_task_lists = get_posts( array(
		'post_type' => 'cp-tasks',
		'meta_query' => array(
			array(
				'key' => '_cp-project-id',
				'value' => $project_id,
			),
			array(
				'key' => '_cp-task-list-id',
				'value' => 0,
			),
		)
	) );
	$task_lists =  get_posts( array(
		'post_type' => array( 'cp-task-lists' ),
		'meta_query' => array(
			array(
				'key' => '_cp-project-id',
				'value' => $project_id,
			),
		)
	) );

	$tasks_and_task_lists = array_merge( $tasks_without_task_lists, $task_lists );
	uasort( $tasks_and_task_lists, 'cp_compare_tasks_and_task_lists' );
	$tasks_and_task_lists = array_values( $tasks_and_task_lists );

	$result = '<div id="menu-instructions" class="post-body-plain';
	$result .= ( ! empty($menu_items) ) ? ' menu-instructions-inactive">' : '">';
	if ( empty( $tasks_and_task_lists ) )
		$result .= '<p>' . __('Next, add your first task in this project.') . '</p>';
	$result .= '</div>';
	$result .= '<ul class="menu" id="menu-to-edit"> ';

	// Output the HTML for each item.
	// Hacked from Walker_Nav_Menu_Edit::start_el()

	foreach ( $tasks_and_task_lists as $item ) {
		ob_start();
		$item_id = $item->ID;
		$title = $item->post_title;
		$task_status = cp_get_task_status( $item->ID ); ?>
		<li id="menu-item-<?php echo $item_id; ?>" class="menu-item <?php echo $task_status; ?>">
			<dl class="menu-item-bar">
				<dt class="menu-item-handle">
					<?php if ( $item->post_type == 'cp-tasks' ) : ?>
					<input class="item-completed" type="checkbox" <?php checked( 'complete', $task_status ); ?>>
					<?php endif; ?>
					<span class="item-title">
						<?php if ( $item->post_type == 'cp-tasks' ) : // for now, only display a link for tasks. ?>
						<a href="<?php echo cp_get_task_permalink( $item_id ); ?>"><?php echo esc_html( $title ); ?></a>
						<?php else: // add a link to task lists if we make a template for them. ?>
						<?php echo esc_html( $title ); ?>
						<?php endif; ?>
					</span>
					<span class="item-controls">
						<a href="javascript:void(0);" class="delete-task" data-id="<?php echo $item_id; ?>">delete</a>
					</span>
				</dt>
			</dl>

			<div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">

				<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
				<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
				<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
				<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
				<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
				<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_type ); ?>" />
			</div><!-- .menu-item-settings-->
			<ul class="menu-item-transport"></ul>
		<?php
		$task_list_tasks = get_posts( array(
			'post_type' => 'cp-tasks',
			'meta_query' => array(
				array(
					'key' => '_cp-project-id',
					'value' => $project_id,
				),
				array(
					'key' => '_cp-task-list-id',
					'value' => $item_id,
				),
			),
			'orderby' => 'menu_order',
			'order' => 'ASC',
		) );
		if ( ! empty( $task_list_tasks ) ) {
			foreach ( $task_list_tasks as $task ) {
				$title = $task->post_title;
				$task_status = cp_get_task_status( $task->ID );
				 ?>
				<li id="menu-item-<?php echo $task->ID; ?>" class="menu-item menu-item-depth-1 <?php echo $task_status; ?>">
					<dl class="menu-item-bar">
						<dt class="menu-item-handle">
							<input class="item-completed" type="checkbox" <?php checked( 'complete', $task_status ); ?>>
							<span class="item-title"><a href="<?php echo cp_get_task_permalink( $task->ID ); ?>"><?php echo esc_html( $title ); ?></a><span>
							<span class="item-controls">
								<a href="javascript:void(0);" class="delete-task" data-id="<?php echo $task->ID; ?>">delete</a>
							</span>
						</dt>
					</dl>

					<div class="menu-item-settings" id="menu-item-settings-<?php echo $task->ID; ?>">

						<input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $task->ID; ?>]" value="<?php echo $task->ID; ?>" />
						<input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $task->ID; ?>]" value="<?php echo esc_attr( $task->object_id ); ?>" />
						<input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $task->ID; ?>]" value="<?php echo esc_attr( $task->object ); ?>" />
						<input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $task->ID; ?>]" value="<?php echo esc_attr( $task->menu_item_parent ); ?>" />
						<input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $task->ID; ?>]" value="<?php echo esc_attr( $task->menu_order ); ?>" />
						<input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $task->ID; ?>]" value="<?php echo esc_attr( $task->post_type ); ?>" />
					</div><!-- .menu-item-settings-->
					<ul class="menu-item-transport"></ul>
			<?php
			}
		}
		$result .= ob_get_clean();
	}

	$result .= ' </ul> ';
	echo $result;
}

?>
<div class="collabpress">
	<div class="project-links" style="float: right;">
		<?php cp_project_links(); ?>
	</div>
	<?php echo cp_project_title(); ?>
	<div class="tasks">
		<h3>Tasks</h3>
		<div class="toggle-view-completed-tasks">Toggle view completed tasks</div>
		<?php cp_output_project_nested_task_lists_and_tasks_html_for_sort( cp_get_project_id() ); ?>
		<?php if ( cp_check_permissions( 'settings_user_role' ) ) { ?>
		<div>
			<a href="#add_new_task_inline_content" class="add-new-task">+ Add new task</a>
		</div>
		<div>
			<a href="#add_new_task_list_inline_content" class="add-new-task-list">+ Add new task list</a>
		</div>
		<?php } ?>
	</div>
</div>
	<div style='display:none'>
		<div id='add_new_task_inline_content' style='padding:10px; background:#fff;'>
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
						<td><p><input name="cp-task-due" class="cp-task-due-date" id="cp-task-due-date" class="regular-text" type="text" value=<?php echo date('n/j/Y') ?> /></p></td>
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
		<div id='add_new_task_list_inline_content' style='padding:10px; background:#fff;'>
			<h2>Add Task List</h2>
			<input type="hidden" id="add_new_task_nonce" value="<?php echo wp_create_nonce( 'add_new_task' ); ?>">
			<input type="hidden" id="cp-project-task-list-id" value="<?php echo cp_get_project_id() ?>">
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row"><?php _e('Title: ', 'collabpress') ?></th>
						<td><fieldset><legend class="screen-reader-text"><span></span></legend>
							<p>
								<input id="cp-task-list"></textarea>
							</p>
						</fieldset></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e('Description: ', 'collabpress') ?></th>
						<td>
							<p>
								<textarea class="large-text code" id="cp-task-list-description" cols="30" rows="10"></textarea>
							</p>
						</td>
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
	$('.toggle-view-completed-tasks').click( function() {
		$('.menu-item.complete').hide( 500 );
	});

	$(document).ready(function() {
		jQuery('#cp-task-due-date').datepicker( {dateFormat: 'm/d/yy'} ); // init the datepicker
		// Init colorbox for New Task and New Task list modals
		$('.add-new-task, .add-new-task-list').colorbox(
			{
				inline: true,
				width: '50%'
			}
		);
	});

	// Delete task handler
	$('.delete-task').click(function(i, el) {
		var confirm_delete = confirm('Are you sure you want to delete this task?');

		if ( ! confirm_delete )
			return;

		var task_el = $(this);

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
				task_el.parents('.menu-item').hide();
			}
		);
	});

	// On New task form submit send AJAX query
	$('#add_new_task_inline_content .submit').click(function() {

		var data = {
			post_title: $('#cp-task').val(),
			project_id: $('#cp-project-id').val(),
			task_description: $('#cp-task').val(),
			task_due_date: $('#cp-task-due-date').val(),
			task_assigned_to: $('#cp-task-assign').val(),
			task_priority: $('#cp-task-priority').val(),
			send_email_notification: $('#notify').val(),
			collabpress_ajax_request_origin: '<?php echo ( is_admin() ? 'admin' : 'frontend' ); ?>',
		};
		data.nonce = $('#add_new_task_nonce').val();
		$('#add_new_task_inline_content .spinner').show();
		$.post(
			ajaxurl,
			{
				action: 'cp_add_new_task',
				data: data
			}, function( response ) {
				$('#add_new_task_inline_content .spinner').hide();
				window.location = response.data.redirect;
			}
		);
	});

	// On New task list form submit send AJAX query
	$('#add_new_task_list_inline_content .submit').click(function() {

		var data = {
			post_title: $('#cp-task-list').val(),
			project_id: $('#cp-project-task-list-id').val(),
			task_list_description: $('#cp-task-list-description').val(),
			collabpress_ajax_request_origin: '<?php echo ( is_admin() ? 'admin' : 'frontend' ); ?>',
		};

		// todo: fix nonces
		// data.nonce = $('#add_new_task_list_nonce').val();
		$('#add_new_task_list_inline_content .spinner').show();
		$.post(
			ajaxurl,
			{
				action: 'cp_add_new_task_list',
				data: data
			}, function( response ) {
				$('#add_new_task_list_inline_content .spinner').hide();
				window.location = response.data.redirect;
			}
		);
	});

	// Handle checkbox change for a task
	$('.menu-item input.item-completed').change( function(event) {
		var data = {
			task_id: $(this)
				.parents( '.menu-item-bar')
				.siblings('.menu-item-settings')
				.children('.menu-item-data-db-id')
				.val(),
			task_status: ( $(this).is(':checked') ? 'complete' : 'open' ),
			collabpress_ajax_request_origin: '<?php echo ( is_admin() ? 'admin' : 'frontend' ); ?>',
		};
		if ( $(this).is(':checked') )
			$(this).parents( '.menu-item' ).addClass( 'complete' );
		else
			$(this).parents( '.menu-item' ).removeClass( 'complete' );

		$.post(
			ajaxurl,
			{
				action: 'cp_update_task_status',
				data: data
			}, function( response ) {}
		);
	});
})(jQuery);
</script>