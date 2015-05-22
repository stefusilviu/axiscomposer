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
	 * Configuration for builder shortcode button.
	 */
	public function init_shortcode() {
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
			'tinyMCE' => array( 'disable' => true ),
		);
	}

	/**
	 * Frontend Shortcode Handle.
	 * @param  array  $atts      Array of attributes.
	 * @param  string $content   Text within enclosing form of shortcode element.
	 * @param  string $shortcode The shortcode found, when == callback name.
	 * @param  string $meta      Meta data.
	 * @return string            Returns the modified html string.
	 */
	public function shortcode_handle( $atts, $content = '', $shortcode = '', $meta = '' ) {

	}
}
