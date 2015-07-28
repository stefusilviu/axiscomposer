<?php

namespace AxisComposer\Tests\Util;

/**
 * Class Main_Class
 * @package AxisComposer\Tests\Util
 */
class Main_Class extends \AC_Unit_Test_Case {

	/** @var \AxisComposer instance */
	protected $ac;

	/**
	 * Setup test
	 *
	 * @since 1.0.0
	 */
	public function setUp() {

		parent::setUp();

		$this->ac = AC();
	}

	/**
	 * Test AC has static instance
	 *
	 * @since 1.0.0
	 */
	public function test_ac_instance() {

		$this->assertClassHasStaticAttribute( '_instance', 'AxisComposer' );
	}

	public function test_constructor() {

	}

	/**
	 * Test that all AC constants are set
	 *
	 * @since 1.0.0
	 */
	public function test_constants() {

		$this->assertEquals( str_replace( 'tests/unit-tests/util/', '', plugin_dir_path( __FILE__ ) ) . 'axiscomposer.php', AC_PLUGIN_FILE );

		$this->assertEquals( $this->ac->version, AC_VERSION );
		$this->assertNotEquals( AC_LOG_DIR, '' );
		$this->assertNotEquals( AC_ICONFONT_DIR, '' );
		$this->assertNotEquals( AC_ICONFONT_URL, '' );
	}

	/**
	 * Test class instance
	 *
	 * @since 1.0.0
	 */
	public function test_ac_class_instances() {
		$this->ac->init();

		$this->assertInstanceOf( 'AC_Integrations', $this->ac->integrations );
	}
}
