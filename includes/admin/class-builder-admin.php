<?php
/**
 * AxisBuilder Admin.
 *
 * @class       AB_Admin
 * @package     AxisBuilder/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Admin Class
 */
class AB_Admin {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		// Functions
		include_once( 'builder-admin-functions.php' );

		// Classes
		include_once( 'class-builder-admin-editor.php' );
		include_once( 'class-builder-admin-meta-boxes.php' );

		// Classes we only need during non-ajax requests
		if ( ! defined( 'DOING_AJAX' ) ) {
			include_once( 'class-builder-admin-menus.php' );
			include_once( 'class-builder-admin-assets.php' );
			include_once( 'class-builder-admin-notices.php' );

			// Help
			if ( apply_filters( 'axisbuilder_enable_admin_help_tab', true ) ) {
				include_once( 'class-builder-admin-help.php' );
			}
		}
	}

	/**
	 * Change the admin footer text on AxisBuilder admin pages.
	 * @param  string $footer_text
	 * @return string
	 */
	public function admin_footer_text( $footer_text ) {
		$current_screen = get_current_screen();

		// Check to make sure we're on a AxisBuilder admin page
		if ( isset( $current_screen->id ) && apply_filters( 'axisbuilder_display_admin_footer_text', in_array( $current_screen->id, axisbuilder_get_screen_ids() ) ) ) {
			// Change the footer text
			$footer_text = sprintf( __( 'Please rate <strong>Axis Builder</strong> <a href="%1$s" target="_blank">&#9733;&#9733;&#9733;&#9733;&#9733;</a> on <a href="%1$s" target="_blank">WordPress.org</a> to help us keep this plugin free. A huge thank you from AxisThemes in advance!', 'axisbuilder' ), 'https://wordpress.org/support/view/plugin-reviews/axis-builder?filter=5#postform' );
		}

		return $footer_text;
	}
}

return new AB_Admin();
