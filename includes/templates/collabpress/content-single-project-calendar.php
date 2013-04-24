<?php global $cp; ?>
<div class="collabpress">
	<div class="project-links" style="float: right;">
		<?php cp_project_links(); ?>
	</div>
	<?php echo cp_project_title(); ?>
	<div class="calendar">
		<h3><?php _e( 'Calendar', 'collabress' ); ?></h3>
		<?php
		$month = ! empty( $_REQUEST['month'] ) ? $_REQUEST['month'] : NULL;
		$year = ! empty($_REQUEST['year'] ) ? $_REQUEST['year'] : NULL;
		cp_draw_calendar( array( 'project' => $cp->project->ID,
			'month' => $month,
			'year' => $year )
		); ?>
	</div>
</div>