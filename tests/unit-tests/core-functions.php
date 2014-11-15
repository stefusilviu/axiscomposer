<?php
/**
 * Test AxisBuilder core functions
 *
 * @since 1.0
 */
class AB_Tests_Core_Functions extends AB_Unit_Test_Case {

	/**
	 * Test wc_get_core_supported_themes()
	 *
	 * @since 1.0
	 */
	public function test_ab_get_core_supported_themes() {

		$expected_themes = array( 'twentyfourteen', 'twentythirteen', 'twentyeleven', 'twentytwelve', 'twentyten' );

		$this->assertEquals( $expected_themes, ab_get_core_supported_themes() );
	}
}
