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
		// Check if the content is empty.
		$content = "\n" . trim( stripslashes( $content ) ) . "\n";
		if ( trim( $content ) == '' ) {
			$content = '';
		}

		$_text .= $content;
	}

	// Add the closing tag.
	return $_text . '[/' . $tag . ']' . "\n\n";
}
