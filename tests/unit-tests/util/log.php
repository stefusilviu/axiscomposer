<?php

namespace AxisComposer\Tests\Util;

/**
 * Class Log
 * @package AxisComposer\Tests\Util
 * @since   1.0.0
 */
class Log extends \AC_Unit_Test_Case {
	public function read_content( $handle ) {
		return file_get_contents( ac_get_log_file_path( $handle ) );
	}

	/**
	 * Test add()
	 *
	 * @since 1.0.0
	 */
	public function test_add() {
		$log = new \AC_Logger();

		$log->add( 'unit-tests', 'this is a message' );

		$this->assertStringMatchesFormat( '%d-%d-%d @ %d:%d:%d - %s', $this->read_content( 'unit-tests' ) );
		$this->assertStringEndsWith( ' - this is a message' . PHP_EOL, $this->read_content( 'unit-tests' ) );
	}

	/**
	 * Test clear()
	 *
	 * @since 1.0.0
	 */
	public function test_clear() {
		$log = new \AC_Logger();

		$log->add( 'unit-tests', 'this is a message' );
		$log->clear( 'unit-tests' );

		$this->assertEquals( '', $this->read_content( 'unit-tests' ) );
	}
}
