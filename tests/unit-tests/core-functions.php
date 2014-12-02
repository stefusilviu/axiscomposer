<?php
/**
 * Test AB core functions
 *
 * @since 1.0
 */
class AB_Tests_Core_Functions extends AB_Unit_Test_Case {

	/**
	 * Test get_builder_core_supported_themes()
	 *
	 * @since 1.0
	 */
	public function test_builder_core_supported_themes() {

		$expected_themes = array( 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentyeleven', 'twentytwelve', 'twentyten' );

		$this->assertEquals( $expected_themes, get_builder_core_supported_themes() );
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

	/**
	 * Test get_builder_core_shortcode_tabs()
	 *
	 * @since 1.0
	 */
	public function test_get_builder_core_shortcode_tabs() {

		$expected_shortcode_tabs = array(
			__( 'Layout Elements',  'axisbuilder' ),
			__( 'Content Elements', 'axisbuilder' ),
			__( 'Media Elements',   'axisbuilder' ),
			__( 'Plugin Additions', 'axisbuilder' ),
			__( 'Custom Elements',  'axisbuilder' ),
		);

		$this->assertEquals( $expected_shortcode_tabs, get_builder_core_shortcode_tabs() );
	}
}
