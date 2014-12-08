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

if ( ! function_exists( 'get_builder_core_supported_themes' ) ) :

/**
 * AxisBuilder Supported Themes
 * @return string[]
 */
function get_builder_core_supported_themes() {
	return array( 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentyeleven', 'twentytwelve', 'twentyten' );
}

endif;

if ( ! function_exists( 'get_builder_core_supported_screens' ) ) :

/**
 * Get a Page Builder Supported Screens or Post types.
 * @return string[]
 */
function get_builder_core_supported_screens() {
	return apply_filters( 'axisbuilder_supported_screens', array( 'post', 'page', 'portfolio', 'axis-portfolio', 'jetpack-portfolio' ) );
}

endif;

if ( ! function_exists( 'get_builder_core_shortcode_tabs' ) ) :

/**
 * Get a Page Builder Supported Shortcode Tabs.
 * @return array
 */
function get_builder_core_shortcode_tabs() {
	$shortcode_tabs = array(
		'layout-tab'  => __( 'Layout Elements',  'axisbuilder' ),
		'content-tab' => __( 'Content Elements', 'axisbuilder' ),
		'media-tab'   => __( 'Media Elements',   'axisbuilder' ),
		'plugin-tab'  => __( 'Plugin Additions', 'axisbuilder' ),
	);

	return apply_filters( 'axisbuilder_shortcode_tabs', $shortcode_tabs );
}

endif;
