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
		$this->id        = 'axisbuilder_layout_row';
		$this->title     = __( 'Grid Row', 'axisbuilder' );
		$this->tooltip   = __( 'Add multiple Grid Rows below each other to create advanced grid layouts. Cells can be styled individually', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 12,
			'type'        => 'layout',
			'name'        => 'ac_layout_row',
			'icon'        => 'icon-gridrow',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/gridrow.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-target-insert',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 1,
			'drop-level'  => 100,
			'html-render' => false,
		);
	}

	/**
	 * Get Settings.
	 * @return array
	 */
	public function get_settings() {

		$this->elements = array(
			array(
				'name'     => __( 'Grid Borders', 'axisbuilder' ),
				'desc'     => __( 'Choose if your layout grid should display any border.', 'axisbuilder' ),
				'id'       => 'border',
				'std'      => 'no-border',
				'type'     => 'select',
				'subtype'  => array(
					__( 'No Borders', 'axisbuilder' )                                  => 'no-border',
					__( 'Borders on top and bottom', 'axisbuilder' )                   => 'axisbuilder-border-top-bottom',
					__( 'Borders between cells', 'axisbuilder' )                       => 'axisbuilder-border-cells',
					__( 'Borders on top and bottom and between cells', 'axisbuilder' ) => 'axisbuilder-border-top-bottom axisbuilder-border-cells'
				)
			),
			array(
				'name'    => __( 'Minimum height', 'axisbuilder' ),
				'desc'    => __( 'Set the minimum height of all the cells in pixel. Eg: 400px', 'axisbuilder' ),
				'id'      => 'min_height',
				'type'    => 'input',
				'std'     => '0'
			),
			array(
				'name'     => __( 'Smartphones Behaviour', 'axisbuilder' ),
				'desc'     => __( 'Choose how the cells inside the grid should behave on smartphones and small screens.', 'axisbuilder' ),
				'id'       => 'smartphones',
				'std'      => 'axisbuilder-flex-cells',
				'type'     => 'select',
				'subtype'  => array(
					__( 'By default each cell is displayed on its own', 'axisbuilder' )               => 'axisbuilder-flex-cells',
					__( 'Cells appear beside each other, just like on large screens', 'axisbuilder' ) => 'axisbuilder-fixed-cells',
				)
			),
			array(
				'name'     => __( 'For Developers: Section ID', 'axisbuilder' ),
				'desc'     => __( 'Apply a custom ID Attribute to the section, so you can apply a unique style via CSS. This option is also helpful if you want to use anchor links to scroll to a sections when a link is clicked', 'axisbuilder' ) . '<br /><br />' . __( 'Use with caution and make sure to only use allowed characters. No special characters can be used.', 'axisbuilder' ),
				'id'       => 'id',
				'std'      => '',
				'type'     => 'input',
				'class'    => 'ac_input_id'
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

		$data['modal-title']       = $this->title;
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

		$output = '<div class="axisbuilder-layout-row axisbuilder-layout-section modal-animation axisbuilder-no-visual-updates axisbuilder-drag ' . $this->shortcode['name'] . '"' . ac_html_data_string( $data ) . '>';
			$output .= '<div class="axisbuilder-sorthandle menu-item-handle">';
				$output .= '<span class="axisbuilder-element-title">' . $this->title . '</span>';
				if ( isset( $this->shortcode['has_fields'] ) ) {
					$output .= '<a class="axiscomposer-edit edit-element-icon" href="#edit" title="' . __( 'Edit Row', 'axisbuilder' ) . '">' . __( 'Edit Row', 'axisbuilder' ) . '</a>';
				}
				$output .= '<a class="axiscomposer-trash trash-element-icon" href="#trash" title="' . __( 'Delete Row', 'axisbuilder' ) . '">' . __( 'Delete Row', 'axisbuilder' ) . '</a>';
				$output .= '<a class="axiscomposer-clone clone-element-icon" href="#clone" title="' . __( 'Clone Row',  'axisbuilder' ) . '">' . __( 'Clone Row',  'axisbuilder' ) . '</a>';
			$output .= '</div>';
			$output .= '<div class="axisbuilder-cell">';
				$output .= '<a class="axisbuilder-cell-set set-cell-icon" href="#set-cell" title="' . __( 'Set Cell Size', 'axisbuilder' ) . '">' . __( 'Set Cell Size', 'axisbuilder' ) . '</a>';
				$output .= '<a class="axisbuilder-cell-add add-cell-icon" href="#add-cell" title="' . __( 'Add Cell',      'axisbuilder' ) . '">' . __( 'Add Cell',      'axisbuilder' ) . '</a>';
			$output .= '</div>';
			$output .= '<div class="axisbuilder-inner-shortcode axisbuilder-connect-sort axisbuilder-drop" data-dragdrop-level="' . $this->shortcode['drop-level'] . '">';
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
			'smartphones' => 'axisbuilder-flex-cells'
		);

		$atts = shortcode_atts( $pairs, $atts, $this->shortcode['name'] );

		extract( $atts );

		$params['id'] = empty( $id ) ? 'axisbuilder-layout-grid-' . self::$grid_count : sanitize_html_class( $id );
		$params['class'] = 'axisbuilder-layout-grid-container ' . $border . ' ' . $smartphones . ' ' . $meta['el_class'];
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

		$output .= axisbuilder_new_section( $params );
		$output .= ac_remove_autop( $content, true );
		$output .= axisbuilder_section_after_element_content( $meta, 'after-submenu', false );

		return $output;
	}
}
