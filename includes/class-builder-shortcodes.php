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

			// Layout Elements
			'AB_Shortcode_Section',
			'AB_Shortcode_Grid_Row',

			'AB_Shortcode_Columns',
			'AB_Shortcode_Columns_One_Half',
			'AB_Shortcode_Columns_One_Third',
			'AB_Shortcode_Columns_Two_Third',
			'AB_Shortcode_Columns_One_Fourth',
			'AB_Shortcode_Columns_Three_Fourth',
			'AB_Shortcode_Columns_One_Fifth',
			'AB_Shortcode_Columns_Two_Fifth',
			'AB_Shortcode_Columns_Three_Fifth',
			'AB_Shortcode_Columns_Four_Fifth',

			'AB_Shortcode_Cells',
			'AB_Shortcode_Cells_One_Half',
			'AB_Shortcode_Cells_One_Third',
			'AB_Shortcode_Cells_Two_Third',
			'AB_Shortcode_Cells_One_Fourth',
			'AB_Shortcode_Cells_Three_Fourth',
			'AB_Shortcode_Cells_One_Fifth',
			'AB_Shortcode_Cells_Two_Fifth',
			'AB_Shortcode_Cells_Three_Fifth',
			'AB_Shortcode_Cells_Four_Fifth',

			// Content Elements
			'AB_Shortcode_Textblock',
			'AB_Shortcode_Heading',
			'AB_Shortcode_Button',
			'AB_Shortcode_Sidebar',
			'AB_Shortcode_Codeblock',
			'AB_Shortcode_Calltoaction',
			'AB_Shortcode_Iconbox',
			'AB_Shortcode_Notification',

			// Media Elements
			'AB_Shortcode_Image',
			'AB_Shortcode_Video',
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
	 * Get Editor Elements.
	 * @return array
	 */
	public function get_editor_element( $content, $args ) {
		$_available_shortcodes = array();

		if ( sizeof( $this->shortcodes ) > 0 ) {
			foreach ( $this->shortcodes as $shortcode ) {
				$_available_shortcodes[ $shortcode->shortcode['name'] ] = $shortcode->prepare_editor_element( $content, $args );
			}
		}

		return $_available_shortcodes;
	}
}
