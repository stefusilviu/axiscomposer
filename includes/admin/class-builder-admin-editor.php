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
		add_action( 'admin_init', array( $this, 'add_editor_button' ) );
		add_action( 'admin_init', array( $this, 'add_shortcode_button' ) );
		add_filter( 'tiny_mce_version', array( $this, 'refresh_tiny_mce' ) );
		add_filter( 'mce_external_languages', array( $this, 'add_tinymce_locales' ), 20, 1 );
	}

	/**
	 * Add a button for toggle between the editors.
	 */
	public function add_editor_button() {
		add_action( 'edit_form_after_title', array( $this, 'wrap_default_editor' ) );
		add_action( 'edit_form_after_editor', array( $this, 'close_default_editor_wrap' ) );
	}

	/**
	 * wrap_default_editor.
	 * @return string
	 */
	public function wrap_default_editor() {
		$screen = get_current_screen();

		if ( in_array( $screen->id, get_builder_core_supported_screens() ) ) {
			global $post_ID;

			$builder_label   = __( 'Use Page Builder', 'axisbuilder' );
			$default_label   = __( 'Use Default Editor', 'axisbuilder' );
			$is_builder_used = get_post_meta( $post_ID, '_axisbuilder_status', true );
			$active_label    = $is_builder_used == 'active' ? $default_label : $builder_label;
			$button_class    = $is_builder_used == 'active' ? 'button-secondary' : 'button-primary';
			$editor_class    = $is_builder_used == 'active' ? ' axisbuilder-hidden-editor' : '';

			echo '<a href="#" id="axisbuilder-button" class="button button-large ' . $button_class . '" data-page-builder="' . $builder_label . '" data-default-editor="' . $default_label . '">' . $active_label . '</a>';
			echo '<div id="postdivrich_wrap" class="axisbuilder' . $editor_class . '">';
		}
	}

	/**
	 * close_default_editor_wrap.
	 * @return string
	 */
	public function close_default_editor_wrap() {
		$screen = get_current_screen();

		if ( in_array( $screen->id, get_builder_core_supported_screens() ) ) {
			echo '</div> <!-- #postdivrich_wrap -->';
		}
	}

	/**
	 * Add a button for shortcodes to the WP editor.
	 */
	public function add_shortcode_button() {
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}

		if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_buttons', array( $this, 'register_shortcode_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'add_shortcode_tinymce_plugin' ) );
		}
	}

	/**
	 * Register the shortcode button.
	 * @param  array $buttons
	 * @return array $buttons
	 */
	public function register_shortcode_button( $buttons ) {
		array_push( $buttons, '|', 'axisbuilder_shortcodes' );

		return $buttons;
	}

	/**
	 * Add the shortcode button to TinyMCE.
	 * @param  array $plugins TinyMCE plugins.
	 * @return array $plugins AxisBuilder TinyMCE plugin.
	 */
	public function add_shortcode_tinymce_plugin( $plugins ) {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		$plugins['axisbuilder_shortcodes'] = AB()->plugin_url() . '/assets/scripts/admin/editor' . $suffix . '.js';

		return $plugins;
	}

	/**
	 * Force TinyMCE to refresh.
	 * @param  int $version
	 * @return int
	 */
	public function refresh_tiny_mce( $version ) {
		$version += 3;

		return $version;
	}

	/**
	 * TinyMCE locales function.
	 * @param  array $locales TinyMCE locales.
	 * @return array
	 */
	public function add_tinymce_locales( $locales ) {
		$locales['axisbuilder_shortcodes'] = AB()->plugin_path() . '/i18n/shortcodes.php';

		return $locales;
	}
}

new AB_Admin_Editor();
