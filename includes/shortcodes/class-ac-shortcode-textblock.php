<?php
/**
 * Text Block Shortcode
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
 * AC_Shortcode_Textblock Class
 */
class AC_Shortcode_Textblock extends AC_Shortcode {

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
		$this->id        = 'textblock';
		$this->method_title       = __( 'Text Block', 'axiscomposer' );
		$this->method_description = __( 'Creates a simple text block', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 60,
			'type'    => 'content',
			'name'    => 'ac_textblock',
			'icon'    => 'icon-textblock',
			'image'   => AC()->plugin_url() . '/assets/images/content/textblock.png', // Fallback if icon is missing :)
			'modal'   => array( 'modal-class' => 'normalscreen' ),
			'target'  => 'ac-target-insert',
			'tinyMCE' => array( 'disable' => true ),
		);
	}

	/**
	 * Initialise Shortcode Settings Form Fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'content' => array(
				'title'             => __( 'Content', 'axiscomposer' ),
				'description'       => __( 'Enter some content for this textblock :)', 'axiscomposer' ),
				'default'           => __( 'Click here to add your own text', 'axiscomposer' ),
				'class'             => 'axiscomposer-tinymce',
				'type'              => 'tinymce'
			),
			'size' => array(
				'title'             => __( 'Font Size', 'axiscomposer' ),
				'description'       => __( 'This sets the custom font size of the text.', 'axiscomposer' ),
				'type'              => 'number',
				'desc_tip'          => true,
				'default'           => 16,
				'custom_attributes' => array(
					'min' => 10,
					'max' => 40
				)
			),
			'font_color' => array(
				'title'             => __( 'Font Color', 'axiscomposer' ),
				'description'       => __( 'This option lets you limit which color you are willing to use.', 'axiscomposer' ),
				'default'           => 'default',
				'type'              => 'select',
				'class'             => 'availability ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'default'  => __( 'Theme Default Color', 'axiscomposer' ),
					'specific' => __( 'Define Custom Color', 'axiscomposer' )
				)
			),
			'color' => array(
				'title'             => __( 'Custom Font Color', 'axiscomposer' ),
				'description'       => __( 'This sets the custom font color of the text.', 'axiscomposer' ),
				'type'              => 'color',
				'desc_tip'          => true,
				'default'           => ''
			)
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
			$class .= 'ac-inherit-color';
			$style .= empty( $color ) ? '' : 'color: ' . $color . ';';
		}

		if ( $style ) {
			$style = 'style="' . $style . '"';
		}

		$output .= '<section class="axiscomposer textblock-section">';
		$output .= '<div class="axiscomposer-textblock ' . $class . '" ' . $style . '>' . ac_apply_autop( ac_remove_autop( $content ) ) . '</div>';
		$output .= '</section>';

		return $output;
	}
}
