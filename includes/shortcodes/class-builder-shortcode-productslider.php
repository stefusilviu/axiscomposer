<?php
/**
 * Product Slider Shortcode
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
 * AB_Shortcode_Productslider Class
 */
class AB_Shortcode_Productslider extends AB_Shortcode {

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
		$this->id        = 'axisbuilder_productslider';
		$this->title     = __( 'Product Slider', 'axisbuilder' );
		$this->tooltip   = __( 'Displays a slideshow of product entries', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 500,
			'type'    => 'plugin',
			'name'    => 'ab_productslider',
			'icon'    => 'icon-productslider',
			'image'   => AB()->plugin_url() . '/assets/images/plugin/productslider.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
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
