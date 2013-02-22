<?php

global $cp_page;

?>

<div class="cp-bp-header cp-task">
	<h3><?php printf( __( 'Task List: <em>%s</em>', 'collabpress' ), esc_html( cp_bp()->get_current_item_task_list_name() ) ) ?></h3>
	<p class="description"><?php echo esc_html( cp_bp()->get_current_item_task_list_description() ) ?></p>
</div>

<div class="cp-meta-box cp-add-task hide-on-load">
	<div title="Click to toggle" class="handlediv"><br></div>
	<h4 class="hndle"><span><?php _e( 'Add Task', 'collabpress' ) ?></span></h4>

	<div class="inside">
		<?php $cp_page->cp_add_task_meta() ?>
	</div>
</div>

<div class="cp-meta-box cp-edit-task hide-on-load">
	<div title="Click to toggle" class="handlediv"><br></div>
	<h4 class="hndle"><span><?php _e( 'Edit Task List', 'collabpress' ) ?></span></h4>

	<div class="inside">
		<?php $cp_page->cp_edit_task_list_meta() ?>
	</div>
</div>

<div class="cp-meta-box cp-task-list-overview">

	<div title="Click to toggle" class="handlediv"><br></div>
	<h4 class="hndle"><span><?php _e( 'Task List Overview', 'collabpress' ) ?></span></h4>

	<div class="inside">
		<?php $cp_page->cp_task_meta() ?>
	</div>
</div>

<?php /* Files are disabled for the moment */ ?>
<?php /*
<div class="cp-meta-box cp-files hide-on-load">
	<div title="Click to toggle" class="handlediv"><br></div>
	<h4 class="hndle"><span><?php _e( 'Files', 'collabpress' ) ?></span></h4>

	<div class="inside">
		<?php $cp_page->cp_files_meta() ?>
	</div>
</div>
*/ ?>

