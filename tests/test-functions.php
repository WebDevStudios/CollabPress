<?php

class WP_Test_Functions extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();

		require_once( dirname(__FILE__) . '/factory.php' );
		$this->factory->task = new CP_UnitTest_Factory_For_Task( $this->factory );
		$this->factory->tasklist = new CP_UnitTest_Factory_For_TaskList( $this->factory );
	}

	function test_cp_get_tasklist_project_id() {
		$tasklist_id = $this->factory->tasklist->create( array( 'project_id' => 3 ) );

		$this->assertEquals( cp_get_tasklist_project_id( $tasklist_id ), 3 );

		// Check the null case. Should never happen
		$tasklist_id2 = $this->factory->tasklist->create();
		$this->assertFalse( cp_get_tasklist_project_id( $tasklist_id2 ) );
	}

	function test_cp_get_task_tasklist_id() {
		$tasklist_id = $this->factory->tasklist->create();
		$task_id = $this->factory->task->create( array( 'task_list_id' => $tasklist_id ) );

		$this->assertEquals( cp_get_task_tasklist_id( $task_id ), $tasklist_id );

		// Check the null case. Should never happen
		$task_id2 = $this->factory->task->create();
		$this->assertFalse( cp_get_task_tasklist_id( $task_id2 ) );
	}

	function test_cp_get_task_project_id() {
		$tasklist_id = $this->factory->tasklist->create( array( 'project_id' => 4 ) );
		$task_id = $this->factory->task->create( array( 'task_list_id' => $tasklist_id ) );
		$this->assertEquals( cp_get_task_project_id( $task_id ), 4 );
	}

	function test_cp_get_tasks_orderby_status() {

		$args1 = array(
			'task_list_id' => 1,
			'status' => 'open',
		);
		$task_id1 = $this->factory->task->create( $args1 );

		$args2 = array(
			'task_list_id' => 1,
			'status' => 'complete',
		);
		$task_id2 = $this->factory->task->create( $args2 );

		$args3 = array(
			'task_list_id' => 1,
			'status' => 'zzz',
		);
		$task_id3 = $this->factory->task->create( $args3 );

		// Shouldn't show up in results
		// Included here to test that meta_value ordering works
		// alongside meta_query
		$args4 = array(
			'task_list_id' => 2,
			'status' => 'aaa',
		);
		$task_id4 = $this->factory->task->create( $args4 );

		$tasks = cp_get_tasks(
			array(
				'orderby' => 'status',
				'order' => 'asc',
				'task_list_id' => 1,
			)
		);

		$this->assertEquals( $tasks, array( get_post( $task_id2 ), get_post( $task_id1 ), get_post( $task_id3 ) ) );
	}

	/**
	 * old argument format: cp_get_tasks( $task_list_id, $status )
	 */
	function test_cp_get_tasks_argument_backpat() {

		$args1 = array(
			'status' => 'open',
			'task_list_id' => 3,
		);
		$task_id1 = $this->factory->task->create( $args1 );

		$args2 = array(
			'status' => 'complete',
			'task_list_id' => 3,
		);
		$task_id2 = $this->factory->task->create( $args2 );

		$args3 = array(
			'status' => 'open',
			'task_list_id' => 4,
		);
		$task_id3 = $this->factory->task->create( $args3 );

		$args4 = array(
			'status' => 'complete',
			'task_list_id' => 4,
		);
		$task_id4 = $this->factory->task->create( $args4 );

		$tasks = cp_get_tasks( 4, 'complete' );

		$this->assertEquals( $tasks, array( get_post( $task_id4 ) ) );
	}

	function test_cp_get_tasks_by_assigned_user() {

		$args1 = array(
			'assigned_user_id' => 2,
		);
		$task_id1 = $this->factory->task->create( $args1 );

		$args2 = array(
			'assigned_user_id' => 1,
		);
		$task_id2 = $this->factory->task->create( $args2 );

		$tasks = cp_get_tasks( array( 'assigned_user_id' => 1 ) );

		$this->assertEquals( $tasks, array( get_post( $task_id2 ) ) );
	}
}

