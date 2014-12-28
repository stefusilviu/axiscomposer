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
		$this->id        = 'axisbuilder_layout_row';
		$this->title     = __( 'Grid Row', 'axisbuilder' );
		$this->tooltip   = __( 'Add multiple Grid Rows below each other to create advanced grid layouts. Cells can be styled individually', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 12,
			'type'        => 'layout',
			'name'        => 'ab_layout_row',
			'icon'        => 'icon-gridrow',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/gridrow.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-target-insert',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 1,
			'drop-level'  => 100,
			'html-render' => false,
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
			$textarea_content = ab_create_shortcode_data( $this->shortcode['name'], $content, $args );
		} else {
			$eventual_content = '';
			$ab_cell_one_half = new AB_Shortcode_Cells_One_Half();
			$shortcode_params = array( 'content' => '', 'args' => '', 'data' => '' );
			// Loading twice as we have to generate 2 cell :)
			$eventual_content .= $ab_cell_one_half->editor_element( $shortcode_params );
			$eventual_content .= $ab_cell_one_half->editor_element( $shortcode_params );
			$textarea_content = ab_create_shortcode_data( $this->shortcode['name'], '[ab_cell_one_half first][/ab_cell_one_half] [ab_cell_one_half][/ab_cell_one_half]', $args );
		}

		$output = '<div class="axisbuilder-layout-row axisbuilder-layout-section popup-animation axisbuilder-no-visual-updates axisbuilder-drag ' . $this->shortcode['name'] . '"' . axisbuilder_html_data_string( $data ) . '>';
			$output .= '<div class="axisbuilder-sorthandle menu-item-handle">';
				$output .= '<span class="axisbuilder-element-title">' . $this->title . '</span>';
				if ( isset( $this->shortcode['popup_editor'] ) ) {
					$output .= '<a class="axisbuilder-edit edit-element-icon" href="#edit" title="' . __( 'Edit Row', 'axisbuilder' ) . '">' . __( 'Edit Row', 'axisbuilder' ) . '</a>';
				}
				$output .= '<a class="axisbuilder-trash trash-element-icon" href="#trash" title="' . __( 'Delete Row', 'axisbuilder' ) . '">' . __( 'Delete Row', 'axisbuilder' ) . '</a>';
				$output .= '<a class="axisbuilder-clone clone-element-icon" href="#clone" title="' . __( 'Clone Row',  'axisbuilder' ) . '">' . __( 'Clone Row',  'axisbuilder' ) . '</a>';
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

	}
}
