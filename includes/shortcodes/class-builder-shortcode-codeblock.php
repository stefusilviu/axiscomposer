<?php
/**
 * Codeblock Shortcode
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

class AB_Shortcode_Codeblock extends AB_Shortcode {

	public function shortcode_button() {

		// Shortcode Button
		$this->config['name']      = __( 'Code Block', 'axisbuilder' );
		$this->config['desc']      = __( 'Add text or code to your website without any formatting or text optimization. Can be used for HTML/CSS/Javascript', 'axisbuilder' );
		$this->config['type']      = __( 'Plugin Additions', 'axisbuilder' );
		$this->config['icon']      = 'codeblock';
		$this->config['order']     = 1;
		$this->config['target']    = '';
		$this->config['tinyMCE']   = array( 'disable' => true );
		$this->config['shortcode'] = 'ab_codeblock';

		// Fallback if icon is missing )
		$this->config['image']     = AB()->plugin_url() . '/assets/images/element-codeblock.png';
	}

}
