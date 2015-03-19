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
