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
		$this->settings['name'] = __( 'Code Block', 'axisbuilder' );
		$this->settings['icon'] = AB()->plugin_url() . '/assets/images/element-codeblock.png';

		// $this->output();
	}

	public function shortcode_element( $params ) {
		$params['inner_html'] .= '<img src="' . $this->settings['icon'] . '" title="' . $this->settings['name'] . '" alt="Code Block" />';
		$params['innerHtml'].= "<div class='axis-element-label'>".$this->config['name']."</div>";
		return $params;
	}

	public function output() {
		print_r( $params );
	}
}

return new AB_Shortcode_Codeblock();
