<?php
/**
 * AxisComposer Admin Iconfont Class.
 *
 * @class       AC_Admin_Iconfont
 * @package     AxisComposer/Admin
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Admin_Iconfont Class
 */
class AC_Admin_Iconfont {

	/**
	 * Handles output of the iconfont page in admin.
	 */
	public static function output() {
		include_once( 'views/html-admin-page-iconfont.php' );
	}
}
