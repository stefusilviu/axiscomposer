<?php
/**
 * AxisBuilder Admin Assets
 *
 * Load Admin Assets.
 *
 * @class       AB_Admin_Assets
 * @package     AxisBuilder/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'AB_Admin_Assets' ) ) :

/**
 * AB_Admin_Assets Class
 */
class AB_Admin_Assets {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Enqueue styles
	 */
	public function admin_styles() {
		global $wp_scripts;
		$screen = get_current_screen();

		// Sitewide menu CSS
		wp_enqueue_style( 'axisbuilder-admin-menu', AB()->plugin_url() . '/assets/styles/menu.css', array(), AB_VERSION );

		if ( in_array( $screen->id, ab_get_screen_ids() ) ) {

			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

			// Admin styles for AB pages only
			wp_enqueue_style( 'axisbuilder-admin-main', AB()->plugin_url() . '/assets/styles/admin.css', array(), AB_VERSION );
			wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), AB_VERSION );
			wp_enqueue_style( 'wp-color-picker' );
		}

		if ( in_array( $screen->id, array( 'widgets' ) ) ) {
			wp_enqueue_style( 'axisbuilder-admin-sidebars', AB()->plugin_url() . '/assets/styles/sidebars.css', array(), AB_VERSION );
		}
	}

	/**
	 * Enqueue scripts
	 */
	public function admin_scripts() {
		global $wp_query, $post;

		$screen       = get_current_screen();
		$ab_screen_id = sanitize_title( __( 'Axis Builder', 'axiscore' ) );
		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Widgets Specific
		if ( in_array( $screen->id, array( 'widgets' ) ) ) {
			wp_enqueue_script( 'axisbuilder-admin-sidebars', AB()->plugin_url() . '/assets/scripts/admin/sidebars' . $suffix . '.js', array( 'jquery' ), AB_VERSION );

			$params = array(
				'remove_sidebar_notice' => __( 'Are you sure you wish to delete the sidebar now?', 'axiscore' ),
				'delete_sidebar_nonce'  => 'axisbuilder_delete_custom_sidebar',
			);

			wp_localize_script( 'axisbuilder-admin-sidebars', 'axisbuilder_admin_sidebars', $params );
		}
	}
}

endif;

return new AB_Admin_Assets();
