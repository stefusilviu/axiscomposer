<?php
/**
 * Contact Form Shortcode
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
 * AB_Shortcode_Contactform Class
 */
class AB_Shortcode_Contactform extends AB_Shortcode {

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
		$this->id        = 'axisbuilder_contactform';
		$this->title     = __( 'Contact Form', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a customizable contact form', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 250,
			'type'    => 'content',
			'name'    => 'ab_contactform',
			'icon'    => 'icon-contactform',
			'image'   => AB()->plugin_url() . '/assets/images/content/contactform.png', // Fallback if icon is missing :)
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
