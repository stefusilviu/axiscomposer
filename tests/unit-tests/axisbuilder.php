<?php
/**
 * Test AxisBuilder class
 *
 * @since 1.0
 */
class AC_Tests_AxisBuilder extends AC_Unit_Test_Case {

	/** @var \AxisBuilder instance */
	protected $ab;

	/**
	 * Setup test
	 *
	 * @since 1.0
	 */
	public function setUp() {

		parent::setUp();

		$this->ab = AC();
	}

	/**
	 * Test AC has static instance
	 *
	 * @since 1.0
	 */
	public function test_ab_instance() {

		$this->assertClassHasStaticAttribute( '_instance', 'AxisBuilder' );
	}

	public function test_constructor() {

	}

	/**
	 * Test that all AC constants are set
	 *
	 * @since 1.0
	 */
	public function test_constants() {

		$this->assertEquals( str_replace( 'tests/unit-tests/', '', plugin_dir_path( __FILE__ ) ) . 'axisbuilder.php', AC_PLUGIN_FILE );

		$this->assertEquals( $this->ab->version, AC_VERSION );
		$this->assertNotEquals( AC_UPLOAD_DIR, '' );
	}

	/**
	 * Test class instance
	 *
	 * @since 1.0
	 */
	public function test_ab_class_instances() {
		$this->ab->init();
	}
}
