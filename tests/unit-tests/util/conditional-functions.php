<?php

namespace AxisComposer\Tests\Util;

/**
 * Class Conditional_Functions
 * @package AxisComposer\Tests\Util
 * @since   1.0.0
 */
class Conditional_Functions extends \AC_Unit_Test_Case {

	/**
	 * Test is_ajax()
	 *
	 * @since 1.0.0
	 */
	public function test_is_ajax() {
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		$this->assertEquals( true, is_ajax() );
	}

}
