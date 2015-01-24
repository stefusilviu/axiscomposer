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
	 * Add menu item
	 */
	public function iconfonts_menu() {
		add_options_page( __( 'Icon Manager', 'axisbuilder' ), __( 'Icon Manager', 'axisbuilder' ), 'edit_theme_options', 'axisbuilder-iconfonts', array( $this, 'iconfonts_page' ), 60 );
	}

	/**
	 * View Icon-Fonts page
	 */
	public function iconfonts_page() {
		$iconfonts = get_option( 'axisbuilder_iconfonts' );
		include_once( 'admin/views/html-admin-page-icon-fonts.php' );
	}

}

new AB_Icon_Fonts();
