<?php
/**
 * AxisBuilder Unit tests Bootstrap
 *
 * @since 1.0
 */
class AB_Unit_Tests_Bootstrap {

	/** @var \AB_Unit_Tests_Bootstrap instance */
	protected static $instance = null;

	/** @var string directory where wordpress-tests-lib is installed */
	public $wp_tests_dir;

	/** @var string testing directory */
	public $tests_dir;

	/** @var string plugin directory */
	public $plugin_dir;

	/**
	 * Setup the unit testing environment
	 *
	 * @since 1.0
	 */
	public function __construct() {

		ini_set( 'display_errors','on' );
		error_reporting( E_ALL );

		$this->tests_dir    = dirname( __FILE__ );
		$this->plugin_dir   = dirname( $this->tests_dir );
		$this->wp_tests_dir = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : $this->plugin_dir . '/tmp/wordpress-tests-lib';

		// Load test function so tests_add_filter() is available
		require_once( $this->wp_tests_dir . '/includes/functions.php' );

		// Load AB
		tests_add_filter( 'muplugins_loaded', array( $this, 'load_axisbuilder' ) );

		// Install AB
		tests_add_filter( 'setup_theme', array( $this, 'install_axisbuilder' ) );

		// Load the WP testing environment
		require_once( $this->wp_tests_dir . '/includes/bootstrap.php' );

		// Load AB testing framework
		$this->includes();
	}

	/**
	 * Load AxisBuilder
	 *
	 * @since 1.1
	 */
	public function load_axisbuilder() {
		require_once( $this->plugin_dir . '/axisbuilder.php' );
	}

	/**
	 * Install AxisBuilder after the test environment and AB have been loaded
	 *
	 * @since 1.0
	 */
	public function install_axisbuilder() {

		// Clean existing install first
		define( 'WP_UNINSTALL_PLUGIN', true );
		include( $this->plugin_dir . '/uninstall.php' );

		AB_Install::install();

		// Reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374
		$GLOBALS['wp_roles']->reinit();

		echo "Installing Axis Builder..." . PHP_EOL;
	}

	/**
	 * Load AB-specific test cases and factories
	 *
	 * @since 1.0
	 */
	public function includes() {

		// Test Cases
		require_once( $this->tests_dir . '/framework/class-builder-unit-test-case.php' );
	}

	/**
	 * Get the single class instance
	 *
	 * @since  1.0
	 * @return AB_Unit_Tests_Bootstrap
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}

AB_Unit_Tests_Bootstrap::instance();
