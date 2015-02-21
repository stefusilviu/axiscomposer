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
		// $iconfonts = self::get_iconfonts();

		include_once( 'views/html-admin-page-iconfonts.php' );
	}

	/**
	 * Returns the iconfonts to show in admin.
	 */
	public static function get_iconfonts() {
		$fonts = get_option( 'axisbuilder_custom_iconfonts' );

		foreach ( $fonts as $iconfont => $info ) {
			$chars   = array();
			$charmap = array();

			$output  = '<div class="iconfonts-container-' . count( $iconfont ) . ' metabox-holder">';
			$output .= '<div class="postbox">';

			// Include Charmap config file
			include( AB_UPLOAD_DIR . $info['include'] . '/' . $info['config'] );

			if ( ! empty( $chars ) ) {
				$charmap = array_merge( $charmap, $chars );
			}

			if ( ! empty( $charmap ) ) {
				foreach ( $charmap as $chars ) {
					$iconfont_count = count( $chars );
				}

				$title  = ( $iconfont === 'icomoon' || $iconfont === 'Fontawesome' ) ? __( 'Default Icons', 'axisbuilder' ) : ucfirst( $iconfont );

				$output .= '<h3 class="iconfont-title"><strong>' . $title . '</strong><span class="iconfont-count count-' . $iconfont_count . '">' . number_format_i18n( $iconfont_count ) . '</span></h3>';

				$output .= '<a class="modal-close modal-close-link" href="#"><span class="close-icon"><span class="screen-reader-text">Close media panel</span></span></a>';

				$output .= '<div class="inside"><div class="iconfont-action"></div>';
				$output .= '<div class="iconfont-search">';
				$output .= '<ul class="iconfont-lists icon">';

				foreach ( $charmap as $chars ) {
					foreach ( $chars as $char ) {
						$output .= '<li title="' . $char . '"><i class="' . $iconfont . '-' . $char . '"></i><label class="iconfont">' . $char . '</label></li>';
					}
				}

				$output .= '</ul></div></div></div></div>';
				echo $output;
			}
		}
	}
}
