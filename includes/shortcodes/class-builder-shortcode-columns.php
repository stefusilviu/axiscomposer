<?php
/**
 * Columns Shortcode
 *
 * @extends     AB_Shortcode
 * @package     AxisBuilder/Shortcodes
 * @category    Shortcodes
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AB_Shortcode_Columns extends AB_Shortcode {

	public function shortcode_button() {

		// Shortcode Button
		$this->config['name']      = __( '1/1', 'axisbuilder' );
		$this->config['desc']      = __( 'Creates a single full width column', 'axisbuilder' );
		$this->config['type']      = __( 'Layout Elements', 'axisbuilder' );
		$this->config['icon']      = 'column-full';
		$this->config['order']     = 2;
		$this->config['target']    = 'axisbuilder-section-drop';
		$this->config['tinyMCE']   = array( 'instantInsert' => '[ab_one_full first]Add Content here[/ab_one_full]' );
		$this->config['shortcode'] = 'ab_one_full';

		// Fallback if icon is missing )
		$this->config['image']     = AB()->plugin_url() . '/assets/images/column-full.png';
	}

}

return new AB_Shortcode_Columns();
