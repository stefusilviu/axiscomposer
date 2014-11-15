<?php
/**
 * Plugin Name: Axis Builder
 * Plugin URI: http://axisthemes.com/axis-builder/
 * Description: A drag and drop builder that helps you build modern and unique page layouts smartly. Beautifully.
 * Author: AxisThemes
 * Author URI: http://axisthemes.com
 * Version: 1.0-bleeding
 * Requires at least: 3.8
 * Tested up to: 4.0
 *
 * Text Domain: axisbuilder
 * Domain Path: /i18n/languages/
 *
 * @package  AxisBuilder
 * @category Core
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'AxisBuilder' ) ) :

/**
 * Main AxisBuilder Class
 *
 * @class   AxisBuilder
 * @version 1.0.0
 */
final class AxisBuilder {

	/**
	 * @var string AxisBuilder Version
	 */
	public $version = '1.0.0';

	/**
	 * @var AxisBuilder The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main AxisBuilder Instance
	 *
	 * Ensure only one instance of AxisBuilder is loaded or can be loaded.
	 *
	 * @static
	 * @see    AC()
	 * @return AxisBuilder - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'axisbuilder' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'axisbuilder' ), '1.0' );
	}

	/**
	 * AxisBuilder Constructor.
	 *
	 * @return AxisBuilder
	 */
	public function __construct() {

		// Define constants
		$this->define_constants();

		// Include required files
		$this->includes();

		// Loaded action
		do_action( 'axisbuilder_loaded' );
	}

	/**
	 * Define AB Constants
	 */
	private function define_constants() {
		define( 'AB_PLUGIN_FILE', __FILE__ );
		define( 'AB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		define( 'AB_VERSION', $this->version );
	}

	/**
	 * Includes required core files used in admin and on the frontend.
	 */
	private function includes() {
		include_once( 'includes/ab-core-functions.php' );
		include_once( 'includes/class-ab-install.php' );

		if ( is_admin() ) {
			include_once( 'includes/admin/class-ab-admin.php' );
		}

		if ( is_ajax() ) {
			$this->ajax_includes();
		}

		// Classes (used on all pages)
		include_once( 'includes/class-ab-sidebars.php' );
	}

	/**
	 * Include required ajax files.
	 */
	public function ajax_includes() {
		include_once( 'includes/class-ab-ajax.php' );
	}

	/** Helper functions ******************************************************/

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get Ajax URL.
	 *
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}
}

endif;

if ( ! function_exists( 'AB' ) ) {

	/**
	 * Returns the main instance of AB to prevent the need to use globals.
	 *
	 * @access public
	 * @since  1.0.0
	 * @return AxisBuilder
	 */
	function AB() {
		return AxisBuilder::instance();
	}
}

// Global for backwards compatibility.
$GLOBALS['axisbuilder'] = AB();
