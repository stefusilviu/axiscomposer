<?php
/**
 * AxisBuilder Conditional Functions
 *
 * Functions for determining the current query/page.
 *
 * @package     AxisBuilder/Functions
 * @category    Core
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * is_axisbuilder_capable - Returns true when the user can manage AxisBuilder.
 * @return bool
 */
function is_axisbuilder_capable() {
	return current_user_can( 'manage_axisbuilder' );
}

if ( ! function_exists( 'is_ajax' ) ) {

	/**
	 * is_capable - Returns true when the page is loaded via ajax.
	 * @return bool
	 */
	function is_ajax() {
		return defined( 'DOING_AJAX' );
	}
}
