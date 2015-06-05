<?php
/**
 * Grid Row Shortcode
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
 * AC_Shortcode_Section Class
 */
class AC_Shortcode_Grid_Row extends AC_Shortcode {

	public static $grid_count = 0;

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
		$this->id                 = 'layout_row';
		$this->method_title       = __( 'Grid Row', 'axiscomposer' );
		$this->method_description = __( 'Add multiple Grid Rows below each other to create advanced grid layouts. Cells can be styled individually', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 12,
			'type'        => 'layout',
			'name'        => 'ac_layout_row',
			'icon'        => 'icon-gridrow',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/gridrow.png', // Fallback if icon is missing :)
			'target'      => 'ac-target-insert',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 1,
			'drop-level'  => 100,
			'html-render' => false,
		);
	}

	/**
	 * Initialise Shortcode Settings Form Fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'border' => array(
				'title'             => __( 'Grid Borders', 'axiscomposer' ),
				'description'       => __( 'This option lets you choose a border style for layout grid.', 'axiscomposer' ),
				'default'           => 'no-border',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'no-border'                      => __( 'No Border', 'axiscomposer' ),
					'border-cells'                   => __( 'Borders between cells', 'axiscomposer' ),
					'border-top-bottom'              => __( 'Borders on top and bottom', 'axiscomposer' ),
					'border-cells border-top-bottom' => __( 'Borders between cells and on top and bottom', 'axiscomposer' )
				)
			),
			'min_height' => array(
				'title'             => __( 'Section Custom Height', 'axiscomposer' ),
				'description'       => __( 'Define a minimum height for the cells. Use a pixel value. eg: 400px', 'axiscomposer' ),
				'type'              => 'text',
				'desc_tip'          => true,
				'default'           => '0'
			),
			'smartphones' => array(
				'title'             => __( 'Grid Borders', 'axiscomposer' ),
				'description'       => __( 'Choose how the cells inside the grid should behave on smartphones and small screens.', 'axiscomposer' ),
				'default'           => 'ac-flex-cells',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'ac-flex-cells'  => __( 'By default each cell is displayed on its own', 'axiscomposer' ),
					'ac-fixed-cells' => __( 'Like on large screen, cells appear beside each other', 'axiscomposer' )
				)
			),
			'id' => array(
				'title'             => __( 'Custom Section ID', 'axiscomposer' ),
				'description'       => __( 'This option lets you set custom section ID you are willing to use for customization.', 'axiscomposer' ),
				'class'             => 'ac_input_id',
				'type'              => 'text',
				'default'           => ''
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

		$data['modal-title']       = $this->method_title;
		$data['modal-action']      = $this->shortcode['name'];
		$data['dragdrop-level']    = $this->shortcode['drag-level'];
		$data['shortcode-handler'] = $this->shortcode['name'];
		$data['shortcode-allowed'] = $this->shortcode['name'];

		if ( $content ) {
			$eventual_content = do_shortcode_builder( $content );
			$textarea_content = ac_shortcode_data( $this->shortcode['name'], $content, $args );
		} else {
			$eventual_content = '';
			$ac_cell_one_half = new AC_Shortcode_Cells_One_Half();
			$shortcode_params = array( 'content' => '', 'args' => '', 'data' => '' );
			// Loading twice as we have to generate 2 cell :)
			$eventual_content .= $ac_cell_one_half->editor_element( $shortcode_params );
			$eventual_content .= $ac_cell_one_half->editor_element( $shortcode_params );
			$textarea_content = ac_shortcode_data( $this->shortcode['name'], '[ac_cell_one_half first][/ac_cell_one_half] [ac_cell_one_half][/ac_cell_one_half]', $args );
		}

		$output = '<div class="ac-layout-row ac-layout-section modal-animation ac-no-visual-updates ac-drag ' . $this->shortcode['name'] . '"' . ac_html_data_string( $data ) . '>';
			$output .= '<div class="ac-sorthandle menu-item-handle">';
				$output .= '<span class="ac-element-title">' . $this->method_title . '</span>';
				if ( isset( $this->shortcode['has_fields'] ) ) {
					$output .= '<a class="axiscomposer-edit edit-element-icon" href="#edit" title="' . __( 'Edit Row', 'axiscomposer' ) . '">' . __( 'Edit Row', 'axiscomposer' ) . '</a>';
				}
				$output .= '<a class="axiscomposer-trash trash-element-icon" href="#trash" title="' . __( 'Delete Row', 'axiscomposer' ) . '">' . __( 'Delete Row', 'axiscomposer' ) . '</a>';
				$output .= '<a class="axiscomposer-clone clone-element-icon" href="#clone" title="' . __( 'Clone Row',  'axiscomposer' ) . '">' . __( 'Clone Row',  'axiscomposer' ) . '</a>';
			$output .= '</div>';
			$output .= '<div class="ac-cell">';
				$output .= '<a class="axiscomposer-cell-set set-cell-icon" href="#set-cell" title="' . __( 'Set Cell Size', 'axiscomposer' ) . '">' . __( 'Set Cell Size', 'axiscomposer' ) . '</a>';
				$output .= '<a class="axiscomposer-cell-add add-cell-icon" href="#add-cell" title="' . __( 'Add Cell',      'axiscomposer' ) . '">' . __( 'Add Cell',      'axiscomposer' ) . '</a>';
			$output .= '</div>';
			$output .= '<div class="ac-inner-shortcode ac-connect-sort ac-drop" data-dragdrop-level="' . $this->shortcode['drop-level'] . '">';
				$output .= '<textarea data-name="text-shortcode" rows="4" cols="20">' . $textarea_content . '</textarea>';
				$output .= $eventual_content;
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
		$output = '';
		$params = array();

		self::$grid_count++;

		// Entire list of supported attributes and their defaults
		$pairs = array(
			'id'          => '',
			'border'      => '',
			'min_height'  => '0',
			'smartphones' => 'ac-flex-cells'
		);

		$atts = shortcode_atts( $pairs, $atts, $this->shortcode['name'] );

		extract( $atts );

		$params['id'] = empty( $id ) ? 'ac-layout-grid-' . self::$grid_count : sanitize_html_class( $id );
		$params['class'] = 'ac-layout-grid-container ' . $border . ' ' . $smartphones . ' ' . $meta['el_class'];
		$params['custom_markup'] = $meta['custom_markup'];
		$params['open_structure'] = false;

		if ( isset( $meta['counter'] ) ) {
			if ( $meta['counter'] == 0 ) {
				$params['close'] = false;
			}

			if ( $meta['counter'] != 0 ) {
				$params['class'] .= ' submenu-not-first';
			}
		}

		AC_Shortcode_Cells::$attributes = $atts;

		$output .= ac_new_section( $params );
		$output .= ac_remove_autop( $content, true );
		$output .= ac_section_after_element_content( $meta, 'after-submenu', false );

		return $output;
	}
}
