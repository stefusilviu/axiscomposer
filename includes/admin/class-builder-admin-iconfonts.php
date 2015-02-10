<?php
/**
 * AxisBuilder Admin Iconfonts Class.
 *
 * @class       AB_Admin_Iconfonts
 * @package     AxisBuilder/Admin
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Admin_Iconfonts Class
 */
class AB_Admin_Iconfonts {

	/**
	 * Handles output of the iconfonts page in admin.
	 */
	public static function output() {
		$iconfonts = self::get_iconfonts();

		include_once( 'views/html-admin-page-iconfonts.php' );
	}

	/**
	 * Returns the iconfonts to show in admin.
	 * @todo get_iconfonts to load entire list of liconfonts for admin.
	 */
	public static function get_iconfonts() {}
}
