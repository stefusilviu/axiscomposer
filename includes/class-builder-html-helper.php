<?php
/**
 * AxisBuilder HTML Helper
 *
 * @class       AB_HTML_Helper
 * @package     AxisBuilder/Classes
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_HTML_Helper Class
 */
class AB_HTML_Helper {

	public static function render_multiple_elements( $elements, $parent_class = false, $display = true ) {
		$output = '';

		foreach ( $elements as $element ) {
			$output .= self::render_element( $element, $parent_class );
		}

		if ( $display ) {
			echo $output;
		} else {
			return $output;
		}
	}

	public static function render_element( $element, $parent_class = false ) {
		print_clean( $element );
	}
}
