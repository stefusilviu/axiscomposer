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
	_deprecated_function( 'get_builder_core_supported_screens', '1.1', 'axisbuilder_get_layout_supported_screens' );
	axisbuilder_get_layout_supported_screens();
}
