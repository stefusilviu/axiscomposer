<?php
/**
 * AxisBuilder Admin Functions
 *
 * @package     AxisBuilder/Admin/Functions
 * @category    Core
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all AxisBuilder screen ids.
 * @return array
 */
function axisbuilder_get_screen_ids() {

	$ab_screen_id = sanitize_title( __( 'AxisBuilder', 'axisbuilder' ) );
	$screen_ids   = array(
		'toplevel_page_' . $ab_screen_id,
		$ab_screen_id . '_page_axisbuilder-iconfonts',
		$ab_screen_id . '_page_axisbuilder-settings',
		$ab_screen_id . '_page_axisbuilder-status',
		$ab_screen_id . '_page_axisbuilder-addons',
		'portfolio',
		'edit-portfolio',
		'edit-portfolio_cat',
		'edit-portfolio_tag'
	);

	return apply_filters( 'axisbuilder_screen_ids', $screen_ids );
}

/**
 * Output admin fields.
 * @param array $options
 */
function axisbuilder_admin_fields( $options ) {

	if ( ! class_exists( 'AB_Admin_Settings' ) ) {
		include 'class-builder-admin-settings.php';
	}

	AB_Admin_Settings::output_fields( $options );
}

/**
 * Update all settings which are passed.
 * @param array $options
 */
function axisbuilder_update_options( $options ) {

	if ( ! class_exists( 'AB_Admin_Settings' ) ) {
		include 'class-builder-admin-settings.php';
	}

	AB_Admin_Settings::save_fields( $options );
}

/**
 * Get a setting from the settings API.
 * @param  mixed $option_name
 * @return string
 */
function axisbuilder_settings_get_option( $option_name, $default = '' ) {

	if ( ! class_exists( 'AB_Admin_Settings' ) ) {
		include 'class-builder-admin-settings.php';
	}

	return AB_Admin_Settings::get_option( $option_name, $default );
}
