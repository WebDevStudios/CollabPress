<?php

/**
 * BuddyPress integration functions
 *
 * @package CollabPress
 * @subpackage CP BP
 * @since 1.2
 */


/**
 * The main CP-BP integration class
 *
 * @package CollabPress
 * @subpackage CP BP
 * @since 1.2
 */
class CP_BP_Integration extends BP_Component {
	var $item_cp_slug;

	var $current_view;
	var $current_item;
	var $current_item_id;
	var $current_item_obj;
	var $current_item_ancestry;

	/**
	 * Constructor
	 *
	 * @package CollabPress
	 * @subpackage CP BP
	 * @since 1.2
	 */
	function __construct() {
		global $bp;

		parent::start(
			'collabpress',
			__( 'CollabPress', 'collabpress' ),
			CP_PLUGIN_DIR
		);

		// Register ourselves as an active BP component
		$bp->active_components['collabpress'] = '1';

		add_filter( 'bp_get_template_stack', array( $this, 'add_cp_to_template_stack' ) );

		// Register BP-specific taxonomies
		$this->register_taxonomies();

		// Set up the CP query
		add_action( 'cp_bp_setup_item', array( &$this, 'do_cp_query' ), 5 );

		// Load the notification framework
		require_once( CP_PLUGIN_DIR . 'includes/cp-bp-notifications.php' );

		// Load the Groups integration, if active
		if ( bp_is_active( 'groups' ) ) {
			require_once( CP_PLUGIN_DIR . 'includes/cp-bp-groups.php' );
			bp_register_group_extension( 'CP_BP_Group_Extension' );
		}

		// Add the admin menus
		add_action( 'cp_after_advanced_settings', array( &$this, 'render_settings' ) );

		// Todo: this MUST check to see whether we're in a BP context!!
		add_filter( 'cp_calendar_permalink', array( $this, 'filter_cp_calendar_permalink' ), 10, 4 );
		add_filter( 'post_type_link', array( &$this, 'filter_permalinks' ), 10, 4 );
		add_filter( 'cp_task_list_link', array( &$this, 'filter_item_link' ), 10, 3 );
		add_filter( 'cp_task_link', array( &$this, 'filter_item_link' ), 10, 3 );

		add_action( 'cp_project_added', array( &$this, 'mark_post_in_group' ) );

		add_action( 'wp_print_styles', array( &$this, 'enqueue_styles' ) );
	}

	/**
	 * Implementation of BP_Component::setup_globals()
	 *
	 * @since 1.3
	 */
	public function setup_globals() {
		$globals = array(
			'slug'                  => 'collabpress',
			'root_slug'             => 'collabpress',
			'has_directory'         => false,
		);
		parent::setup_globals( $globals );
	}

	/**
	 * Set up navigation
	 *
	 * @since 1.3
	 */
	function setup_nav() {
		// Add 'Example' to the main navigation
		$main_nav = array(
			'name'                    => __( 'Projects', 'collabpress' ),
			'slug'                    => $this->slug,
			'position'                => 44,
			'screen_function'         => array( $this, 'template_loader' ),
			'default_subnav_slug'     => 'tasks',
			'show_for_displayed_user' => array( $this, 'show_tab_for_current_user' ),
		);

		$projects_link = trailingslashit( bp_loggedin_user_domain() . $this->slug );

		// Add a few subnav items under the main Example tab
		$sub_nav[] = array(
			'name'            =>  bp_is_my_profile() ? __( 'My Tasks', 'collabpress' ) : sprintf( __( '%s&#8217s Tasks', 'collabpress' ), bp_get_user_firstname() ),
			'slug'            => 'tasks',
			'parent_url'      => $projects_link,
			'parent_slug'     => $this->slug,
			'screen_function' => array( $this, 'template_loader' ),
			'position'        => 10
		);

		parent::setup_nav( $main_nav, $sub_nav );
	}

	public function show_tab_for_current_user() {
		$show = bp_is_my_profile() || current_user_can( 'bp_moderate' );
		return apply_filters( 'cp_bp_show_tab_for_current_user', $show );
	}

	public function template_loader() {
		add_action( 'bp_template_content', array( $this, 'template_content_loader' ) );
		bp_core_load_template( 'members/single/plugins' );
	}

	public function template_content_loader() {
		$template = '';
		switch ( bp_current_action() ) {
			case 'tasks' :
				$template = 'collabpress/user-tasks';
				break;
		}

		if ( function_exists( 'bp_get_template_part' ) ) {
			// BP 1.7+ has theme compatibility support
			bp_get_template_part( $template );
		} else {
			// For versions of BP <1.7, there's no built-in support
			// for custom templates (sorry, theme authors - didn't
			// want to add the overhead for a legacy system)
			include( apply_filters( 'cp_bp_legacy_user_template', CP_PLUGIN_DIR . '/includes/templates/' . $template . '.php' ) );
		}
	}

	public function add_cp_to_template_stack( $stack ) {
		$stack[] = CP_PLUGIN_DIR . '/includes/templates/';
		return $stack;
	}

	/**
	 * Double hack. I can't run this in the Group Extension, because it's loaded too late.
	 */
	function mark_post_in_group( $project_id ) {
		if ( function_exists( 'bp_is_group' ) && bp_is_group() ) {
			// Hack. The new project action is fired before taxonomies are registered,
			// so we have to do it manually
			$this->register_taxonomies();
			wp_set_post_terms( $project_id, bp_get_current_group_id(), 'cp-bp-group', true );
		}
	}

	/**
	 * Register the taxonomies needed for BP integration
	 *
	 * @package CollabPress
	 * @subpackage CP BP
	 * @since 1.2
	 */
	function register_taxonomies() {
		// Load plugin options
		$cp_options = get_option( 'cp_options' );

		// Check if debug mode is enabled
		$cp_debug_mode = ( $cp_options['debug_mode'] == 'enabled' ) ? true : false;

		// Groups. Todo: abstract this to the groups class
		register_taxonomy( 'cp-bp-group', 'cp-projects', array(
			'label' 	=> 'BP Groups',
			'public' 	=> $cp_debug_mode,
			'query_var' 	=> 'cp-bp-group'
		) );

	}

	/**
	 * Display the proper permalink for CP BP content
	 *
	 * This function filters 'post_type_link', which in turn powers get_permalink() and related
	 * functions.
	 *
	 * In brief, the purpose is to make sure that CP permalinks point to the proper place.
	 * Ideally I would use a rewrite rule to accomplish this, but it's impossible to write
	 * regex that will be able to tell which group/user a piece of CP data should be associated
	 * with.
	 *
	 * @package CollabPress
	 * @since 1.2
	 *
	 * @param str $link The permalink
	 * @param obj $post The post object
	 * @param bool $leavename
	 * @param bool $sample See get_post_permalink() for an explanation of these two params
	 * @return str $link The filtered permalink
	 */
	function filter_permalinks( $link, $post, $leavename, $sample ) {
		// Check to see whether we are in a BP context
		// Todo: test this!! Might need an exception for the front page
		if ( !bp_current_component() || is_admin() || is_network_admin() )
			return $link;

		switch ( $post->post_type ) {
			case 'cp-projects' :
				$link = cp_bp_get_project_permalink( $post->ID, $post );

				break;

			case 'cp-task-lists' :
				$link = cp_bp_get_task_list_permalink( $post->ID, $post );
				break;

			case 'cp-tasks' :
				$link = cp_bp_get_task_permalink( $post->ID, $post );
				break;
		}

		return $link;
	}

	function filter_cp_calendar_permalink( $link, $project, $year, $month ) {
		global $cp;
		if ( !bp_current_component() || is_admin() || is_network_admin() )
			return $link;
		$link = add_query_arg( array(
			'year' => $year,
			'month' => $month,
			), bp_get_group_permalink( groups_get_current_group() ) . 'calendar' );
		return $link;
	}
	function filter_item_link( $link, $post_id, $parent_id = false ) {
		if ( !bp_current_component() || is_admin() || is_network_admin() )
			return $link;

		return get_permalink( $post_id );
	}

	function do_cp_query() {
		global $cp_page;

		$args = array(
			$this->current_view => $this->current_item_id,
			'add_meta_boxes' => false
		);

		// For these post types, we must look up the associated project
		if ( 'task_list' == $this->current_view || 'task' == $this->current_view ) {
			$args['project'] = get_post_meta( $this->current_item_id, '_cp-project-id', true );
		}
	}

	function get_current_item_obj_data( $type, $data ) {
		$obj_data = 0;
		if ( isset( $this->current_item_ancestry ) ) {
			foreach ( (array) $this->current_item_ancestry->ancestors as $ancestor ) {
				if ( $type == $ancestor->type ) {
					$obj_data = $ancestor->{$data};
					break;
				}
			}
		}

		return $obj_data;
	}

	function get_current_item_project() {
		return $this->get_current_item_obj_data( 'cp-projects', 'id' );
	}

	function get_current_item_task_list() {
		return $this->get_current_item_obj_data( 'cp-task-lists', 'id' );
	}

	function get_current_item_task() {
		return $this->get_current_item_obj_data( 'cp-tasks', 'id' );
	}

	function get_current_item_project_slug() {
		return $this->get_current_item_obj_data( 'cp-projects', 'slug' );
	}

	function get_current_item_task_list_slug() {
		return $this->get_current_item_obj_data( 'cp-task-lists', 'slug' );
	}

	function get_current_item_task_slug() {
		return $this->get_current_item_obj_data( 'cp-tasks', 'slug' );
	}

	function get_current_item_project_name() {
		return $this->get_current_item_obj_data( 'cp-projects', 'name' );
	}

	function get_current_item_task_list_name() {
		return $this->get_current_item_obj_data( 'cp-task-lists', 'name' );
	}

	function get_current_item_task_name() {
		return $this->get_current_item_obj_data( 'cp-tasks', 'name' );
	}

	function get_current_item_project_description() {
		return $this->get_current_item_obj_data( 'cp-projects', 'description' );
	}

	function get_current_item_task_list_description() {
		return $this->get_current_item_obj_data( 'cp-task-lists', 'description' );
	}

	function get_current_item_task_description() {
		return $this->get_current_item_obj_data( 'cp-tasks', 'description' );
	}

	/***********************************
	 * ADMIN METHODS
	 ***********************************/

	/**
	 * Render the BP-specific settings options
	 *
	 * @package CollabPress
	 * @subpackage CP BP
	 * @since 1.2
	 */
	function render_settings() {

		// Only show to super admins
		if ( !is_super_admin() ) {
			return;
		}

		$options = cp_get_options();

		?>

		<tr>
			<td colspan="2"><h3><?php _e( 'BuddyPress', 'collabpress' ); ?></h3><hr /></td>
		</tr>

		<?php if ( bp_is_active( 'groups' ) ) : ?>
			<tr>
				<th scope="row"><label for="cp_options[bp][groups_enabled]"><?php _e( 'Groups tab', 'collabpress' ); ?></label></th>

				<td>
					<select name="cp_options[bp][groups_enabled]">
						<option value="disabled" <?php selected( $options['bp']['groups_enabled'], 'disabled' ); ?>><?php _e('Disabled', 'collabpress') ?></option>
						<option value="enabled" <?php selected( $options['bp']['groups_enabled'], 'enabled' ); ?>><?php _e('Enabled', 'collabpress') ?></option>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="cp_options[bp][groups_admins_can_disable]"><?php _e( 'Allow group admins to disable tab', 'collabpress' ); ?></label></th>

				<td>
					<select name="cp_options[bp][groups_admins_can_disable]">
						<option value="allow" <?php selected( $options['bp']['groups_admins_can_disable'], 'alow' ); ?>><?php _e( 'Allow', 'collabpress' ) ?></option>
						<option value="disallow" <?php selected( $options['bp']['groups_admins_can_disable'], 'disallow' ); ?>><?php _e( "Don't allow", 'collabpress' ) ?></option>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="cp_options[bp][groups_admins_can_customize]"><?php _e( 'Allow group admins to customize tab name and slug', 'collabpress' ); ?></label></th>

				<td>
					<select name="cp_options[bp][groups_admins_can_customize]">
						<option value="allow" <?php selected( $options['bp']['groups_admins_can_customize'], 'allow' ); ?>><?php _e( 'Allow', 'collabpress' ) ?></option>
						<option value="disallow" <?php selected( $options['bp']['groups_admins_can_customize'], 'disallow' ); ?>><?php _e( "Don't allow", 'collabpress') ?></option>
					</select>
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="cp_options[bp][groups_default_tab_name]"><?php _e( 'Default group tab name', 'collabpress' ); ?></label></th>

				<td>
					<input type="text" name="cp_options[bp][groups_default_tab_name]" id="groups-default-tab-name" value="<?php echo esc_html( $options['bp']['groups_default_tab_name'] ) ?>" />
				</td>
			</tr>

			<tr>
				<th scope="row"><label for="cp_options[bp][groups_default_tab_slug]"><?php _e( 'Default group tab slug', 'collabpress' ); ?></label></th>

				<td>
					<input type="text" name="cp_options[bp][groups_default_tab_slug]" id="groups-default-tab-slug" value="<?php echo esc_html( $options['bp']['groups_default_tab_slug'] ) ?>" />
				</td>
			</tr>
		<?php endif ?>

		<?php
	}

	public function enqueue_styles() {
		if ( bp_is_user() && bp_is_current_component( 'collabpress' ) ) {
			wp_enqueue_style( 'cp-bp', CP_PLUGIN_URL . 'includes/css/bp.css' );
		}
	}
}

/**
 * Loads the BP component
 *
 * @since 1.3
 */
function cp_bp_load_component() {
	global $bp;
	$bp->collabpress = new CP_BP_Integration;
}
cp_bp_load_component();

/**
 * Convenience function for accessing CP_BP_Integration object
 *
 * @since 1.3
 * @return obj CP_BP_Integration piece from $bp global
 */
function cp_bp() {
	if ( function_exists( 'buddypress' ) ) {
		return buddypress()->collabpress;
	} else {
		global $bp;
		return $bp->collabpress;
	}
}

/**
 * Assemble arguments for querying a list of projects
 *
 * @package CollabPress
 * @subpackage CP BP
 * @since 1.2
 *
 * @param array $args Optional arguments. See $defaults for more info
 * @return array $query_args The args to put into WP_Query
 */
function cp_bp_projects_query_args( $args = array() ) {
	$defaults = array(
		'paged'		 => 1,
		'posts_per_page' => 10,
		'author'	 => false,
		'meta_query' 	 => false,
		'tax_query'	 => array(),
		'order'		 => 'DESC',
		'orderby'	 => false
	);

	$r = wp_parse_args( $args, $defaults );
	extract( $r, EXTR_SKIP );

	// Things like group filtering happens with the tax_query. By using apply_filters() here,
	// we can let optional components do their own work.
	$tax_query = apply_filters( 'cp_bp_projects_tax_query', $tax_query, $r, $args, $defaults );

	$query_args = array(
		'post_type' 	 => 'cp-projects',
		'meta_query' 	 => $meta_query,
		'tax_query'	 => $tax_query,
		'author'	 => $author,
		'paged'		 => $paged,
		'posts_per_page' => $posts_per_page,
		'order'		 => $order,
		'orderby'	 => $orderby
	);

	return apply_filters( 'cp_bp_projects_query_args', $query_args, $r, $args, $defaults );
}

/**
 * Get the proper link for a CP project
 *
 * @package CollabPress
 * @subpackage CP BP
 * @since 1.2
 *
 * @param
 */
function cp_bp_get_project_permalink( $project_id = false, $project = false ) {
	global $post;

	// Only run another query if we have to
	if ( $project_id && !$project && ( ( isset( $post->ID ) && $project_id != $post->ID ) || !isset( $post->ID ) ) ) {
		$project = get_post( $project_id );
	} else if ( $project_id && !$project && isset( $post->ID ) && $project_id == $post->ID ) {
		$project = $post;
	}

	if ( empty( $project ) )
		return false;

	// Check the post type
	if ( 'cp-projects' != $project->post_type )
		return false;

	// Get the associated item. This defaults to the project author, but can be overridden via
	// the cp_bp_get_project_permalink_item_id filter (eg for groups)
	$parent_item = apply_filters( 'cp_bp_get_project_permalink_parent_item', array(
		'item_id'	=> $project->post_author,
		'item_type'	=> 'user',
		'item_link'	=> bp_core_get_user_domain( $project->post_author ),
		'item_cp_slug'	=> cp_bp()->item_cp_slug // Todo: This has to be alterable
	), $project );

	// Assemble the permalink
	$link = $parent_item['item_link'] . $parent_item['item_cp_slug'] . '/' . $project->post_name;

	return apply_filters( 'cp_bp_get_project_permalink', $link, $project );
}

function cp_bp_get_task_list_permalink( $task_list_ID, $task_list_obj = false ) {
	return apply_filters( 'cp_bp_get_task_list_permalink', cp_bp_get_item_permalink( 'task-list', $task_list_ID, $task_list_obj ), $task_list_ID, $task_list_obj );
}

function cp_bp_get_task_permalink( $task_ID, $task_obj = false ) {
	return apply_filters( 'cp_bp_get_task_permalink', cp_bp_get_item_permalink( 'task', $task_ID, $task_obj ), $task_ID, $task_obj );
}

function cp_bp_get_item_permalink( $item_type = 'project', $item_id, $item_obj = false ) {
	global $post;

	if ( 'project' == $item_type ) {
		$link = cp_bp_get_project_permalink( $item_id, $item_obj );
	} else {
		if ( !$item_obj ) {
			$item_obj = get_post( $item_id );
		}

		$item_ancestry = cp_bp_get_item_ancestry( $item_obj );

		$slug_chain = array();
		foreach( $item_ancestry->ancestors as $ancestor ) {
			// We're getting the project URL from cp_bp_get_project_permalink()
			if ( 'cp-projects' == $ancestor->type )
				continue;

			$slug_chain[] = $ancestor->slug;
		}

		$slug_chain = implode( '/', $slug_chain );

		$project_id = get_post_meta( $item_id, '_cp-project-id', true );
		$project_url = cp_bp_get_project_permalink( $project_id );

		$link = $project_url . '/' . $slug_chain;
	}

	return $link;
}

function cp_bp_get_item_ancestry( $item_obj ) {
	$ancestry = new stdClass;

	$ancestry->item_id   = $item_obj->ID;
	$ancestry->item_type = $item_obj->post_type;

	$ancestors = array();

	// First, add the item itself to the ancestry
	$ancestors[] = cp_bp_make_ancestor( $item_obj );

	// If this is a task, we'll need to get the task list
	if ( 'cp-tasks' == $item_obj->post_type ) {
		$cp_task_list_id = get_post_meta( $item_obj->ID, '_cp-task-list-id', true );

		if ( $cp_task_list_id ) {
			$cp_task_list    = get_post( $cp_task_list_id );
			$ancestors[] = cp_bp_make_ancestor( $cp_task_list );
		}
	}

	// If this is not a project, we'll get the project
	if ( 'cp-projects' != $item_obj->post_type ) {
		$cp_project_id = get_post_meta( $item_obj->ID, '_cp-project-id', true );

		if ( $cp_project_id ) {
			$cp_project    = get_post( $cp_project_id );
			$ancestors[] = cp_bp_make_ancestor( $cp_project );
		}
	}

	// Switch it around to make it easier to build our URLs
	$ancestry->ancestors = array_reverse( $ancestors );

	return $ancestry;
}

function cp_bp_make_ancestor( $item_obj ) {
	$ancestor = new stdClass;

	$ancestor->id		= $item_obj->ID;
	$ancestor->type 	= $item_obj->post_type;
	$ancestor->slug 	= $item_obj->post_name;
	$ancestor->name 	= $item_obj->post_title;

	// Set the meta key for the description
	if ( 'cp-projects' == $item_obj->post_type ) {
		$description_meta_key = '_cp-project-description';
	} else if ( 'cp-task-lists' == $item_obj->post_type ) {
		$description_meta_key = '_cp-task-list-description';
	}

	if ( isset( $description_meta_key ) )
		$ancestor->description  = get_post_meta( $item_obj->ID, $description_meta_key, true );

	return $ancestor;
}

?>
