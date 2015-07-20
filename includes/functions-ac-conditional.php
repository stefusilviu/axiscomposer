<?php
/**
 * AxisComposer Conditional Functions
 *
 * Functions for determining the current query/page.
 *
 * @package  AxisComposer/Functions
 * @category Core
 * @author   AxisThemes
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

if ( ! function_exists( 'is_shortcode_tag' ) ) {

	/**
	 * is_shortcode_tag - Returns true when the shortcode tag is found.
	 * @param  string $tag Shortcode tag to check.
	 * @return bool
	 */
	function is_shortcode_tag( $tag = '' ) {
		global $post;

		return is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, $tag );
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
