<?php
/**
 * Partner/Logo Element Shortcode
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
 * AB_Shortcode_Logoelement Class
 */
class AB_Shortcode_Logoelement extends AB_Shortcode {

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
		$this->id        = 'axisbuilder_logoelement';
		$this->title     = __( 'Partner/Logo Element', 'axisbuilder' );
		$this->tooltip   = __( 'Displays a partner/logo grid or slider', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 380,
			'type'    => 'media',
			'name'    => 'ab_logoelement',
			'icon'    => 'icon-logoelement',
			'image'   => AB()->plugin_url() . '/assets/images/media/logoelement.png', // Fallback if icon is missing :)
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
