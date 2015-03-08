<?php
/**
 * Display notices in admin.
 *
 * @class       AB_Admin_Notices
 * @package     AxisBuilder/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Admin_Notices Class
 */
class AB_Admin_Notices {

	/**
	 * Array of notices - name => callback
	 * @var array
	 */
	private $notices = array(
		'theme_support'       => 'theme_check_notice',
		'translation_upgrade' => 'translation_upgrade_notice'
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'switch_theme', array( $this, 'reset_admin_notices' ) );
		add_action( 'axisbuilder_installed', array( $this, 'reset_admin_notices' ) );
		add_action( 'wp_loaded', array( $this, 'hide_notices' ) );
		add_action( 'axisbuilder_hide_translation_upgrade_notice', array( $this, 'hide_translation_upgrade_notice' ) );
		add_action( 'admin_print_styles', array( $this, 'add_notices' ) );
	}

	/**
	 * Reset notices for themes when switched or a new version of AB is installed.
	 */
	public function reset_admin_notices() {
		if ( ! current_theme_supports( 'axisbuilder' ) && ! in_array( get_option( 'template' ), axisbuilder_get_core_supported_themes() ) ) {
			self::add_notice( 'theme_support' );
		}
	}

	/**
	 * Show a notice
	 * @param string $name
	 */
	public static function add_notice( $name ) {
		$notices = array_unique( array_merge( get_option( 'axisbuilder_admin_notices', array() ), array( $name ) ) );
		update_option( 'axisbuilder_admin_notices', $notices );
	}

	/**
	 * Remove a notice from being displayed
	 * @param string $name
	 */
	public static function remove_notice( $name ) {
		$notices = array_diff( get_option( 'axisbuilder_admin_notices', array() ), array( $name ) );
		update_option( 'axisbuilder_admin_notices', $notices );
	}

	/**
	 * See if a notice is being shown
	 * @param  string  $name
	 * @return boolean
	 */
	public static function has_notice( $name ) {
		return in_array( $name, get_option( 'axisbuilder_admin_notices', array() ) );
	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public function hide_notices() {
		if ( isset( $_GET['axisbuilder-hide-notice'] ) ) {
			$hide_notice = sanitize_text_field( $_GET['axisbuilder-hide-notice'] );
			self::remove_notice( $hide_notice );
			do_action( 'axisbuilder_hide_' . $hide_notice . '_notice' );
		}
	}

	/**
	 * Hide translation upgrade message
	 */
	public function hide_translation_upgrade_notice() {
		update_option( 'axisbuilder_language_pack_version', array( AB_VERSION , get_locale() ) );
	}

	/**
	 * Add notices + styles if needed.
	 */
	public function add_notices() {
		$notices = get_option( 'axisbuilder_admin_notices', array() );

		foreach ( $notices as $notice ) {
			wp_enqueue_style( 'axisbuilder-activation', AB()->plugin_url() . '/assets/styles/activation.css', array(), AB_VERSION );
			add_action( 'admin_notices', array( $this, $this->notices[ $notice ] ) );
		}
	}

	/**
	 * Show the Theme Check notice
	 */
	public function theme_check_notice() {
		if ( ! current_theme_supports( 'axisbuilder' ) ) {
			include( 'views/html-notice-theme-support.php' );
		}
	}

	/**
	 * Show the translation upgrade notice
	 */
	public function translation_upgrade_notice() {
		$screen = get_current_screen();

		if ( 'update-core' !== $screen->id ) {
			include( 'views/html-notice-translation-upgrade.php' );
		}
	}
}

new AB_Admin_Notices();
