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
		add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Body Class
	 */
	public function admin_body_class( $classes ) {
		$screen = get_current_screen();

		// Class for builder Canvas & Modal grid
		if ( in_array( $screen->id, get_builder_core_supported_screens() ) ) {
			$classes .= 'axisbuilder-flex-grid';
		}

		return $classes;
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
			wp_enqueue_style( 'axisbuilder-modal', AB()->plugin_url() . '/assets/styles/modal.css', array(), AB_VERSION );
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
		wp_register_script( 'axisbuilder_admin', AB()->plugin_url() . '/assets/scripts/admin/admin' . $suffix . '.js', array( 'jquery', 'axisbuilder_modal', 'axisbuilder_helper', 'axisbuilder_history', 'axisbuilder_shortcodes', 'axisbuilder_tooltip', 'axisbuilder_sweet_alert' ), AB_VERSION, true );

		wp_register_script( 'axisbuilder_modal', AB()->plugin_url() . '/assets/scripts/admin/modal' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );

		wp_register_script( 'axisbuilder_helper', AB()->plugin_url() . '/assets/scripts/admin/helper' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );

		wp_register_script( 'axisbuilder_history', AB()->plugin_url() . '/assets/scripts/admin/history' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );

		wp_register_script( 'axisbuilder_shortcodes', AB()->plugin_url() . '/assets/scripts/admin/shortcodes' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );

		wp_register_script( 'axisbuilder_tooltip', AB()->plugin_url() . '/assets/scripts/tooltip/tooltip' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );

		wp_register_script( 'axisbuilder_sweet_alert', AB()->plugin_url() . '/assets/scripts/sweetalert/sweet-alert' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );

		// Modal i10n
		$modal_params = array(
			'save'        => __( 'Save', 'axisbuilder' ),
			'close'       => __( 'Close', 'axisbuilder' ),
			'success'     => __( 'All right!', 'axisbuilder' ),
			'attention'   => __( 'Attention!', 'axisbuilder' ),
			'error'       => __( 'An error occured', 'axisbuilder' ),
			'timeout'     => __( 'Your session timed out. Simply reload the page and try again', 'axisbuilder' ),
			'ajax_error'  => __( 'Error fetching content - please reload the page and try again', 'axisbuilder' ),
			'login_error' => __( 'It seems your are no longer logged in. Please reload the page and try again', 'axisbuilder' ),

			// Row/Cell Specific
			'select_layout'   => __( 'Select a cell layout', 'axisbuilder' ),
			'no_layout'       => __( 'The current number of cells does not allow any layout variations', 'axisbuilder' ),
			'add_one_cell'    => __( 'You need to add at least one cell', 'axisbuilder' ),
			'remove_one_cell' => __( 'You need to remove at least one cell', 'axisbuilder' ),
		);

		// History i10n
		$history_params = array(
			'theme_name'     => $theme->get( 'Name' ),
			'theme_version'  => $theme->get( 'Version' ),
			'plugin_version' => AB_VERSION
		);

		wp_localize_script( 'axisbuilder_modal', 'axisbuilder_modal', $modal_params );
		wp_localize_script( 'axisbuilder_history', 'axisbuilder_history', $history_params );

		// AxisBuilder admin pages
		if ( in_array( $screen->id, get_builder_core_supported_screens() ) ) {

			wp_enqueue_script( 'axisbuilder_admin' );

			// Enqueue Example only
			wp_enqueue_media();
			wp_enqueue_script( 'axisbuilder_example', AB()->plugin_url() . '/assets/example.js', array( 'jquery' ), AB_VERSION, 'all' );

			// Core Essential Scripts :)
			wp_enqueue_script( 'iris' );
			wp_enqueue_script( 'jquery-blockui' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-droppable' );
			wp_enqueue_script( 'jquery-ui-datepicker' );

			$params = array(
				'post_id'                         => isset( $post->ID ) ? $post->ID : '',
				'plugin_url'                      => AB()->plugin_url(),
				'ajax_url'                        => admin_url( 'admin-ajax.php' ),
				'debug_mode'                      => current_theme_supports( 'axisbuilder-debug' ) ? 'enabled' : 'disabled',
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
