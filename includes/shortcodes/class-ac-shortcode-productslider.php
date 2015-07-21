<?php
/**
 * Product Slider Shortcode
 *
 * @extends  AC_Shortcode
 * @version  1.0.0
 * @package  AxisComposer/Shortcodes
 * @category Shortcodes
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Shortcode_Productslider Class
 */
class AC_Shortcode_Productslider extends AC_Shortcode {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id                 = 'productslider';
		$this->method_title       = __( 'Product Slider', 'axiscomposer' );
		$this->method_description = __( 'Displays a slideshow of product entries', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 500,
			'type'    => 'plugin',
			'name'    => 'ac_productslider',
			'icon'    => 'icon-productslider',
			'image'   => AC()->plugin_url() . '/assets/images/plugin/productslider.png', // Fallback if icon is missing :)
			'target'  => 'ac-target-insert',
			'tinyMCE' => array( 'disable' => false ),
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
