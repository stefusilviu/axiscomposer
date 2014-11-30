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

$strings = 'tinyMCE.addI18n({' . _WP_Editors::$mce_locale . ': {
	axisbuilder_shortcodes: {
		shortcode_title: "' . esc_js( __( 'Insert Builder Shortcode', 'axisbuilder' ) ) . '",
		shortcode_text: "' . esc_js( __( 'Axis Builder', 'axisbuilder' ) ) . '",
	}
}});';
