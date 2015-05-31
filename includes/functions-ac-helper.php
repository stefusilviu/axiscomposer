<?php
/**
 * AxisComposer Helper Functions
 *
 * Helper functions related to shortcodes.
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
 * Converts an array into a html data string.
 * @param  array  $data        Array for html data.
 * @return string $data_string converted html data.
 */
function ac_html_data_string( $data ) {
	$data_string = '';

	foreach ( $data as $key => $value ) {
		if ( is_array( $value ) ) {
			$value = implode( ', ', $value );
		}

		$data_string .= ' data-' . $key . '="' . $value . '"';
	}

	return $data_string;
}

/**
 * Fetch all available sidebars.
 */
function ac_get_registered_sidebars( $sidebars = array(), $exclude = array() ) {
	global $wp_registered_sidebars;

	foreach ( $wp_registered_sidebars as $sidebar ) {
		if ( ! in_array( $sidebar['name'], $exclude ) ) {
			$sidebars[$sidebar['name']] = $sidebar['name'];
		}
	}

	return $sidebars;
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
	// $_shortcode = str_replace('\n', '', $_shortcode );

	return $_shortcode;
}

/**
 * Creates shortcode pattern that only matches pagebuilder shortcodes.
 * @param  array        $predefined_tags Prefefined Tags.
 * @return array|string Matched pagebuilder shortcode pattern.
 */
function ac_shortcode_pattern( $predefined_tags = false ) {
	global $shortcode_tags, $_ac_shortcode_tags;

	// Store the {old|new} shortcode tags
	$_old_shortcodes = $shortcode_tags;
	$_new_shortcodes = ac_get_shortcode_data( 'name' );

	// If pagebuilder has shortcodes build the pattern.
	if ( ! empty( $_new_shortcodes ) ) {
		$shortcode_tags = array_flip( $_new_shortcodes );
	}

	// Filter out all elements that are not in the predefined tags array.
	if ( is_array( $predefined_tags ) ) {
		$predefined_tags = array_flip( $predefined_tags );
		$shortcode_tags  = shortcode_atts( $predefined_tags, $shortcode_tags );
	}

	// Create the pattern and store it ;)
	$_ac_shortcode_tags = get_shortcode_regex();

	// Restore the original(old) shortcode tags ;)
	$shortcode_tags = $_old_shortcodes;

	return $_ac_shortcode_tags;
}

/**
 * Fetch the pagebuilder shortcodes data.
 * @param  string $data Shortcode data type.
 * @return array        All shortcodes data.
 */
function ac_get_shortcode_data( $data ) {
	$pagebuilder_shortcodes = array();

	foreach ( AC()->shortcodes->get_shortcodes() as $load_shortcodes ) {
		$pagebuilder_shortcodes[] = $load_shortcodes->shortcode[$data];
	}

	return $pagebuilder_shortcodes;
}

/**
 * Search content for pagebuilder shortcodes and filter shortcodes through their hooks.
 *
 * If there are no shortcode tags defined, then the content will be returned
 * without any filtering. This might cause issues when plugins are disabled but
 * the shortcode will still show up in the post or content.
 *
 * @since 1.0.0
 *
 * @uses $_ac_shortcode_tags
 *
 * @param  string $content Content to search for shortcodes
 * @return string Content with shortcodes filtered out.
 */
function do_shortcode_builder( $content ) {
	global $_ac_shortcode_tags;
	return preg_replace_callback( "/$_ac_shortcode_tags/s", 'do_shortcode_tag_builder', $content );
}

/**
 * Regular Expression callable for do_shortcode_builder() for calling shortcode hook.
 * @see get_shortcode_regex for details of the match array contents.
 *
 * @since 1.0.0
 * @access private
 * @uses $shortcode_tags
 *
 * @param array $m Regular expression match array
 * @return mixed False on failure.
 */
function do_shortcode_tag_builder( $m ) {
	global $shortcode_tags;

	// allow [[foo]] syntax for escaping a tag
	if ( $m[1] == '[' && $m[6] == ']' ) {
		return substr($m[0], 1, -1);
	}

	// Let's initialized values as an array
	$values = array();

	// Check for enclosing tag or self closing
	$values['tag']     = $m[2];
	$values['attr']    = shortcode_parse_atts( stripslashes( $m[3] ) );
	$values['closing'] = strpos( $m[0], '[/'.$m[2].']' );
	$values['content'] = ( $values['closing'] !== false ) ? $m[5] : null;

	if ( isset( $_POST['params']['extract'] ) ) {
		// If we open a modal winndow check for the nested shortcodes
		if ( $values['content'] ) {
			$values['content'] = do_shortcode_builder( $values['content'] );
		}

		$_POST['extracted_shortcode'][] = $values;
		return $m[0];
	}

	if ( in_array( $values['tag'], ac_get_shortcode_data( 'name' ) ) ) {
		$_available_shortcodes = AC()->shortcodes->get_editor_element( $values['content'], $values['attr'] );
		return $_available_shortcodes[ $values['tag'] ];
	} else {
		return $m[0];
	}
}

/**
 * Applies WordPress autop filter.
 * @param  string  $content      HTML content by the WordPress Editor.
 * @param  boolean $do_shortcode Content with shortcodes filtered out.
 * @return string  $content
 */
function ac_apply_autop( $content, $do_shortcode = true ) {
	$content = wpautop( $content );

	if ( $do_shortcode ) {
		$content = do_shortcode( shortcode_unautop( $content ) );
	}

	return $content;
}

/**
 * Removes WordPress autop and invalid nesting of <p> & <br> tags.
 * @param  string  $content      HTML content by the WordPress Editor.
 * @param  boolean $do_shortcode Content with shortcodes filtered out.
 * @return string  $content
 */
function ac_remove_autop( $content, $do_shortcode = false ) {
	global $shortcode_tags;
	$tagnames  = array_keys( $shortcode_tags );
	$tagregexp = join( '|', array_map( 'preg_quote', $tagnames ) );

	// Opening Tag
	$content = preg_replace( "/(<p>)?\[($tagregexp)(\s[^\]]+)?\](<\/p>|<br \/>)?/", "[$2$3]", $content );

	// Closing Tag
	$content = preg_replace( "/(<p>)?\[\/($tagregexp)](<\/p>|<br \/>)?/", "[/$2]", $content );

	if ( $do_shortcode ) {
		$content = do_shortcode( shortcode_unautop( $content ) );
	}

	$content = preg_replace( '#^<\/p>|^<br\s?\/?>|<p>$|<p>\s*(&nbsp;)?\s*<\/p>#', '', $content );

	return $content;
}
