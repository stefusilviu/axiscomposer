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
	exit;
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

		if ( in_array( $screen->id, get_builder_core_supported_screens() ) ) {

			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

			// Admin styles for AB Builder only
			wp_enqueue_style( 'axisbuilder-builder', AB()->plugin_url() . '/assets/styles/builder.css', array(), AB_VERSION );
			wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), AB_VERSION );
			wp_enqueue_style( 'wp-color-picker' );
		}
	}

	/**
	 * Enqueue scripts
	 */
	public function admin_scripts() {
		global $wp_query, $post;

		$screen       = get_current_screen();
		$wc_screen_id = sanitize_title( __( 'Axis Builder', 'axsisbuilder' ) );
		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.min' : ''; // For test purpose only replace position of '.min' :)

		// Register Scripts
		wp_register_script( 'axisbuilder_admin', AB()->plugin_url() . '/assets/scripts/admin/axisbuilder_admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-ui-core' ), AB_VERSION );

		// AxisBuilder admin pages
		if ( in_array( $screen->id, get_builder_core_supported_screens() ) ) {

			wp_enqueue_script( 'axisbuilder_admin' );

			// Core Essential Scripts :)
			wp_enqueue_script( 'iris' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script( 'jquery-ui-dialog' );
			wp_enqueue_script( 'jquery-ui-button' );

			$params = array(

			);

			wp_localize_script( 'axisbuilder_admin', 'axisbuilder_admin', $params );
		}
	}
}

endif;

return new AB_Admin_Assets();
