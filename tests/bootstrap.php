<?php
/**
 * AxisComposer Unit tests Bootstrap
 *
 * @since 1.0.0
 */
class AC_Unit_Tests_Bootstrap {

	/** @var AC_Unit_Tests_Bootstrap instance */
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
	 * @since 1.0.0
	 */
	public function __construct() {

		ini_set( 'display_errors','on' );
		error_reporting( E_ALL );

		// Ensure server variable is set for WP email functions.
		if ( ! isset( $_SERVER['SERVER_NAME'] ) ) {
			$_SERVER['SERVER_NAME'] = 'localhost';
		}

		$this->tests_dir    = dirname( __FILE__ );
		$this->plugin_dir   = dirname( $this->tests_dir );
		$this->wp_tests_dir = getenv( 'WP_TESTS_DIR' ) ? getenv( 'WP_TESTS_DIR' ) : '/tmp/wordpress-tests-lib';

		// Load test function so tests_add_filter() is available
		require_once( $this->wp_tests_dir . '/includes/functions.php' );

		// Load AC
		tests_add_filter( 'muplugins_loaded', array( $this, 'load_ac' ) );

		// Install AC
		tests_add_filter( 'setup_theme', array( $this, 'install_ac' ) );

		// Load the WP testing environment
		require_once( $this->wp_tests_dir . '/includes/bootstrap.php' );

		// Load AC testing framework
		$this->includes();
	}

	/**
	 * Load AxisComposer
	 *
	 * @since 1.0.0
	 */
	public function load_ac() {
		require_once( $this->plugin_dir . '/axiscomposer.php' );
	}

	/**
	 * Install AxisComposer after the test environment and AC have been loaded
	 *
	 * @since 1.0.0
	 */
	public function install_ac() {

		// Clean existing install first
		define( 'WP_UNINSTALL_PLUGIN', true );
		update_option( 'axiscomposer_status_options', array( 'uninstall_data' => 1 ) );
		include( $this->plugin_dir . '/uninstall.php' );

		AC_Install::install();

		// Reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374
		$GLOBALS['wp_roles']->reinit();

		echo "Installing AxisComposer..." . PHP_EOL;
	}

	/**
	 * Load AC-specific test cases and factories
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		// Framework
		require_once( $this->tests_dir . '/framework/class-ac-unit-test-factory.php' );

		// Test Cases
		require_once( $this->tests_dir . '/framework/class-ac-unit-test-case.php' );
	}

	/**
	 * Get the single class instance
	 *
	 * @since  1.0.0
	 * @return AC_Unit_Tests_Bootstrap
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}
}

AC_Unit_Tests_Bootstrap::instance();
