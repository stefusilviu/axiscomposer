<?php

namespace WooCommerce\Tests\Util;

/**
 * Class Core_Functions
 * @package AxisComposer\Tests\Util
 * @since   1.0.0
 */
class Core_Functions extends \AC_Unit_Test_Case {

	/**
	 * Test ac_get_core_supported_themes()
	 *
	 * @since 1.0.0
	 */
	public function test_ac_get_core_supported_themes() {

		$expected_themes = array( 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentytwelve','twentyeleven', 'twentyten' );

		$this->assertEquals( $expected_themes, ac_get_core_supported_themes() );
	}

	/**
	 * Test ac_get_core_supported_iconfonts()
	 *
	 * @since 1.0.0
	 */
	public function test_ac_get_core_supported_iconfonts() {

		$expected_iconfonts['entypo-fontello'] = array(
			'default'  => true,
			'version'  => '?v=3',
			'charmap'  => 'charmap.php',
			'font_url' => AC()->plugin_url() . '/assets/fonts/entypo-fontello',
			'font_dir' => AC()->plugin_path() . '/assets/fonts/entypo-fontello'
		);

		$this->assertEquals( $expected_iconfonts, ac_get_core_supported_iconfonts() );
	}

	/**
	 * Test ac_get_layout_supported_screens()
	 *
	 * @since 1.0.0
	 */
	public function test_ac_get_layout_supported_screens() {

		$expected_screens = array( 'post', 'page', 'portfolio', 'jetpack-portfolio' );

		$this->assertEquals( $expected_screens, ac_get_layout_supported_screens() );
	}
}
