<?php
/**
 * Setup menus in WP admin.
 *
 * @class       AB_Admin_Menu
 * @package     AxisBuilder/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Admin_Menu Class
 */
class AB_Admin_Menu {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Add menus
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'iconfonts_menu' ), 20 );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 50 );
		add_action( 'admin_menu', array( $this, 'status_menu' ), 60 );

		if ( apply_filters( 'axisbuilder_show_addons_page', true ) ) {
			add_action( 'admin_menu', array( $this, 'addons_menu' ), 70 );
		}

		// add_action( 'admin_head', array( $this, 'menu_highlight' ) );
		// add_action( 'admin_head', array( $this, 'menu_order_count' ) );
		// add_filter( 'menu_order', array( $this, 'menu_order' ) );
		add_filter( 'custom_menu_order', array( $this, 'custom_menu_order' ) );
	}

	/**
	 * Add menu items
	 */
	public function admin_menu() {
		add_menu_page( __( 'Axis Builder', 'axisbuilder' ), __( 'Axis Builder', 'axisbuilder' ), 'update_plugins', 'axisbuilder', null, null, '59.5' );
	}

	/**
	 * Add menu item
	 */
	public function iconfonts_menu() {
		add_submenu_page( 'axisbuilder', __( 'Icon Fonts', 'axisbuilder' ),  __( 'Icon Fonts', 'axisbuilder' ) , 'edit_theme_options', 'axisbuilder-iconfonts', array( $this, 'iconfonts_page' ) );
	}

	/**
	 * Add menu item
	 */
	public function settings_menu() {
		$settings_page = add_submenu_page( 'axisbuilder', __( 'AxisBuilder Settings', 'axisbuilder' ),  __( 'Settings', 'axisbuilder' ) , 'edit_theme_options', 'axisbuilder-settings', array( $this, 'settings_page' ) );

		add_action( 'load-' . $settings_page, array( $this, 'settings_page_init' ) );
	}

	/**
	 * Loads shortcodes methods into memory for use within settings.
	 */
	public function settings_page_init() {
		AB()->shortcodes();
	}

	/**
	 * Add menu item
	 */
	public function status_menu() {
		add_submenu_page( 'axisbuilder', __( 'AxisBuilder Status', 'axisbuilder' ),  __( 'System Status', 'axisbuilder' ) , 'edit_theme_options', 'axisbuilder-status', array( $this, 'status_page' ) );
		register_setting( 'axisbuilder_status_settings_fields', 'axisbuilder_status_options' );
	}

	/**
	 * Addons menu item
	 */
	public function addons_menu() {
		add_submenu_page( 'axisbuilder', __( 'AxisBuilder Add-ons/Extensions', 'axisbuilder' ),  __( 'Add-ons', 'axisbuilder' ) , 'edit_theme_options', 'axisbuilder-addons', array( $this, 'addons_page' ) );
	}

	/**
	 * Custom menu order
	 * @return bool
	 */
	public function custom_menu_order() {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Init the iconfonts page
	 */
	public function iconfonts_page() {
		// AB_Admin_Iconfonts::output();
	}

	/**
	 * Init the settings page
	 */
	public function settings_page() {
		// AB_Admin_Settings::output();
	}

	/**
	 * Init the status page
	 */
	public function status_page() {
		// AB_Admin_Status::output();
	}

	/**
	 * Init the addons page
	 */
	public function addons_page() {
		// AB_Admin_Addons::output();
	}
}

return new AB_Admin_Menu();
