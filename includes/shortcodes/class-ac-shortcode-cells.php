<?php
/**
 * Cells Shortcode
 *
 * Note: Main AC_Shortcode_Cells is extended for different class for ease.
 *
 * @extends  AC_Shortcode
 * @version  1.0.0
 * @package  AxisComposer/Shortcodes
 * @category Shortcodes
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Shortcode_Cells Class
 */
class AC_Shortcode_Cells extends AC_Shortcode {

	public static $cell_class = '';
	public static $attributes = array();

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
		$this->id                 = 'cell_one_full';
		$this->method_title       = __( '1/1', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with full width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 13,
			'type'        => 'layout',
			'name'        => 'ac_cell_one_full',
			'icon'        => 'icon-one-full',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/one-full.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}

	/**
	 * Initialise Shortcode Settings Form Fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'vertical_align' => array(
				'title'             => __( 'Vertical align', 'axiscomposer' ),
				'description'       => __( 'Choose the vertical alignment of your cells content.', 'axiscomposer' ),
				'default'           => 'top',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'top'    => __( 'Top', 'axiscomposer' ),
					'middle' => __( 'Middle', 'axiscomposer' ),
					'bottom' => __( 'Bottom', 'axiscomposer' )
				)
			),
			'background_color' => array(
				'title'             => __( 'Background Color', 'axiscomposer' ),
				'description'       => __( 'This sets the background color for your cell. Leave empty to use the default.', 'axiscomposer' ),
				'type'              => 'color',
				'desc_tip'          => true,
				'default'           => ''
			),
			'background_image' => array(
				'title'             => __( 'Background Image', 'axiscomposer' ),
				'description'       => __( 'Either upload a new, or choose an existing image from your media library.', 'axiscomposer' ),
				'label'             => __( 'Insert Image', 'axiscomposer' ),
				'type'              => 'image',
				'desc_tip'          => true,
				'default'           => ''
			),
			'background_attachment' => array(
				'title'             => __( 'Background Attachment', 'axiscomposer' ),
				'description'       => __( 'Background can either scroll with the page, be fixed.', 'axiscomposer' ),
				'default'           => 'scroll',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'scroll'   => __( 'Scroll', 'axiscomposer' ),
					'repeat'   => __( 'Fixed', 'axiscomposer' )
				)
			),
			'background_position' => array(
				'title'             => __( 'Background Position', 'axiscomposer' ),
				'default'           => 'top left',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'top left'      => __( 'Top Left', 'axiscomposer' ),
					'top center'    => __( 'Top Center', 'axiscomposer' ),
					'top right'     => __( 'Top Right', 'axiscomposer' ),
					'bottom left'   => __( 'Bottom Left', 'axiscomposer' ),
					'bottom center' => __( 'Bottom Center', 'axiscomposer' ),
					'bottom right'  => __( 'Bottom Right', 'axiscomposer' ),
					'center left'   => __( 'Center Left', 'axiscomposer' ),
					'center center' => __( 'Center Center', 'axiscomposer' ),
					'center right'  => __( 'Center Right', 'axiscomposer' )
				)
			),
			'background_repeat' => array(
				'title'             => __( 'Background Repeat', 'axiscomposer' ),
				'default'           => 'no-repeat',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'no-repeat' => __( 'No Repeat', 'axiscomposer' ),
					'repeat'    => __( 'Tile', 'axiscomposer' ),
					'repeat-x'  => __( 'Tile Horizontally', 'axiscomposer' ),
					'repeat-y'  => __( 'Tile Vertically', 'axiscomposer' ),
					'stretch'   => __( 'Stretch to Fit', 'axiscomposer' )
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
			'ac_cell_one_full'     => '1/1',
			'ac_cell_one_half'     => '1/2',
			'ac_cell_one_third'    => '1/3',
			'ac_cell_two_third'    => '2/3',
			'ac_cell_one_fourth'   => '1/4',
			'ac_cell_three_fourth' => '3/4',
			'ac_cell_one_fifth'    => '1/5',
			'ac_cell_two_fifth'    => '2/5',
			'ac_cell_three_fifth'  => '3/5',
			'ac_cell_four_fifth'   => '4/5',
		);

		$data['width']             = $this->shortcode['name'];
		$data['modal-title']       = __( 'Edit Cell', 'axiscomposer' );
		$data['modal-action']      = $this->shortcode['name'];
		$data['dragdrop-level']    = $this->shortcode['drag-level'];
		$data['shortcode-handler'] = $this->shortcode['name'];
		$data['shortcode-allowed'] = $this->shortcode['name'];

		$output = '<div class="ac-layout-column ac-layout-cell ac-no-visual-updates ac-drag ' . $this->shortcode['name'] . '"' . ac_html_data_string( $data ) . '>';
			$output .= '<div class="ac-sorthandle">';
				$output .= '<span class="ac-column-size">' . $size[ $this->shortcode['name'] ] . '</span>';
				if ( isset( $this->shortcode['has_fields'] ) ) {
					$output .= '<a class="axiscomposer-edit edit-element-icon" href="#edit" title="' . __( 'Edit Cell', 'axiscomposer' ) . '">' . __( 'Edit Cell', 'axiscomposer' ) . '</a>';
				}
				$output .= '<a class="axiscomposer-trash trash-element-icon" href="#trash" title="' . __( 'Delete Cell', 'axiscomposer' ) . '">' . __( 'Delete Cell', 'axiscomposer' ) . '</a>';
				$output .= '<a class="axiscomposer-clone clone-element-icon" href="#clone" title="' . __( 'Clone Cell',  'axiscomposer' ) . '">' . __( 'Clone Cell',  'axiscomposer' ) . '</a>';
			$output .= '</div>';
			$output .= '<div class="ac-inner-shortcode ac-connect-sort ac-drop" data-dragdrop-level="' . $this->shortcode['drop-level'] . '">';
				$output .= '<span class="ac-fake-cellborder"></span>';
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
			$extra_class .= 'ac-inherit-color';
		}

		if ( $atts['background_repeat'] == 'stretch' ) {
			$extra_class .= 'ac-full-stretch';
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
		$shortcode = str_replace( 'ac_cell_', 'ac_', $shortcode );

		$axiscomposer_config['current_column'] = $shortcode;

		if ( ! empty( $outer_style ) ) {
			$outer_style = 'style="' . $outer_style . '"';
		}

		if ( ! empty( $inner_style ) ) {
			$inner_style = 'style="' . $inner_style . '"';
		}

		$output  = '<div class="flex-cell no-margin ' . $shortcode . $meta['el_class'] . $extra_class . self::$cell_class . '" ' . $outer_style . '>';
		$output .= '<div class="flex-cell-inner ' . $inner_style . '">';
		$output .= ac_format_content( $content );
		$output .= '</div></div>';

		unset( $axiscomposer_config['current_column'] );

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

		if ( isset( $atts[ $key ] ) && $atts[ $key ] !== '' ) {
			switch ( $new_key ) {
				case 'background-image':
					$style_string = $new_key . ':url(' . $atts[ $key ] . ');';
				break;

				case 'background-repeat':
					if ( $atts[ $key ] == 'stretch' ) {
						$atts[ $key ] = 'no-repeat';
					}
					$style_string = $new_key . ':' . $atts[ $key ] . ';';
				break;

				default:
					$style_string = $new_key . ':' . $atts[ $key ] . ';';
				break;
			}
		}

		return $style_string;
	}
}

/**
 * AC_Shortcode_Columns_One_Half Class
 */
class AC_Shortcode_Cells_One_Half extends AC_Shortcode_Cells {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id                 = 'cell_one_half';
		$this->method_title       = __( '1/2', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 50% width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 14,
			'type'        => 'layout',
			'name'        => 'ac_cell_one_half',
			'icon'        => 'icon-one-half',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/one-half.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}
}

/**
 * AC_Shortcode_Columns_One_Third Class
 */
class AC_Shortcode_Cells_One_Third extends AC_Shortcode_Cells {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id                 = 'cell_one_third';
		$this->method_title       = __( '1/3', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 33% width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 15,
			'type'        => 'layout',
			'name'        => 'ac_cell_one_third',
			'icon'        => 'icon-one-third',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/one-third.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}
}

/**
 * AC_Shortcode_Columns_Two_Third Class
 */
class AC_Shortcode_Cells_Two_Third extends AC_Shortcode_Cells {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id                 = 'cell_two_third';
		$this->method_title       = __( '2/3', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 67% width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 16,
			'type'        => 'layout',
			'name'        => 'ac_cell_two_third',
			'icon'        => 'icon-two-third',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/two-third.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}
}

/**
 * AC_Shortcode_Columns_One_Fourth Class
 */
class AC_Shortcode_Cells_One_Fourth extends AC_Shortcode_Cells {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id                 = 'cell_one_fourth';
		$this->method_title       = __( '1/4', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 25% width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 17,
			'type'        => 'layout',
			'name'        => 'ac_cell_one_fourth',
			'icon'        => 'icon-one-fourth',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/one-fourth.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}
}

/**
 * AC_Shortcode_Columns_Three_Fourth Class
 */
class AC_Shortcode_Cells_Three_Fourth extends AC_Shortcode_Cells {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id                 = 'cell_three_fourth';
		$this->method_title       = __( '3/4', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 75% width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 18,
			'type'        => 'layout',
			'name'        => 'ac_cell_three_fourth',
			'icon'        => 'icon-three-fourth',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/three-fourth.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 2,
			'html-render' => false,
			'invisible'   => true
		);
	}
}

/**
 * AC_Shortcode_Columns_One_Fifth Class
 */
class AC_Shortcode_Cells_One_Fifth extends AC_Shortcode_Cells {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id                 = 'cell_one_fifth';
		$this->method_title       = __( '1/5', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 20% width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 19,
			'type'        => 'layout',
			'name'        => 'ac_cell_one_fifth',
			'icon'        => 'icon-one-fifth',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/one-fifth.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}
}

/**
 * AC_Shortcode_Columns_Two_Fifth Class
 */
class AC_Shortcode_Cells_Two_Fifth extends AC_Shortcode_Cells {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id                 = 'cell_two_fifth';
		$this->method_title       = __( '2/5', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 40% width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 20,
			'type'        => 'layout',
			'name'        => 'ac_cell_two_fifth',
			'icon'        => 'icon-two-fifth',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/two-fifth.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}
}

/**
 * AC_Shortcode_Columns_Three_Fifth Class
 */
class AC_Shortcode_Cells_Three_Fifth extends AC_Shortcode_Cells {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id                 = 'cell_three_fifth';
		$this->method_title       = __( '3/5', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 60% width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 21,
			'type'        => 'layout',
			'name'        => 'ac_cell_three_fifth',
			'icon'        => 'icon-three-fifth',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/three-fifth.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}
}

/**
 * AC_Shortcode_Columns_Four_Fifth Class
 */
class AC_Shortcode_Cells_Four_Fifth extends AC_Shortcode_Cells {

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id                 = 'cell_four_fifth';
		$this->method_title       = __( '4/5', 'axiscomposer' );
		$this->method_description = __( 'Creates a single column with 80% width', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 22,
			'type'        => 'layout',
			'name'        => 'ac_cell_four_fifth',
			'icon'        => 'icon-four-fifth',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/columns/four-fifth.png', // Fallback if icon is missing :)
			'target'      => 'ac-section-drop',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 2,
			'drop-level'  => 1,
			'html-render' => false,
			'invisible'   => true
		);
	}
}
