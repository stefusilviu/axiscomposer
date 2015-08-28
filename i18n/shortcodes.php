<?php
/**
 * TinyMCE i18n
 *
 * @package  AxisComposer/i18n
 * @category i18n
 * @author   AxisThemes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( '_WP_Editors' ) ) {
    require( ABSPATH . WPINC . '/class-wp-editor.php' );
}

if ( ! function_exists( 'axiscomposer_tinymce_plugin_translation' ) ) :

/**
 * TinyMCE Plugin Translation.
 * @return string $translated TinyMCE language strings.
 */
function axiscomposer_tinymce_plugin_translation() {

	// Default TinyMCE strings.
	$mce_translation = array(
		'shortcode_title' => __( 'Insert Page Builder Shortcode', 'axiscomposer' ),
		'shortcode_text'  => __( 'AxisComposer', 'axiscomposer' ),
		'layout_label'    => __( 'Layout Elements', 'axiscomposer' ),
		'content_label'   => __( 'Content Elements', 'axiscomposer' ),
		'media_label'     => __( 'Media Elements', 'axiscomposer' ),
		'plugin_label'    => __( 'Plugin Additions', 'axiscomposer' ),
	);

	// Fetch all necessary shortcodes information.
	$mce_translation['shortcodes'] = AC()->shortcodes->get_mce_shortcodes();

	/**
	 * Filter translated strings prepared for TinyMCE.
	 * @param array $mce_translation Key/value pairs of strings.
	 * @since 1.0.0
	 */
	$mce_translation = apply_filters( 'axiscomposer_mce_translations', $mce_translation );

	$mce_locale = _WP_Editors::$mce_locale;
	$translated = 'tinyMCE.addI18n("' . $mce_locale . '.axiscomposer_shortcodes", ' . json_encode( $mce_translation ) . ");\n";

	return $translated;
}

endif;

$strings = axiscomposer_tinymce_plugin_translation();
