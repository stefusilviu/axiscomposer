<?php
/**
 * AxisBuilder Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @package     AxisBuilder/Functions
 * @category    Core
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include core functions (available in both admin and frontend)
include( 'builder-helper-functions.php' );
include( 'builder-deprecated-functions.php' );

/**
 * Clean variables
 * @param  string $var
 * @return string
 */
function axisbuilder_clean( $var ) {
	return sanitize_text_field( $var );
}

/**
 * Get all Custom Post Types Screen
 * @return array
 */
function axisbuilder_get_screen_types() {
	global $wp_post_types;
	$post_types   = get_post_types( array( 'public' => true, 'show_in_menu' => true, '_builtin' => false ), 'names' );
	$screen_types = apply_filters( 'axisbuilder_screens_types', array(
		'post' => __( 'Post', 'axisbuilder' ),
		'page' => __( 'Page', 'axisbuilder' )
	) );

	// Fetch Public Custom Post Types
	foreach ( $post_types as $post_type ) {
		$screen_types[ $post_type ] = $wp_post_types[ $post_type ]->labels->name;
	}

	if ( apply_filters( 'axisbuilder_sort_screens', true ) ) {
		asort( $screen_types );
	}

	return $screen_types;
}

/**
 * AxisBuilder Core Supported Themes
 * @return array
 */
function axisbuilder_get_core_supported_themes() {
	return array( 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentytwelve','twentyeleven', 'twentyten' );
}

/**
 * Get a Page Builder Supported Screens or Post types
 * @return array
 */
function get_builder_core_supported_screens() {
	return apply_filters( 'axisbuilder_supported_screens', array( 'post', 'page', 'axis-portfolio', 'jetpack-portfolio' ) );
}
