<?php

// Set up the pagination and sortable columns helpers
if ( !class_exists( 'BBG_CPT_Pag' ) )
	require_once( CP_PLUGIN_DIR . 'includes/tools/boones-pagination/boones-pagination.php' );

$pagination = new BBG_CPT_Pag;

if ( !class_exists( 'BBG_CPT_Sort' ) )
	require_once( CP_PLUGIN_DIR . 'includes/tools/boones-sortable-columns/boones-sortable-columns.php' );

$cols = array(
	array(
		'name'  => 'task',
		'title' => __( 'Task', 'collabpress' ),
	),
	array(
		'name'  => 'project',
		'title' => __( 'Project', 'collabpress' ),
		'is_sortable' => false,
	),
	array(
		'name'  => 'status',
		'title' => __( 'Status', 'collabpress' )
	),
	array(
		'name'  => 'due_date',
		'title' => __( 'Due Date', 'collabpress' ),
		'is_sortable' => false,
	)
);
$sortable = new BBG_CPT_Sort( $cols );

// Get some pagination and sorting arguments to pass to the query
$query_args = array(
	'posts_only'       => false,
	'assigned_user_id' => bp_displayed_user_id(),
	'orderby'          => $sortable->get_orderby,
	'order'            => $sortable->get_order,
	'posts_per_page'   => $pagination->get_per_page,
	'paged'            => $pagination->get_paged,
);

// Fire the projects query
$tasks = cp_get_tasks( $query_args );

// Set up pagination with data from the query
$pagination->setup_query( $tasks );

?>

<?php if ( $tasks->have_posts() ) : ?>

	<div class="tablenav top">
		<div class="tablenav-pages unconfirmed-pagination">
			<div class="currently-viewing alignleft">
				<?php $pagination->currently_viewing_text() ?>
			</div>

			<div class="pag-links alignright">
				<?php $pagination->paginate_links() ?>
			</div>
		</div>
	</div>

	<table class="cb-tasks-list">

	<thead>
		<?php if ( $sortable->have_columns() ) : while ( $sortable->have_columns() ) : $sortable->the_column() ?>
			<?php $sortable->the_column_th() ?>
		<?php endwhile; endif ?>
	</thead>

	<tbody>
	<?php while ( $tasks->have_posts() ) : $tasks->the_post() ?>

		<?php $project_id = cp_get_task_project_id( get_the_ID() ) ?>

		<?php if ( cp_check_project_permissions( bp_loggedin_user_id(), $project_id ) ) : ?>

		<tr>
			<td class="name">
				<a title="<?php the_title() ?>" href="<?php the_permalink() ?>"><?php the_title() ?></a>
			</td>

			<td class="project">
				<?php $project = get_post( $project_id ) ?>
				<a href="<?php echo get_permalink( $project_id ) ?>"><?php echo $project->post_title ?></a>
			</td>

			<td class="status">
				<?php echo get_post_meta( get_the_ID(), '_cp-task-status', true ) ?>
			</td>

			<td class="due_date">
				<?php echo get_post_meta( get_the_ID(), '_cp-task-due', true ) ?>
			</td>
		</tr>

		<?php endif ?>

	<?php endwhile ?>
	</tbody>

	</table>

<?php else : ?>

	<p><?php _e( 'There are no projects for this view.', 'collabpress' ) ?></p>

<?php endif ?>


<ul class="collabpress cp-tasks">
<?php foreach ( cp_get_tasks( array( 'assigned_user_id' => bp_displayed_user_id() ) ) as $task ) : ?>


<?php endforeach; ?>
</ul>
