<?php
/**
 * AxisComposer Admin Functions
 *
 * @author   AxisThemes
 * @category Core
 * @package  AxisComposer/Admin/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all AxisComposer screen ids.
 * @return array
 */
function ac_get_screen_ids() {

	$ac_screen_id = sanitize_title( __( 'AxisComposer', 'axiscomposer' ) );
	$screen_ids   = array(
		'toplevel_page_' . $ac_screen_id,
		$ac_screen_id . '_page_ac-iconfont',
		$ac_screen_id . '_page_ac-settings',
		$ac_screen_id . '_page_ac-status',
		$ac_screen_id . '_page_ac-addons',
		'portfolio',
		'edit-portfolio',
		'edit-portfolio_cat',
		'edit-portfolio_tag'
	);

	return apply_filters( 'axiscomposer_screen_ids', $screen_ids );
}

/**
 * Output admin fields.
 * @param array $options
 */
function axiscomposer_admin_fields( $options ) {

	if ( ! class_exists( 'AC_Admin_Settings' ) ) {
		include 'class-ac-admin-settings.php';
	}

	AC_Admin_Settings::output_fields( $options );
}

/**
 * Update all settings which are passed.
 * @param array $options
 */
function axiscomposer_update_options( $options ) {

	if ( ! class_exists( 'AC_Admin_Settings' ) ) {
		include 'class-ac-admin-settings.php';
	}

	AC_Admin_Settings::save_fields( $options );
}

/**
 * Get a setting from the settings API.
 * @param  mixed $option_name
 * @param  mixed $default
 * @return string
 */
function axiscomposer_settings_get_option( $option_name, $default = '' ) {

	if ( ! class_exists( 'AC_Admin_Settings' ) ) {
		include 'class-ac-admin-settings.php';
	}

	return AC_Admin_Settings::get_option( $option_name, $default );
}
