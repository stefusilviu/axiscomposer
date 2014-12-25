<?php
/**
 * Special Heading Shortcode
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
 * AB_Shortcode_Heading Class
 */
class AB_Shortcode_Heading extends AB_Shortcode {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Configuration for builder shortcode special heading.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_heading';
		$this->title     = __( 'Special Heading', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a Special Heading', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 80,
			'type'    => 'content',
			'name'    => 'ab_heading',
			'icon'    => 'icon-heading',
			'image'   => AB()->plugin_url() . '/assets/images/content/heading.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}
}
