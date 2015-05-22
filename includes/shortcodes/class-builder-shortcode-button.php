<?php
/**
 * Button Shortcode
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
 * AB_Shortcode_Button Class
 */
class AB_Shortcode_Button extends AB_Shortcode {

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
		$this->id        = 'axisbuilder_button';
		$this->title     = __( 'Button', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a colored button', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 120,
			'type'    => 'content',
			'name'    => 'ab_button',
			'icon'    => 'icon-button',
			'image'   => AB()->plugin_url() . '/assets/images/content/button.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinyMCE' => array( 'disable' => false ),
		);
	}

	/**
	 * Get Settings.
	 * @return array
	 */
	public function get_settings() {

		$this->elements = array(
			array(
				'name'		=> __( 'Button Label', 'axisbuilder' ),
				'desc'		=> __( 'Enter the text for button', 'axisbuilder' ),
				'id'		=> 'label',
				'type'		=> 'input',
				'std'		=> __( 'Click here to add your button label', 'axisbuilder' )
			),

			array(
				'name'		=> __( 'Button Link', 'axisbuilder' ),
				'desc'		=> __( 'Enter the button link', 'axisbuilder' ),
				'id'		=> 'link',
				'type'		=> 'input',
				'std'		=> ''
			),

			array(
				'name'		=> __( 'Button Size', 'axisbuilder' ),
				'desc'		=> __( 'Choose the size of your button', 'axisbuilder' ),
				'id'		=> 'size',
				'type'		=> 'select',
				'std'		=> 'medium',
				'subtype'	=> array(
					__( 'Small', 'axisbuilder' )	=> 'small',
					__( 'Medium', 'axisbuilder' )	=> 'medium',
					__( 'Large', 'axisbuilder' )	=> 'large',
				)
			),

			array(
				'name'		=> __( 'Button Position', 'axisbuilder' ),
				'desc'		=> __( 'Choose alignment of your button', 'axisbuilder' ),
				'id'		=> 'position',
				'type'		=> 'select',
				'std'		=> 'center',
				'subtype'	=> array(
					__( 'Left Align', 'axisbuilder' )	=> 'left',
					__( 'Center Align', 'axisbuilder' )	=> 'center',
					__( 'Right Align', 'axisbuilder' )	=> 'right',
				)
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
