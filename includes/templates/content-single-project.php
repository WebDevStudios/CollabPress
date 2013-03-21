<div class="collabpress">
	<div class="project-links" style="float: right;">
		<?php cp_project_links(); ?>
	</div>
	<?php cp_project_title(); ?>
		<h3>Users</h3>
		<div class="users">
		<?php foreach ( cp_get_project_users() as $user ) {
			echo get_avatar( $user->ID );
		} ?>
		</div>
		<a class="view-all-link" href="<?php cp_project_users_permalink(); ?>">View all users</a>
		<h3>Tasks</h3>
		<div class="tasks">
		<?php if ( cp_has_tasks() ) : ?>
			<?php while( cp_tasks() ) : cp_the_task(); ?>
				<div class="collabpress-task">
					<a href="<?php cp_task_permalink(); ?>"><?php the_title(); ?></a>
				</div>
			<?php endwhile; ?>
		<?php endif; ?>
		</div>
		<a class="view-all-link" href="<?php cp_project_tasks_permalink(); ?>"> View all tasks</a>
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