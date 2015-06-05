<?php
/**
 * Special Heading Shortcode
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
 * AC_Shortcode_Heading Class
 */
class AC_Shortcode_Heading extends AC_Shortcode {

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
		$this->id        = 'heading';
		$this->title     = __( 'Special Heading', 'axiscomposer' );
		$this->method_description = __( 'Creates a Special Heading', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 80,
			'type'    => 'content',
			'name'    => 'ac_heading',
			'icon'    => 'icon-heading',
			'image'   => AC()->plugin_url() . '/assets/images/content/heading.png', // Fallback if icon is missing :)
			'target'  => 'ac-target-insert',
			'tinyMCE' => array( 'disable' => true ),
		);
	}

	/**
	 * Initialise Shortcode Settings Form Fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'text' => array(
				'title'             => __( 'Heading Text', 'axiscomposer' ),
				'description'       => __( 'This option lets you enter heading text.', 'axiscomposer' ),
				'default'           => __( 'Add your heading text here.', 'axiscomposer' ),
				'type'              => 'text',
				'desc_tip'          => true
			),
			'type' => array(
				'title'             => __( 'Heading Type', 'axiscomposer' ),
				'description'       => __( 'This sets the custom heading tag of the text.', 'axiscomposer' ),
				'default'           => 'H3',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'H1' => __( 'H1', 'axiscomposer' ),
					'H2' => __( 'H2', 'axiscomposer' ),
					'H3' => __( 'H3', 'axiscomposer' ),
					'H4' => __( 'H4', 'axiscomposer' ),
					'H5' => __( 'H5', 'axiscomposer' ),
					'H6' => __( 'H6', 'axiscomposer' )
				)
			),
			'size' => array(
				'title'             => __( 'Heading Font Size', 'axiscomposer' ),
				'description'       => __( 'This sets the custom font size of the heading text.', 'axiscomposer' ),
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
				'description'       => __( 'This sets the custom font color of the heading text.', 'axiscomposer' ),
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

	}
}
