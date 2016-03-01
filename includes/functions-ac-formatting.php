<?php
/**
 * AxisComposer Formatting
 *
 * Functions for formatting data.
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
 * Clean variables using sanitize_text_field.
 * @param  string|array $var
 * @return string
 */
function ac_clean( $var ) {
	return is_array( $var ) ? array_map( 'ac_clean', $var ) : sanitize_text_field( $var );
}

/**
 * Sanitize a string destined to be a tooltip.
 *
 * @since  1.0.0  Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
 * @param  string $var
 * @return string
 */
function ac_sanitize_tooltip( $var ) {
	return htmlspecialchars( wp_kses( html_entity_decode( $var ), array(
		'br'     => array(),
		'em'     => array(),
		'strong' => array(),
		'small'  => array(),
		'span'   => array(),
		'ul'     => array(),
		'li'     => array(),
		'ol'     => array(),
		'p'      => array(),
	) ) );
}

/**
 * Merge two arrays.
 *
 * @param  array $a1
 * @param  array $a2
 * @return array
 */
function ac_array_overlay( $a1, $a2 ) {
	foreach ( $a1 as $k => $v ) {
		if ( ! array_key_exists( $k, $a2 ) ) {
			continue;
		}
		if ( is_array( $v ) && is_array( $a2[ $k ] ) ) {
			$a1[ $k ] = ac_array_overlay( $v, $a2[ $k ] );
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
function ac_let_to_num( $size ) {
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
function ac_strtolower( $string ) {
	return function_exists( 'mb_strtolower' ) ? mb_strtolower( $string ) : strtolower( $string );
}

/**
 * Trim a string and append a suffix.
 * @param  string  $string
 * @param  integer $chars
 * @param  string  $suffix
 * @return string
 */
function ac_trim_string( $string, $chars = 200, $suffix = '...' ) {
	if ( strlen( $string ) > $chars ) {
		if ( function_exists( 'mb_substr' ) ) {
			$string = mb_substr( $string, 0, ( $chars - mb_strlen( $suffix ) ) ) . $suffix;
		} else {
			$string = substr( $string, 0, ( $chars - strlen( $suffix ) ) ) . $suffix;
		}
	}
	return $string;
}

/**
 * Format content to display shortcodes.
 * @param  string $raw_string
 * @return string
 */
function ac_format_content( $raw_string ) {
	return apply_filters( 'axiscomposer_format_content', do_shortcode( ac_format_shortcode( shortcode_unautop( wpautop( $raw_string ) ) ) ), $raw_string );
}

/**
 * Format portfolio short description.
 * @param  string $content
 * @return string
 */
function ac_format_portfolio_short_description( $content ) {
	// Add support for Jetpack Markdown
	if ( class_exists( 'WPCom_Markdown' ) ) {
		$markdown = WPCom_Markdown::get_instance();

		return wpautop( $markdown->transform( $content, array( 'unslash' => false ) ) );
	}

	return $content;
}

add_filter( 'axiscomposer_short_description', 'ac_format_portfolio_short_description', 9999999 );

/**
 * Format unicode to display actual icon.
 * @param  string $raw_string
 * @return string
 */
function ac_format_unicode( $raw_string ) {
	if ( strpos( $raw_string, 'u' ) === 0 ) {
		$raw_string = str_replace( 'u', '&#x', $raw_string );
	}

	return apply_filters( 'axiscomposer_format_unicode', wp_kses_decode_entities( $raw_string ), $raw_string );
}

/**
 * Format shortcode tags to display content.
 * @param  string $raw_string
 * @return string
 */
function ac_format_shortcode( $raw_string ) {
	$args = array(
		'<p>['      => '[',
		']</p>'     => ']',
		']<br />'   => ']',
		"<br />\n[" => '[',
	);

	return strtr( $raw_string, $args );
}
