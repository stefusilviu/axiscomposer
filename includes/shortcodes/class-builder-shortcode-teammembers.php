<?php
/**
 * Team Members Shortcode
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
 * AB_Shortcode_Teammembers Class
 */
class AB_Shortcode_Teammembers extends AB_Shortcode {

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
		$this->id        = 'axisbuilder_teammembers';
		$this->title     = __( 'Team Members', 'axisbuilder' );
		$this->tooltip   = __( 'Display a team members image with additional information', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 230,
			'type'    => 'content',
			'name'    => 'ab_teammembers',
			'icon'    => 'icon-teammembers',
			'image'   => AB()->plugin_url() . '/assets/images/content/teammembers.png', // Fallback if icon is missing :)
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
