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
	 * @var array
	 */
	public $id;

	/**
	 * Shortcode Title
	 * @var array
	 */
	public $title;

	/**
	 * Shortcode Tooltip
	 * @var array
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
	 * Loads shortcode tabs contents.
	 */
	public function shortcode_loader( $type = 'plugin' ) {
		return 'This section will now load shortcode buttons :)';
	}

	/**
	 * Auto-set shortcode configurations.
	 */
	protected function shortcode_config() {
		$this->shortcode['php_class'] = get_class( $this );

		if ( empty( $this->shortcode['drag-level'] ) ) {
			$this->shortcode['drag-level'] = 10;
		}

		if ( empty( $this->shortcode['drop-level'] ) ) {
			$this->shortcode['drop-level'] = 10;
		}
	}
}
