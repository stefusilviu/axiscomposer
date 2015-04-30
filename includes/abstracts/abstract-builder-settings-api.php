<?php
/**
 * Abstract Settings API Class
 *
 * Admin Settings API used by Shortcodes.
 *
 * @class       AB_Settings_API
 * @package     AxisBuilder/Abstracts
 * @category    Abstract Class
 * @author      AxisThemes
 * @since       1.0.0
 */
abstract class AB_Settings_API {

	/**
	 * The plugin ID. Used for option names.
	 * @var string
	 */
	public $plugin_id = 'axisbuilder_';

	/**
	 * Method ID.
	 * @var string
	 */
	public $id = '';

	/**
	 * Method title.
	 * @var string
	 */
	public $method_title = '';

	/**
	 * Method description.
	 * @var string
	 */
	public $method_description = '';

	/**
	 * 'yes' if the method is enabled
	 * @var string
	 */
	public $enabled;

	/**
	 * Setting values.
	 * @var array
	 */
	public $settings = array();

	/**
	 * Form option fields.
	 * @var array
	 */
	public $form_fields = array();

	/**
	 * Validation errors.
	 * @var array
	 */
	public $errors = array();

	/**
	 * Sanitized fields after validation.
	 * @var array
	 */
	public $sanitized_fields = array();

	/**
	 * Admin Options
	 *
	 * Setup the gateway settings screen.
	 * Override this in your gateway.
	 *
	 * @since 1.0.0
	 */
	public function admin_options() { ?>

		<h3><?php echo ( ! empty( $this->method_title ) ) ? $this->method_title : __( 'Settings', 'woocommerce' ) ; ?></h3>

		<?php echo ( ! empty( $this->method_description ) ) ? wpautop( $this->method_description ) : ''; ?>

		<table class="form-table">
			<?php //$this->generate_settings_html(); ?>
		</table><?php
	}

	/**
	 * Initialise Settings Form Fields
	 *
	 * Add an array of fields to be displayed
	 * on the gateway's settings screen.
	 *
	 * @since 1.0.0
	 * @return string
	 */
	public function init_form_fields() {}

	/**
	 * Get the form fields after they are initialized
	 *
	 * @return array of options
	 */
	public function get_form_fields() {
		return apply_filters( 'axisbuilder_settings_api_form_fields_' . $this->id, $this->form_fields );
	}
}
