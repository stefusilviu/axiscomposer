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
		$this->config['name'] = __( 'Code Block', 'axisbuilder' );
		$this->config['icon'] = AB()->plugin_url() . '/assets/images/element-codeblock.png';
	}

}

return new AB_Shortcode_Codeblock();
