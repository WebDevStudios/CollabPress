<div class="collabpress">

	<div class="tabbed-list">

		<?php cp_project_links(); ?>

	</div>

	<div class="project-breadcrumb">
		<h3 class="project-title"><?php cp_project_title(); ?></h3>
		<h3>&nbsp;»&nbsp;<?php _e( 'Calendar', 'collabpress' ); ?></h3>
	</div>
	<div class="calendar">
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
