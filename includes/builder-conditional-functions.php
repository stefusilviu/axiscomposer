<?php
/**
 * AxisBuilder Conditional Functions
 *
 * Functions for determining the current query/page.
 *
 * @package     AxisBuilder/Functions
 * @category    Core
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'is_ajax' ) ) {

	/**
	 * is_ajax - Returns true when the page is loaded via ajax.
	 * @return bool
	 */
	function is_ajax() {
		return defined( 'DOING_AJAX' );
	}
}

if ( ! function_exists( 'is_pagebuilder_active' ) ) {

	/**
	 * is_pagebuilder_active - Returns true when Page Builder is active.
	 * @return bool
	 */
	function is_pagebuilder_active( $post_ID ) {
		return apply_filters( 'axisbuilder_pagebuilder_active', get_post_meta( $post_ID, '_axisbuilder_status', true ), $post_ID );
	}
}
