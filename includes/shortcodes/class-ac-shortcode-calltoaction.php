<?php
/**
 * Call To Action Shortcode
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
 * AC_Shortcode_Calltoaction Class
 */
class AC_Shortcode_Calltoaction extends AC_Shortcode {

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
		$this->id        = 'calltoaction';
		$this->title     = __( 'Call To Action', 'axiscomposer' );
		$this->tooltip   = __( 'Creates a call to action button', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 130,
			'type'    => 'content',
			'name'    => 'ac_calltoaction',
			'icon'    => 'icon-calltoaction',
			'image'   => AC()->plugin_url() . '/assets/images/content/calltoaction.png', // Fallback if icon is missing :)
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
