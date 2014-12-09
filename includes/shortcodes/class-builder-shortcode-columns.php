<?php
/**
 * Columns Shortcode
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
		$this->id        = 'axisbuilder_columns';
		$this->title     = __( '1/1', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single full width column', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 20,
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
		$this->shortcode_name = __( '1/2', 'axisbuilder' );
		$this->shortcode_desc = __( 'Creates a single column with 50&percnt; width', 'axisbuilder' );
		$this->shortcode_data = array(
			'id'    => 'one_half',
			'icon'  => 'one-half',
			'type'  => 'layout-tab',
			'image' => AB()->plugin_url() . '/assets/images/columns/one-half.png', // Fallback if icon is missing :)
		);
		$this->popup_settings = array(

		);
		parent::__construct();
	}

	public function shortcode_button() {

		// Shortcode Button
		$this->config['name']       = __( '1/2', 'axisbuilder' );
		$this->config['desc']       = __( 'Creates a single column with 50&percnt; width', 'axisbuilder' );
		$this->config['type']       = __( 'Layout Elements', 'axisbuilder' );
		$this->config['icon']       = 'one-half';
		$this->config['order']      = 90;
		$this->config['target']     = 'axisbuilder-section-drop';
		$this->config['tinyMCE']    = array( 'name' => '1/2 + 1/2', 'instantInsert' => '[ab_one_half first]Add Content here[/ab_one_half]\n\n\n[ab_one_half]Add Content here[/ab_one_half]' );
		$this->config['shortcode']  = 'ab_one_half';

		// Drag-Drop level
		$this->config['drag-level'] = 2;
		$this->config['drag-level'] = 2;

		// Fallback if icon is missing )
		$this->config['image']      = AB()->plugin_url() . '/assets/images/columns/one-half.png';
	}
}

class AB_Shortcode_Columns_One_Third extends AB_Shortcode_Columns {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		$this->shortcode_name = __( '1/3', 'axisbuilder' );
		$this->shortcode_desc = __( 'Creates a single column with 30&percnt; width', 'axisbuilder' );
		$this->shortcode_data = array(
			'id'    => 'one_third',
			'icon'  => 'one-third',
			'type'  => 'layout-tab',
			'image' => AB()->plugin_url() . '/assets/images/columns/one-third.png', // Fallback if icon is missing :)
		);
		$this->popup_settings = array(

		);
		parent::__construct();
	}

	public function shortcode_button() {

		// Shortcode Button
		$this->config['name']       = __( '1/3', 'axisbuilder' );
		$this->config['desc']       = __( 'Creates a single column with 33&percnt; width', 'axisbuilder' );
		$this->config['type']       = __( 'Layout Elements', 'axisbuilder' );
		$this->config['icon']       = 'one-third';
		$this->config['order']      = 80;
		$this->config['target']     = 'axisbuilder-section-drop';
		$this->config['tinyMCE']    = array( 'name' => '1/3 + 1/3 + 1/3', 'instantInsert' => '[ab_one_third first]Add Content here[/ab_one_third]\n\n\n[ab_one_third]Add Content here[/ab_one_third]\n\n\n[ab_one_third]Add Content here[/ab_one_third]' );
		$this->config['shortcode']  = 'ab_one_third';

		// Drag-Drop level
		$this->config['drag-level'] = 2;
		$this->config['drag-level'] = 2;

		// Fallback if icon is missing )
		$this->config['image']      = AB()->plugin_url() . '/assets/images/columns/one-third.png';
	}
}

class AB_Shortcode_Columns_Two_Third extends AB_Shortcode_Columns {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		$this->shortcode_name = __( '2/3', 'axisbuilder' );
		$this->shortcode_desc = __( 'Creates a single column with 67&percnt; width', 'axisbuilder' );
		$this->shortcode_data = array(
			'id'    => 'two-third',
			'icon'  => 'two_third',
			'type'  => 'layout-tab',
			'image' => AB()->plugin_url() . '/assets/images/columns/two-third.png', // Fallback if icon is missing :)
		);
		$this->popup_settings = array(

		);
		parent::__construct();
	}

	public function shortcode_button() {

		// Shortcode Button
		$this->config['name']       = __( '2/3', 'axisbuilder' );
		$this->config['desc']       = __( 'Creates a single column with 67&percnt; width', 'axisbuilder' );
		$this->config['type']       = __( 'Layout Elements', 'axisbuilder' );
		$this->config['icon']       = 'two-third';
		$this->config['order']      = 70;
		$this->config['target']     = 'axisbuilder-section-drop';
		$this->config['tinyMCE']    = array( 'name' => '2/3 + 1/3', 'instantInsert' => '[ab_two_third first]Add Content here[/ab_two_third]\n\n\n[ab_one_third]Add Content here[/ab_one_third]' );
		$this->config['shortcode']  = 'ab_one_third';

		// Drag-Drop level
		$this->config['drag-level'] = 2;
		$this->config['drag-level'] = 2;

		// Fallback if icon is missing )
		$this->config['image']      = AB()->plugin_url() . '/assets/images/columns/two-third.png';
	}
}

class AB_Shortcode_Columns_One_Fourth extends AB_Shortcode_Columns {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		$this->shortcode_name = __( '1/4', 'axisbuilder' );
		$this->shortcode_desc = __( 'Creates a single column with 25&percnt; width', 'axisbuilder' );
		$this->shortcode_data = array(
			'id'    => 'one-fourth',
			'icon'  => 'one_fourth',
			'type'  => 'layout-tab',
			'image' => AB()->plugin_url() . '/assets/images/columns/one-fourth.png', // Fallback if icon is missing :)
		);
		$this->popup_settings = array(

		);
		parent::__construct();
	}

	public function shortcode_button() {

		// Shortcode Button
		$this->config['name']       = __( '1/4', 'axisbuilder' );
		$this->config['desc']       = __( 'Creates a single column with 25&percnt; width', 'axisbuilder' );
		$this->config['type']       = __( 'Layout Elements', 'axisbuilder' );
		$this->config['icon']       = 'one-fourth';
		$this->config['order']      = 60;
		$this->config['target']     = 'axisbuilder-section-drop';
		$this->config['tinyMCE']    = array( 'name' => '1/4 + 1/4 + 1/4 + 1/4', 'instantInsert' => '[ab_one_fourth first]Add Content here[/ab_one_fourth]\n\n\n[ab_one_fourth]Add Content here[/ab_one_fourth]\n\n\n[ab_one_fourth]Add Content here[/ab_one_fourth]\n\n\n[ab_one_fourth]Add Content here[/ab_one_fourth]' );
		$this->config['shortcode']  = 'ab_one_third';

		// Drag-Drop level
		$this->config['drag-level'] = 2;
		$this->config['drag-level'] = 2;

		// Fallback if icon is missing )
		$this->config['image']      = AB()->plugin_url() . '/assets/images/columns/one-fourth.png';
	}
}

class AB_Shortcode_Columns_Three_Fourth extends AB_Shortcode_Columns {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		$this->shortcode_name = __( '3/4', 'axisbuilder' );
		$this->shortcode_desc = __( 'Creates a single column with 75&percnt; width', 'axisbuilder' );
		$this->shortcode_data = array(
			'id'    => 'three-fourth',
			'icon'  => 'three_fourth',
			'type'  => 'layout-tab',
			'image' => AB()->plugin_url() . '/assets/images/columns/three-fourth.png', // Fallback if icon is missing :)
		);
		$this->popup_settings = array(

		);
		parent::__construct();
	}

	public function shortcode_button() {

		// Shortcode Button
		$this->config['name']       = __( '3/4', 'axisbuilder' );
		$this->config['desc']       = __( 'Creates a single column with 75&percnt; width', 'axisbuilder' );
		$this->config['type']       = __( 'Layout Elements', 'axisbuilder' );
		$this->config['icon']       = 'three-fourth';
		$this->config['order']      = 50;
		$this->config['target']     = 'axisbuilder-section-drop';
		$this->config['tinyMCE']    = array( 'name' => '3/4 + 1/4', 'instantInsert' => '[ab_three_fourth first]Add Content here[/ab_three_fourth]\n\n\n[ab_one_fourth]Add Content here[/ab_one_fourth]' );
		$this->config['shortcode']  = 'ab_one_third';

		// Drag-Drop level
		$this->config['drag-level'] = 2;
		$this->config['drag-level'] = 2;

		// Fallback if icon is missing )
		$this->config['image']      = AB()->plugin_url() . '/assets/images/columns/three-fourth.png';
	}
}

// Load Columns
$load_columns = array(
	// 'AB_Shortcode_Columns_One_Half',
	// 'AB_Shortcode_Columns_One_Third',
	// 'AB_Shortcode_Columns_Two_Third',
	// 'AB_Shortcode_Columns_One_Fourth',
	// 'AB_Shortcode_Columns_Three_Fourth',
);

foreach ( $load_columns as $column ) {
	$load_column = new $column();
}
