<?php
/**
 * Video Shortcode
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
 * AB_Shortcode_Video Class
 */
class AB_Shortcode_Video extends AB_Shortcode {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Configuration for builder shortcode video.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_video';
		$this->title     = __( 'Video', 'axisbuilder' );
		$this->tooltip   = __( 'Display a video of your choice', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 370,
			'type'    => 'media',
			'name'    => 'ab_video',
			'icon'    => 'icon-video',
			'image'   => AB()->plugin_url() . '/assets/images/media/video.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}
}
