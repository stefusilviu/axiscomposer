<?php
/**
 * AxisComposer Iconfont Functions.
 *
 * Functions for iconfont specific things.
 *
 * @package     AxisComposer/Functions
 * @category    Core
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Iconfont extensions.
 * @return array
 */
function ac_get_iconfont_extensions() {
	return (array) apply_filters( 'axiscomposer_iconfont_extensions', array( 'svg', 'ttf', 'otf', 'woff', 'woff2', 'eot' ) );
}
