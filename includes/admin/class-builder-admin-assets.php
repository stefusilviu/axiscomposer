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
			wp_enqueue_style( 'axisbuilder-modal', AB()->plugin_url() . '/assets/styles/modal.css', array(), AB_VERSION );
			wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), AB_VERSION );
			wp_enqueue_style( 'wp-color-picker' );
		}

		if ( $color_scheme !== 'fresh' ) {
			wp_enqueue_style( 'axisbuilder-colors', AB()->plugin_url() . '/assets/styles/colors.css', array(), AB_VERSION );
		}

		if ( in_array( $screen->id, array( 'widgets' ) ) ) {
			wp_enqueue_style( 'axisbuilder-admin-sidebars', AB()->plugin_url() . '/assets/styles/sidebars.css', array(), AB_VERSION );
		}
	}

	/**
	 * Enqueue scripts.
	 */
	public function admin_scripts() {
		global $wp_query, $post;

		$theme  = wp_get_theme();
		$screen = get_current_screen();
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register Scripts
		wp_register_script( 'axisbuilder-admin', AB()->plugin_url() . '/assets/scripts/admin/admin' . $suffix . '.js', array( 'jquery', 'axisbuilder-modal', 'axisbuilder-helper', 'axisbuilder-history', 'axisbuilder-tooltip', 'axisbuilder-shortcodes' ), AB_VERSION, true );

		wp_register_script( 'axisbuilder-modal', AB()->plugin_url() . '/assets/scripts/admin/modal' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );

		wp_register_script( 'axisbuilder-helper', AB()->plugin_url() . '/assets/scripts/admin/helper' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );

		wp_register_script( 'axisbuilder-history', AB()->plugin_url() . '/assets/scripts/admin/history' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );

		wp_register_script( 'axisbuilder-tooltip', AB()->plugin_url() . '/assets/scripts/tooltip/tooltip' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );

		wp_register_script( 'axisbuilder-shortcodes', AB()->plugin_url() . '/assets/scripts/admin/shortcodes' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );

		// Modal
		$modal_params = array(
			'ajax_url'                 => admin_url( 'admin-ajax.php' ),
			'error'                    => esc_js( __( 'An error occured', 'axisbuilder' ) ),
			'success'                  => esc_js( __( 'All right!', 'axisbuilder' ) ),
			'attention'                => esc_js( __( 'Attention!', 'axisbuilder' ) ),
			'i18n_save_button'         => esc_js( __( 'Save', 'axisbuilder' ) ),
			'i18n_close_button'        => esc_js( __( 'Close', 'axisbuilder' ) ),
			'i18n_ajax_error'          => esc_js( __( 'Error fetching content - please reload the page and try again', 'axisbuilder' ) ),
			'i18n_login_error'         => esc_js( __( 'It seems your are no longer logged in. Please reload the page and try again', 'axisbuilder' ) ),
			'i18n_session_error'       => esc_js( __( 'Your session timed out. Simply reload the page and try again', 'axisbuilder' ) ),
			'get_modal_elements_nonce' => wp_create_nonce( 'get-modal-elements' )
		);

		wp_localize_script( 'axisbuilder-modal', 'axisbuilder_modal', $modal_params );

		// History
		$history_params = array(
			'theme_name'     => $theme->get( 'Name' ),
			'theme_version'  => $theme->get( 'Version' ),
			'plugin_version' => AB_VERSION
		);

		wp_localize_script( 'axisbuilder-history', 'axisbuilder_history', $history_params );

		// Shortcodes
		$shortcodes_params = array(
			'i18n_no_layout'       => esc_js( __( 'The current number of cells does not allow any layout variations', 'axisbuilder' ) ),
			'i18n_add_one_cell'    => esc_js( __( 'You need to add at least one cell', 'axisbuilder' ) ),
			'i18n_remove_one_cell' => esc_js( __( 'You need to remove at least one cell', 'axisbuilder' ) ),
			'i18n_select_layout'   => esc_js( __( 'Select a cell layout', 'axisbuilder' ) )
		);

		wp_localize_script( 'axisbuilder-shortcodes', 'axisbuilder_shortcodes', $shortcodes_params );

		// AxisBuilder admin pages
		if ( in_array( $screen->id, get_builder_core_supported_screens() ) ) {

			wp_enqueue_script( 'axisbuilder-admin' );

			// Core Essential Scripts :)
			wp_enqueue_script( 'iris' );
			wp_enqueue_script( 'jquery-ui-core' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-droppable' );
			wp_enqueue_script( 'jquery-ui-datepicker' );

			$params = array(
				'post_id'                         => isset( $post->ID ) ? $post->ID : '',
				'plugin_url'                      => AB()->plugin_url(),
				'ajax_url'                        => admin_url( 'admin-ajax.php' ),
				'debug_mode'                      => current_theme_supports( 'axisbuilder-debug' ) ? 'enabled' : 'disabled',
				'i18n_delete_all_canvas_elements' => esc_js( __( 'Are you sure you want to delete all canvas element(s)? This cannot be undone.', 'axisbuilder' ) ),
				'i18n_last_warning'               => esc_js( __( 'Last warning, are you sure?', 'axisbuilder' ) ),
			);

			wp_localize_script( 'axisbuilder-admin', 'axisbuilder_admin', $params );
		}

		// Widgets Specific
		if ( in_array( $screen->id, array( 'widgets' ) ) ) {
			wp_enqueue_script( 'axisbuilder-admin-sidebars', AB()->plugin_url() . '/assets/scripts/admin/sidebars' . $suffix . '.js', array( 'jquery' ), AB_VERSION );

			$params = array(
				'ajax_url'                    => admin_url( 'admin-ajax.php' ),
				'delete_custom_sidebar_nonce' => wp_create_nonce( 'delete-custom-sidebar' ),
				'i18n_delete_custom_sidebar'  => esc_js( __( 'Are you sure you wish to delete the sidebar now?', 'axisbuilder' ) ),
				'i18n_last_warning'           => esc_js( __( 'Last warning, are you sure?', 'axisbuilder' ) )
			);

			wp_localize_script( 'axisbuilder-admin-sidebars', 'axisbuilder_admin_sidebars', $params );
		}
	}
}

endif;

return new AB_Admin_Assets();
