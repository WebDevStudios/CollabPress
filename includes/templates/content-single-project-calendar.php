<?php global $cp; ?>
<div class="collabpress">
	<?php cp_get_sidebar(); ?>
	<div class="collabpress-content" style="border: dashed 1px black; width: 75%; margin-left: 5px;min-height: 400px; padding: 5px; float: left">
		<div class="project-links" style="float: right;">
			<?php cp_project_links(); ?>
		</div>
		<?php echo cp_project_title(); ?>
		<div class="calendar">
			<h3>Calendar</h3>
			<?php 
			$month = ! empty( $_REQUEST['month'] ) ? $_REQUEST['month'] : NULL;
			$year = ! empty($_REQUEST['year'] ) ? $_REQUEST['year'] : NULL;
			cp_draw_calendar( array( 'project' => $cp->project->ID, 
				'month' => $month, 
				'year' => $year ) 
			); ?>
		</div>

	</div>
</div>