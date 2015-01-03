<?php
/**
 * Section Shortcode
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
class AB_Shortcode_Section extends AB_Shortcode {

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
		$this->id        = 'axisbuilder_section';
		$this->title     = __( 'Color Section', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a section with unique background image and colors', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 11,
			'type'        => 'layout',
			'name'        => 'ab_section',
			'icon'        => 'icon-section',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/section.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-target-insert',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 1,
			'drop-level'  => 1,
			'html-render' => false
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
				'name'    => __( 'Custom Background Color', 'axisbuilder' ),
				'desc'    => __( 'Select a custom background color for your Section here. Leave empty to use the default.', 'axisbuilder' ),
				'id'      => 'color',
				'type'    => 'colorpicker',
				'std'     => ''
			),
			array(
				'name'     => __( 'Custom Background Image', 'axisbuilder' ),
				'desc'     => __( 'Either upload a new, or choose an existing image from your media library. Leave empty if you want to use the background image of the color scheme defined above.', 'axisbuilder' ),
				'title'    => __( 'Insert Image', 'axisbuilder' ),
				'button'   => __( 'Insert', 'axisbuilder' ),
				// 'delete'   => __( 'Delete', 'axisbuilder' ),
				'id'       => 'src',
				'std'      => '',
				'type'     => 'image'
			),
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

		if ( ! empty( $this->shortcode['modal-on-load'] ) ) {
			$data['modal-on-load'] = $this->shortcode['modal-on-load'];
		}

		$output = '<div class="axisbuilder-layout-section popup-animation axisbuilder-no-visual-updates axisbuilder-drag ' . $this->shortcode['name'] . '"' . axisbuilder_html_data_string( $data ) . '>';
			$output .= '<div class="axisbuilder-sorthandle menu-item-handle">';
				$output .= '<span class="axisbuilder-element-title">' . $this->title . '</span>';
				if ( isset( $this->shortcode['popup_editor'] ) ) {
					$output .= '<a class="axisbuilder-edit edit-element-icon" href="#edit" title="' . __( 'Edit Section', 'axisbuilder' ) . '">' . __( 'Edit Section', 'axisbuilder' ) . '</a>';
				}
				$output .= '<a class="axisbuilder-trash trash-element-icon" href="#trash" title="' . __( 'Delete Section', 'axisbuilder' ) . '">' . __( 'Delete Section', 'axisbuilder' ) . '</a>';
				$output .= '<a class="axisbuilder-clone clone-element-icon" href="#clone" title="' . __( 'Clone Section',  'axisbuilder' ) . '">' . __( 'Clone Section',  'axisbuilder' ) . '</a>';
			$output .= '</div>';
			$output .= '<div class="axisbuilder-inner-shortcode axisbuilder-connect-sort axisbuilder-drop" data-dragdrop-level="' . $data['dragdrop-level'] . '">';
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

	}
}
