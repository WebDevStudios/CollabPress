<div class="collabpress">
	<?php cp_get_sidebar(); ?>
	<div class="collabpress-content" style="border: dashed 1px black; width: 75%; margin-left: 5px;min-height: 400px; padding: 5px; float: left">
		<div class="collabpress-task">
			<div class="project-links" style="float: right;">
				<?php cp_project_links(); ?>
			</div>
			<?php cp_project_title(); ?>
			<?php cp_task_title(); ?><a href="javascript:void();">Edit</a><BR>
			<?php if ( $due_date = cp_task_due_date() ) {
				echo 'Due date: ' . $due_date . '<BR>';
			} ?>
			<?php if ( $priority = cp_task_priority() ) {
				echo 'Priority: ' . $priority . '<BR>';
			} ?>
			<?php cp_task_comments(); ?>
		</div> 
	</div>
</div>