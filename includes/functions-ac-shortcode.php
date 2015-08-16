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
 * Generate a string from shortcode parameters.
 * @param  string $tag
 * @param  array  $attrs
 * @param  string $content
 * @param  string $type
 * @return string
 */
function ac_shortcode_string( $tag, $attrs = array(), $content = '', $type = 'closed' ) {
	$_text = '[' . $tag;

	// Parse shortcode attributes.
	if ( is_array( $attrs ) ) {
		foreach ( $attrs as $name => $value ) {
			if ( is_numeric( $name ) ) {
				$_text .= ' ' . $value;
			} else {
				$_text .= ' ' . $name . '="' . $value . '"';
			}
		}
	}

	// Close the opening tag.
	if ( 'single' === $type ) {
		return $_text . ']';
	} elseif ( 'self-closing' === $type ) {
		return $_text . ' /]';
	}

	// Complete the opening tag.
	$_text .= ']';

	if ( $content ) {
		$_text .= "\n" . trim( stripslashes( $content ) ) . "\n";
	}

	// Add the closing tag.
	return $_text . '[/' . $tag . ']';
}

/**
 * Create a new shortcode data programmatically.
 * @deprecated 1.0 Deprecated in favour of ac_shortcode_string.
 */
function ac_shortcode_data( $name, $content = null, $args = array() ) {
	$_shortcode = '[' . $name;

	if ( is_array( $args ) ) {
		foreach ( $args as $key => $arg ) {
			if ( is_numeric( $key ) ) {
				$_shortcode .= ' ' . $arg;
			} else {
				$_shortcode .= ' ' . $key . '="' . $arg . '"';
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
