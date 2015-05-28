<?php
/**
 * Product List Shortcode
 *
 * @extends     AC_Shortcode
 * @package     AxisBuilder/Shortcodes
 * @category    Shortcodes
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Shortcode_Productlist Class
 */
class AC_Shortcode_Productlist extends AC_Shortcode {

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
		$this->id        = 'axisbuilder_productlist';
		$this->title     = __( 'Product List', 'axisbuilder' );
		$this->tooltip   = __( 'Displays a list of Product Entries', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 520,
			'type'    => 'plugin',
			'name'    => 'ab_productlist',
			'icon'    => 'icon-productlist',
			'image'   => AB()->plugin_url() . '/assets/images/plugin/productlist.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
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
