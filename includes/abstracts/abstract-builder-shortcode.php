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
		$this->shortcode_button();
		$this->shortcode_config();
	}

	/**
	 * Abstract method for builder shortcode button configuration.
	 */
	abstract function shortcode_button();

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

		// Load popup when shortcode is clicked.
		if ( method_exists( $this, 'popup_elements' ) ) {
			$this->popup_elements();
			if ( ! empty( $this->settings ) ) {
				$this->shortcode['popup_editor'] = true;
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

		if ( isset( $args['content'] ) ) {
			unset( $args['content'] );
		}

		$params['args']    = $args;
		$params['content'] = $content;
		// $params['data']    = isset( $this->shortcode['modal_data'] ) ? $this->shortcode['modal_data'] : '';

		// Fetch the parameters array from the child classes visual_appearance which should describe the html code :)
		$params = self::visual_appearance( $params );


		$output = $params;

		// return $output;
	}

	public static function visual_appearance( $params ) {
		return $params;
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
