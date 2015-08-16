<?php
/**
 * AxisComposer Shortcode Functions
 *
 * Functions for the shortcode system.
 *
 * @author   AxisThemes
 * @category Core
 * @package  AxisComposer/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create a new shortcode data programmatically.
 */
function ac_shortcode_data( $name, $content = null, $args = array() ) {
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
	// $_shortcode = str_replace( '\n', '', $_shortcode );

	return $_shortcode;
}
