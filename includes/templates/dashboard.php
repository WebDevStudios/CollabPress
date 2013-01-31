<div class="collabpress">
	<div class="collabpress-sidebar" style="border: dashed 1px black; width: 20%; max-width: 200px; min-height: 400px; padding: 5px; float: left">
		Sidebar
	</div>
	<div class="collabpress-content" style="border: dashed 1px black; width: 75%; margin-left: 5px;min-height: 400px; padding: 5px; float: left">
		<?php foreach( cp_get_projects() as $project ) { ?>
			<div class="collabpress-project">
				<a href="<?php cp_project_permalink( $project->ID ); ?>"><?php echo $project->post_title; ?></a>
			</div>
		<?php } ?>
	</div>
</div>