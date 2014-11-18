<?php
/**
 * AxisBuilder Notices
 *
 * Display notices in admin.
 *
 * @class       AB_Admin_Notices
 * @package     AxisBuilder/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'AB_Admin_Notices' ) ) :

/**
 * AB_Admin_Notices Class
 */
class AB_Admin_Notices {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'switch_theme', array( $this, 'reset_admin_notices' ) );
		add_action( 'axisbuilder_updated', array( $this, 'reset_admin_notices' ) );
		add_action( 'admin_print_styles', array( $this, 'add_notices' ) );
	}

	/**
	 * Reset notices for themes when switched or a new version of AB is installed
	 */
	public function reset_admin_notices() {
		update_option( 'axisbuilder_admin_notices', array( 'theme_support' ) );
	}

	/**
	 * Add notices + styles if needed.
	 */
	public function add_notices() {

		$notices = get_option( 'axisbuilder_admin_notices', array() );

		if ( ! empty( $_GET['dismiss_ab_theme_support_notice'] ) ) {
			$notices = array_diff( $notices, array( 'theme_support' ) );
			update_option( 'axisbuilder_admin_notices', $notices );
		}

		if ( in_array( 'theme_support', $notices ) && ! current_theme_supports( 'axisbuilder' ) ) {
			$template = get_option( 'template' );

			if ( ! in_array( $template, ab_get_core_supported_themes() ) ) {
				wp_enqueue_style( 'axisbuilder-activation', AB()->plugin_url() . '/assets/styles/activation.css', array(), AB_VERSION );
				add_action( 'admin_notices', array( $this, 'theme_support_notice' ) );
			}
		}

		if ( in_array( 'translation_upgrade', $notices ) ) {
			wp_enqueue_style( 'axisbuilder-activation', AB()->plugin_url() . '/assets/styles/activation.css', array(), AB_VERSION );
			add_action( 'admin_notices', array( $this, 'translation_upgrade_notice' ) );
		}
	}

	/**
	 * Show the Theme Support notice.
	 */
	public function theme_support_notice() {
		include( 'views/html-notice-theme-support.php' );
	}

	/**
	 * Show the Translation upgrade notice.
	 */
	public function translation_upgrade_notice() {
		$screen = get_current_screen();

		if ( 'update-core' !== $screen->id ) {
			include( 'views/html-notice-translation-upgrade.php' );
		}
	}
}

endif;

return new AB_Admin_Notices();
