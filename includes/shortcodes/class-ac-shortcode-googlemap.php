<?php
/**
 * Google Map Shortcode
 *
 * @extends  AC_Shortcode
 * @package  AxisComposer/Shortcodes
 * @category Shortcodes
 * @author   AxisThemes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Shortcode_Googlemap Class
 */
class AC_Shortcode_Googlemap extends AC_Shortcode {

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
		$this->id                 = 'googlemap';
		$this->method_title       = __( 'Google Map', 'axiscomposer' );
		$this->method_description = __( 'Displays a google map with one or multiple locations', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 390,
			'type'    => 'media',
			'name'    => 'ac_googlemap',
			'icon'    => 'icon-googlemap',
			'image'   => AC()->plugin_url() . '/assets/images/media/googlemap.png', // Fallback if icon is missing :)
			'target'  => 'ac-target-insert',
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
