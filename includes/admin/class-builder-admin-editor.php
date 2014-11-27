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

			$builder_label   = __( 'Use Page Builder', 'axisbuilder' );
			$default_label   = __( 'Use Default Editor', 'axisbuilder' );
			$is_builder_used = get_post_meta( $post_ID, '_axisLayoutBuilder_active', true ) ? true : false;
			$active_label    = $is_builder_used ? $default_label : $builder_label;
			$active_class    = $is_builder_used ? 'axisbuilder-button-active' : '';
			$editor_class    = $is_builder_used ? 'axisbuilder-hidden-editor' : '';

			echo '<a href="#" id="axisbuilder-button" class="button button-large button-primary' . $active_class . '" data-page-builder="' . $builder_label . '" data-default-editor="' . $default_label . '">' . $active_label . '</a>';
			echo '<div id="postdivrich_wrap" class="axisbuilder' . $editor_class . '">';
		}
	}

	public function close_default_editor_wrap() {
		$screen = get_current_screen();

		if ( in_array( $screen->id, get_builder_core_supported_screens() ) ) {
			echo '</div> <!-- #postdivrich_wrap -->';
		}
	}
}

return new AB_Admin_Editor();
