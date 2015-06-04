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
			'label' => array(
				'title'             => __( 'Button Label', 'axiscomposer' ),
				'description'       => __( 'This option lets you define button label.', 'axiscomposer' ),
				'default'           => __( 'Add your button label here.', 'axiscomposer' ),
				'type'              => 'text',
				'desc_tip'          => true
			),
			'link' => array(
				'title'             => __( 'Button Link', 'axiscomposer' ),
				'description'       => __( 'This option lets you enter button link.', 'axiscomposer' ),
				'type'              => 'text',
				'desc_tip'          => true,
				'default'           => ''
			),
			'size' => array(
				'title'             => __( 'Button Size', 'axiscomposer' ),
				'description'       => __( 'This sets the custom size of the button.', 'axiscomposer' ),
				'default'           => 'medium',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'small'  => __( 'Small', 'axiscomposer' ),
					'medium' => __( 'Medium', 'axiscomposer' ),
					'large'  => __( 'Large', 'axiscomposer' )
				)
			),
			'position' => array(
				'title'             => __( 'Button Position', 'axiscomposer' ),
				'description'       => __( 'This sets the custom alignment of the button.', 'axiscomposer' ),
				'default'           => 'center',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'left'   => __( 'Left Align', 'axiscomposer' ),
					'center' => __( 'Center Align', 'axiscomposer' ),
					'right'  => __( 'Right Align', 'axiscomposer' )
				)
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
