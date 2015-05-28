<?php
/**
 * Notification Shortcode
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
 * AC_Shortcode_Notification Class
 */
class AC_Shortcode_Notification extends AC_Shortcode {

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
		$this->id        = 'axisbuilder_notification';
		$this->title     = __( 'Notification', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a notification box to inform visitors', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 150,
			'type'    => 'content',
			'name'    => 'ab_notification',
			'icon'    => 'icon-notification',
			'image'   => AB()->plugin_url() . '/assets/images/content/notification.png', // Fallback if icon is missing :)
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
