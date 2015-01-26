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
 * @class    AxisBuilder
 * @property mixed $shortcodes The shortcodes class
 * @version  1.0.0
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
	 * @since 1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'axisbuilder' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'axisbuilder' ), '1.0' );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 * @param  mixed $key
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( in_array( $key, array( 'shortcodes' ) ) ) {
			return $this->$key();
		}
	}

	/**
	 * AxisBuilder Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();

		// Hooks
		add_filter( 'widget_text', 'do_shortcode' );
		add_action( 'init', array( $this, 'init' ), 0 );

		// Loaded action
		do_action( 'axisbuilder_loaded' );
	}

	/**
	 * Define constant if not already set
	 * @param  string $name
	 * @param  string|bool $value
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * What type of request is this?
	 * @param  string $type admin, ajax, cron or frontend
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Define AB Constants.
	 */
	private function define_constants() {
		$this->define( 'AB_PLUGIN_FILE', __FILE__ );
		$this->define( 'AB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'AB_VERSION', $this->version );
		$this->define( 'AB_CONFIG_DIR', $this->plugin_path() . '/config/' );
	}

	/**
	 * Includes the required core files used in admin and on the frontend.
	 */
	private function includes() {
		include_once( 'includes/builder-core-functions.php' );
		include_once( 'includes/builder-widget-functions.php' );
		include_once( 'includes/class-builder-autoloader.php' );
		include_once( 'includes/class-builder-install.php' );

		if ( $this->is_request( 'admin' ) ) {
			include_once( 'includes/admin/class-builder-admin.php' );
		}

		if ( $this->is_request( 'ajax' ) ) {
			$this->ajax_includes();
		}

		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_includes();
		}

		include_once( 'includes/abstracts/abstract-builder-shortcode.php' );   // Shortcodes
		include_once( 'includes/class-builder-sidebars.php' );                 // Sidebar Builder
		include_once( 'includes/class-builder-icon-fonts.php' );               // Iconfonts Manager
	}

	/**
	 * Include required ajax files.
	 */
	public function ajax_includes() {
		include_once( 'includes/class-builder-ajax.php' );                     // Ajax functions for admin and the front-end
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		include_once( 'includes/class-builder-frontend-scripts.php' );         // Frontend Scripts
	}

	/**
	 * Init AxisBuilder when WordPress Initialises.
	 */
	public function init() {
		// Before init action
		do_action( 'before_axisbuilder_init' );

		// Set up localisation
		$this->load_plugin_textdomain();

		// Set up shortcodes
		$this->shortcodes();

		// Init action
		do_action( 'axisbuilder_init' );
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Admin Locales are found in:
	 * 		- WP_LANG_DIR/axis-builder/axisbuilder-admin-LOCALE.mo
	 * 		- WP_LANG_DIR/plugins/axisbuilder-admin-LOCALE.mo
	 *
	 * Frontend/global Locales found in:
	 * 		- WP_LANG_DIR/axis-builder/axisbuilder-LOCALE.mo
	 * 	 	- axis-builder/i18n/languages/axisbuilder-LOCALE.mo (which if not found falls back to:)
	 * 	 	- WP_LANG_DIR/plugins/axisbuilder-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'axisbuilder' );

		if ( $this->is_request( 'admin' ) ) {
			load_textdomain( 'axisbuilder', WP_LANG_DIR . '/axis-builder/axisbuilder-admin-' . $locale . '.mo' );
			load_textdomain( 'axisbuilder', WP_LANG_DIR . '/plugins/axisbuilder-admin-' . $locale . '.mo' );
		}

		load_textdomain( 'axisbuilder', WP_LANG_DIR . '/axis-builder/axisbuilder-' . $locale . '.mo' );
		load_plugin_textdomain( 'axisbuilder', false, plugin_basename( dirname( __FILE__ ) ) . "/i18n/languages" );
	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get Ajax URL.
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

	/**
	 * Get shortcodes class
	 * @return AB_Shortcodes
	 */
	public function shortcodes() {
		return AB_Shortcodes::instance();
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
