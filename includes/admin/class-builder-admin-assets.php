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

		// Sitewide menu CSS
		wp_enqueue_style( 'axisbuilder-menu', AB()->plugin_url() . '/assets/css/menu.css', array(), AB_VERSION );

		$screen = get_current_screen();

		if ( in_array( $screen->id, axisbuilder_get_screen_ids() ) || in_array( $screen->id, axisbuilder_get_allowed_screen_types() ) ) {

			$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

			// Admin styles for AB pages only
			wp_enqueue_style( 'axisbuilder-admin', AB()->plugin_url() . '/assets/css/admin.css', array(), AB_VERSION );
			wp_enqueue_style( 'jquery-ui-style', '//code.jquery.com/ui/' . $jquery_version . '/themes/smoothness/jquery-ui.css', array(), $jquery_version );
			wp_enqueue_style( 'wp-color-picker' );
		}

		if ( 'fresh' !== get_user_option( 'admin_color', get_current_user_id() ) ) {
			wp_enqueue_style( 'axisbuilder-colors', AB()->plugin_url() . '/assets/css/colors.css', array(), AB_VERSION );
		}

		if ( in_array( $screen->id, array( 'widgets' ) ) && ( 'yes' === get_option( 'axisbuilder_sidebar_enabled', 'yes' ) ) ) {
			wp_enqueue_style( 'axisbuilder-admin-sidebars', AB()->plugin_url() . '/assets/css/sidebars.css', array(), AB_VERSION );
		}

		if ( in_array( $screen->id, array( 'axisbuilder_page_axisbuilder-iconfonts' ) ) ) {
			wp_enqueue_style( 'axisbuilder-admin-iconfonts', AB()->plugin_url() . '/assets/css/iconfonts.css', array(), AB_VERSION );
		}
	}

	/**
	 * Enqueue scripts.
	 */
	public function admin_scripts() {
		global $post;

		get_currentuserinfo();

		$screen = get_current_screen();
		$status = get_option( 'axisbuilder_status_options', array() );
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register Scripts
		wp_register_script( 'axisbuilder-admin', AB()->plugin_url() . '/assets/js/admin/admin' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip' ), AB_VERSION );
		wp_register_script( 'axisbuilder-backbone-modal', AB()->plugin_url() . '/assets/js/admin/modal' . $suffix . '.js', array( 'jquery', 'underscore', 'backbone' ), AB_VERSION );
		wp_register_script( 'axisbuilder-admin-meta-boxes', AB()->plugin_url() . '/assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-blockui', 'wp-color-picker', 'jquery-ui-datepicker', 'jquery-ui-sortable', 'jquery-ui-droppable', 'jquery-tiptip', 'axisbuilder-enhanced-select', 'plupload-all', 'stupidtable' ), AB_VERSION );
		wp_register_script( 'jquery-blockui', AB()->plugin_url() . '/assets/js/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.66', true );
		wp_register_script( 'jquery-tiptip', AB()->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip' . $suffix . '.js', array( 'jquery' ), AB_VERSION, true );
		wp_register_script( 'stupidtable', AB()->plugin_url() . '/assets/js/stupidtable/stupidtable' . $suffix . '.js', array( 'jquery' ), AB_VERSION );
		wp_register_script( 'select2', AB()->plugin_url() . '/assets/js/select2/select2' . $suffix . '.js', array( 'jquery' ), '3.5.2' );
		wp_register_script( 'axisbuilder-enhanced-select', AB()->plugin_url() . '/assets/js/admin/enhanced-select' . $suffix . '.js', array( 'jquery', 'select2' ), AB_VERSION );
		wp_localize_script( 'axisbuilder-enhanced-select', 'axisbuilder_enhanced_select_params', array(
			'ajax_url'                  => admin_url( 'admin-ajax.php' ),
			'search_post_types_nonce'   => wp_create_nonce( 'search-post-types' ),
			'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'axisbuilder' ),
			'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'axisbuilder' ),
			'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'axisbuilder' ),
			'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'axisbuilder' ),
			'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'axisbuilder' ),
			'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'axisbuilder' ),
			'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'axisbuilder' ),
			'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'axisbuilder' ),
			'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'axisbuilder' ),
			'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'axisbuilder' ),
			'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'axisbuilder' ),
			'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'axisbuilder' )
		) );

		// AxisBuilder admin pages
		if ( in_array( $screen->id, axisbuilder_get_screen_ids() ) ) {
			wp_enqueue_script( 'iris' );
			wp_enqueue_script( 'axisbuilder-admin' );
			wp_enqueue_script( 'jquery-ui-sortable' );
			wp_enqueue_script( 'jquery-ui-autocomplete' );
			wp_enqueue_script( 'axisbuilder-enhanced-select' );

			$params = array(
				'ajax_url' => admin_url( 'admin-ajax.php' )
			);

			wp_localize_script( 'axisbuilder-admin', 'axisbuilder_admin', $params );
		}

		// Meta boxes
		if ( in_array( $screen->id, axisbuilder_get_allowed_screen_types() ) ) {
			wp_enqueue_script( 'axisbuilder-admin-builder-meta-boxes', AB()->plugin_url() . '/assets/js/admin/meta-boxes-builder' . $suffix . '.js', array( 'axisbuilder-admin-meta-boxes', 'axisbuilder-backbone-modal' ), AB_VERSION );
			wp_enqueue_script( 'axisbuilder-admin-builder-meta-boxes-position', AB()->plugin_url() . '/assets/js/admin/meta-boxes-builder-position' . $suffix . '.js', array( 'axisbuilder-admin-meta-boxes' ), AB_VERSION );

			$params = array(
				'post_id'                         => isset( $post->ID ) ? $post->ID : '',
				'ajax_url'                        => admin_url( 'admin-ajax.php' ),
				'plugin_url'                      => AB()->plugin_url(),
				'debug_mode'                      => empty( $status['builder_debug_mode'] ) ? 'no' : 'yes',
				'modal_item_nonce'                => wp_create_nonce( 'modal-item' ),
				'i18n_no_layout'                  => esc_js( __( 'The current number of cells does not allow any layout variations.', 'axisbuilder' ) ),
				'i18n_add_one_cell'               => esc_js( __( 'You need to add at least one cell', 'axisbuilder' ) ),
				'i18n_remove_one_cell'            => esc_js( __( 'You need to remove at least one cell', 'axisbuilder' ) ),
				'i18n_select_cell_layout'         => esc_js( __( 'Select a cell layout', 'axisbuilder' ) ),
				'i18n_css_class_id_error'         => esc_js( __( 'Please enter in a value without any invalid or special characters.', 'axisbuilder' ) ),
				'i18n_trash_elements_title'       => esc_js( __( 'Permanently Delete all Canvas Elements', 'axisbuilder' ) ),
				'i18n_trash_elements_least'       => esc_js( sprintf( __( 'You need to add at least one canvas element below for this action. %sYour history session has beeen reset :)%s', 'axisbuilder' ), '<br /><mark class="yes">', '</mark>' ) ),
				'i18n_trash_elements_notice'      => esc_js( sprintf( __( 'All Page Builder content will be permanently lost and cannot be undone. %sAre you positive you want to delete all canvas elements?%s', 'axisbuilder' ), '<br /><mark class="no">', '</mark>' ) ),
				'i18n_backbone_loading_falied'    => esc_js( __( 'Loading failed - Your session timed out. Please reload the page and try again.', 'axisbuilder' ) ),
				'i18n_backbone_dismiss_button'    => esc_js( __( 'Dismiss', 'axisbuilder' ) )
			);

			wp_localize_script( 'axisbuilder-admin-builder-meta-boxes', 'axisbuilder_admin_meta_boxes_builder', $params );
		}

		if ( in_array( $screen->id, axisbuilder_get_layout_supported_screens() ) ) {
			wp_enqueue_script( 'axisbuilder-admin-layout-meta-boxes', AB()->plugin_url() . '/assets/js/admin/meta-boxes-layout' . $suffix . '.js', array( 'axisbuilder-admin-meta-boxes' ), AB_VERSION );
		}

		// System status
		if ( 'axisbuilder_page_axisbuilder-status' === $screen->id ) {
			wp_enqueue_script( 'zeroclipboard', AB()->plugin_url() . '/assets/js/zeroclipboard/jquery.zeroclipboard' . $suffix . '.js', array( 'jquery' ), AB_VERSION );
		}

		// Widgets Specific
		if ( in_array( $screen->id, array( 'widgets' ) ) && ( 'yes' === get_option( 'axisbuilder_sidebar_enabled', 'yes' ) ) ) {
			wp_enqueue_script( 'axisbuilder-admin-sidebars', AB()->plugin_url() . '/assets/js/admin/sidebars' . $suffix . '.js', array( 'axisbuilder-backbone-modal' ), AB_VERSION );

			$params = array(
				'ajax_url'                    => admin_url( 'admin-ajax.php' ),
				'delete_custom_sidebar_nonce' => wp_create_nonce( 'delete-custom-sidebar' )
			);

			wp_localize_script( 'axisbuilder-admin-sidebars', 'axisbuilder_admin_sidebars', $params );
		}

		// Iconfonts Specific
		if ( in_array( $screen->id, array( 'axisbuilder_page_axisbuilder-iconfonts' ) ) ) {
			wp_enqueue_media();
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script( 'axisbuilder-admin-iconfonts', AB()->plugin_url() . '/assets/js/admin/iconfonts' . $suffix . '.js', array( 'jquery', 'underscore', 'backbone' ), AB_VERSION );

			$params = array(
				'ajax_url'                     => admin_url( 'admin-ajax.php' ),
				'add_custom_iconfont_nonce'    => wp_create_nonce( 'add-custom-iconfont' ),
				'delete_custom_iconfont_nonce' => wp_create_nonce( 'delete-custom-iconfont' ),
			);

			wp_localize_script( 'axisbuilder-admin-iconfonts', 'axisbuilder_admin_iconfonts', $params );
		}
	}
}

new AB_Admin_Assets();
