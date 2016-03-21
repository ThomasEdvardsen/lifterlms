<?php

use Brain\Monkey;

class LLMS_Engagements_Test extends PHPUnit_Framework_TestCase {

	protected function setUp() {
		parent::setUp();
		Monkey::setUpWP();

		// global $wpdb;
  //       $wpdb = Mockery::mock( 'wpdb' );
  //       $wpdb
  //           ->shouldReceive( 'update' )
  //           ->withAnyArgs()
  //           ->andReturnNull();

	}

	protected function tearDown() {
		Monkey::tearDownWP();
		parent::tearDown();
	}

	public function test() {

		$this->assertEquals( '1', 2 );
		$this->assertNotEquals( 1, 0 );

	}

}
