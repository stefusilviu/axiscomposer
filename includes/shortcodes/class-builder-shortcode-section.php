<?php
/**
 * Section Shortcode
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
 * AB_Shortcode_Section Class
 */
class AB_Shortcode_Section extends AB_Shortcode {

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
		$this->id        = 'axisbuilder_section';
		$this->title     = __( 'Color Section', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a section with unique background image and colors', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 11,
			'type'        => 'layout',
			'name'        => 'ab_section',
			'icon'        => 'icon-section',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/section.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-target-insert',
			'tinymce'     => array( 'disable' => true ),
			'drag-level'  => 1,
			'drop-level'  => 1,
			'html-render' => false
		);
	}
}
