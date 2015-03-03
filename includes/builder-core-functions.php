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
include( 'builder-formatting-functions.php' );
include( 'builder-helper-functions.php' );

/**
 * Get an image size.
 *
 * Variable is filtered by axisbuilder_get_image_size_{image_size}
 *
 * @param  mixed $image_size
 * @return array
 */
function axisbuilder_get_image_size( $image_size ) {
	if ( is_array( $image_size ) ) {
		$width  = isset( $image_size[0] ) ? $image_size[0] : '300';
		$height = isset( $image_size[1] ) ? $image_size[1] : '300';
		$crop   = isset( $image_size[2] ) ? $image_size[2] : 1;

		$size = array(
			'width'  => $width,
			'height' => $height,
			'crop'   => $crop
		);
		$image_size = $width . '_' . $height;
	} elseif ( in_array( $image_size, array( 'portfolio_thumbnail', 'portfolio_single' ) ) ) {
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
 * Queue some JavaScript code to be output in the footer.
 * @param string $code
 */
function axisbuilder_enqueue_js( $code ) {
	global $axisbuilder_queued_js;

	if ( empty( $axisbuilder_queued_js ) ) {
		$axisbuilder_queued_js = '';
	}

	$axisbuilder_queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 */
function axisbuilder_print_js() {
	global $axisbuilder_queued_js;

	if ( ! empty( $axisbuilder_queued_js ) ) {

		echo "<!-- AxisBuilder JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) {";

		// Sanitize
		$axisbuilder_queued_js = wp_check_invalid_utf8( $axisbuilder_queued_js );
		$axisbuilder_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $axisbuilder_queued_js );
		$axisbuilder_queued_js = str_replace( "\r", '', $axisbuilder_queued_js );

		echo $axisbuilder_queued_js . "});\n</script>\n";

		unset( $axisbuilder_queued_js );
	}
}

/**
 * AxisBuilder Core Supported Themes.
 * @return array
 */
function axisbuilder_get_core_supported_themes() {
	return array( 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentytwelve','twentyeleven', 'twentyten' );
}

/**
 * Get a Page Builder Layout Supported Screens or Post types.
 * @return array
 */
function axisbuilder_get_layout_supported_screens() {
	return apply_filters( 'axisbuilder_layout_supported_screens', array( 'post', 'page', 'portfolio', 'jetpack-portfolio' ) );
}
