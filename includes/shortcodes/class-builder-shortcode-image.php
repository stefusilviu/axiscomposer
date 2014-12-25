<?php
/**
 * Image Shortcode
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
 * AB_Shortcode_Image Class
 */
class AB_Shortcode_Image extends AB_Shortcode {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Configuration for builder shortcode image.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_image';
		$this->title     = __( 'Image', 'axisbuilder' );
		$this->tooltip   = __( 'Inserts a image of your choice', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 360,
			'type'    => 'media',
			'name'    => 'ab_image',
			'icon'    => 'icon-image',
			'image'   => AB()->plugin_url() . '/assets/images/media/image.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}
}
