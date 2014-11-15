<?php
/**
 * AxisBuilder Admin Functions
 *
 * @package		AxisBuilder/Admin/Functions
 * @category	Core
 * @author		AxisThemes
 * @since		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get all AxisBuilder screen ids
 *
 * @return array
 */
function ab_get_screen_ids() {
	$ab_screen_id = sanitize_title( __( 'Axis Builder', 'axisbuilder' ) );

	return apply_filters( 'axisbuilder_screen_ids', array(
		'toplevel_page_' . $ab_screen_id,
		$ab_screen_id . '_page_ab-settings',
		$ab_screen_id . '_page_ab-status',
		$ab_screen_id . '_page_ab-extensions',
	) );
}
