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
		include_once( 'views/html-admin-page-iconfonts.php' );
	}
}
