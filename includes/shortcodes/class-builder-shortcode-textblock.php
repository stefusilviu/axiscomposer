<?php
/**
 * Text Block Shortcode
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
 * AB_Shortcode_Textblock Class
 */
class AB_Shortcode_Textblock extends AB_Shortcode {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Configuration for builder shortcode button.
	 */
	public function init_shortcode() {
		$this->id        = 'axisbuilder_textblock';
		$this->title     = __( 'Text Block', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a simple text block', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 60,
			'type'    => 'content',
			'name'    => 'ab_textblock',
			'icon'    => 'icon-textblock',
			'image'   => AB()->plugin_url() . '/assets/images/content/textblock.png', // Fallback if icon is missing :)
			'modal'   => array( 'modal-class' => 'normalscreen' ),
			'target'  => 'axisbuilder-target-insert',
			'tinyMCE' => array( 'disable' => true ),
		);
	}

	/**
	 * Get Settings.
	 * @return array
	 */
	public function get_settings() {

		$this->elements = array(
			// array(
			// 	'type'   => 'open_tab',
			// 	'nodesc' => true
			// ),

			// array(
			// 	'name'   => __( 'Content', 'axisbuilder' ),
			// 	'type'   => 'tab',
			// 	'nodesc' => true
			// ),

			array(
				'name'    => __( 'Content', 'axisbuilder' ),
				'desc'    => __( 'Enter some content for this textblock', 'axisbuilder' ),
				'id'      => 'content',
				'type'    => 'tinymce',
				'std'     => __( 'Click here to add your own text', 'axisbuilder' )
			),

			array(
				'name'    => __( 'Font Size', 'axisbuilder' ),
				'desc'    => __( 'Choose the font size of the text in px', 'axisbuilder' ),
				'type'    => 'number',
				'id'      => 'size',
				'min'     => '10',
				'max'     => '40',
				'std'     => '16'
			),

			// array(
			// 	'type'   => 'close_div',
			// 	'nodesc' => true
			// ),

			// array(
			// 	'name'   => __( 'Colors', 'axisbuilder' ),
			// 	'type'   => 'tab',
			// 	'nodesc' => true
			// ),

			array(
				'name'    => __( 'Font Colors', 'axisbuilder' ),
				'desc'    => __( 'Either use the themes default colors or apply some custom ones', 'axisbuilder' ),
				'id'      => 'font_color',
				'std'     => '',
				'type'    => 'select',
				'subtype' => array(
					__( 'Default', 'axisbuilder' ) => 'default',
					__( 'Define Custom Colors', 'axisbuilder' ) => 'custom'
				)
			),

			array(
				'name'     => __( 'Custom Font Color', 'axisbuilder' ),
				'desc'     => __( 'Select a custom font color. Leave empty to use the default', 'axisbuilder' ),
				'id'       => 'color',
				'std'      => '',
				'required' => array( 'font_color', 'equals', 'custom' ),
				'type'     => 'colorpicker'
			),

			// array(
			// 	'type'   => 'close_div',
			// 	'nodesc' => true
			// ),

			// array(
			// 	'type'   => 'close_div',
			// 	'nodesc' => true
			// ),
		);
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

		// Entire list of supported attributes and their defaults
		$pairs = array(
			'size'       => '',
			'font_color' => '',
			'color'      => ''
		);

		$atts = shortcode_atts( $pairs, $atts, $this->shortcode['name'] );

		extract( $atts );

		$class = empty( $meta['custom_class'] ) ? '' : $meta['custom_class'];

		if ( $size ) {
			$style = 'font-size: ' . $size . 'px; ';
		}

		if ( $font_color ) {
			$class .= 'axisbuilder-inherit-color';
			$style .= empty( $color ) ? '' : 'color: ' . $color . ';';
		}

		if ( $style ) {
			$style = 'style="' . $style . '"';
		}

		$output .= '<section class="axisbuilder textblock-section">';
		$output .= '<div class="axisbuilder-textblock ' . $class . '" ' . $style . '>' . axisbuilder_apply_autop( axisbuilder_remove_autop( $content ) ) . '</div>';
		$output .= '</section>';

		return $output;
	}
}
