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
include( 'builder-conditional-functions.php' );
include( 'builder-deprecated-functions.php' );
include( 'builder-helper-functions.php' );

/**
 * Clean variables
 * @param  string $var
 * @return string
 */
function axisbuilder_clean( $var ) {
	return sanitize_text_field( $var );
}

/**
 * Get an image size.
 *
 * Variable is filtered by axisbuilder_get_image_size_{image_size}
 *
 * @param  string $image_size
 * @return array
 */
function axisbuilder_get_image_size( $image_size ) {
	if ( in_array( $image_size, array( 'portfolio_thumbnail', 'portfolio_single' ) ) ) {
		$size           = get_option( $image_size . '_image_size', array() );
		$size['width']  = isset( $size['width'] ) ? $size['width'] : '300';
		$size['height'] = isset( $size['height'] ) ? $size['height'] : '300';
		$size['crop']   = isset( $size['crop'] ) ? $size['crop'] : 0;
	} else {
		$size = array(
			'width'  => '300',
			'height' => '300',
			'crop'   => 1
		);
	}

	return apply_filters( 'axisbuilder_get_image_size_' . $image_size, $size );
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
		$screen_types[ $post_type ] = $wp_post_types[ $post_type ]->labels->menu_name;
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
	return apply_filters( 'axisbuilder_supported_screens', array( 'post', 'page', 'portfolio', 'jetpack-portfolio' ) );
}

/**
 * Enables template debug mode
 */
function axisbuilder_template_debug_mode() {
	if ( ! defined( 'AB_TEMPLATE_DEBUG_MODE' ) ) {
		$status_options = get_option( 'axisbuilder_status_options', array() );
		if ( ! empty( $status_options['template_debug_mode'] ) && current_user_can( 'manage_options' ) ) {
			define( 'AB_TEMPLATE_DEBUG_MODE', true );
		} else {
			define( 'AB_TEMPLATE_DEBUG_MODE', false );
		}
	}
}
add_action( 'after_setup_theme', 'axisbuilder_template_debug_mode', 20 );
