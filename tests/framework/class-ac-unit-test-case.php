<?php
/**
 * AC Unit Test Case
 *
 * Provides AxisComposer-specific setup/tear down/assert methods, custom factories,
 * and helper functions
 *
 * @since 1.0
 */
class AC_Unit_Test_Case extends WP_UnitTestCase {

	/**
	 * Setup test case
	 *
	 * @since 1.0
	 */
	public function setUp() {

		parent::setUp();

		$this->setOutputCallback( array( $this, 'filter_output' ) );

		// Register post types before each test
		AC_Post_Types::register_post_types();
		AC_Post_Types::register_taxonomies();
	}

	/**
	 * Strip newlines and tabs when using expectedOutputString() as otherwise
	 * the most template-related tests will fail due to indentation/alignment in
	 * the template not matching the sample strings set in the tests
	 *
	 * @since 1.0
	 */
	public function filter_output( $output ) {

		$output = preg_replace( '/[\n]+/S', '', $output );
		$output = preg_replace( '/[\t]+/S', '', $output );

		return $output;
	}

	/**
	 * Asserts thing is not WP_Error
	 *
	 * @param mixed  $actual
	 * @param string $message
	 */
	public function assertNotWPError( $actual, $message = '' ) {
		$this->assertNotInstanceOf( 'WP_Error', $actual, $message );
	}

	/**
	 * Asserts thing is WP_Error
	 *
	 * @param mixed  $actual
	 * @param string $message
	 */
	public function assertIsWPError( $actual, $message = '' ) {
		$this->assertInstanceOf( 'WP_Error', $actual, $message );
	}

	/**
	 * Backport assertNotFalse to PHPUnit 3.6.12 which only runs in PHP 5.2
	 *
	 * @since  1.0
	 * @param  $condition
	 * @param  string $message
	 * @return mixed
	 */
	public static function assertNotFalse( $condition, $message = '' ) {

		if ( version_compare( phpversion(), '5.3', '<' ) ) {

			self::assertThat( $condition, self::logicalNot( self::isFalse() ), $message );

		} else {

			parent::assertNotFalse( $condition, $message );
		}
	}
}
