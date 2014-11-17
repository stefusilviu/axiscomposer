<?php
/**
 * Setup menus in WP admin.
 *
 * @class		AB_Admin_Menus
 * @package		AxisBuilder/Admin
 * @category	Admin
 * @author		AxisThemes
 * @since		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'AB_Admin_Menus' ) ) :

/**
 * AB_Admin_Menus Class
 */
class AB_Admin_Menus {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Add menus
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'iconfont_menu' ), 20 );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 50 );
		add_action( 'admin_menu', array( $this, 'status_menu' ), 60 );

		if ( apply_filters( 'axisbuilder_show_addons_page', true ) ) {
			add_action( 'admin_menu', array( $this, 'addons_menu' ), 70 );
		}

		add_action( 'admin_head', array( $this, 'menu_unset' ) );
		add_filter( 'menu_order', array( $this, 'menu_order' ) );
		add_filter( 'custom_menu_order', array( $this, 'custom_menu_order' ) );
	}

	/**
	 * Add menu item
	 */
	public function admin_menu() {
		global $menu;

		if ( current_user_can( 'edit_theme_options' ) ) {
			$menu[] = array( '', 'read', 'separator-axisbuilder', '', 'wp-menu-separator axisbuilder' );
		}

		add_menu_page( __( 'Axis Builder', 'axisbuilder' ), __( 'Axis Builder', 'axisbuilder' ), 'edit_theme_options', 'axisbuilder', null, null, '54.6' );
	}

	/**
	 * Addons menu item
	 */
	public function iconfont_menu() {
		add_submenu_page( 'axisbuilder', __( 'Axis Builder Iconfont Manager', 'axisbuilder' ),  __( 'Iconfonts', 'axisbuilder' ) , 'edit_theme_options', 'ab-iconfonts', array( $this, 'iconfonts_page' ) );
	}

	/**
	 * Add menu items
	 */
	public function settings_menu() {
		$settings_page = add_submenu_page( 'axisbuilder', __( 'Axis Builder Settings', 'axisbuilder' ),  __( 'Settings', 'axisbuilder' ) , 'edit_theme_options', 'ab-settings', array( $this, 'settings_page' ) );

		add_action( 'load-' . $settings_page, array( $this, 'settings_page_init' ) );
	}

	/**
	 * Load 'some' methods into memory for use within settings.
	 */
	public function settings_page_init() {

	}

	/**
	 * Add menu item
	 */
	public function status_menu() {
		add_submenu_page( 'axisbuilder', __( 'Axis Builder Status', 'axisbuilder' ),  __( 'System Status', 'axisbuilder' ) , 'edit_theme_options', 'ab-status', array( $this, 'status_page' ) );
		register_setting( 'axisbuilder_status_settings_fields', 'axisbuilder_status_options' );
	}

	/**
	 * Addons menu item
	 */
	public function addons_menu() {
		add_submenu_page( 'axisbuilder', __( 'Axis Builder Add-ons/Extensions', 'axisbuilder' ),  __( 'Add-ons', 'axisbuilder' ) , 'edit_theme_options', 'ab-addons', array( $this, 'addons_page' ) );
	}

	/**
	 * Unset the correct top admin submenu item.
	 */
	public function menu_unset() {
		global $submenu;

		if ( isset( $submenu['axisbuilder'] ) && isset( $submenu['axisbuilder'][1] ) ) {
			$submenu['axisbuilder'][0] = $submenu['axisbuilder'][1];
			unset( $submenu['axisbuilder'][1] );
		}
	}

	/**
	 * Reorder the AB menu items in admin.
	 *
	 * @param mixed $menu_order
	 * @return array
	 */
	public function menu_order( $menu_order ) {
		// Initialize our custom order array
		$axisbuilder_menu_order = array();

		// Get the index of our custom separator
		$axisbuilder_separator = array_search( 'separator-axisbuilder', $menu_order );

		// Get the index of portfolio menu
		$axisbuilder_portfolio = array_search( 'edit.php?post_type=portfolio', $menu_order );

		// Loop through menu order and do some rearranging
		foreach ( $menu_order as $index => $item ) {

			if ( ( ( 'axisbuilder' ) == $item ) ) {
				$axisbuilder_menu_order[] = 'separator-axisbuilder';
				$axisbuilder_menu_order[] = $item;
				$axisbuilder_menu_order[] = 'edit.php?post_type=portfolio';
				unset( $menu_order[$axisbuilder_separator] );
				unset( $menu_order[$axisbuilder_portfolio] );
			} elseif ( !in_array( $item, array( 'separator-axisbuilder' ) ) ) {
				$axisbuilder_menu_order[] = $item;
			}
		}

		// Return order
		return $axisbuilder_menu_order;
	}

	/**
	 * Custom menu order
	 *
	 * @return bool
	 */
	public function custom_menu_order() {
		return current_user_can( 'edit_theme_options' ) ? true : false;
	}

	/**
	 * Init the iconfonts page
	 */
	public function iconfonts_page() {

	}

	/**
	 * Init the settings page
	 */
	public function settings_page() {

	}

	/**
	 * Init the status page
	 */
	public function status_page() {

	}

	/**
	 * Init the addons page
	 */
	public function addons_page() {

	}
}

endif;

return new AB_Admin_Menus();
