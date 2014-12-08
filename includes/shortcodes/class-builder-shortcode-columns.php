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

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		$this->id        = 'axisbuilder_columns';
		$this->title     = __( '1/1', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single full width column', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 20,
			'type'    => 'layout',
			'name'    => 'ab_one_full',
			'icon'    => 'icon-one-full',
			'image'   => AB()->plugin_url() . '/assets/images/columns/one-full.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
		$this->settings = array(

		);
		parent::__construct();
	}
}
