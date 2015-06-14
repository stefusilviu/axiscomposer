<?php
/**
 * AxisComposer Formatting
 *
 * Functions for formatting data.
 *
 * @package     AxisComposer/Functions
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
function ac_clean( $var ) {
	return sanitize_text_field( $var );
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
 * Merge two arrays
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
		$string = substr( $string, 0, ( $chars - strlen( $suffix ) ) ) . $suffix;
	}
	return $string;
}

/**
 * Format content to display shortcodes.
 * @param  string $raw_string
 * @return string
 */
function ac_format_content( $raw_string ) {
	return apply_filters( 'axiscomposer_format_content', do_shortcode( shortcode_unautop( wpautop( $raw_string ) ) ), $raw_string );
}

/**
 * Format shortcode tags to display content.
 * @param  string $raw_string
 * @return string
 */
function ac_format_shortcode( $raw_string ) {
	$content = strtr( $raw_string, array(
		'<p>['      => '[',
		']</p>'     => ']',
		']<br />'   => ']',
		"<br />\n[" => '[',
	) );

	return apply_filters( 'axiscomposer_format_shortcode', $content, $raw_string );
}


/**
 * Don't auto-p wrap shortcodes that stand alone
 *
 * Ensures that shortcodes are not wrapped in `<p>...</p>`.
 *
 * @since 2.9.0
 *
 * @param string $pee The content.
 * @return string The filtered content.
 */
function ac_shortcode_unautop( $pee ) {
	global $shortcode_tags;

	if ( empty( $shortcode_tags ) || !is_array( $shortcode_tags ) ) {
		return $pee;
	}

	$tagregexp = join( '|', array_map( 'preg_quote', array_keys( $shortcode_tags ) ) );
	$spaces = wp_spaces_regexp();

	$pattern =
		  '/'
		. '<p>'                              // Opening paragraph
		. '(?:' . $spaces . ')*+'            // Optional leading whitespace
		. '('                                // 1: The shortcode
		.     '\\['                          // Opening bracket
		.     "($tagregexp)"                 // 2: Shortcode name
		.     '(?![\\w-])'                   // Not followed by word character or hyphen
		                                     // Unroll the loop: Inside the opening shortcode tag
		.     '[^\\]\\/]*'                   // Not a closing bracket or forward slash
		.     '(?:'
		.         '\\/(?!\\])'               // A forward slash not followed by a closing bracket
		.         '[^\\]\\/]*'               // Not a closing bracket or forward slash
		.     ')*?'
		.     '(?:'
		.         '\\/\\]'                   // Self closing tag and closing bracket
		.     '|'
		.         '\\]'                      // Closing bracket
		.         '(?:'                      // Unroll the loop: Optionally, anything between the opening and closing shortcode tags
		.             '[^\\[]*+'             // Not an opening bracket
		.             '(?:'
		.                 '\\[(?!\\/\\2\\])' // An opening bracket not followed by the closing shortcode tag
		.                 '[^\\[]*+'         // Not an opening bracket
		.             ')*+'
		.             '\\[\\/\\2\\]'         // Closing shortcode tag
		.         ')?'
		.     ')'
		. ')'
		. '(?:' . $spaces . ')*+'            // optional trailing whitespace
		. '<\\/p>'                           // closing paragraph
		. '/s';

	return preg_replace( $pattern, '$1', $pee );
}
