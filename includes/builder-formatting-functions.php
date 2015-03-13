<?php
/**
 * AxisBuilder Formatting
 *
 * Functions for formatting data.
 *
 * @package     AxisBuilder/Functions
 * @category    Core
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Clean variables
 * @param  string $var
 * @return string
 */
function axisbuilder_clean( $var ) {
	return sanitize_text_field( $var );
}

/**
 * Sanitize a string destined to be a tooltip. Prevents XSS.
 * @param string $var
 * @return string
 */
function axisbuilder_sanitize_tooltip( $var ) {
	return wp_kses( html_entity_decode( $var ), array(
		'br'     => array(),
		'em'     => array(),
		'strong' => array(),
		'span'   => array(),
		'ul'     => array(),
		'li'     => array(),
		'ol'     => array(),
		'p'      => array(),
    ) );
}

/**
 * Merge two arrays
 * @param  array $a1
 * @param  array $a2
 * @return array
 */
function axisbuilder_array_overlay( $a1, $a2 ) {
	foreach ( $a1 as $k => $v ) {
		if ( ! array_key_exists( $k, $a2 ) ) {
			continue;
		}
		if ( is_array( $v ) && is_array( $a2[ $k ] ) ) {
			$a1[ $k ] = axisbuilder_array_overlay( $v, $a2[ $k ] );
		} else {
			$a1[ $k ] = $a2[ $k ];
		}
	}
	return $a1;
}

/**
 * let_to_num function.
 *
 * This function transforms the php.ini notation for numbers (like '2M') to an integer.
 *
 * @param  $size
 * @return int
 */
function axisbuilder_let_to_num( $size ) {
	$l   = substr( $size, -1 );
	$ret = substr( $size, 0, -1 );
	switch ( strtoupper( $l ) ) {
		case 'P':
			$ret *= 1024;
		case 'T':
			$ret *= 1024;
		case 'G':
			$ret *= 1024;
		case 'M':
			$ret *= 1024;
		case 'K':
			$ret *= 1024;
	}
	return $ret;
}

/**
 * Make a string lowercase.
 * Try to use mb_strtolower() when available.
 *
 * @param  string $string
 * @return string
 */
function axisbuilder_strtolower( $string ) {
	return function_exists( 'mb_strtolower' ) ? mb_strtolower( $string ) : strtolower( $string );
}

/**
 * Trim a string and append a suffix.
 * @param  string  $string
 * @param  integer $chars
 * @param  string  $suffix
 * @return string
 */
function axisbuilder_trim_string( $string, $chars = 200, $suffix = '...' ) {
	if ( strlen( $string ) > $chars ) {
		$string = substr( $string, 0, ( $chars - strlen( $suffix ) ) ) . $suffix;
	}
	return $string;
}

/**
 * Format content to display shortcodes.
 * @param  string $raw_string
 * @return string
 */
function axisbuilder_format_content( $raw_string ) {
	return apply_filters( 'axisbuilder_format_content', do_shortcode( shortcode_unautop( wpautop( $raw_string ) ) ), $raw_string );
}
