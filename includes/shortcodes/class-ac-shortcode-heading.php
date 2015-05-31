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
		$this->tooltip   = __( 'Creates a Special Heading', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 80,
			'type'    => 'content',
			'name'    => 'ac_heading',
			'icon'    => 'icon-heading',
			'image'   => AC()->plugin_url() . '/assets/images/content/heading.png', // Fallback if icon is missing :)
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
				'name' => __( 'Heading Text', 'axiscomposer' ),
				'desc' => __( 'Enter the heading text', 'axiscomposer' ),
				'id'   => 'text',
				'type' => 'input',
				'std'  => __( 'Click here to add your heading text', 'axiscomposer' )
			),

			array(
				'name'  => __( 'Heading Type', 'axiscomposer' ),
				'desc'  => __( 'Choose the type of your heading', 'axiscomposer' ),
				'id'    => 'type',
				'type'  => 'select',
				'std'   => 'H3',
				'subtype'	=> array(
					__( 'H1', 'axiscomposer' ) => 'H1',
					__( 'H2', 'axiscomposer' ) => 'H2',
					__( 'H3', 'axiscomposer' ) => 'H3',
					__( 'H4', 'axiscomposer' ) => 'H4',
					__( 'H5', 'axiscomposer' ) => 'H5',
					__( 'H6', 'axiscomposer' ) => 'H6',
				)
			),

			array(
				'name'    => __( 'Heading Font Size', 'axiscomposer' ),
				'desc'    => __( 'Choose the font size of the heading in px', 'axiscomposer' ),
				'type'    => 'number',
				'id'      => 'size',
				'min'     => '10',
				'max'     => '40',
				'std'     => ''
			),

			array(
				'name'    => __( 'Font Colors', 'axiscomposer' ),
				'desc'    => __( 'Either use the themes default colors or apply some custom ones', 'axiscomposer' ),
				'id'      => 'font_color',
				'std'     => '',
				'type'    => 'select',
				'subtype' => array(
					__( 'Default', 'axiscomposer' ) => 'default',
					__( 'Define Custom Colors', 'axiscomposer' ) => 'custom'
				)
			),

			array(
				'name'     => __( 'Custom Font Color', 'axiscomposer' ),
				'desc'     => __( 'Select a custom font color. Leave empty to use the default', 'axiscomposer' ),
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
