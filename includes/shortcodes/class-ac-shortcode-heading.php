<?php
/**
 * Special Heading Shortcode
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
 * AB_Shortcode_Heading Class
 */
class AB_Shortcode_Heading extends AB_Shortcode {

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
		$this->id        = 'axisbuilder_heading';
		$this->title     = __( 'Special Heading', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a Special Heading', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 80,
			'type'    => 'content',
			'name'    => 'ab_heading',
			'icon'    => 'icon-heading',
			'image'   => AB()->plugin_url() . '/assets/images/content/heading.png', // Fallback if icon is missing :)
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
			array(
				'name' => __( 'Heading Text', 'axisbuilder' ),
				'desc' => __( 'Enter the heading text', 'axisbuilder' ),
				'id'   => 'text',
				'type' => 'input',
				'std'  => __( 'Click here to add your heading text', 'axisbuilder' )
			),

			array(
				'name'  => __( 'Heading Type', 'axisbuilder' ),
				'desc'  => __( 'Choose the type of your heading', 'axisbuilder' ),
				'id'    => 'type',
				'type'  => 'select',
				'std'   => 'H3',
				'subtype'	=> array(
					__( 'H1', 'axisbuilder' ) => 'H1',
					__( 'H2', 'axisbuilder' ) => 'H2',
					__( 'H3', 'axisbuilder' ) => 'H3',
					__( 'H4', 'axisbuilder' ) => 'H4',
					__( 'H5', 'axisbuilder' ) => 'H5',
					__( 'H6', 'axisbuilder' ) => 'H6',
				)
			),

			array(
				'name'    => __( 'Heading Font Size', 'axisbuilder' ),
				'desc'    => __( 'Choose the font size of the heading in px', 'axisbuilder' ),
				'type'    => 'number',
				'id'      => 'size',
				'min'     => '10',
				'max'     => '40',
				'std'     => ''
			),

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
