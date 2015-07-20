<?php
/**
 * AxisComposer Widget Functions
 *
 * Widget related functions and widget registration.
 *
 * @package  AxisComposer/Functions
 * @category Core
 * @author   AxisThemes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include Widget classes
include_once( 'abstracts/abstract-ac-widget.php' );
include_once( 'widgets/class-ac-widget-advertisement.php' );

/**
 * Register Widgets
 * @since 1.0.0
 */
function ac_register_widgets() {
	register_widget( 'AC_Widget_Advertisement' );
}
add_action( 'widgets_init', 'ac_register_widgets' );
