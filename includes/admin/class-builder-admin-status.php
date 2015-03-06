<?php
/**
 * Debug/Status page
 *
 * @class       AB_Admin_Status
 * @package     AxisBuilder/Admin/System Status
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Admin_Status Class
 */
class AB_Admin_Status {

	/**
	 * Handles output of the status page in admin.
	 */
	public static function output() {
		include_once( 'views/html-admin-page-status.php' );
	}

	/**
	 * Handles output of reports
	 */
	public static function status_report() {

	}

	/**
	 * Handles output of tools
	 */
	public static function status_tools() {

	}
}
