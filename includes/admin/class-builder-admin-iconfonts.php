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
	 */
	public static function get_iconfonts() {
		$iconfonts = get_option( 'axisbuilder_custom_iconfonts' );
		$counter   = count( $iconfonts );

		foreach ( $iconfonts as $iconfont => $info ) {
			$icons    = array();
			$icon_set = array();

			$output   = '<div class="icon_set-' . $iconfont . ' metabox-holder">';
			$output  .= '<div class="postbox">';

			// Include Config file xD
			include( AB_UPLOAD_DIR . $info['include'] . '/' . $info['config'] );

			if ( ! empty( $icons ) ) {
				$icon_set = array_merge( $icon_set, $icons );
			}

			if ( ! empty( $icon_set ) ) {
				foreach ( $icon_set as $icons ) {
					$count = count( $icons );
				}

				$output .= '</div><!-- .postbox-->';
				$output .= '</div><!-- .icon_set-' . $iconfont . ' -->';
				echo $output;
			}
		}
	}
}
