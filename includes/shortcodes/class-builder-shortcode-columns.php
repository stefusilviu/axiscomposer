<?php
/**
 * Columns Shortcode
 *
 * Note: Main AB_Shortcode_Columns is extended for different class for ease.
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
 * AB_Shortcode_Columns Class
 */
class AB_Shortcode_Columns extends AB_Shortcode {

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
		$this->id        = 'axisbuilder_col_one_full';
		$this->title     = __( '1/1', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with full width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 1,
			'type'    => 'layout',
			'name'    => 'ab_one_full',
			'icon'    => 'icon-one-full',
			'image'   => AB()->plugin_url() . '/assets/images/columns/one-full.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}

	/**
	 * Editor Elements.
	 *
	 * This method defines the visual appearance of an element on the Builder canvas.
	 */
	public function editor_element( $params ) {
		extract( $params );

		$data = $this->shortcode;

		$size = array(
			'ab_one_full'     => '1/1',
			'ab_one_half'     => '1/2',
			'ab_one_third'    => '1/3',
			'ab_two_third'    => '2/3',
			'ab_one_fourth'   => '1/4',
			'ab_three_fourth' => '3/4',
			'ab_one_fifth'    => '1/5',
			'ab_two_fifth'    => '2/5',
			'ab_three_fifth'  => '3/5',
			'ab_four_fifth'   => '4/5',
		);

		$extra_class = isset( $args[0] ) ? ( $args[0] == 'first' ) ? ' axisbuilder-first-column' : '' : '';

		$output  = '<div class="axisbuilder-layout-column axisbuilder-layout-column-no-cell popup-animation axisbuilder-drag ' . $data['name'] . $extra_class . '" data-dragdrop-level="' . $data['drag-level'] . '" data-width="' . $data['name'] . '">';
			$output .= '<div class="axisbuilder-sorthandle menu-item-handle">';
				$output .= '<a class="axisbuilder-decrease axisbuilder-change-column-size" href="#decrease" title="' . __( 'Decrease Column Size', 'axisbuilder' ) . '">-</a>';
				$output .= '<span class="axisbuilder-column-size">' . $size[ $data['name'] ] . '</span>';
				$output .= '<a class="axisbuilder-increase axisbuilder-change-column-size" href="#increase" title="' . __( 'Increase Column Size', 'axisbuilder' ) . '">+</a>';
				$output .= '<a class="axisbuilder-trash" href="#trash" title="' . __( 'Delete Column Size', 'axisbuilder' ) . '">x</a>';
				$output .= '<a class="axisbuilder-clone" href="#clone" title="' . __( 'Clone Column Size', 'axisbuilder' ) . '">' . __( 'Clone Column', 'axisbuilder' ) . '</a>';
			$output .= '</div>';
			$output .= '<div class="axisbuilder-inner-shortcode axisbuilder-connect-sort axisbuilder-drop" data-dragdrop-level="' . $data['drop-level'] . '">';
				$output .= '<textarea data-name="text-shortcode" rows="4" cols="20"></textarea>';
				// $outout .= $content;
			$output .= '</div>';
		$output .= '</div>';

		return $output;
	}
}

/**
 * AB_Shortcode_Columns_One_Half Class
 */
class AB_Shortcode_Columns_One_Half extends AB_Shortcode_Columns {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
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
	}
}

/**
 * AB_Shortcode_Columns_One_Third Class
 */
class AB_Shortcode_Columns_One_Third extends AB_Shortcode_Columns {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_col_one_third';
		$this->title     = __( '1/3', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 33&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 3,
			'type'    => 'layout',
			'name'    => 'ab_one_third',
			'icon'    => 'icon-one-third',
			'image'   => AB()->plugin_url() . '/assets/images/columns/one-third.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}
}

/**
 * AB_Shortcode_Columns_Two_Third Class
 */
class AB_Shortcode_Columns_Two_Third extends AB_Shortcode_Columns {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_col_two_third';
		$this->title     = __( '2/3', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 67&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 4,
			'type'    => 'layout',
			'name'    => 'ab_two_third',
			'icon'    => 'icon-two-third',
			'image'   => AB()->plugin_url() . '/assets/images/columns/two-third.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}
}

/**
 * AB_Shortcode_Columns_One_Fourth Class
 */
class AB_Shortcode_Columns_One_Fourth extends AB_Shortcode_Columns {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_col_one_fourth';
		$this->title     = __( '1/4', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 25&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 5,
			'type'    => 'layout',
			'name'    => 'ab_one_fourth',
			'icon'    => 'icon-one-fourth',
			'image'   => AB()->plugin_url() . '/assets/images/columns/one-fourth.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}
}

/**
 * AB_Shortcode_Columns_Three_Fourth Class
 */
class AB_Shortcode_Columns_Three_Fourth extends AB_Shortcode_Columns {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_col_three_fourth';
		$this->title     = __( '3/4', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 75&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 6,
			'type'    => 'layout',
			'name'    => 'ab_three_fourth',
			'icon'    => 'icon-three-fourth',
			'image'   => AB()->plugin_url() . '/assets/images/columns/three-fourth.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}
}

/**
 * AB_Shortcode_Columns_One_Fifth Class
 */
class AB_Shortcode_Columns_One_Fifth extends AB_Shortcode_Columns {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_col_one_fifth';
		$this->title     = __( '1/5', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 20&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 7,
			'type'    => 'layout',
			'name'    => 'ab_one_fifth',
			'icon'    => 'icon-one-fifth',
			'image'   => AB()->plugin_url() . '/assets/images/columns/one-fifth.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}
}

/**
 * AB_Shortcode_Columns_Two_Fifth Class
 */
class AB_Shortcode_Columns_Two_Fifth extends AB_Shortcode_Columns {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_col_two_fifth';
		$this->title     = __( '2/5', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 40&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 8,
			'type'    => 'layout',
			'name'    => 'ab_two_fifth',
			'icon'    => 'icon-two-fifth',
			'image'   => AB()->plugin_url() . '/assets/images/columns/two-fifth.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}
}

/**
 * AB_Shortcode_Columns_Three_Fifth Class
 */
class AB_Shortcode_Columns_Three_Fifth extends AB_Shortcode_Columns {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_col_three_fifth';
		$this->title     = __( '3/5', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 60&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 9,
			'type'    => 'layout',
			'name'    => 'ab_three_fifth',
			'icon'    => 'icon-three-fifth',
			'image'   => AB()->plugin_url() . '/assets/images/columns/three-fifth.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}
}

/**
 * AB_Shortcode_Columns_Four_Fifth Class
 */
class AB_Shortcode_Columns_Four_Fifth extends AB_Shortcode_Columns {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_col_four_fifth';
		$this->title     = __( '4/5', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 80&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 10,
			'type'    => 'layout',
			'name'    => 'ab_four_fifth',
			'icon'    => 'icon-four-fifth',
			'image'   => AB()->plugin_url() . '/assets/images/columns/four-fifth.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
	}
}
