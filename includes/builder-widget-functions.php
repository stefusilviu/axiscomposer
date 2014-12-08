<?php
/**
 * AxisBuilder Widget Functions
 *
 * Widget related functions and widget registration.
 *
 * @package     AxisBuilder/Functions
 * @category    Core
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include Widget classes
include_once( 'abstracts/abstract-builder-widget.php' );
include_once( 'widgets/class-builder-widget-advertisement.php' );

/**
 * Register Widgets
 * @since 1.0.0
 */
function axisbuilder_register_widgets() {
	register_widget( 'AB_Widget_Advertisement' );
}
add_action( 'widgets_init', 'axisbuilder_register_widgets' );
