<?php
/**
 * AxisBuilder Shortcodes class
 *
 * Loads shortcodes via hooks for use in the theme.
 *
 * @class       AB_Shortcodes
 * @package     AxisBuilder/Classes/Shortcode
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */
class AB_Shortcodes {

	 /** @var array Array of shortcode classes */
	public $shortcodes;

	/**
	 * @var AB_Shortcodes The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main AB_Shortcodes Instance
	 *
	 * Ensures only one instance of AB_Shortcodes is loaded or can be loaded.
	 *
	 * @static
	 * @return AB_Shortcodes - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 * @since 1.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'axisbuilder' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'axisbuilder' ), '1.0' );
	}

	/**
	 * __construct function.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Load shortcodes and hook in functions.
	 */
	public function init() {
		$load_shortcodes = array(
			'AB_Shortcode_Columns',
			'AB_Shortcode_Codeblock'
		);

		// Filter
		$load_shortcodes = apply_filters( 'axisbuilder_shortcodes', $load_shortcodes );

		// Get sort order End
		$order_end = 999;

		// Load shortcodes in order
		foreach ( $load_shortcodes as $shortcode ) {
			$load_shortcode = new $shortcode();

			if ( isset( $load_shortcode->shortcode['sort'] ) && is_numeric( $load_shortcode->shortcode['sort'] ) ) {
				// Add in position
				$this->shortcodes[ $load_shortcode->shortcode['sort'] ] = $load_shortcode;
			} else {
				// Add to end of the array
				$this->shortcodes[ $order_end ] = $load_shortcode;
				$order_end++;
			}
		}

		ksort( $this->shortcodes );
	}

	/**
	 * Get shortcodes.
	 * @return array
	 */
	public function get_shortcodes() {
		$_available_shortcodes = array();

		if ( sizeof( $this->shortcodes ) > 0 ) {
			foreach ( $this->shortcodes as $shortcode ) {
				$_available_shortcodes[ $shortcode->id ] = $shortcode;
			}
		}

		return $_available_shortcodes;
	}

	/**
	 * Get shortcode Buttons.
	 * @return string
	 */
	public function load_shortcode_buttons( $type = 'plugin' ) {
		return $type;
	}
}
