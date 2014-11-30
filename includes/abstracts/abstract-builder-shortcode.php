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
	public $settings;

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {

		$this->shortcode_button();

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
	 *
	 */
	public function shortcode_element( $params ) {
		$params['inner_html'] = '';
		if ( isset( $this->settings['icon'] ) ) {
			$params['inner_html'] .= '<img src="' . $this->settings['icon'] . '" title="' . $this->settings['name'] . '" alt="" />';
		}
		$params['inner_html'] .= '<div class="axisbuilder-element-label">' . $this->settings['name'] . '</div>';

		return $params;
	}

}
