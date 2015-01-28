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

		// Font file extract by ajax functions
		add_action( 'wp_ajax_axisbuilder_add_zipped_font', array( $this, 'add_zipped_font' ) );
		add_action( 'wp_ajax_axisbuilder_remove_zipped_font', array( $this, 'remove_zipped_font' ) );
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
	 * AJAX Add Zipped Icon Fonts.
	 */
	public function add_zipped_font() {
		check_ajax_referer( 'add-custom-iconfont', 'security' );

		$this->is_capable();

		// Get the file Path if the Zip file
		// $attachment = $_POST['value'];
		// $zipfile    = realpath( get_attached_file( $attachment['id'] ) );

		exit( 'axisbuilder_iconfont_added' );
	}

	/**
	 * AJAX Add Zipped Icon Fonts.
	 */
	public function remove_zipped_font() {
		check_ajax_referer( 'delete-custom-iconfont', 'security' );

		$this->is_capable();
	}

	/**
	 * Generate Icon Font Sets Preview
	 */
	public static function get_iconfont_sets() {

	}
}

new AB_Icon_Fonts();
