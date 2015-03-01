<?php
/**
 * Deprecated functions
 *
 * Where functions come to die.
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
 * @deprecated
 */
function get_builder_core_supported_screens() {
	return axisbuilder_get_layout_supported_screens();
}
