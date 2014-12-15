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

if ( ! function_exists( 'ab_build_shortcode_pattern' ) ) :

/**
 * Creates the shortcode pattern that only matches builder shortcodes.
 * @param  array        $predefined_tags Prefefined Tags.
 * @return array|string Matched builder shortcode pattern.
 */
function ab_build_shortcode_pattern( $predefined_tags = false ) {
	global $shortcode_tags, $_builder_shortcode_tags;

	// Store the {old|new} shortcode tags
	$_old_shortcodes = $shortcode_tags;
	$_new_shortcodes = ab_fetch_shortcode_data( 'name' );

	// If builder has shortcodes build the pattern.
	if ( ! empty( $_new_shortcodes ) ) {
		$shortcode_tags = array_flip( $_new_shortcodes );
	}

	// Filter out all elements that are not in the predefined tags array.
	if ( is_array( $predefined_tags ) ) {
		$predefined_tags = array_flip( $predefined_tags );
		$shortcode_tags  = shortcode_atts( $predefined_tags, $shortcode_tags );
	}

	// Create the pattern and store it ;)
	$_builder_shortcode_tags = get_shortcode_regex();

	// Restore the original(old) shortcode tags ;)
	$shortcode_tags = $_old_shortcodes;

	return $_builder_shortcode_tags;
}

endif;

if ( ! function_exists( 'ab_fetch_shortcode_data' ) ) :

/**
 * Fetch the builder shortcodes data.
 * @param  string $data Shortcode data type.
 * @return array        All shortcodes data.
 */
function ab_fetch_shortcode_data( $data ) {
	$builder_shortcodes = array();

	foreach ( AB()->shortcodes->get_shortcodes() as $load_shortcodes ) {
		$builder_shortcodes[] = $load_shortcodes->shortcode[$data];
	}

	return $builder_shortcodes;
}

endif;

if ( ! function_exists( 'print_clean' ) ) :

/**
 * Print formatted data passed.
 * @param  array|string $data Raw data.
 * @return array|string $data Clean data.
 */
function print_clean( $data ) {
	print '<pre>';
	print_r( $data );
	print '</pre>';
}

endif;

function do_shortcode_builder( $text ) {
	global $_builder_shortcode_tags;
	return preg_replace_callback("/$_builder_shortcode_tags/s", 'do_shortcode_tag_builder', $text );
}

function do_shortcode_tag_builder( $m ) {
	global $shortcode_tags;

	// allow [[foo]] syntax for escaping a tag
	if ( $m[1] == '[' && $m[6] == ']' ) {
		return substr($m[0], 1, -1);
	}

	$tag     = $m[2];
	$attr    = shortcode_parse_atts( $m[3] );
	$close   = strpos( $m[0], "[/$tag]" );
	$content = ( $close !== false ) ? $m[5] : null;

	if ( in_array( $tag, ab_fetch_shortcode_data( 'name' ) ) ) {
		$_available_shortcodes = AB()->shortcodes->get_editor_element( $content, $attr );
		return $_available_shortcodes[$tag];
	} else {
		return $m[0];
	}
}
