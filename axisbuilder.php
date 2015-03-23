<?php
/**
 * Plugin Name: AxisBuilder
 * Plugin URI: http://axisthemes.com/axisbuilder/
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
	exit;
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
		$this->init_hooks();

		do_action( 'axisbuilder_loaded' );
	}

	/**
	 * Hook into actions and filters
	 */
	private function init_hooks() {
		register_activation_hook( __FILE__, array( 'AB_Install', 'install' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_filter( 'widget_text', 'do_shortcode' );
	}

	/**
	 * Define AB Constants.
	 */
	private function define_constants() {
		$upload_dir = wp_upload_dir();

		$this->define( 'AB_PLUGIN_FILE', __FILE__ );
		$this->define( 'AB_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'AB_VERSION', $this->version );
		$this->define( 'AB_UPLOAD_DIR', $upload_dir['basedir'] . '/axisbuilder-uploads/' );
		$this->define( 'AB_UPLOAD_URL', $upload_dir['baseurl'] . '/axisbuilder-uploads/' );
	}

	/**
	 * Define constant if not already set
	 * @param string $name
	 * @param string|bool $value
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
	 * Includes the required core files used in admin and on the frontend.
	 */
	public function includes() {
		include_once( 'includes/builder-core-functions.php' );
		include_once( 'includes/builder-widget-functions.php' );
		include_once( 'includes/class-builder-autoloader.php' );
		include_once( 'includes/class-builder-install.php' );
		include_once( 'includes/class-builder-ajax.php' );

		if ( $this->is_request( 'admin' ) ) {
			include_once( 'includes/admin/class-builder-admin.php' );
		}

		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_includes();
		}

		if ( 'yes' === get_option( 'axisbuilder_sidebar_enabled', 'yes' ) ) {
			include_once( 'includes/class-builder-sidebars.php' );             // Sidebar Builder
		}

		include_once( 'includes/class-builder-iconfonts.php' );                // Iconfonts Manager
		include_once( 'includes/class-builder-post-types.php' );               // Registers post types
		include_once( 'includes/class-builder-localization.php' );             // Download/update languages
		include_once( 'includes/abstracts/abstract-builder-shortcode.php' );   // Shortcodes
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		include_once( 'includes/class-builder-frontend-scripts.php' );         // Frontend Scripts
	}

	/**
	 * Function used to Init AxisBuilder Template Functions - This makes them pluggable by plugins and themes.
	 */
	public function include_template_functions() {
		include_once( 'includes/builder-template-functions.php' );
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
	 * Ensure theme and server variables compatibility and setup image sizes.
	 */
	public function setup_environment() {
		$this->add_thumbnail_support();
		$this->add_image_sizes();
		$this->fix_server_vars();
	}

	/**
	 * Ensure post thumbnail support is turned on.
	 */
	private function add_thumbnail_support() {
		if ( ! current_theme_supports( 'post-thumbnails' ) ) {
			add_theme_support( 'post-thumbnails' );
		}
		add_post_type_support( 'portfolio', 'thumbnail' );
	}

	/**
	 * Add AB Image sizes to WP.
	 */
	private function add_image_sizes() {
		$portfolio_thumbnail = axisbuilder_get_image_size( 'portfolio_thumbnail' );
		$portfolio_single	 = axisbuilder_get_image_size( 'portfolio_single' );

		add_image_size( 'portfolio_thumbnail', $portfolio_thumbnail['width'], $portfolio_thumbnail['height'], $portfolio_thumbnail['crop'] );
		add_image_size( 'portfolio_single', $portfolio_single['width'], $portfolio_single['height'], $portfolio_single['crop'] );
	}

	/**
	 * Fix `$_SERVER` variables for various setups.
	 *
	 * Note: Removed IIS handling due to wp_fix_server_vars()
	 */
	private function fix_server_vars() {
		// NGINX Proxy
		if ( ! isset( $_SERVER['REMOTE_ADDR'] ) && isset( $_SERVER['HTTP_REMOTE_ADDR'] ) ) {
			$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_REMOTE_ADDR'];
		}

		if ( ! isset( $_SERVER['HTTPS'] ) ) {
			if ( ! empty( $_SERVER['HTTP_HTTPS'] ) ) {
				$_SERVER['HTTPS'] = $_SERVER['HTTP_HTTPS'];
			} elseif ( ! empty( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) {
				$_SERVER['HTTPS'] = '1';
			}
		}
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
	 * Get the template path.
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'axisbuilder_template_path', 'axisbuilder/' );
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
