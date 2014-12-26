<?php
/**
 * Notification Shortcode
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
 * AB_Shortcode_Notification Class
 */
class AB_Shortcode_Notification extends AB_Shortcode {

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
			'tinymce' => array( 'disable' => true ),
		);
	}
}
