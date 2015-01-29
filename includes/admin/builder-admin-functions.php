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
 * Get all AxisBuilder screen ids
 * @return array
 */
function axisbuilder_get_screen_ids() {

	$axis_screen_id = sanitize_title( __( 'Axis Builder', 'woocommerce' ) );
	$screen_ids     = array(
		'toplevel_page_' . $axis_screen_id,
		$axis_screen_id . '_page_axisbuilder-iconfonts',
		$axis_screen_id . '_page_axisbuilder-settings',
		$axis_screen_id . '_page_axisbuilder-status',
		$axis_screen_id . '_page_axisbuilder-addons'
	);

	return apply_filters( 'axisbuilder_screen_ids', $screen_ids );
}
