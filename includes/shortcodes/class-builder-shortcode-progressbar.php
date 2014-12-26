<?php
/**
 * Progress Bar Shortcode
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
 * AB_Shortcode_ProgressBar Class
 */
class AB_Shortcode_ProgressBar extends AB_Shortcode {

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
		$this->id        = 'axisbuilder_progressbar';
		$this->title     = __( 'Progress Bars', 'axisbuilder' );
		$this->tooltip   = __( 'Create some progress bars', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 150,
			'type'    => 'content',
			'name'    => 'ab_progressbar',
			'icon'    => 'icon-progressbar',
			'image'   => AB()->plugin_url() . '/assets/images/content/progressbar.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}
}
