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
		$this->id        = 'axisbuilder_animatedcountdown';
		$this->title     = __( 'Animated Countdown', 'axiscomposer' );
		$this->tooltip   = __( 'Display an count down to a specific date', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 210,
			'type'    => 'content',
			'name'    => 'ac_animatedcountdown',
			'icon'    => 'icon-animatedcountdown',
			'image'   => AC()->plugin_url() . '/assets/images/content/animatedcountdown.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinyMCE' => array( 'disable' => true ),
		);

		// Optional
		$this->time_config = array(
			__('Second',   'axiscomposer' ) =>'1',
			__('Minute',   'axiscomposer' ) =>'2',
			__('Hour',     'axiscomposer' ) =>'3',
			__('Day',      'axiscomposer' ) =>'4',
			__('Week',     'axiscomposer' ) =>'5',
			// __('Month', 'axiscomposer' ) =>'6',
			// __('Year',  'axiscomposer' ) =>'7'
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
			// 	'name'   => __( 'Content', 'axiscomposer' ),
			// 	'type'   => 'tab',
			// 	'nodesc' => true
			// ),

			// array(
			// 	'name'     => __( 'Date', 'axiscomposer' ),
			// 	'desc'     => __( 'Pick a date in the future', 'axiscomposer' ),
			// 	'id'       => 'date',
			// 	'std'      => '',
			// 	'type'     => 'datepicker',
			// 	'container_class' => 'ac_third ac_third_first'
			// ),

			array(
				'name'    => __( 'Hour', 'axiscomposer' ),
				'desc'    => __( 'Pick the hour of the day', 'axiscomposer' ),
				'type'    => 'number',
				'id'      => 'hour',
				'min'     => '1',
				'max'     => '23',
				'std'     => '12'
			),

			array(
				'name'    => __( 'Minute', 'axiscomposer' ),
				'desc'    => __( 'Pick the minute of the hour', 'axiscomposer' ),
				'type'    => 'number',
				'id'      => 'minute',
				'min'     => '0',
				'max'     => '59',
				'std'     => '0'
			),

			array(
				'name'     => __( 'Smallest time unit', 'axiscomposer' ),
				'desc'     => __( 'The smallest unit that will be displayed', 'axiscomposer' ),
				'id'       => 'min',
				'std'      => '1',
				'type'     => 'select',
				'subtype'  => $this->time_config
			),

			array(
				'name'     => __( 'Largest time unit', 'axiscomposer' ),
				'desc'     => __( 'The largest unit that will be displayed', 'axiscomposer' ),
				'id'       => 'max',
				'std'      => '5',
				'type'     => 'select',
				'subtype'  => $this->time_config
			),

			array(
				'name'     => __( 'Text Alignment', 'axiscomposer' ),
				'desc'     => __( 'Choose here, how to align your text', 'axiscomposer' ),
				'id'       => 'align',
				'std'      => 'center',
				'type'     => 'select',
				'subtype'  => array(
					__( 'Left', 'axiscomposer' )   => 'left',
					__( 'Right', 'axiscomposer' )  => 'right',
					__( 'Center', 'axiscomposer' ) => 'center'
				)
			),

			array(
				'name'    => __( 'Number Font Size', 'axiscomposer' ),
				'desc'    => __( 'Choose Size of the number in px', 'axiscomposer' ),
				'type'    => 'number',
				'id'      => 'size',
				'min'     => '20',
				'max'     => '90',
				'std'     => '24'
			),

			// array(
			// 	'type'   => 'close_div',
			// 	'nodesc' => true
			// ),

			// array(
			// 	'name'   => __( 'Colors', 'axiscomposer' ),
			// 	'type'   => 'tab',
			// 	'nodesc' => true
			// ),

			array(
				'name'     => __( 'Colors', 'axiscomposer' ),
				'desc'     => __( 'Choose the colors here', 'axiscomposer' ),
				'id'       => 'style',
				'std'      => 'default',
				'type'     => 'select',
				'subtype'  => array(
					__( 'Theme Default', 'axiscomposer' )     => 'default',
					__( 'Transparent Dark', 'axiscomposer' )  => 'transparent-dark',
					__( 'Transparent Light', 'axiscomposer' ) => 'transparent-light',
				)
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
