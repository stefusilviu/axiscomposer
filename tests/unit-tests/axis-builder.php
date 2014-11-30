<?php
/**
 * Test AxisBuilder class
 *
 * @since 1.0
 */
class AB_Tests_AxisBuilder extends AB_Unit_Test_Case {

	/** @var \AxisBuilder instance */
	protected $ab;

	/**
	 * Setup test
	 *
	 * @since 1.0
	 */
	public function setUp() {

		parent::setUp();

		$this->ab = AB();
	}

	/**
	 * Test AB has static instance
	 *
	 * @since 1.0
	 */
	public function test_ab_instance() {

		$this->assertClassHasStaticAttribute( '_instance', 'AxisBuilder' );
	}

	public function test_constructor() {

	}

	/**
	 * Test that all AB constants are set
	 *
	 * @since 2.2
	 */
	public function test_constants() {

		$this->assertEquals( str_replace( 'tests/unit-tests/', '', plugin_dir_path( __FILE__ ) ) . 'axis-builder.php', AB_PLUGIN_FILE );

		$this->assertEquals( $this->ab->version, AB_VERSION );
		$this->assertNotEquals( AB_CONFIG_DIR, '' );
		$this->assertNotEquals( AB_SHORTCODE_DIR, '' );
		$this->assertNotEquals( AB_UPLOAD_DIR, '' );
	}
}
