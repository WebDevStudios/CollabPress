<div class="collabpress">

	<div class="tabbed-list">

		<?php cp_project_links(); ?>

	</div>

	<div class="project-title">
		<h3><?php cp_project_title(); ?></h3>
	</div>
	<div class="calendar">
		<h3><?php _e( 'Calendar', 'collabress' ); ?></h3>
		<?php
		global $cp;
		$month = ! empty( $_REQUEST['month'] ) ? $_REQUEST['month'] : NULL;
		$year = ! empty($_REQUEST['year'] ) ? $_REQUEST['year'] : NULL;
		cp_draw_calendar( array( 'project' => $cp->project->ID,
			'month' => $month,
			'year' => $year )
		); ?>
	</div>
</div>