<?php
/**
 * One Full Columns Shortcode
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

class AB_Shortcode_Columns extends AB_Shortcode {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		$this->id        = 'axisbuilder_col_one_full';
		$this->title     = __( '1/1', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single full width column', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 1,
			'type'    => 'layout',
			'name'    => 'ab_one_full',
			'icon'    => 'icon-one-full',
			'image'   => AB()->plugin_url() . '/assets/images/columns/one-full.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
		$this->settings = array(

		);
		parent::__construct();
	}
}

class AB_Shortcode_Columns_One_Half extends AB_Shortcode_Columns {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		$this->id        = 'axisbuilder_col_one_half';
		$this->title     = __( '1/2', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 50&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 2,
			'type'    => 'layout',
			'name'    => 'ab_one_half',
			'icon'    => 'icon-one-half',
			'image'   => AB()->plugin_url() . '/assets/images/columns/one-half.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
		$this->settings = array(

		);

		$this->shortcode_config();
		// parent::__construct();
	}
}
