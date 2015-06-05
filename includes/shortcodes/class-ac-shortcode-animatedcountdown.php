<?php
/**
 * Animated Countdown Shortcode
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
 * AC_Shortcode_Animatedcountdown Class
 */
class AC_Shortcode_Animatedcountdown extends AC_Shortcode {

	protected $time_config;

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
		$this->id        = 'animatedcountdown';
		$this->title     = __( 'Animated Countdown', 'axiscomposer' );
		$this->method_description = __( 'Display an count down to a specific date', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 210,
			'type'    => 'content',
			'name'    => 'ac_animatedcountdown',
			'icon'    => 'icon-animatedcountdown',
			'image'   => AC()->plugin_url() . '/assets/images/content/animatedcountdown.png', // Fallback if icon is missing :)
			'target'  => 'ac-target-insert',
			'tinyMCE' => array( 'disable' => true ),
		);

		// Time config
		$this->time_config = array(
			'1' => __( 'Second',   'axiscomposer' ),
			'2' => __( 'Minute',   'axiscomposer' ),
			'3' => __( 'Hour',     'axiscomposer' ),
			'4' => __( 'Day',      'axiscomposer' ),
			'5' => __( 'Week',     'axiscomposer' ),
			'6' => __( 'Month',    'axiscomposer' ),
			'7' => __( 'Year',     'axiscomposer' )
		);
	}

	/**
	 * Initialise Shortcode Settings Form Fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'hour' => array(
				'title'             => __( 'Hour', 'axiscomposer' ),
				'description'       => __( 'This option lets you pick the hour of the day. (24 is 0)', 'axiscomposer' ),
				'type'              => 'number',
				'desc_tip'          => true,
				'default'           => 12,
				'custom_attributes' => array(
					'min' => 1,
					'max' => 24
				)
			),
			'minute' => array(
				'title'             => __( 'Minute', 'axiscomposer' ),
				'description'       => __( 'This option lets you pick the minute of the hour. (60 is 0)', 'axiscomposer' ),
				'type'              => 'number',
				'desc_tip'          => true,
				'default'           => 60,
				'custom_attributes' => array(
					'min' => 1,
					'max' => 60
				)
			),
			'min' => array(
				'title'             => __( 'Smallest time unit', 'axiscomposer' ),
				'description'       => __( 'This sets the smallest unit that will be displayed.', 'axiscomposer' ),
				'default'           => '1',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => $this->time_config
			),
			'max' => array(
				'title'             => __( 'Largest time unit', 'axiscomposer' ),
				'description'       => __( 'This sets the largest unit that will be displayed.', 'axiscomposer' ),
				'default'           => '5',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => $this->time_config
			),
			'align' => array(
				'title'             => __( 'Text Alignment', 'axiscomposer' ),
				'description'       => __( 'This sets the custom alignment of the text.', 'axiscomposer' ),
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
			),
			'size' => array(
				'title'             => __( 'Number Font Size', 'axiscomposer' ),
				'description'       => __( 'This sets the custom font size of the number.', 'axiscomposer' ),
				'type'              => 'number',
				'desc_tip'          => true,
				'default'           => 24,
				'custom_attributes' => array(
					'min' => 20,
					'max' => 90
				)
			),
			'style' => array(
				'title'             => __( 'Color Scheme', 'axiscomposer' ),
				'description'       => __( 'This sets lets you set custom color scheme.', 'axiscomposer' ),
				'default'           => 'default',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'default'            => __( 'Theme Default', 'axiscomposer' ),
					'transparent-dark'   => __( 'Transparent Dark', 'axiscomposer' ),
					'transparent-light'  => __( 'Transparent Light', 'axiscomposer' )
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
