<?php
/**
 * AxisBuilder Icon Fonts
 *
 * Handles the Icon Fonts Upload easily.
 *
 * @class       AB_Icon_Fonts
 * @package     AxisBuilder/Classes
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Icon_Fonts Class
 */
class AB_Icon_Fonts {

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( apply_filters( 'axisbuilder_show_icon_fonts_manager_page', true ) ) {
			add_action( 'admin_menu', array( $this, 'iconfonts_menu' ), 50 );
		}
	}

	/**
	 * Check for capability.
	 */
	public function is_capable() {
		if ( ! current_user_can( 'update_plugins' ) ) {
			exit( __( 'Using this feature is reserved for Super Admins. You unfortunately don\'t have the necessary permissions.', 'axisbuilder' ) );
		}
	}

	/**
	 * Add menu item
	 */
	public function iconfonts_menu() {
		add_options_page( __( 'Icon Manager', 'axisbuilder' ), __( 'Icon Manager', 'axisbuilder' ), 'edit_theme_options', 'axisbuilder-iconfonts', array( $this, 'iconfonts_page' ), 60 );
	}

	/**
	 * View Icon-Fonts page
	 */
	public function iconfonts_page() {
		include_once( 'admin/views/html-admin-page-icon-fonts.php' );
	}

	/**
	 * Generate Icon Font Sets Preview
	 */
	public static function get_iconfont_sets() {

	}
}

new AB_Icon_Fonts();
