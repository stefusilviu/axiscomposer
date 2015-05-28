<?php
/**
 * Accordion Shortcode
 *
 * @extends     AC_Shortcode
 * @package     AxisComposer/Shortcodes
 * @category    Shortcodes
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Shortcode_Accordion Class
 */
class AC_Shortcode_Accordion extends AC_Shortcode {

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
