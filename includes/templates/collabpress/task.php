<?php 

global $cp_page, $cp_bp_integration;

?>

<div class="cp-bp-header cp-task">
	<h3><?php printf( __( 'Task: <em>%s</em>', 'collabpress' ), $cp_bp_integration->get_current_item_task_name() ) ?></h3>
</div>

<div class="cp-meta-box cp-edit-task">
	<div title="Click to toggle" class="handlediv"><br></div>
	<h4 class="hndle"><span><?php _e( 'Edit Task', 'collabpress' ) ?></span></h4>
	
	<div class="inside">
		<?php $cp_page->cp_edit_task_meta() ?>
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
	
