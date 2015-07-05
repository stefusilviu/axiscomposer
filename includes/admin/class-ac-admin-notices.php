<?php
/**
 * Display notices in admin.
 *
 * @class       AC_Admin_Notices
 * @package     AxisComposer/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Admin_Notices Class
 */
class AC_Admin_Notices {

	/**
	 * Array of notices - name => callback
	 * @var array
	 */
	private $core_notices = array(
		'install'             => 'install_notice',
		'update'              => 'update_notice',
		'theme_support'       => 'theme_check_notice',
		'translation_upgrade' => 'translation_upgrade_notice'
	);

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'switch_theme', array( $this, 'reset_admin_notices' ) );
		add_action( 'axiscomposer_installed', array( $this, 'reset_admin_notices' ) );
		add_action( 'wp_loaded', array( $this, 'hide_notices' ) );
		add_action( 'axiscomposer_hide_translation_upgrade_notice', array( $this, 'hide_translation_upgrade_notice' ) );

		if ( current_user_can( 'manage_axiscomposer' ) ) {
			add_action( 'admin_print_styles', array( $this, 'add_notices' ) );
		}
	}

	/**
	 * Remove all notices
	 */
	public static function remove_all_notices() {
		delete_option( 'axiscomposer_admin_notices' );
	}

	/**
	 * Reset notices for themes when switched or a new version of AC is installed.
	 */
	public function reset_admin_notices() {
		if ( ! current_theme_supports( 'axiscomposer' ) && ! in_array( get_option( 'template' ), ac_get_core_supported_themes() ) ) {
			self::add_notice( 'theme_support' );
		}
	}

	/**
	 * Show a notice
	 * @param string $name
	 */
	public static function add_notice( $name ) {
		$notices = array_unique( array_merge( get_option( 'axiscomposer_admin_notices', array() ), array( $name ) ) );
		update_option( 'axiscomposer_admin_notices', $notices );
	}

	/**
	 * Remove a notice from being displayed
	 * @param string $name
	 */
	public static function remove_notice( $name ) {
		$notices = array_diff( get_option( 'axiscomposer_admin_notices', array() ), array( $name ) );
		update_option( 'axiscomposer_admin_notices', $notices );
	}

	/**
	 * See if a notice is being shown
	 * @param  string  $name
	 * @return boolean
	 */
	public static function has_notice( $name ) {
		return in_array( $name, get_option( 'axiscomposer_admin_notices', array() ) );
	}

	/**
	 * Hide a notice if the GET variable is set.
	 */
	public function hide_notices() {
		if ( isset( $_GET['ac-hide-notice'] ) ) {
			$hide_notice = sanitize_text_field( $_GET['ac-hide-notice'] );
			self::remove_notice( $hide_notice );
			do_action( 'axiscomposer_hide_' . $hide_notice . '_notice' );
		}
	}

	/**
	 * Hide translation upgrade message
	 */
	public function hide_translation_upgrade_notice() {
		update_option( 'axiscomposer_language_pack_version', array( AC_VERSION , get_locale() ) );
	}

	/**
	 * Add notices + styles if needed.
	 */
	public function add_notices() {
		$notices = get_option( 'axiscomposer_admin_notices', array() );

		if ( $notices ) {
			wp_enqueue_style( 'axiscomposer-activation', AC()->plugin_url() . '/assets/css/activation.css', array(), AC_VERSION );
			foreach ( $notices as $notice ) {
				if ( ! empty( $this->core_notices[ $notice ] ) && apply_filters( 'axiscomposer_show_admin_notice', true, $notice ) ) {
					add_action( 'admin_notices', array( $this, $this->core_notices[ $notice ] ) );
				}
			}
		}
	}

	/**
	 * If we need to update, include a message with the update button
	 */
	public function update_notice() {
		include( 'views/html-notice-update.php' );
	}

	/**
	 * If we have just installed, show a message with the install button
	 */
	public function install_notice() {
		include( 'views/html-notice-install.php' );
	}

	/**
	 * Show the Theme Check notice
	 */
	public function theme_check_notice() {
		if ( ! current_theme_supports( 'axiscomposer' ) && ! in_array( get_option( 'template' ), ac_get_core_supported_themes() ) ) {
			include( 'views/html-notice-theme-support.php' );
		} else {
			self::remove_notice( 'theme_support' );
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

new AC_Admin_Notices();
