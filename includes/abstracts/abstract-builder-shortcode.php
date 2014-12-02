<?php
/**
 * Abstract Shortcode Class
 *
 * The AxisBuilder shortcode class handles individual shortcode data.
 *
 * @class       AB_Shortcode
 * @package     AxisBuilder/Abstracts
 * @category    Shortcodes
 * @author      AxisThemes
 * @since       1.0.0
 */
abstract class AB_Shortcode {

	/**
	 * Settings
	 * @var array
	 */
	public $config;

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		$this->init();
		$this->shortcode_button();
		$this->shortcode_config();
	}

	/**
	 * Initialize.
	 */
	public function init() {
		add_action( 'axisbuilder_display_shortcode_buttons', array( $this, 'shortcode_display' ) );
	}

	/**
	 * Creates the shortcode button.
	 */
	abstract function shortcode_button();

	/**
	 * Handles the shortcode frontend.
	 */
	// abstract function shortcode_handle( $atts, $content = '', $shortcodename = '', $meta = '' );

	/**
	 * Loads the shortcode extra assets.
	 */
	public function shortcode_assets() {}

	/**
	 * Visual appearance of an Element.
	 */
	public function shortcode_element( $params ) {
		$params['inner_html'] = '';
		if ( isset( $this->config['icon'] ) ) {
			$params['inner_html'] .= '<img src="' . $this->config['icon'] . '" title="' . $this->config['name'] . '" alt="" />';
		}
		$params['inner_html'] .= '<div class="axisbuilder-element-label">' . $this->config['name'] . '</div>';

		return $params;
	}

	/**
	 * Display Shortcodes.
	 */
	public function shortcode_display() {
		global $ab_shortcode;
		// $ab_shortcode = array();
		$ab_shortcode[] = $this->config;

		return (array) $ab_shortcode;
	}

	/**
	 * Auto-set shortcode configurations.
	 */
	protected function shortcode_config() {
		$this->config['php_class'] = get_class( $this );

		if ( empty( $this->config['drag-level'] ) ) {
			$this->config['drag-level'] = 10;
		}

		if ( empty( $this->config['drop-level'] ) ) {
			$this->config['drop-level'] = 10;
		}
	}
}
