<?php
/**
 * Columns Shortcode
 *
 * Note: Main AC_Shortcode_Columns is extended for different class for ease.
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
 * AC_Shortcode_Columns Class
 */
class AC_Shortcode_Columns extends AC_Shortcode {

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
		$this->id        = 'axisbuilder_col_one_full';
		$this->title     = __( '1/1', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with full width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 1,
			'type'        => 'layout',
			'name'        => 'ab_one_full',
			'icon'        => 'icon-one-full',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/one-full.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'instantInsert' => '[ab_one_full first]Add Content here[/ab_one_full]' ),
			'drag-level'  => 2,
			'drop-level'  => 2,
			'html-render' => false
		);
	}

	/**
	 * Editor Elements.
	 *
	 * This method defines the visual appearance of an element on the Builder canvas.
	 */
	public function editor_element( $params ) {
		extract( $params );

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

		$output  = '<div class="axisbuilder-layout-column axisbuilder-layout-column-no-cell modal-animation axisbuilder-drag ' . $this->shortcode['name'] . $extra_class . '" data-dragdrop-level="' . $this->shortcode['drag-level'] . '" data-width="' . $this->shortcode['name'] . '">';
			$output .= '<div class="axisbuilder-sorthandle menu-item-handle">';
				$output .= '<a class="axisbuilder-change-column-size layout-element-icon axisbuilder-decrease" href="#decrease" title="' . __( 'Decrease Column Size', 'axisbuilder' ) . '"></a>';
				$output .= '<span class="axisbuilder-column-size">' . $size[ $this->shortcode['name'] ] . '</span>';
				$output .= '<a class="axisbuilder-change-column-size layout-element-icon axisbuilder-increase" href="#increase" title="' . __( 'Increase Column Size', 'axisbuilder' ) . '"></a>';
				$output .= '<a class="axisbuilder-trash trash-element-icon" href="#trash" title="' . __( 'Delete Column', 'axisbuilder' ) . '">' . __( 'Delete Column', 'axisbuilder' ) . '</a>';
				$output .= '<a class="axisbuilder-clone clone-element-icon" href="#clone" title="' . __( 'Clone Column',  'axisbuilder' ) . '">' . __( 'Clone Column',  'axisbuilder' ) . '</a>';
			$output .= '</div>';
			$output .= '<div class="axisbuilder-inner-shortcode axisbuilder-connect-sort axisbuilder-drop" data-dragdrop-level="' . $this->shortcode['drop-level'] . '">';
				$output .= '<textarea data-name="text-shortcode" rows="4" cols="20">' . ac_shortcode_data( $this->shortcode['name'], $content, $args ) . '</textarea>';
				if ( $content ) {
					$content = do_shortcode_builder( $content );
					$output .= $content;
				}
			$output .= '</div>';
		$output .= '</div>';

		return $output;
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
		global $axisbuilder_config;

		$axisbuilder_config['current_column'] = $shortcode;

		$class = substr( str_replace( '_', '-', strtolower( $shortcode ) ), 3 );
		$first = ( isset( $atts[0] ) && trim( $atts[0] ) == 'first' ) ? 'first ' : '';

		$output  = '<div class="axisbuilder flex-column ' . $class . ' ' . $first . $meta['el_class'] . '">';
		$content = empty( $axisbuilder_config['conditionals']['is_axisbuilder_template'] ) ? ac_apply_autop( ac_remove_autop( $content ) ) : ac_remove_autop( $content, true );
		$output .= trim( $content );
		$output .= '</div>';

		unset( $axisbuilder_config['current_column'] );

		return $output;
	}
}

/**
 * AC_Shortcode_Columns_One_Half Class
 */
class AC_Shortcode_Columns_One_Half extends AC_Shortcode_Columns {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id        = 'axisbuilder_col_one_half';
		$this->title     = __( '1/2', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 50&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 2,
			'type'        => 'layout',
			'name'        => 'ab_one_half',
			'icon'        => 'icon-one-half',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/one-half.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'name' => '1/2 + 1/2', 'instantInsert' => '[ab_one_half first]Add Content here[/ab_one_half]' . "\n\n\n" . '[ab_one_half]Add Content here[/ab_one_half]' ),
			'drag-level'  => 2,
			'drop-level'  => 2,
			'html-render' => false
		);
	}
}

/**
 * AC_Shortcode_Columns_One_Third Class
 */
class AC_Shortcode_Columns_One_Third extends AC_Shortcode_Columns {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id        = 'axisbuilder_col_one_third';
		$this->title     = __( '1/3', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 33&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 3,
			'type'        => 'layout',
			'name'        => 'ab_one_third',
			'icon'        => 'icon-one-third',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/one-third.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'name' => '1/3 + 1/3 + 1/3', 'instantInsert' => '[ab_one_third first]Add Content here[/ab_one_third]' . "\n\n\n" . '[ab_one_third]Add Content here[/ab_one_third]' . "\n\n\n" . '[ab_one_third]Add Content here[/ab_one_third]' ),
			'drag-level'  => 2,
			'drop-level'  => 2,
			'html-render' => false
		);
	}
}

/**
 * AC_Shortcode_Columns_Two_Third Class
 */
class AC_Shortcode_Columns_Two_Third extends AC_Shortcode_Columns {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id        = 'axisbuilder_col_two_third';
		$this->title     = __( '2/3', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 67&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 4,
			'type'        => 'layout',
			'name'        => 'ab_two_third',
			'icon'        => 'icon-two-third',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/two-third.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'name' => '2/3 + 1/3', 'instantInsert' => '[ab_two_third first]Add 2/3 Content here[/ab_two_third]' . "\n\n\n" . '[ab_one_third]Add 1/3 Content here[/ab_one_third]' ),
			'drag-level'  => 2,
			'drop-level'  => 2,
			'html-render' => false
		);
	}
}

/**
 * AC_Shortcode_Columns_One_Fourth Class
 */
class AC_Shortcode_Columns_One_Fourth extends AC_Shortcode_Columns {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id        = 'axisbuilder_col_one_fourth';
		$this->title     = __( '1/4', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 25&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 5,
			'type'        => 'layout',
			'name'        => 'ab_one_fourth',
			'icon'        => 'icon-one-fourth',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/one-fourth.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'name' => '1/4 + 1/4 + 1/4 + 1/4', 'instantInsert' => '[ab_one_fourth first]Add Content here[/ab_one_fourth]' . "\n\n\n" . '[ab_one_fourth]Add Content here[/ab_one_fourth]' . "\n\n\n" . '[ab_one_fourth]Add Content here[/ab_one_fourth]' . "\n\n\n" . '[ab_one_fourth]Add Content here[/ab_one_fourth]' ),
			'drag-level'  => 2,
			'drop-level'  => 2,
			'html-render' => false
		);
	}
}

/**
 * AC_Shortcode_Columns_Three_Fourth Class
 */
class AC_Shortcode_Columns_Three_Fourth extends AC_Shortcode_Columns {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id        = 'axisbuilder_col_three_fourth';
		$this->title     = __( '3/4', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 75&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 6,
			'type'        => 'layout',
			'name'        => 'ab_three_fourth',
			'icon'        => 'icon-three-fourth',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/three-fourth.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'name' => '3/4 + 1/4', 'instantInsert' => '[ab_three_fourth first]Add 3/4 Content here[/ab_three_fourth]' . "\n\n\n" . '[ab_one_fourth]Add 1/4 Content here[/ab_one_fourth]' ),
			'drag-level'  => 2,
			'drop-level'  => 2,
			'html-render' => false
		);
	}
}

/**
 * AC_Shortcode_Columns_One_Fifth Class
 */
class AC_Shortcode_Columns_One_Fifth extends AC_Shortcode_Columns {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id        = 'axisbuilder_col_one_fifth';
		$this->title     = __( '1/5', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 20&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 7,
			'type'        => 'layout',
			'name'        => 'ab_one_fifth',
			'icon'        => 'icon-one-fifth',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/one-fifth.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'name' => '1/5 + 1/5 + 1/5 + 1/5 + 1/5', 'instantInsert' => '[ab_one_fifth first]1/5[/ab_one_fifth]' . "\n\n\n" . '[ab_one_fifth]2/5[/ab_one_fifth]' . "\n\n\n" . '[ab_one_fifth]3/5[/ab_one_fifth]' . "\n\n\n" . '[ab_one_fifth]4/5[/ab_one_fifth]' . "\n\n\n" . '[ab_one_fifth]5/5[/ab_one_fifth]' ),
			'drag-level'  => 2,
			'drop-level'  => 2,
			'html-render' => false
		);
	}
}

/**
 * AC_Shortcode_Columns_Two_Fifth Class
 */
class AC_Shortcode_Columns_Two_Fifth extends AC_Shortcode_Columns {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id        = 'axisbuilder_col_two_fifth';
		$this->title     = __( '2/5', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 40&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 8,
			'type'        => 'layout',
			'name'        => 'ab_two_fifth',
			'icon'        => 'icon-two-fifth',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/two-fifth.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'name' => '2/5', 'instantInsert' => '[ab_two_fifth first]2/5[/ab_two_fifth]' ),
			'drag-level'  => 2,
			'drop-level'  => 2,
			'html-render' => false
		);
	}
}

/**
 * AC_Shortcode_Columns_Three_Fifth Class
 */
class AC_Shortcode_Columns_Three_Fifth extends AC_Shortcode_Columns {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id        = 'axisbuilder_col_three_fifth';
		$this->title     = __( '3/5', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 60&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 9,
			'type'        => 'layout',
			'name'        => 'ab_three_fifth',
			'icon'        => 'icon-three-fifth',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/three-fifth.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'name' => '3/5', 'instantInsert' => '[ab_three_fifth first]3/5[/ab_three_fifth]' ),
			'drag-level'  => 2,
			'drop-level'  => 2,
			'html-render' => false
		);
	}
}

/**
 * AC_Shortcode_Columns_Four_Fifth Class
 */
class AC_Shortcode_Columns_Four_Fifth extends AC_Shortcode_Columns {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id        = 'axisbuilder_col_four_fifth';
		$this->title     = __( '4/5', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 80&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 10,
			'type'        => 'layout',
			'name'        => 'ab_four_fifth',
			'icon'        => 'icon-four-fifth',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/four-fifth.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'name' => '4/5', 'instantInsert' => '[ab_four_fifth first]4/5[/ab_four_fifth]' ),
			'drag-level'  => 2,
			'drop-level'  => 2,
			'html-render' => false
		);
	}
}
