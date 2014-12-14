<?php
/**
 * AxisBuilder Helper Functions
 *
 * Helper functions related to shortcodes.
 *
 * @package     AxisBuilder/Functions
 * @category    Core
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'ab_create_shortcode_data' ) ) :

/**
 * Create a new shortcode data programmatically.
 */
function ab_create_shortcode_data( $name, $content = null, $args = array() ) {
	$_shortcode = '[' . $name;

	if ( is_array( $args ) ) {
		foreach ( $args as $key => $arg ) {
			if ( is_numeric( $key ) ) {
				$_shortcode .= ' ' . $arg;
			} else {
				if ( ( strpos( $arg, "'" ) === false ) && ( strpos( $arg, '&#039;' ) === false ) ) {
					$_shortcode .= " " . $key . "='" . $arg . "'";
				} else {
					$_shortcode .= ' ' . $key . '="' . $arg . '"';
				}
			}
		}
	}

	$_shortcode .= ']';

	if ( ! is_null( $content ) ) {
		// Strip-slashes and trim the content
		$content = "\n" . trim( stripslashes( $content ) ) . "\n"; // Testdrive: add htmlentities()

		// If the content is empty without tabs and line breaks remove it completely
		if ( trim( $content ) == '' ) {
			$content = '';
		}

		$_shortcode .= $content . '[/' . $name . ']';
	}

	$_shortcode .= "\n\n";
	// $_shortcode = str_replace('\n', '', $_shortcode );

	return $_shortcode;
}

endif;
