<?php
/**
 * AxisComposer Conditional Functions
 *
 * Functions for determining the current query/page.
 *
 * @package     AxisComposer/Functions
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
	 * @param  int $post_id Post ID.
	 * @return bool
	 */
	function is_pagebuilder_active( $post_id ) {
		return apply_filters( 'axiscomposer_is_pagebuilder_active', 'active' === get_post_meta( $post_id, '_pagebuilder_status', true ) ? true : false, $post_id );
	}
}

if ( ! function_exists( 'is_gist_shortcode' ) ) {

	/**
	 * is_gist_shortcode - Returns true when the gist shortcode is loaded.
	 * @return bool
	 */
	function is_gist_shortcode() {
		global $post;

		return is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'ac_gist' );
	}
}
