<?php
/**
 * AxisBuilder Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @package     AxisBuilder/Functions
 * @category    Core
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Include core functions (available in both admin and frontend)
include( 'axis-conditional-functions.php' );
include( 'axis-formatting-functions.php' );

/**
 * AxisBuilder Supported Themes
 *
 * @return array
 */
function ab_get_core_supported_themes() {
	return array( 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentyeleven', 'twentytwelve', 'twentyten' );
}
