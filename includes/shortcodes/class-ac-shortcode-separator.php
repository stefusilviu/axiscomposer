<?php
/**
 * Separator Shortcode
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
 * AC_Shortcode_Separator Class
 */
class AC_Shortcode_Separator extends AC_Shortcode {

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
		$this->id                 = 'separator';
		$this->method_title       = __( 'Separator / Whitespace', 'axiscomposer' );
		$this->method_description = __( 'Creates a delimiter/whitespace to separate elements', 'axiscomposer' );
		$this->shortcode = array(
			'sort'      => 70,
			'type'      => 'content',
			'name'      => 'ac_separator',
			'icon'      => 'icon-separator',
			'image'     => AC()->plugin_url() . '/assets/images/content/separator.png', // Fallback if icon is missing :)
			'target'    => 'ac-target-insert',
			'tinyMCE'   => array( 'disable' => true ),
			'invisible' => true
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
