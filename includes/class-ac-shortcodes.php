<?php
/**
 * AxisComposer Shortcodes class
 *
 * Handles shortcode and loads shortcode methods via hooks.
 *
 * @class       AC_Shortcodes
 * @package     AxisComposer/Classes/Shortcode
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Shortcodes Class
 */
class AC_Shortcodes {

	/** @var array Array of shortcode classes */
	public $shortcodes;

	/**
	 * @var AC_Shortcodes The single instance of the class
	 * @since 1.0
	 */
	protected static $_instance = null;

	/**
	 * Main AC_Shortcodes Instance
	 *
	 * Ensures only one instance of AC_Shortcodes is loaded or can be loaded.
	 *
	 * @static
	 * @return AC_Shortcodes - Main instance
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
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'axiscomposer' ), '1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 * @since 1.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'axiscomposer' ), '1.0' );
	}

	/**
	 * Initialize shortcodes.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Load shortcodes and hook in functions.
	 */
	public function init() {
		$load_shortcodes = array(

			// Layout Elements
			'AC_Shortcode_Section',
			'AC_Shortcode_Grid_Row',

			'AC_Shortcode_Columns',
			'AC_Shortcode_Columns_One_Half',
			'AC_Shortcode_Columns_One_Third',
			'AC_Shortcode_Columns_Two_Third',
			'AC_Shortcode_Columns_One_Fourth',
			'AC_Shortcode_Columns_Three_Fourth',
			'AC_Shortcode_Columns_One_Fifth',
			'AC_Shortcode_Columns_Two_Fifth',
			'AC_Shortcode_Columns_Three_Fifth',
			'AC_Shortcode_Columns_Four_Fifth',

			'AC_Shortcode_Cells',
			'AC_Shortcode_Cells_One_Half',
			'AC_Shortcode_Cells_One_Third',
			'AC_Shortcode_Cells_Two_Third',
			'AC_Shortcode_Cells_One_Fourth',
			'AC_Shortcode_Cells_Three_Fourth',
			'AC_Shortcode_Cells_One_Fifth',
			'AC_Shortcode_Cells_Two_Fifth',
			'AC_Shortcode_Cells_Three_Fifth',
			'AC_Shortcode_Cells_Four_Fifth',

			// Content Elements
			'AC_Shortcode_Gist',
			'AC_Shortcode_Textblock',
			'AC_Shortcode_Heading',
			'AC_Shortcode_Button',
			'AC_Shortcode_Sidebar',
			'AC_Shortcode_Calltoaction',
			'AC_Shortcode_Iconbox',
			'AC_Shortcode_Notification',
			'AC_Shortcode_Progressbar',
			'AC_Shortcode_Separator',
			'AC_Shortcode_Accordion',
			'AC_Shortcode_Animatednumbers',
			'AC_Shortcode_Tabs',
			'AC_Shortcode_Animatedcountdown',
			'AC_Shortcode_Comments',
			'AC_Shortcode_Teammembers',
			'AC_Shortcode_Catalogue',
			'AC_Shortcode_Contactform',

			// Media Elements
			'AC_Shortcode_Image',
			'AC_Shortcode_Video',
			'AC_Shortcode_Logoelement',
			'AC_Shortcode_Googlemap',

			// Plugin Additions
			'AC_Shortcode_Productslider',
			'AC_Shortcode_Productgrid',
			'AC_Shortcode_Productlist',
		);

		// Filter
		$load_shortcodes = apply_filters( 'axiscomposer_shortcodes', $load_shortcodes );

		// Get sort order End
		$order_end = 999;

		// Load shortcodes in order
		foreach ( $load_shortcodes as $shortcode ) {
			$load_shortcode = is_string( $shortcode ) ? new $shortcode() : $shortcode;

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
	 * Get TinyMCE shortcodes.
	 * @return array
	 */
	public function get_mce_shortcodes() {
		$_available_shortcodes = array();

		if ( sizeof( $this->shortcodes ) > 0 ) {
			foreach ( $this->shortcodes as $load_shortcodes ) {
				if ( empty( $load_shortcodes->shortcode['tinyMCE']['disable'] ) ) {
					$_available_shortcodes[ $load_shortcodes->shortcode['name'] ]['title']   = $load_shortcodes->method_title;
					$_available_shortcodes[ $load_shortcodes->shortcode['name'] ]['type']    = $load_shortcodes->shortcode['type'];
					$_available_shortcodes[ $load_shortcodes->shortcode['name'] ]['tinyMCE'] = $load_shortcodes->shortcode['tinyMCE'];
				}
			}
		}

		return $_available_shortcodes;
	}

	/**
	 * Get Editor Elements.
	 * @return array
	 */
	public function get_editor_element( $content, $args ) {
		$_available_shortcodes = array();

		if ( sizeof( $this->shortcodes ) > 0 ) {
			foreach ( $this->shortcodes as $load_shortcodes ) {
				$_available_shortcodes[ $load_shortcodes->shortcode['name'] ] = $load_shortcodes->prepare_editor_element( $content, $args );
			}
		}

		return $_available_shortcodes;
	}
}
