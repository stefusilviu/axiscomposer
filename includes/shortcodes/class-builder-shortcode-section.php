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
			'tinymce'     => array( 'disable' => true ),
			'drag-level'  => 1,
			'drop-level'  => 1,
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

		$data['modal-title']       = $this->title;
		$data['modal-action']      = $this->shortcode['name'];
		$data['dragdrop-level']    = $this->shortcode['drag-level'];
		$data['shortcode-handler'] = $this->shortcode['name'];
		$data['shortcode-allowed'] = $this->shortcode['name'];

		$output = '<div class="axisbuilder-layout-section popup-animation axisbuilder-no-visual-updates axisbuilder-drag ' . $this->shortcode['name'] . ' ' . $class . '"' . axisbuilder_html_data_string( $data ) . '>';
			$output .= '<div class="axisbuilder-sorthandle menu-item-handle">';
				$output .= '<span class="axisbuilder-element-title">' . $this->title . '</span>';
				if ( isset( $this->shortcode['popup_editor'] ) ) {
					$output .= '<a class="axisbuilder-edit edit-element-icon" href="#edit" title="' . __( 'Edit Section', 'axisbuilder' ) . '">' . __( 'Edit Section', 'axisbuilder' ) . '</a>';
				}
				$output .= '<a class="axisbuilder-trash trash-element-icon" href="#trash" title="' . __( 'Delete Section', 'axisbuilder' ) . '">' . __( 'Delete Section', 'axisbuilder' ) . '</a>';
				$output .= '<a class="axisbuilder-clone clone-element-icon" href="#clone" title="' . __( 'Clone Section',  'axisbuilder' ) . '">' . __( 'Clone Section',  'axisbuilder' ) . '</a>';
			$output .= '</div>';
			$output .= '<div class="axisbuilder-inner-shortcode axisbuilder-connect-sort axisbuilder-drop" data-dragdrop-level="' . $data['drop-level'] . '">';
				$output .= '<textarea data-name="text-shortcode" rows="4" cols="20">' . ab_create_shortcode_data( $this->shortcode['name'], $content, $args ) . '</textarea>';
				if ( $content ) {
					$content = do_shortcode_builder( $content );
					$output .= $content;
				}
			$output .= '</div>';
		$output .= '</div>';

		return $output;
	}
}
