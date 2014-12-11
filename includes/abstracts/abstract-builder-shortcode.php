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
		$load_shortcode_data = array(
			'class'      => '',
			'target'     => '',
			'drag-level' => 2,
			'drop-level' => 2,
			'href-class' => get_class( $this )
		);

		foreach ( $load_shortcode_data as $key => $data ) {
			if ( empty( $this->shortcode[$key] ) ) {
				$this->shortcode[$key] = $data;
			}
		}
	}

	/**
	 * Add-on for custom CSS class to each element.
	 */
	public function custom_css( $elements ) {
		$elements[] = array(
			'id'   => 'custom_class',
			'name' => __( 'custom CSS Class', 'axisbuilder' ),
			'desc' => __( 'Add a custom css class for the element here. Ensure the use of allowed characters (latin characters, underscores, dashes and numbers)', 'axisbuilder' ),
			'type' => 'input',
			'std'  => ''
		);

		return $elements;
	}

	/**
	 * Render shortcode canvas elements.
	 */
	public static function shortcode_canvas( $content = false, $args = array() ) {

		// Set default content unless it was already passed
		if ( $content === false ) {
			$content = self::fetch_default_content( $content );
		}

		// Set default arguments unless it was already passed
		if ( empty( $args ) ) {
			$args = self::fetch_default_args( $args );
		}
	}

	/**
	 * Fetch default content
	 */
	public static function fetch_default_content() {

	}

	/**
	 * Fetch default args
	 */
	public static function fetch_default_args() {

	}
}
