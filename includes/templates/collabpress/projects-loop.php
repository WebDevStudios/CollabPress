<?php

/**
 * The Projects Loop template
 *
 * @package CollabPress
 * @subpackage CP BP
 * @since 1.2
 */

// Set up the pagination and sortable columns helpers
if ( !class_exists( 'BBG_CPT_Pag' ) )
	require_once( CP_PLUGIN_DIR . 'includes/tools/boones-pagination/boones-pagination.php' );

$pagination = new BBG_CPT_Pag;

if ( !class_exists( 'BBG_CPT_Sort' ) )
	require_once( CP_PLUGIN_DIR . 'includes/tools/boones-sortable-columns/boones-sortable-columns.php' );

$cols = array(
	array(
		'name'		=> 'title',
		'title'		=> __( 'Project Name', 'collabpress' ),
		'is_default'	=> true
	),
	array(
		'name'		=> 'author',
		'title'		=> __( 'Project Creator', 'collabpress' )
	),
	array(
		'name'		=> 'date',
		'title'		=> __( 'Date Created', 'collabpress' )
	)
);
$sortable = new BBG_CPT_Sort( $cols );

// Get some pagination and sorting arguments to pass to the query
$query_args = array(
	'orderby'		=> $sortable->get_orderby,
	'order'			=> $sortable->get_order,
	'posts_per_page'	=> $pagination->get_per_page,
	'paged'			=> $pagination->get_paged,
);

// Fire the projects query
$cp_bp_projects = new WP_Query( cp_bp_projects_query_args( $query_args ) );

// Set up pagination with data from the query
$pagination->setup_query( $cp_bp_projects );

?>

<?php if ( $cp_bp_projects->have_posts() ) : ?>

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

	<table class="cb-projects-list">

	<thead>
		<?php if ( $sortable->have_columns() ) : while ( $sortable->have_columns() ) : $sortable->the_column() ?>
			<?php $sortable->the_column_th() ?>
		<?php endwhile; endif ?>
	</thead>

	<tbody>
	<?php while ( $cp_bp_projects->have_posts() ) : $cp_bp_projects->the_post() ?>

		<?php if ( cp_check_project_permissions( bp_loggedin_user_id(), get_the_ID() ) ) : ?>

		<tr>
			<td class="name">
				<a title="<?php the_title() ?>" href="<?php the_permalink() ?>"><?php the_title() ?></a>
			</td>

			<td class="author">
				<?php echo bp_core_fetch_avatar( array( 'item_id' => get_the_author_meta( 'ID' ) ) ) ?>
				<?php echo bp_core_get_userlink( get_the_author_meta( 'ID' ) ) ?>
			</td>

			<td class="date_created">
				<?php echo get_the_date() ?>
			</td>
		</tr>

		<?php endif ?>

	<?php endwhile ?>
	</tbody>

	</table>

<?php else : ?>

	<p><?php _e( 'There are no projects for this view.', 'collabpress' ) ?></p>

<?php endif ?>

<hr />

<?php if ( cp_check_permissions( 'settings_user_role' ) ) : ?>	
	<div class="cp-meta-box cp-new-project hide-on-load">
		<div title="Click to toggle" class="handlediv"><br></div>
		<h4 class="hndle"><span><?php _e( 'New Project', 'collabpress' ) ?></span></h4>
	
		<div class="inside">
			<?php cp_add_project() ?>
		</div>
	</div>
<?php endif ?>