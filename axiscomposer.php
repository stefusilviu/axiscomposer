<?php
/**
 * Plugin Name: AxisComposer
 * Plugin URI: http://www.axisthemes.com/axiscomposer/
 * Description: A drag and drop builder that helps you compose anything. Beautifully.
 * Version: 1.0.0-dev
 * Author: AxisThemes
 * Author URI: http://axisthemes.com
 * Requires at least: 4.2
 * Tested up to: 4.3
 *
 * Text Domain: axiscomposer
 * Domain Path: /i18n/languages/
 *
 * @package  AxisComposer
 * @category Core
 * @author   AxisThemes
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'AxisComposer' ) ) :

/**
 * Main AxisComposer Class
 *
 * @class    AxisComposer
 * @property mixed $shortcodes The shortcodes class
 * @version  1.0.0
 */
final class AxisComposer {

	/**
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * @var AxisComposer The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * @var AC_Integrations $integrations
	 */
	public $integrations = null;

	/**
	 * Main AxisComposer Instance
	 *
	 * Ensure only one instance of AxisComposer is loaded or can be loaded.
	 *
	 * @static
	 * @see    AC()
	 * @return AxisComposer - Main instance
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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'axiscomposer' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'axiscomposer' ), '1.0' );
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
	 * AxisComposer Constructor.
	 */
	public function __construct() {
		$this->define_constants();
		$this->includes();
		$this->init_hooks();

		do_action( 'axiscomposer_loaded' );
	}

	/**
	 * Hook into actions and filters.
	 */
	private function init_hooks() {
		register_activation_hook( __FILE__, array( 'AC_Install', 'install' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_environment' ) );
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );
		add_action( 'init', array( $this, 'shortcodes' ) );
		add_action( 'init', array( $this, 'init' ), 0 );
	}

	/**
	 * Define AC Constants.
	 */
	private function define_constants() {
		$upload_dir = wp_upload_dir();
		if ( is_ssl() ) {
			$upload_dir['baseurl'] = str_replace( 'http://', 'https://', $upload_dir['baseurl'] );
		}

		$this->define( 'AC_PLUGIN_FILE', __FILE__ );
		$this->define( 'AC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		$this->define( 'AC_VERSION', $this->version );
		$this->define( 'AC_LOG_DIR', $upload_dir['basedir'] . '/ac-logs/' );
		$this->define( 'AC_ICONFONT_DIR', $upload_dir['basedir'] . '/ac-iconfonts/' );
		$this->define( 'AC_ICONFONT_URL', $upload_dir['baseurl'] . '/ac-iconfonts/' );
	}

	/**
	 * Define constant if not already set.
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
		include_once( 'includes/functions-ac-core.php' );
		include_once( 'includes/functions-ac-widget.php' );
		include_once( 'includes/class-ac-autoloader.php' );
		include_once( 'includes/class-ac-install.php' );
		include_once( 'includes/class-ac-ajax.php' );

		if ( $this->is_request( 'admin' ) ) {
			include_once( 'includes/admin/class-ac-admin.php' );
		}

		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_includes();
		}

		if ( 'yes' === get_option( 'axiscomposer_sidebar_enabled', 'yes' ) ) {
			include_once( 'includes/class-ac-sidebars.php' );                // Sidebar Builder
		}

		include_once( 'includes/class-ac-iconfont.php' );                    // Iconfont Manager
		include_once( 'includes/class-ac-post-types.php' );                  // Registers post types
		include_once( 'includes/abstracts/abstract-ac-settings-api.php' );   // Settings API (for shortcodes, and integrations)
		include_once( 'includes/abstracts/abstract-ac-shortcode.php' );      // Shortcodes
		include_once( 'includes/abstracts/abstract-ac-integration.php' );    // An integration with a service
		include_once( 'includes/class-ac-integrations.php' );                // Loads integrations
		include_once( 'includes/class-ac-language-pack-upgrader.php' );      // Download/update languages
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		include_once( 'includes/class-ac-frontend-scripts.php' );            // Frontend Scripts
	}

	/**
	 * Function used to Init AxisComposer Template Functions - This makes them pluggable by plugins and themes.
	 */
	public function include_template_functions() {
		include_once( 'includes/functions-ac-template.php' );
	}

	/**
	 * Init AxisComposer when WordPress Initialises.
	 */
	public function init() {
		// Before init action
		do_action( 'before_axiscomposer_init' );

		// Set up localisation
		$this->load_plugin_textdomain();

		// Load class instances
		$this->integrations = new AC_Integrations();                         // Integrations class

		// Init action
		do_action( 'axiscomposer_init' );
	}

	/**
	 * Load Localisation files.
	 *
	 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
	 *
	 * Admin Locales are found in:
	 * 		- WP_LANG_DIR/axiscomposer/axiscomposer-admin-LOCALE.mo
	 * 		- WP_LANG_DIR/plugins/axiscomposer-admin-LOCALE.mo
	 *
	 * Frontend/global Locales found in:
	 * 		- WP_LANG_DIR/axiscomposer/axiscomposer-LOCALE.mo
	 * 	 	- axiscomposer/i18n/languages/axiscomposer-LOCALE.mo (which if not found falls back to:)
	 * 	 	- WP_LANG_DIR/plugins/axiscomposer-LOCALE.mo
	 */
	public function load_plugin_textdomain() {
		$locale = apply_filters( 'plugin_locale', get_locale(), 'axiscomposer' );

		if ( $this->is_request( 'admin' ) ) {
			load_textdomain( 'axiscomposer', WP_LANG_DIR . '/axiscomposer/axiscomposer-admin-' . $locale . '.mo' );
			load_textdomain( 'axiscomposer', WP_LANG_DIR . '/plugins/axiscomposer-admin-' . $locale . '.mo' );
		}

		load_textdomain( 'axiscomposer', WP_LANG_DIR . '/axiscomposer/axiscomposer-' . $locale . '.mo' );
		load_plugin_textdomain( 'axiscomposer', false, plugin_basename( dirname( __FILE__ ) ) . "/i18n/languages" );
	}

	/**
	 * Ensure theme compatibility and setup image sizes.
	 */
	public function setup_environment() {
		$this->add_thumbnail_support();
		$this->add_image_sizes();
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
	 * Add AC Image sizes to WP.
	 */
	private function add_image_sizes() {
		$portfolio_thumbnail = ac_get_image_size( 'portfolio_thumbnail' );
		$portfolio_single	 = ac_get_image_size( 'portfolio_single' );

		add_image_size( 'portfolio_thumbnail', $portfolio_thumbnail['width'], $portfolio_thumbnail['height'], $portfolio_thumbnail['crop'] );
		add_image_size( 'portfolio_single', $portfolio_single['width'], $portfolio_single['height'], $portfolio_single['crop'] );
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
		return apply_filters( 'axiscomposer_template_path', 'axiscomposer/' );
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
	 * @return AC_Shortcodes
	 */
	public function shortcodes() {
		return AC_Shortcodes::instance();
	}
}

endif;

/**
 * Returns the main instance of AC to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return AxisComposer
 */
function AC() {
	return AxisComposer::instance();
}

// Global for backwards compatibility.
$GLOBALS['axiscomposer'] = AC();
