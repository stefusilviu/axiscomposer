<?php
/**
 * AxisBuilder Editor
 *
 * Central Editor Class.
 *
 * @class       AB_Admin_Editor
 * @package     AxisBuilder/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Admin_Editor Class
 */
class AB_Admin_Editor {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'init' ) );
	}

	/**
	 * Init Editor
	 */
	public function init() {
		// Add Toggle Button
		$this->apply_editor_wrap();
	}

	/**
	 * Apply Editor Wrap
	 */
	public function apply_editor_wrap() {
		add_action( 'edit_form_after_title', array( $this, 'wrap_default_editor' ) );
		add_action( 'edit_form_after_editor', array( $this, 'close_default_editor_wrap' ) );
	}

	public function wrap_default_editor() {
		$screen = get_current_screen();

		if ( in_array( $screen->id, get_builder_core_supported_screens() ) ) {
			global $post_ID;

			$builder_label = __( 'Use Page Builder', 'axisbuilder' );
			$default_label = __( 'Use Default Editor', 'axisbuilder' );

			echo '<div id="postdivrich_wrap" class="axisbuilder">';
			echo '<a id="axisbuilder-button" href="#" class="button button-large button-primary" data-active-button="' . $default_label . '" data-inactive-button="' . $builder_label . '">' . $builder_label . '</a>';
		}
	}

	public function close_default_editor_wrap() {
		$screen = get_current_screen();

		if ( in_array( $screen->id, get_builder_core_supported_screens() ) ) {
			echo "</div> <!-- end postdivrich_wrap-->";
		}
	}
}

return new AB_Admin_Editor();
