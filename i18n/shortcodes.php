<?php
/**
 * tinyMCE i18n
 *
 * @package     AxisBuilder/i18n
 * @category    i18n
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '_WP_Editors' ) ) {
    require( ABSPATH . WPINC . '/class-wp-editor.php' );
}

function axisbuilder_tinymce_plugin_translation() {

	$mce_translation = array(
		// Default TinyMCE strings
		'shortcode_title' => __( 'Insert Builder Shortcode', 'axisbuilder' ),
		'shortcode_text'  => __( 'Axis Builder', 'axisbuilder' ),

		'layout_label'    => __( 'Layout Elements', 'axisbuilder' ),
		'content_label'   => __( 'Content Elements', 'axisbuilder' ),
		'media_label'     => __( 'Media Elements', 'axisbuilder' ),
		'plugin_label'    => __( 'Plugin Additions', 'axisbuilder' ),
	);

	// Fetch all necessary shortcodes information.
	$mce_translation['shortcodes'] = AB()->shortcodes->get_mce_shortcodes();

	/**
	 * Filter translated strings prepared for TinyMCE.
	 * @param array  $mce_translation Key/value pairs of strings.
	 * @since 1.0.0
	 */
	$mce_translation = apply_filters( 'axisbuilder_mce_translations', $mce_translation );

	$mce_locale = _WP_Editors::$mce_locale;
	$translated = 'tinyMCE.addI18n("' . $mce_locale . '.axisbuilder_shortcodes", ' . json_encode( $mce_translation ) . ");\n";

	return $translated;
}

$strings = axisbuilder_tinymce_plugin_translation();
