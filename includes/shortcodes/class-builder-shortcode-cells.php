<?php
/**
 * Cells Shortcode
 *
 * Note: Main AB_Shortcode_Cells is extended for different class for ease.
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
 * AB_Shortcode_Cells Class
 */
class AB_Shortcode_Cells extends AB_Shortcode {

	public static $cell_class = '';
	public static $attributes = array();

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
		$this->id        = 'axisbuilder_cell_one_full';
		$this->title     = __( '1/1', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with full width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 13,
			'type'        => 'layout',
			'name'        => 'ab_cell_one_full',
			'icon'        => 'icon-one-full',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/one-full.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}

	/**
	 * Popup Elements
	 *
	 * If this method is defined the elements automatically gets an edit button.
	 * When pressed opens a popup modal window that allows to edit the element properties.
	 */
	public function popup_elements() {
		$this->elements = array(
			array(
				'name'     => __( 'Vertical align', 'axisbuilder' ),
				'desc'     => __( 'Choose the vertical alignment of your cells content.', 'axisbuilder' ),
				'id'       => 'vertical_align',
				'std'      => 'top',
				'type'     => 'select',
				'subtype'  => array(
					__( 'Top', 'axisbuilder' )    => 'top',
					__( 'Middle', 'axisbuilder' ) => 'middle',
					__( 'Bottom', 'axisbuilder' ) => 'bottom'
				)
			),
			// array(
			// 	'name'     => __( 'Cell Padding', 'axisbuilder' ),
			// 	'desc'     => __( 'Set the distance from the cell content to the border here. Both pixel and &percnt; based values are accepted. eg: 30px, 5&percnt;', 'axisbuilder' ),
			// 	'id'       => 'padding',
			// 	'std'      => '30px',
			// 	'type'     => 'input', // Will be multi_input
			// 	'sync'     => true,
			// 	'subtype'  => array(
			// 		__( 'Top', 'axisbuilder' )    => 'top',
			// 		__( 'Right', 'axisbuilder' )  => 'right',
			// 		__( 'Bottom', 'axisbuilder' ) => 'bottom',
			// 		__( 'Left', 'axisbuilder' )   => 'left'
			// 	)
			// ),
			array(
				'name'     => __( 'Custom Background Color', 'axisbuilder' ),
				'desc'     => __( 'Select a custom background color for your Section here. Leave empty to use the default.', 'axisbuilder' ),
				'id'       => 'background_color',
				'type'     => 'colorpicker',
				'std'      => ''
			),
			array(
				'name'     => __( 'Custom Background Image', 'axisbuilder' ),
				'desc'     => __( 'Either upload a new, or choose an existing image from your media library. Leave empty if you want to use the background image.', 'axisbuilder' ),
				'title'    => __( 'Insert Image', 'axisbuilder' ),
				'button'   => __( 'Insert', 'axisbuilder' ),
				'id'       => 'src',
				'std'      => '',
				'type'     => 'image'
			),
			array(
				'name'     => __( 'Background Attachment', 'axisbuilder' ),
				'desc'     => __( 'Background can either scroll with the page, be fixed.', 'axisbuilder' ),
				'id'       => 'background_attachment',
				'std'      => 'scroll',
				'type'     => 'select',
				'required' => array( 'src', 'not', '' ),
				'subtype'  => array(
					__( 'Scroll', 'axisbuilder' )   => 'scroll',
					__( 'Fixed', 'axisbuilder' )    => 'fixed'
				)
			),
			array(
				'name'     => __( 'Background Position', 'axisbuilder' ),
				'id'       => 'background_position',
				'std'      => 'top left',
				'type'     => 'select',
				'required' => array( 'src', 'not', '' ),
				'subtype'  => array(
					__( 'Top Left', 'axisbuilder' )       =>'top left',
					__( 'Top Center', 'axisbuilder' )     =>'top center',
					__( 'Top Right', 'axisbuilder' )      =>'top right',
					__( 'Bottom Left', 'axisbuilder' )    =>'bottom left',
					__( 'Bottom Center', 'axisbuilder' )  =>'bottom center',
					__( 'Bottom Right', 'axisbuilder' )   =>'bottom right',
					__( 'Center Left', 'axisbuilder' )    =>'center left',
					__( 'Center Center', 'axisbuilder' )  =>'center center',
					__( 'Center Right', 'axisbuilder' )   =>'center right'
				)
			),
			array(
				'name'     => __( 'Background Repeat', 'axisbuilder' ),
				'id'       => 'background_repeat',
				'std'      => 'no-repeat',
				'type'     => 'select',
				'required' => array( 'src', 'not', '' ),
				'subtype'  => array(
					__( 'No Repeat', 'axisbuilder' )         => 'no-repeat',
					__( 'Tile', 'axisbuilder' )              => 'repeat',
					__( 'Tile Horizontally', 'axisbuilder' ) => 'repeat-x',
					__( 'Tile Vertically', 'axisbuilder' )   => 'repeat-y',
					__( 'Stretch to Fit', 'axisbuilder' )    => 'stretch'
				)
			)
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
			'ab_cell_one_full'     => '1/1',
			'ab_cell_one_half'     => '1/2',
			'ab_cell_one_third'    => '1/3',
			'ab_cell_two_third'    => '2/3',
			'ab_cell_one_fourth'   => '1/4',
			'ab_cell_three_fourth' => '3/4',
			'ab_cell_one_fifth'    => '1/5',
			'ab_cell_two_fifth'    => '2/5',
			'ab_cell_three_fifth'  => '3/5',
			'ab_cell_four_fifth'   => '4/5',
		);

		$data['width']             = $this->shortcode['name'];
		$data['modal-title']       = __( 'Edit Cell', 'axisbuilder' );
		$data['modal-action']      = $this->shortcode['name'];
		$data['dragdrop-level']    = $this->shortcode['drag-level'];
		$data['shortcode-handler'] = $this->shortcode['name'];
		$data['shortcode-allowed'] = $this->shortcode['name'];

		if ( ! empty( $this->shortcode['modal-on-load'] ) ) {
			$data['modal-on-load'] = $this->shortcode['modal-on-load'];
		}

		$output = '<div class="axisbuilder-layout-column axisbuilder-layout-cell axisbuilder-no-visual-updates axisbuilder-drag ' . $this->shortcode['name'] . '"' . axisbuilder_html_data_string( $data ) . '>';
			$output .= '<div class="axisbuilder-sorthandle">';
				$output .= '<span class="axisbuilder-column-size">' . $size[ $this->shortcode['name'] ] . '</span>';
				if ( isset( $this->shortcode['popup_editor'] ) ) {
					$output .= '<a class="axisbuilder-edit edit-element-icon" href="#edit" title="' . __( 'Edit Cell', 'axisbuilder' ) . '">' . __( 'Edit Cell', 'axisbuilder' ) . '</a>';
				}
				$output .= '<a class="axisbuilder-trash trash-element-icon" href="#trash" title="' . __( 'Delete Cell', 'axisbuilder' ) . '">' . __( 'Delete Cell', 'axisbuilder' ) . '</a>';
				$output .= '<a class="axisbuilder-clone clone-element-icon" href="#clone" title="' . __( 'Clone Cell',  'axisbuilder' ) . '">' . __( 'Clone Cell',  'axisbuilder' ) . '</a>';
			$output .= '</div>';
			$output .= '<div class="axisbuilder-inner-shortcode axisbuilder-connect-sort axisbuilder-drop" data-dragdrop-level="' . $this->shortcode['drop-level'] . '">';
				$output .= '<span class="axisbuilder-fake-cellborder"></span>';
				$output .= '<textarea data-name="text-shortcode" rows="4" cols="20">' . ab_create_shortcode_data( $this->shortcode['name'], $content, $args ) . '</textarea>';
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
		$extra_class = $outer_style = $inner_style = '';

		// Entire list of supported attributes and their defaults
		$pairs = array(
			'vertical_align'        => '',
			'padding'               => '',
			'color'                 => '',
			'background_color'      => '',
			'background_position'   => '',
			'background_repeat'     => '',
			'background_attachment' => '',
			'fetch_image'           => '',
			'attachment'            => '',
			'attachment_size'       => ''
		);

		$atts = shortcode_atts( $pairs, $atts, $this->shortcode['name'] );

		if ( ! empty( self::$attributes['min_height'] ) ) {
			$min_height  = (int) self::$attr['min_height'];
			$outer_style = 'height: ' . $min_height . 'px; min-height: ' . $min_height . 'px;';
		}

		if ( ! empty( $atts['attachment'] ) ) {
			$src = wp_get_attachment_image_src( $atts['attachment'], $atts['attachment_size'] );
			if ( ! empty( $src[0] ) ) {
				$atts['fetch_image'] = $src[0];
			}
		}

		if ( ! empty( $atts['colors'] ) ) {
			$extra_class .= 'axisbuilder-inherit-color';
		}

		if ( $atts['background_repeat'] == 'stretch' ) {
			$extra_class .= 'axisbuilder-full-stretch';
		}

		// Padding fetch
		$explode_padding = explode( ',', $atts['padding'] );
		if ( count( $explode_padding ) > 1 ) {
			$atts['padding'] = '';

			foreach ( $explode_padding as $padding ) {
				if ( empty( $padding ) ) {
					$padding = '0';
					$atts['padding'] .= $padding . ' ';
				}
			}
		}

		if ( ! empty( $atts['fetch_image'] ) ) {
			$outer_style .= $this->style_string( $atts, 'fetch_image', 'background-image' );
			$outer_style .= $this->style_string( $atts, 'background_position', 'background-position' );
			$outer_style .= $this->style_string( $atts, 'background_repeat', 'background-repeat' );
			$outer_style .= $this->style_string( $atts, 'background_attachment', 'background-attachment' );
		}

		$outer_style .= $this->style_string( $atts, 'vertical_align', 'vertical-align' );
		$outer_style .= $this->style_string( $atts, 'padding' );
		$outer_style .= $this->style_string( $atts, 'background_color', 'background-color' );

		// Modify the shorycode name
		$shortcode = str_replace( 'ab_cell_', 'ab_', $shortcode );

		$axisbuilder_config['current_column'] = $shortcode;

		if ( ! empty( $outer_style ) ) {
			$outer_style = 'style="' . $outer_style . '"';
		}

		if ( ! empty( $inner_style ) ) {
			$inner_style = 'style="' . $inner_style . '"';
		}

		$output  = '<div class="flex-cell no-margin ' . $shortcode . $meta['el_class'] . $extra_class . self::$cell_class . '" ' . $outer_style . '>';
		$output .= '<div class="flex-cell-inner ' . $inner_style . '">';
		$output .= empty( $axisbuilder_config['conditionals']['is_axisbuilder_template'] ) ? axisbuilder_apply_autop( axisbuilder_remove_autop( $content ) ) : axisbuilder_remove_autop( $content, true );
		$output .= '</div></div>';

		unset( $axisbuilder_config['current_column'] );

		return $output;
	}

	/**
	 * Style String.
	 * @param  array  $atts    Array of attributes.
	 * @param  string $key     Key for style string.
	 * @param  string $new_key If needed new style string.
	 * @return string          Returns the html style string.
	 */
	protected function style_string( $atts, $key, $new_key = null ) {
		$style_string = '';

		if ( empty( $new_key ) ) {
			$new_key = $key;
		}

		if ( isset( $atts[$key] ) && $atts[$key] !== '' ) {
			switch ( $new_key ) {
				case 'background-image':
					$style_string = $new_key . ':url(' . $atts[$key] . ');';
				break;

				case 'background-repeat':
					if ( $atts[$key] == 'stretch' ) {
						$atts[$key] = 'no-repeat';
					}
					$style_string = $new_key . ':' . $atts[$key] . ';';
				break;

				default:
					$style_string = $new_key . ':' . $atts[$key] . ';';
				break;
			}
		}

		return $style_string;
	}
}

/**
 * AB_Shortcode_Columns_One_Half Class
 */
class AB_Shortcode_Cells_One_Half extends AB_Shortcode_Cells {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_cell_one_half';
		$this->title     = __( '1/2', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 50&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 14,
			'type'        => 'layout',
			'name'        => 'ab_cell_one_half',
			'icon'        => 'icon-one-half',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/one-half.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}
}

/**
 * AB_Shortcode_Columns_One_Third Class
 */
class AB_Shortcode_Cells_One_Third extends AB_Shortcode_Cells {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_cell_one_third';
		$this->title     = __( '1/3', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 33&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 15,
			'type'        => 'layout',
			'name'        => 'ab_cell_one_third',
			'icon'        => 'icon-one-third',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/one-third.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}
}

/**
 * AB_Shortcode_Columns_Two_Third Class
 */
class AB_Shortcode_Cells_Two_Third extends AB_Shortcode_Cells {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_cell_two_third';
		$this->title     = __( '2/3', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 67&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 16,
			'type'        => 'layout',
			'name'        => 'ab_cell_two_third',
			'icon'        => 'icon-two-third',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/two-third.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}
}

/**
 * AB_Shortcode_Columns_One_Fourth Class
 */
class AB_Shortcode_Cells_One_Fourth extends AB_Shortcode_Cells {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_cell_one_fourth';
		$this->title     = __( '1/4', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 25&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 17,
			'type'        => 'layout',
			'name'        => 'ab_cell_one_fourth',
			'icon'        => 'icon-one-fourth',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/one-fourth.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}
}

/**
 * AB_Shortcode_Columns_Three_Fourth Class
 */
class AB_Shortcode_Cells_Three_Fourth extends AB_Shortcode_Cells {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_cell_three_fourth';
		$this->title     = __( '3/4', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 75&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 18,
			'type'        => 'layout',
			'name'        => 'ab_cell_three_fourth',
			'icon'        => 'icon-three-fourth',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/three-fourth.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 2,
			'html-render' => false,
			'invisible'   => true
		);
	}
}

/**
 * AB_Shortcode_Columns_One_Fifth Class
 */
class AB_Shortcode_Cells_One_Fifth extends AB_Shortcode_Cells {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_cell_one_fifth';
		$this->title     = __( '1/5', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 20&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 19,
			'type'        => 'layout',
			'name'        => 'ab_cell_one_fifth',
			'icon'        => 'icon-one-fifth',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/one-fifth.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}
}

/**
 * AB_Shortcode_Columns_Two_Fifth Class
 */
class AB_Shortcode_Cells_Two_Fifth extends AB_Shortcode_Cells {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_cell_two_fifth';
		$this->title     = __( '2/5', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 40&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 20,
			'type'        => 'layout',
			'name'        => 'ab_cell_two_fifth',
			'icon'        => 'icon-two-fifth',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/two-fifth.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}
}

/**
 * AB_Shortcode_Columns_Three_Fifth Class
 */
class AB_Shortcode_Cells_Three_Fifth extends AB_Shortcode_Cells {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_cell_three_fifth';
		$this->title     = __( '3/5', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 60&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 21,
			'type'        => 'layout',
			'name'        => 'ab_cell_three_fifth',
			'icon'        => 'icon-three-fifth',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/three-fifth.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}
}

/**
 * AB_Shortcode_Columns_Four_Fifth Class
 */
class AB_Shortcode_Cells_Four_Fifth extends AB_Shortcode_Cells {

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
		$this->id        = 'axisbuilder_cell_four_fifth';
		$this->title     = __( '4/5', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a single column with 80&percnt; width', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 22,
			'type'        => 'layout',
			'name'        => 'ab_cell_four_fifth',
			'icon'        => 'icon-four-fifth',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/columns/four-fifth.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}
}
