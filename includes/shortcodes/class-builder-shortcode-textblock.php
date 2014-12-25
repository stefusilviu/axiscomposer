<?php
/**
 * Text Block Shortcode
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
 * AB_Shortcode_Textblock Class
 */
class AB_Shortcode_Textblock extends AB_Shortcode {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Configuration for builder shortcode text block.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_textblock';
		$this->title     = __( 'Text Block', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a simple text block', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 280,
			'type'    => 'content',
			'name'    => 'ab_textblock',
			'icon'    => 'icon-textblock',
			'image'   => AB()->plugin_url() . '/assets/images/content/textblock.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}
}
