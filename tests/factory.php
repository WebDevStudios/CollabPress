<?php

class CP_UnitTest_Factory_For_Task extends WP_UnitTest_Factory_For_Thing {

	function __construct( $factory = null ) {
		parent::__construct( $factory );
		$this->default_generation_definitions = array(
			'post_status' => 'publish',
			'post_title' => new WP_UnitTest_Generator_Sequence( 'Task title %s' ),
			'post_content' => new WP_UnitTest_Generator_Sequence( 'Task description %s' ),
			'post_type' => 'cp-tasks'
		);
	}

	function create_object( $args ) {
		$post_id = wp_insert_post( $args );

		if ( isset( $args['assigned_user_id'] ) ) {
			update_post_meta( $post_id, '_cp-task-assign', $args['assigned_user_id'] );
		}

		if ( isset( $args['status'] ) ) {
			update_post_meta( $post_id, '_cp-task-status', $args['status'] );
		}

		if ( isset( $args['task_list_id'] ) ) {
			update_post_meta( $post_id, '_cp-task-list-id', $args['task_list_id'] );
		}

		return $post_id;
	}

	/**
	 * @todo
	 */
	function update_object( $object, $fields ) {}

	function get_object_by_id( $post_id ) {
		return get_post( $post_id );
	}
}

class CP_UnitTest_Factory_For_TaskList extends WP_UnitTest_Factory_For_Thing {

	function __construct( $factory = null ) {
		parent::__construct( $factory );
		$this->default_generation_definitions = array(
			'post_status' => 'publish',
			'post_title' => new WP_UnitTest_Generator_Sequence( 'TaskList title %s' ),
			'post_content' => new WP_UnitTest_Generator_Sequence( 'TaskList description %s' ),
			'post_type' => 'cp-task-list'
		);
	}

	function create_object( $args ) {
		$post_id = wp_insert_post( $args );

		if ( isset( $args['project_id'] ) ) {
			update_post_meta( $post_id, '_cp-project-id', $args['project_id'] );
		}

		return $post_id;
	}

	/**
	 * @todo
	 */
	function update_object( $object, $fields ) {}

	function get_object_by_id( $post_id ) {
		return get_post( $post_id );
	}
}
