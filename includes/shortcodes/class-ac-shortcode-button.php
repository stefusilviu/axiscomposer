<?php
/**
 * Button Shortcode
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
 * AC_Shortcode_Button Class
 */
class AC_Shortcode_Button extends AC_Shortcode {

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
		$this->id        = 'button';
		$this->title     = __( 'Button', 'axiscomposer' );
		$this->tooltip   = __( 'Creates a colored button', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 120,
			'type'    => 'content',
			'name'    => 'ac_button',
			'icon'    => 'icon-button',
			'image'   => AC()->plugin_url() . '/assets/images/content/button.png', // Fallback if icon is missing :)
			'target'  => 'ac-target-insert',
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
				'name'		=> __( 'Button Label', 'axiscomposer' ),
				'desc'		=> __( 'Enter the text for button', 'axiscomposer' ),
				'id'		=> 'label',
				'type'		=> 'input',
				'std'		=> __( 'Click here to add your button label', 'axiscomposer' )
			),

			array(
				'name'		=> __( 'Button Link', 'axiscomposer' ),
				'desc'		=> __( 'Enter the button link', 'axiscomposer' ),
				'id'		=> 'link',
				'type'		=> 'input',
				'std'		=> ''
			),

			array(
				'name'		=> __( 'Button Size', 'axiscomposer' ),
				'desc'		=> __( 'Choose the size of your button', 'axiscomposer' ),
				'id'		=> 'size',
				'type'		=> 'select',
				'std'		=> 'medium',
				'subtype'	=> array(
					__( 'Small', 'axiscomposer' )	=> 'small',
					__( 'Medium', 'axiscomposer' )	=> 'medium',
					__( 'Large', 'axiscomposer' )	=> 'large',
				)
			),

			array(
				'name'		=> __( 'Button Position', 'axiscomposer' ),
				'desc'		=> __( 'Choose alignment of your button', 'axiscomposer' ),
				'id'		=> 'position',
				'type'		=> 'select',
				'std'		=> 'center',
				'subtype'	=> array(
					__( 'Left Align', 'axiscomposer' )	=> 'left',
					__( 'Center Align', 'axiscomposer' )	=> 'center',
					__( 'Right Align', 'axiscomposer' )	=> 'right',
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
