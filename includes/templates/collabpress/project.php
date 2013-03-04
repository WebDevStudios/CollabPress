<?php

global $cp_page;

?>

<div class="cp-bp-header cp-project">
	<h3><?php printf( __( 'Project: <em>%s</em>', 'collabpress' ), esc_html( cp_bp()->get_current_item_project_name() ) ) ?></h3>
	<p class="description"><?php echo esc_html( cp_bp()->get_current_item_project_description() ) ?></p>
</div>

<div class="cp-meta-box cp-add-task-list hide-on-load">
	<div title="Click to toggle" class="handlediv"><br></div>
	<h4 class="hndle"><span><?php _e( 'Add Task List', 'collabpress' ) ?></span></h4>

	<div class="inside">
		<?php $cp_page->cp_add_task_list_meta() ?>
	</div>
</div>

<div class="cp-meta-box cp-project-overview">
	<div title="Click to toggle" class="handlediv"><br></div>
	<h4 class="hndle"><span><?php _e( 'Project Overview', 'collabpress' ) ?></span></h4>

	<div class="inside">
		<?php $cp_page->cp_task_list_meta() ?>
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


<?php if ( cp_check_permissions( 'settings_user_role' ) ) : ?>
	<div class="cp-meta-box cp-edit-project hide-on-load">
		<div title="Click to toggle" class="handlediv"><br></div>
		<h4 class="hndle"><span><?php _e( 'Edit Project', 'collabpress' ) ?></span></h4>

		<div class="inside">
			<?php $cp_page->cp_edit_project_meta() ?>
		</div>
	</div>
<?php endif ?>
