<?php
/**
 * Animated Countdown Shortcode
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
 * AB_Shortcode_Animatedcountdown Class
 */
class AB_Shortcode_Animatedcountdown extends AB_Shortcode {

	protected $time_config;

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
		$this->id        = 'axisbuilder_animatedcountdown';
		$this->title     = __( 'Animated Countdown', 'axisbuilder' );
		$this->tooltip   = __( 'Display an count down to a specific date', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 210,
			'type'    => 'content',
			'name'    => 'ab_animatedcountdown',
			'icon'    => 'icon-animatedcountdown',
			'image'   => AB()->plugin_url() . '/assets/images/content/animatedcountdown.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinyMCE' => array( 'disable' => true ),
		);

		// Optional
		$this->time_config = array(
			__('Second',   'axisbuilder' ) =>'1',
			__('Minute',   'axisbuilder' ) =>'2',
			__('Hour',     'axisbuilder' ) =>'3',
			__('Day',      'axisbuilder' ) =>'4',
			__('Week',     'axisbuilder' ) =>'5',
			// __('Month', 'axisbuilder' ) =>'6',
			// __('Year',  'axisbuilder' ) =>'7'
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

			// array(
			// 	'name'     => __( 'Date', 'axisbuilder' ),
			// 	'desc'     => __( 'Pick a date in the future', 'axisbuilder' ),
			// 	'id'       => 'date',
			// 	'std'      => '',
			// 	'type'     => 'datepicker',
			// 	'container_class' => 'ab_third ab_third_first'
			// ),

			array(
				'name'     => __( 'Hour', 'axisbuilder' ),
				'desc'     => __( 'Pick the hour of the day', 'axisbuilder' ),
				'id'       => 'hour',
				'std'      => '12',
				'type'     => 'select',
				'container_class' => 'ab_third',
				'subtype'  => axisbuilder_num_to_array( 0, 23, 1, array(), ' h' )
			),

			array(
				'name'     => __( 'Minute', 'axisbuilder' ),
				'desc'     => __( 'Pick the minute of the hour', 'axisbuilder' ),
				'id'       => 'minute',
				'std'      => '0',
				'type'     => 'select',
				'container_class' => 'ab_third',
				'subtype'  => axisbuilder_num_to_array( 0, 23, 1, array(), ' h' )
			),

			array(
				'name'     => __( 'Smallest time unit', 'axisbuilder' ),
				'desc'     => __( 'The smallest unit that will be displayed', 'axisbuilder' ),
				'id'       => 'min',
				'std'      => '1',
				'type'     => 'select',
				'subtype'  => $this->time_config
			),

			array(
				'name'     => __( 'Largest time unit', 'axisbuilder' ),
				'desc'     => __( 'The largest unit that will be displayed', 'axisbuilder' ),
				'id'       => 'max',
				'std'      => '5',
				'type'     => 'select',
				'subtype'  => $this->time_config
			),

			array(
				'name'     => __( 'Text Alignment', 'axisbuilder' ),
				'desc'     => __( 'Choose here, how to align your text', 'axisbuilder' ),
				'id'       => 'align',
				'std'      => 'center',
				'type'     => 'select',
				'subtype'  => array(
					__( 'Left', 'axisbuilder' )   => 'left',
					__( 'Right', 'axisbuilder' )  => 'right',
					__( 'Center', 'axisbuilder' ) => 'center'
				)
			),

			array(
				'name'     => __( 'Number Font Size', 'axisbuilder' ),
				'desc'     => __( 'Size of your numbers in Pixel', 'axisbuilder' ),
				'id'       => 'size',
				'std'      => '',
				'type'     => 'select',
				'subtype'  => axisbuilder_num_to_array( 20, 90, 1, array( __( 'Default Size', 'axisbuilder' ) => '' ) )
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
				'name'     => __( 'Colors', 'axisbuilder' ),
				'desc'     => __( 'Choose the colors here', 'axisbuilder' ),
				'id'       => 'style',
				'std'      => 'default',
				'type'     => 'select',
				'subtype'  => array(
					__( 'Theme Default', 'axisbuilder' )     => 'default',
					__( 'Transparent Dark', 'axisbuilder' )  => 'transparent-dark',
					__( 'Transparent Light', 'axisbuilder' ) => 'transparent-light',
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
