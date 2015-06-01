<?php
/**
 * AxisComposer Admin Iconfonts Class.
 *
 * @class       AC_Admin_Iconfonts
 * @package     AxisComposer/Admin
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Admin_Iconfonts Class
 */
class AC_Admin_Iconfonts {

	/**
	 * Handles output of the iconfonts page in admin.
	 */
	public static function output() {
		include_once( 'views/html-admin-page-iconfonts.php' );
	}

	/**
	 * Returns the iconfonts to show in admin.
	 */
	public static function get_iconfonts() {
		$fonts = get_option( 'axiscomposer_custom_iconfonts' );

		foreach ( $fonts as $iconfont => $info ) {
			$chars = $charmap = array();
			$title = ( 'Fontawesome' == $iconfont ) ? __( 'Default Icons', 'axiscomposer' ) : esc_html( ucfirst( $iconfont ) );

			// Include Charmap config file
			include( AC_UPLOAD_DIR . $info['include'] . '/' . $info['config'] );

			if ( ! empty( $chars ) ) {
				$charmap = array_merge( $charmap, $chars );
				foreach ( $charmap as $chars ) {
					$iconfont_lists = '';
					$iconfont_count = count( $chars );
					foreach ( $chars as $char ) {
						$iconfont_lists .= '<li title="' . $char . '"><i class="' . $iconfont . '-' . $char . '"></i><label class="iconfont">' . $char . '</label></li>';
					}
				}
			}

			if ( ! empty( $charmap ) ) {
				$output  = '<div id="' . esc_attr( strtolower( $iconfont ) ) . '" class="metabox-holder iconfont-wrap">';
				$output .= '<div class="postbox">';
				$output .= '<h3 class="iconfont-title"><strong>' . $title . '</strong> &mdash; <span class="iconfont-count count-' . $iconfont_count . '">' . number_format_i18n( $iconfont_count ) . '</span></h3>';
				$output .= '<a class="modal-close modal-close-link del-iconfont" href="#" data-delete="' . $iconfont . '"><span class="close-icon"><span class="screen-reader-text">Close media panel</span></span></a>';
				$output .= '<div class="inside">';
				$output .= '<div class="icons-container">';
				$output .= '<ul class="iconfont-lists icon">';
				$output .= $iconfont_lists;
				$output .= '</ul>';
				$output .= '</div></div></div></div>';
				echo $output;
			}
		}
	}
}
