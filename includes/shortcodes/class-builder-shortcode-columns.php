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
		$this->config['order']     = 100;
		$this->config['target']    = 'axisbuilder-section-drop';
		$this->config['tinyMCE']   = array( 'instantInsert' => '[ab_one_full first]Add Content here[/ab_one_full]' );
		$this->config['shortcode'] = 'ab_one_full';

		// Fallback if icon is missing )
		$this->config['image']     = AB()->plugin_url() . '/assets/images/column-full.png';
	}

}

class AB_Shortcode_Columns_One_Half extends AB_Shortcode_Columns {

	public function shortcode_button() {

		// Shortcode Button
		$this->config['name']      = __( '1/2', 'axisbuilder' );
		$this->config['desc']      = __( 'Creates a single column with 50&percnt; width', 'axisbuilder' );
		$this->config['type']      = __( 'Layout Elements', 'axisbuilder' );
		$this->config['icon']      = 'column-full';
		$this->config['order']     = 90;
		$this->config['target']    = 'axisbuilder-section-drop';
		$this->config['tinyMCE']   = array( 'name' => '1/2 + 1/2', 'instantInsert' => '[ab_one_half first]Add Content here[/ab_one_half]\n\n\n[ab_one_half]Add Content here[/ab_one_half]' );
		$this->config['shortcode'] = 'ab_one_half';

		// Fallback if icon is missing )
		$this->config['image']     = AB()->plugin_url() . '/assets/images/column-half.png';
	}

}

new AB_Shortcode_Columns_One_Half();
