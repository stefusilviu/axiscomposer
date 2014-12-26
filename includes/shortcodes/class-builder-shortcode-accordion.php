<?php
/**
 * Accordion Shortcode
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
 * AB_Shortcode_Accordion Class
 */
class AB_Shortcode_Accordion extends AB_Shortcode {

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
		$this->id        = 'axisbuilder_accordion';
		$this->title     = __( 'Accordion', 'axisbuilder' );
		$this->tooltip   = __( 'Creates toggles or accordion', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 180,
			'type'    => 'content',
			'name'    => 'ab_accordion',
			'icon'    => 'icon-accordion',
			'image'   => AB()->plugin_url() . '/assets/images/content/accordion.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}
}
