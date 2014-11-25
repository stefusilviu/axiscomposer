<?php
/**
 * Plugin Name: Axis Builder
 * Plugin URI: http://axisthemes.com/axis-builder/
 * Description: A drag and drop builder that helps you build modern and unique page layouts smartly. Beautifully.
 * Author: AxisThemes
 * Author URI: http://axisthemes.com
 * Version: 1.0-bleeding
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: axisbuilder
 * Domain Path: /languages/
 *
 * @package  AxisBuilder
 * @category Core
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
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
	 * @var string
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
	 * @see    AB()
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
		$this->define_constants();
		$this->includes();

		// Hooks
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'widgets_init', array( $this, 'include_widgets' ) );

		// Loaded action
		do_action( 'axisbuilder_loaded' );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 *
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( method_exists( $this, $key ) ) {
			return $this->$key();
		}
	}

	/**
	 * Define constant if not already set
	 *
	 * @param  string $name
	 * @param  string $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Define AB Constants
	 */
	private function define_constants() {
		$upload_dir = wp_upload_dir();

		$this->define( 'AB_PLUGIN_FILE', __FILE__ );
		$this->define( 'AB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'AB_VERSION', $this->version );
		$this->define( 'AB_CONFIG_DIR', $this->plugin_path() . '/config/' );
		$this->define( 'AB_SHORTCODE_DIR', $this->plugin_path() . '/shortcodes/' );
		$this->define( 'AB_LOG_DIR', $upload_dir['basedir'] . '/axis-logs/' );
		$this->define( 'AB_UPLOAD_DIR', $upload_dir['basedir'] . '/axisbuilder-uploads/' );
	}

	/**
	 * Includes required core files used in admin and on the frontend.
	 */
	private function includes() {
		include_once( 'includes/class-ab-autoloader.php' );
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

		// Download/Update languages
		include_once( 'includes/class-ab-localization.php' );
	}

	/**
	 * Include required ajax files.
	 */
	public function ajax_includes() {
		include_once( 'includes/class-ab-ajax.php' );
	}

	/**
	 * Include core widgets
	 */
	public function include_widgets() {
		include_once( 'includes/abstracts/abstract-ab-widget.php' );

		register_widget( 'AB_Widget_Advertisement' );
	}

	/**
	 * Init AxisBuilder when WordPress Initialises.
	 */
	public function init() {
		// Before init action
		do_action( 'before_axisbuilder_init' );

		// Set up localisation
		$this->load_plugin_textdomain();

		// Init action
		do_action( 'axisbuilder_init' );
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'axisbuilder' );
		$dir    = trailingslashit( WP_LANG_DIR );

		/**
		 * Admin Locale. Looks in:
		 *
		 * 		- WP_LANG_DIR/axis-builder/axisbuilder-admin-LOCALE.mo
		 * 		- WP_LANG_DIR/plugins/axisbuilder-admin-LOCALE.mo
		 */
		if ( is_admin() ) {
			load_textdomain( 'axisbuilder', $dir . 'axis-builder/axisbuilder-admin-' . $locale . '.mo' );
			load_textdomain( 'axisbuilder', $dir . 'plugins/axisbuilder-admin-' . $locale . '.mo' );
		}

		/**
		 * Frontend/global Locale. Looks in:
		 *
		 * 		- WP_LANG_DIR/axis-builder/axisbuilder-LOCALE.mo
		 * 	 	- axisbuilder/i18n/languages/axisbuilder-LOCALE.mo (which if not found falls back to:)
		 * 	 	- WP_LANG_DIR/plugins/axisbuilder-LOCALE.mo
		 */
		load_textdomain( 'axisbuilder', $dir . 'axis-builder/axisbuilder-' . $locale . '.mo' );
		load_plugin_textdomain( 'axisbuilder', false, plugin_basename( dirname( __FILE__ ) ) . "/i18n/languages" );
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

/**
 * Returns the main instance of AB to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return AxisBuilder
 */
function AB() {
	return AxisBuilder::instance();
}

// Global for backwards compatibility.
$GLOBALS['axisbuilder'] = AB();
