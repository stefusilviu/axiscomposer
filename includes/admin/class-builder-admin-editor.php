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
		add_action( 'admin_init', array( $this, 'add_shortcode_button' ) );
		add_filter( 'tiny_mce_version', array( $this, 'refresh_tiny_mce' ) );
		add_filter( 'mce_external_languages', array( $this, 'add_tinymce_locales' ), 20, 1 );
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

return new AB_Admin_Editor();
