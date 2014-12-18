<?php
/**
 * Grid Row Shortcode
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
 * AB_Shortcode_Section Class
 */
class AB_Shortcode_Grid_Row extends AB_Shortcode {

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
		$this->id        = 'axisbuilder_grid_row';
		$this->title     = __( 'Grid Row', 'axisbuilder' );
		$this->tooltip   = __( 'Add multiple Grid Rows below each other to create advanced grid layouts. Cells can be styled individually', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 12,
			'type'        => 'layout',
			'name'        => 'ab_gridrow',
			'icon'        => 'icon-gridrow',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/gridrow.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-target-insert',
			'tinymce'     => array( 'disable' => true ),
			'drag-level'  => 1,
			'drop-level'  => 100,
			'html-render' => false
		);
	}
}
