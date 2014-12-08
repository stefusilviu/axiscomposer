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
	 * @todo Remove
	 */
	public $config;

	/**
	 * Shortcode Name
	 * @var string
	 */
	public $shortcode_name;

	/**
	 * Shortcode Tooltip Description
	 * @var string
	 */
	public $shortcode_desc;

	/**
	 * Shortcode Data Configurations
	 * @var array
	 */
	public $shortcode_data;

	/**
	 * Shortcode Popup Settings
	 * @var array
	 */
	public $popup_settings;

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		$this->shortcode_tabs();

		$this->shortcode_button();
		$this->shortcode_config();
		$this->shortcode_display();
	}

	/**
	 * Loads shortcode tabs contents.
	 */
	public function shortcode_tabs() {
		$load_shortcode_tabs = get_builder_core_shortcode_tabs();

		foreach ( $this->shortcode_data as $shortcode ) {
			print '<pre>';
			print_r( $shortcode );
			print '</pre>';
		}
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
		global $axisbuilder_shortcodes;
		// $axisbuilder_shortcodes = array();
		$axisbuilder_shortcodes[] = $this->config;

		return (array) $axisbuilder_shortcodes;
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

		// If we got elements for the popup editor activate it
		if ( method_exists( $this, 'popup_elements' ) ) {

			// Load via the shortcodes function
			$this->popup_elements();

			if ( ! empty( $this->elements ) ) {
				global $axisbuilder_elements;
				$axisbuilder_elements = $this->elements;

				$this->config['popup_editor'] = true;

				$this->extra_config_element_iterator( $this->elements );

				// Remove any duplicate values
				if ( ! empty( $this->config['modal_on_load'] ) ) {
					$this->config['modal_on_load'] = array_unique( $this->config['modal_on_load'] );
				}
			}
		}
	}

	/**
	 * Helper function to iterate recursively over element and subelement trees.
	 */
	protected function extra_config_element_iterator( $elements ) {
		foreach ( $elements as $element ) {
			switch ( $element['type'] ) {
				case 'multi_input':
					$this->config['modal_on_load'][] = 'modal_load_multi_input';
				break;

				case 'tab_container':
					$this->config['modal_on_load'][] = 'modal_load_tabs';
				break;

				case 'tiny_mce':
					$this->config['modal_on_load'][] = 'modal_load_tiny_mce';
				break;

				case 'colorpicker':
					$this->config['modal_on_load'][] = 'modal_load_colorpicker';
				break;

				case 'datepicker':
					$this->config['modal_on_load'][] = 'modal_load_datepicker';
				break;

				case 'table':
					$this->config['modal_on_load'][] = 'modal_load_tablebuilder';
				break;

				case 'modal_group':
					$this->config['modal_on_load'][] = 'modal_start_sorting';
					$this->config['modal_on_load'][] = 'modal_tab_functions';
					$this->config['modal_on_load'][] = 'modal_hotspot_helper';
					$this->extra_config_element_iterator( $element['subelements'] );
				break;
			}
		}
	}
}
