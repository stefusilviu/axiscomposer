<?php
/**
 * Test AB core functions
 *
 * @since 1.0
 */
class AB_Tests_Core_Functions extends AB_Unit_Test_Case {

	/**
	 * Test get_builder_core_support_themes()
	 *
	 * @since 1.0
	 */
	public function test_builder_core_support_themes() {

		$expected_themes = array( 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentyeleven', 'twentytwelve', 'twentyten' );

		$this->assertEquals( $expected_themes, get_builder_core_support_themes() );
	}

	/**
	 * Test get_builder_core_supported_screens()
	 *
	 * @since 1.0
	 */
	public function test_get_builder_core_supported_screens() {

		$expected_screens = array( 'post', 'page', 'portfolio', 'axis-portfolio', 'jetpack-portfolio' );

		$this->assertEquals( $expected_screens, get_builder_core_supported_screens() );
	}
}
