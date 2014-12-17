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
	 * Enqueue styles.
	 */
	public function admin_styles() {
		global $wp_scripts;
		$screen       = get_current_screen();
		$color_scheme = get_user_option( 'admin_color', get_current_user_id() );

		// Sitewide menu CSS
		wp_enqueue_style( 'axisbuilder-menu', AB()->plugin_url() . '/assets/styles/menu.css', array(), AB_VERSION );
		wp_enqueue_style( 'axisbuilder-example', AB()->plugin_url() . '/assets/example.css', array(), AB_VERSION );

		if ( in_array( $screen->id, get_builder_core_supported_screens() ) ) {

			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

			// Admin styles for AB pages only
			wp_enqueue_style( 'axisbuilder-admin', AB()->plugin_url() . '/assets/styles/admin.css', array(), AB_VERSION );
			wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), AB_VERSION );
			wp_enqueue_style( 'wp-color-picker' );
		}

		if ( $color_scheme !== 'fresh' ) {
			wp_enqueue_style( 'axisbuilder-colors', AB()->plugin_url() . '/assets/styles/colors.css', array(), AB_VERSION );
		}
	}

	/**
	 * Enqueue scripts.
	 */
	public function admin_scripts() {
		global $wp_query, $post;

		$theme        = wp_get_theme();
		$screen       = get_current_screen();
		// $ab_screen_id = sanitize_title( __( 'Axis Builder', 'axsisbuilder' ) );
		$suffix       = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.min' : ''; // For test purpose only replace position of '.min' :)

		// Register Scripts
		wp_register_script( 'axisbuilder_admin', AB()->plugin_url() . '/assets/scripts/admin/admin' . $suffix . '.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-droppable', 'jquery-ui-datepicker', 'axisbuilder_helper', 'axisbuilder_history', 'axisbuilder_shortcodes', 'axisbuilder_tooltip', 'axisbuilder_sweet_alert' ), AB_VERSION, true );

		wp_register_script( 'axisbuilder_helper', AB()->plugin_url() . '/assets/scripts/admin/helper' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );

		wp_register_script( 'axisbuilder_history', AB()->plugin_url() . '/assets/scripts/admin/history' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );

		wp_register_script( 'axisbuilder_shortcodes', AB()->plugin_url() . '/assets/scripts/admin/shortcodes' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );

		wp_register_script( 'axisbuilder_tooltip', AB()->plugin_url() . '/assets/scripts/tooltip/tooltip' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );

		wp_register_script( 'axisbuilder_sweet_alert', AB()->plugin_url() . '/assets/scripts/sweetalert/sweet-alert' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );

		// History
		$params = array(
			'theme_name'     => $theme->get( 'Name' ),
			'theme_version'  => $theme->get( 'Version' ),
			'plugin_version' => AB_VERSION
		);

		wp_localize_script( 'axisbuilder_history', 'axisbuilder_history', $params );

		// AxisBuilder admin pages
		if ( in_array( $screen->id, get_builder_core_supported_screens() ) ) {

			wp_enqueue_script( 'axisbuilder_admin' );

			// Example only
			wp_register_script( 'axisbuilder_example', AB()->plugin_url() . '/assets/scripts/example.js', array( 'jquery' ), AB_VERSION );

			// Core Essential Scripts :)
			wp_enqueue_script( 'iris' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-tabs' );
			wp_enqueue_script( 'jquery-ui-dialog' );
			wp_enqueue_script( 'jquery-ui-button' );

			$params = array(
				'post_id'                         => isset( $post->ID ) ? $post->ID : '',
				'plugin_url'                      => AB()->plugin_url(),
				'ajax_url'                        => admin_url( 'admin-ajax.php' ),
				'debug_mode'                      => current_theme_supports( 'axisbuilder-debug' ),
				'shortcodes_to_interface_nonce'   => wp_create_nonce( 'shortcodes-to-interface' ),
				'i18n_delete_all_canvas_elements' => esc_js( __( 'Are you sure you want to delete all canvas element(s)? This cannot be undone.', 'axisbuilder' ) ),
				'i18n_last_warning'               => esc_js( __( 'Last warning, are you sure?', 'axisbuilder' ) ),
			);

			wp_localize_script( 'axisbuilder_admin', 'axisbuilder_admin', $params );
		}
	}
}

endif;

return new AB_Admin_Assets();
