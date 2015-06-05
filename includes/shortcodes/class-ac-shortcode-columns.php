<?php
/**
 * Columns Shortcode
 *
 * Note: Main AC_Shortcode_Columns is extended for different class for ease.
 *
 * @extends     AC_Shortcode
 * @package     AxisComposer/Shortcodes
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
		$this->id        = 'col_one_full';
		$this->method_title       = __( '1/1', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with full width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 1,
			'type'        => 'layout',
			'name'        => 'ac_one_full',
			'icon'        => 'icon-one-full',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/one-full.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'instantInsert' => '[ac_one_full first]Add Content here[/ac_one_full]' ),
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
			'ac_one_full'     => '1/1',
			'ac_one_half'     => '1/2',
			'ac_one_third'    => '1/3',
			'ac_two_third'    => '2/3',
			'ac_one_fourth'   => '1/4',
			'ac_three_fourth' => '3/4',
			'ac_one_fifth'    => '1/5',
			'ac_two_fifth'    => '2/5',
			'ac_three_fifth'  => '3/5',
			'ac_four_fifth'   => '4/5',
		);

		$extra_class = isset( $args[0] ) ? ( $args[0] == 'first' ) ? ' ac-first-column' : '' : '';

		$output  = '<div class="ac-layout-column ac-layout-column-no-cell modal-animation ac-drag ' . $this->shortcode['name'] . $extra_class . '" data-dragdrop-level="' . $this->shortcode['drag-level'] . '" data-width="' . $this->shortcode['name'] . '">';
			$output .= '<div class="ac-sorthandle menu-item-handle">';
				$output .= '<a class="ac-change-column-size layout-element-icon ac-decrease" href="#decrease" title="' . __( 'Decrease Column Size', 'axiscomposer' ) . '"></a>';
				$output .= '<span class="ac-column-size">' . $size[ $this->shortcode['name'] ] . '</span>';
				$output .= '<a class="ac-change-column-size layout-element-icon ac-increase" href="#increase" title="' . __( 'Increase Column Size', 'axiscomposer' ) . '"></a>';
				$output .= '<a class="axiscomposer-trash trash-element-icon" href="#trash" title="' . __( 'Delete Column', 'axiscomposer' ) . '">' . __( 'Delete Column', 'axiscomposer' ) . '</a>';
				$output .= '<a class="axiscomposer-clone clone-element-icon" href="#clone" title="' . __( 'Clone Column',  'axiscomposer' ) . '">' . __( 'Clone Column',  'axiscomposer' ) . '</a>';
			$output .= '</div>';
			$output .= '<div class="ac-inner-shortcode ac-connect-sort ac-drop" data-dragdrop-level="' . $this->shortcode['drop-level'] . '">';
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
		global $axiscomposer_config;

		$axiscomposer_config['current_column'] = $shortcode;

		$class = substr( str_replace( '_', '-', strtolower( $shortcode ) ), 3 );
		$first = ( isset( $atts[0] ) && trim( $atts[0] ) == 'first' ) ? 'first ' : '';

		$output  = '<div class="axiscomposer flex-column ' . $class . ' ' . $first . $meta['el_class'] . '">';
		$content = empty( $axiscomposer_config['conditionals']['is_axiscomposer_template'] ) ? ac_apply_autop( ac_remove_autop( $content ) ) : ac_remove_autop( $content, true );
		$output .= trim( $content );
		$output .= '</div>';

		unset( $axiscomposer_config['current_column'] );

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
		$this->id        = 'col_one_half';
		$this->method_title       = __( '1/2', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 50&percnt; width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 2,
			'type'        => 'layout',
			'name'        => 'ac_one_half',
			'icon'        => 'icon-one-half',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/one-half.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'name' => '1/2 + 1/2', 'instantInsert' => '[ac_one_half first]Add Content here[/ac_one_half]' . "\n\n\n" . '[ac_one_half]Add Content here[/ac_one_half]' ),
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
		$this->id        = 'col_one_third';
		$this->method_title       = __( '1/3', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 33&percnt; width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 3,
			'type'        => 'layout',
			'name'        => 'ac_one_third',
			'icon'        => 'icon-one-third',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/one-third.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'name' => '1/3 + 1/3 + 1/3', 'instantInsert' => '[ac_one_third first]Add Content here[/ac_one_third]' . "\n\n\n" . '[ac_one_third]Add Content here[/ac_one_third]' . "\n\n\n" . '[ac_one_third]Add Content here[/ac_one_third]' ),
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
		$this->id        = 'col_two_third';
		$this->method_title       = __( '2/3', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 67&percnt; width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 4,
			'type'        => 'layout',
			'name'        => 'ac_two_third',
			'icon'        => 'icon-two-third',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/two-third.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'name' => '2/3 + 1/3', 'instantInsert' => '[ac_two_third first]Add 2/3 Content here[/ac_two_third]' . "\n\n\n" . '[ac_one_third]Add 1/3 Content here[/ac_one_third]' ),
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
		$this->id        = 'col_one_fourth';
		$this->method_title       = __( '1/4', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 25&percnt; width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 5,
			'type'        => 'layout',
			'name'        => 'ac_one_fourth',
			'icon'        => 'icon-one-fourth',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/one-fourth.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'name' => '1/4 + 1/4 + 1/4 + 1/4', 'instantInsert' => '[ac_one_fourth first]Add Content here[/ac_one_fourth]' . "\n\n\n" . '[ac_one_fourth]Add Content here[/ac_one_fourth]' . "\n\n\n" . '[ac_one_fourth]Add Content here[/ac_one_fourth]' . "\n\n\n" . '[ac_one_fourth]Add Content here[/ac_one_fourth]' ),
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
		$this->id        = 'col_three_fourth';
		$this->method_title       = __( '3/4', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 75&percnt; width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 6,
			'type'        => 'layout',
			'name'        => 'ac_three_fourth',
			'icon'        => 'icon-three-fourth',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/three-fourth.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'name' => '3/4 + 1/4', 'instantInsert' => '[ac_three_fourth first]Add 3/4 Content here[/ac_three_fourth]' . "\n\n\n" . '[ac_one_fourth]Add 1/4 Content here[/ac_one_fourth]' ),
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
		$this->id        = 'col_one_fifth';
		$this->method_title       = __( '1/5', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 20&percnt; width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 7,
			'type'        => 'layout',
			'name'        => 'ac_one_fifth',
			'icon'        => 'icon-one-fifth',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/one-fifth.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'name' => '1/5 + 1/5 + 1/5 + 1/5 + 1/5', 'instantInsert' => '[ac_one_fifth first]1/5[/ac_one_fifth]' . "\n\n\n" . '[ac_one_fifth]2/5[/ac_one_fifth]' . "\n\n\n" . '[ac_one_fifth]3/5[/ac_one_fifth]' . "\n\n\n" . '[ac_one_fifth]4/5[/ac_one_fifth]' . "\n\n\n" . '[ac_one_fifth]5/5[/ac_one_fifth]' ),
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
		$this->id        = 'col_two_fifth';
		$this->method_title       = __( '2/5', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 40&percnt; width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 8,
			'type'        => 'layout',
			'name'        => 'ac_two_fifth',
			'icon'        => 'icon-two-fifth',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/two-fifth.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'name' => '2/5', 'instantInsert' => '[ac_two_fifth first]2/5[/ac_two_fifth]' ),
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
		$this->id        = 'col_three_fifth';
		$this->method_title       = __( '3/5', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 60&percnt; width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 9,
			'type'        => 'layout',
			'name'        => 'ac_three_fifth',
			'icon'        => 'icon-three-fifth',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/three-fifth.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'name' => '3/5', 'instantInsert' => '[ac_three_fifth first]3/5[/ac_three_fifth]' ),
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
		$this->id        = 'col_four_fifth';
		$this->method_title       = __( '4/5', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 80&percnt; width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 10,
			'type'        => 'layout',
			'name'        => 'ac_four_fifth',
			'icon'        => 'icon-four-fifth',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/four-fifth.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'name' => '4/5', 'instantInsert' => '[ac_four_fifth first]4/5[/ac_four_fifth]' ),
			'drag-level'  => 2,
			'drop-level'  => 2,
			'html-render' => false
		);
	}
}
