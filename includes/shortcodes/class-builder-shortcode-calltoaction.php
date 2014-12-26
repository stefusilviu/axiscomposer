<?php
/**
 * Call To Action Shortcode
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
 * AB_Shortcode_Calltoaction Class
 */
class AB_Shortcode_Calltoaction extends AB_Shortcode {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_calltoaction';
		$this->title     = __( 'Call To Action', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a call to action button', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 130,
			'type'    => 'content',
			'name'    => 'ab_calltoaction',
			'icon'    => 'icon-calltoaction',
			'image'   => AB()->plugin_url() . '/assets/images/content/calltoaction.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}
}
