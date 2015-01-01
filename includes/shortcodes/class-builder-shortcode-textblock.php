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
	public function shortcode_button() {
		$this->id        = 'axisbuilder_textblock';
		$this->title     = __( 'Text Block', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a simple text block', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 60,
			'type'    => 'content',
			'name'    => 'ab_textblock',
			'icon'    => 'icon-textblock',
			'image'   => AB()->plugin_url() . '/assets/images/content/textblock.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinyMCE' => array( 'disable' => true ),
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
				'desc'    => __( 'Select Size of the text in px', 'axisbuilder' ),
				'id'      => 'size',
				'type'    => 'select',
				'subtype' => axisbuilder_num_to_array( 10, 40, 1, array( __( 'Defalut Size', 'axisbuilder' ) => '' ) ),
				'std'     => ''
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

	}
}
