<div class="collabpress">
	<div class="collabpress-sidebar" style="border: dashed 1px black; width: 20%; max-width: 200px; min-height: 400px; padding: 5px; float: left">
		Sidebar
	</div>
	<div class="collabpress-content" style="border: dashed 1px black; width: 75%; margin-left: 5px;min-height: 400px; padding: 5px; float: left">
		<?php echo cp_project_title(); ?>
		<?php if ( cp_has_tasks() ) : ?>
			<?php while( cp_tasks() ) : cp_the_task(); ?>
				<div class="collabpress-task">
					<?php the_title(); ?>

				</div>
			<?php endwhile; ?>
		<?php endif; ?>
	</div>
</div>