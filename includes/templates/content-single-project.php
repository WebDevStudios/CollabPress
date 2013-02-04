<div class="collabpress">
	<?php cp_get_sidebar(); ?>
	<div class="collabpress-content" style="border: dashed 1px black; width: 75%; margin-left: 5px;min-height: 400px; padding: 5px; float: left">
		<div class="project-links" style="float: right;">
			<?php cp_project_links(); ?>
		</div>
		<?php cp_project_title(); ?>
		<div class="users">
			<h3>Users</h3>
			<?php foreach ( cp_get_project_users() as $user ) {
				echo get_avatar( $user->ID );
			} ?>
			<a href="<?php cp_project_users_permalink(); ?>">View all users</a>
		</div>
		<div class="tasks">
			<h3>Tasks</h3>
			<?php if ( cp_has_tasks() ) : ?>
				<?php while( cp_tasks() ) : cp_the_task(); ?>
					<div class="collabpress-task">
						<a href="<?php cp_task_permalink(); ?>"><?php the_title(); ?></a>
					</div>
				<?php endwhile; ?>
			<?php endif; ?>
			<a href="<?php cp_project_tasks_permalink(); ?>"> View all tasks</a>
		</div>
		<div class="files">
			<h3>Files</h3>
			<a href="<?php cp_project_files_permalink(); ?>">View all files</a>
		</div>

	</div>
</div>