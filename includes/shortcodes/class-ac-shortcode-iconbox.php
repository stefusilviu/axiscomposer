<?php
/**
 * Icon Box Shortcode
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
 * AC_Shortcode_Iconbox Class
 */
class AC_Shortcode_Iconbox extends AC_Shortcode {

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
		$this->id        = 'iconbox';
		$this->title     = __( 'Icon Box', 'axiscomposer' );
		$this->method_description = __( 'Creates a content box with icon', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 140,
			'type'    => 'content',
			'name'    => 'ac_iconbox',
			'icon'    => 'icon-iconbox',
			'image'   => AC()->plugin_url() . '/assets/images/content/iconbox.png', // Fallback if icon is missing :)
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
