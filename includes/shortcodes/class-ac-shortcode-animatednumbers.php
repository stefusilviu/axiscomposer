<?php
/**
 * Animated Numbers Shortcode
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
 * AC_Shortcode_Animatednumbers Class
 */
class AC_Shortcode_Animatednumbers extends AC_Shortcode {

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
		$this->id                 = 'animatednumbers';
		$this->method_title       = __( 'Animated Numbers', 'axiscomposer' );
		$this->method_description = __( 'Display an Animated number with subtitle', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 230,
			'type'    => 'content',
			'name'    => 'ac_animatednumbers',
			'icon'    => 'icon-animatednumbers',
			'image'   => AC()->plugin_url() . '/assets/images/content/animatednumbers.png', // Fallback if icon is missing :)
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
