<?php

class WP_Test_Functions extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();

		require_once( dirname(__FILE__) . '/factory.php' );
		$this->factory->task = new CP_UnitTest_Factory_For_Task( $this->factory );
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
}

