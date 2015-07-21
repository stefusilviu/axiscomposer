<?php
/**
 * AxisComposer Iconfont Functions.
 *
 * Functions for iconfont specific things.
 *
 * @author   AxisThemes
 * @category Core
 * @package  AxisComposer/Functions
 * @version  1.0.0
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

/**
 * Get all iconfonts charlist.
 * @return array
 */
function ac_get_iconfont_charlist() {
	return AC_Iconfont::load_all_charlist();
}
