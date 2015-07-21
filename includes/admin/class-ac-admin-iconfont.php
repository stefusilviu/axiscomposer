<?php
/**
 * AxisComposer Admin Iconfont Class.
 *
 * @class    AC_Admin_Iconfont
 * @version  1.0.0
 * @package  AxisComposer/Admin
 * @category Admin
 * @author   AxisThemes
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
