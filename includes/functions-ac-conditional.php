<?php
/**
 * AxisComposer Conditional Functions
 *
 * Functions for determining the current query/page.
 *
 * @author   AxisThemes
 * @category Core
 * @package  AxisComposer/Functions
 * @version  1.0.0
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
	 * @param  int $post_id Post ID.
	 * @return bool
	 */
	function is_pagebuilder_active( $post_id ) {
		return apply_filters( 'axiscomposer_is_pagebuilder_active', 'active' === get_post_meta( $post_id, '_pagebuilder_status', true ), $post_id );
	}
}
