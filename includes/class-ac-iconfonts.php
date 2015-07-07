<?php
/**
 * AxisComposer Iconfonts
 *
 * Handles the Iconfonts Upload easily.
 *
 * @class       AC_Iconfonts
 * @package     AxisComposer/Classes
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Iconfonts Class
 */
class AC_Iconfonts {

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts',    array( __CLASS__, 'inline_styles' ), 11 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'inline_styles' ), 11 );
	}

	/**
	 * Adds iconfont inline styles.
	 */
	public static function inline_styles() {
		$font_face = '';

		if ( current_user_can( 'manage_axiscomposer' ) ) {
			wp_add_inline_style( is_admin() ? 'axiscomposer-admin' : 'axiscomposer-general', $font_face );
		}
	}
}

AC_Iconfonts::init();
