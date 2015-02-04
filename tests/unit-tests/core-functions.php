<?php
/**
 * Test AB core functions
 *
 * @since 1.0
 */
class AB_Tests_Core_Functions extends AB_Unit_Test_Case {

	/**
	 * Test axisbuilder_clean() - note this is a basic type test as WP core already
	 * has coverage for sanitized_text_field()
	 *
	 * @since 1.0
	 */
	public function test_axisbuilder_clean() {

		$this->assertInternalType( 'string', axisbuilder_clean( 'cleaned' ) );
	}

	/**
	 * Test test_axisbuilder_get_core_supported_themes()
	 *
	 * @since 1.0
	 */
	public function test_axisbuilder_get_core_supported_themes() {

		$expected_themes = array( 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentytwelve','twentyeleven', 'twentyten' );

		$this->assertEquals( $expected_themes, axisbuilder_get_core_supported_themes() );
	}
}
