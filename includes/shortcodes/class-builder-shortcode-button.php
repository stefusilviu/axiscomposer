<?php
/**
 * Button Shortcode
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

/**
 * AB_Shortcode_Button Class
 */
class AB_Shortcode_Button extends AB_Shortcode {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_button';
		$this->title     = __( 'Button', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a colored button', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 120,
			'type'    => 'content',
			'name'    => 'ab_button',
			'icon'    => 'icon-button',
			'image'   => AB()->plugin_url() . '/assets/images/content/button.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}
}
