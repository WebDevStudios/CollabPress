<div class="collabpress">
	<div class="overall-links" style="float: right">
		<?php cp_overall_links(); ?>
	</div>
	<div class="clear"></div>
	<div class="calendar">
		<h3><?php _e( 'Calendar', 'collabpress' ); ?></h3>
		<?php
		$month = ! empty( $_REQUEST['month'] ) ? $_REQUEST['month'] : NULL;
		$year = ! empty($_REQUEST['year'] ) ? $_REQUEST['year'] : NULL;
		cp_draw_calendar(
			array(
				'month' => $month,
				'year' => $year,
			)
		); ?>
	</div>
</div>