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
	 * Shortcode ID
	 * @var string
	 */
	public $id;

	/**
	 * Shortcode Title
	 * @var string
	 */
	public $title;

	/**
	 * Shortcode Tooltip
	 * @var string
	 */
	public $tooltip;

	/**
	 * Shortcode Configs
	 * @var array
	 */
	public $shortcode;

	/**
	 * Shortcode Settings
	 * @var array
	 */
	public $settings;

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		$this->shortcode_config();
	}

	/**
	 * Auto-set shortcode configurations.
	 */
	protected function shortcode_config() {
		$this->shortcode['php_class'] = get_class( $this );

		if ( empty( $this->shortcode['class'] ) ) {
			$this->shortcode['class'] = '';
		}

		if ( empty( $this->shortcode['target'] ) ) {
			$this->shortcode['target'] = '';
		}

		if ( empty( $this->shortcode['drag-level'] ) ) {
			$this->shortcode['drag-level'] = 10;
		}

		if ( empty( $this->shortcode['drop-level'] ) ) {
			$this->shortcode['drop-level'] = 10;
		}
	}
}
