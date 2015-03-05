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

		if ( apply_filters( 'axisbuilder_show_addons_page', false ) ) {
			add_action( 'admin_menu', array( $this, 'addons_menu' ), 70 );
		}

		add_action( 'admin_head', array( $this, 'menu_order_count' ) );
		add_filter( 'menu_order', array( $this, 'menu_order' ) );
		add_filter( 'custom_menu_order', array( $this, 'custom_menu_order' ) );
	}

	/**
	 * Add menu items
	 */
	public function admin_menu() {
		global $menu;

		if ( current_user_can( 'manage_axisbuilder' ) ) {
			$menu[] = array( '', 'read', 'separator-axisbuilder', '', 'wp-menu-separator axisbuilder' );
		}

		add_menu_page( __( 'AxisBuilder', 'axisbuilder' ), __( 'AxisBuilder', 'axisbuilder' ), 'manage_axisbuilder', 'axisbuilder', null, null, '56.5' );
	}

	/**
	 * Add menu item
	 */
	public function iconfonts_menu() {
		add_submenu_page( 'axisbuilder', __( 'Iconfonts', 'axisbuilder' ),  __( 'Iconfonts', 'axisbuilder' ) , 'manage_axisbuilder', 'axisbuilder-iconfonts', array( $this, 'iconfonts_page' ) );
	}

	/**
	 * Add menu item
	 */
	public function settings_menu() {
		$settings_page = add_submenu_page( 'axisbuilder', __( 'AxisBuilder Settings', 'axisbuilder' ),  __( 'Settings', 'axisbuilder' ) , 'manage_axisbuilder', 'axisbuilder-settings', array( $this, 'settings_page' ) );

		add_action( 'load-' . $settings_page, array( $this, 'settings_page_init' ) );
	}

	/**
	 * Loads shortcodes methods into memory for use within settings.
	 */
	public function settings_page_init() {
		AB()->shortcodes();
	}

	/**
	 * Addons menu item
	 */
	public function addons_menu() {
		add_submenu_page( 'axisbuilder', __( 'AxisBuilder Add-ons/Extensions', 'axisbuilder' ),  __( 'Add-ons', 'axisbuilder' ) , 'manage_axisbuilder', 'axisbuilder-addons', array( $this, 'addons_page' ) );
	}

	/**
	 * Adds the iconfont processing count to the menu
	 */
	public function menu_order_count() {
		global $submenu;

		if ( isset( $submenu['axisbuilder'] ) ) {
			// Remove 'AxisBuilder' sub menu item
			unset( $submenu['axisbuilder'][0] );

			// Add count if user has access
			if ( current_user_can( 'manage_axisbuilder' ) && ( $iconfont_count = 0 ) ) {
				foreach ( $submenu['axisbuilder'] as $key => $menu_item ) {
					if ( 0 === strpos( $menu_item[0], _x( 'Icon Fonts', 'Admin menu name', 'axisbuilder' ) ) ) {
						$submenu['axisbuilder'][ $key ][0] .= ' <span class="awaiting-mod update-plugins count-' . $iconfont_count . '"><span class="processing-count">' . number_format_i18n( $iconfont_count ) . '</span></span>';
						break;
					}
				}
			}
		}
	}

	/**
	 * Reorder the AB menu items in admin.
	 * @param  mixed $menu_order
	 * @return array
	 */
	public function menu_order( $menu_order ) {
		// Initialize our custom order array
		$axisbuilder_menu_order = array();

		// Get the index of our custom separator
		$axisbuilder_separator = array_search( 'separator-axisbuilder', $menu_order );

		// Get index of portfolio menu
		$axisbuilder_portfolio = array_search( 'edit.php?post_type=portfolio', $menu_order );

		// Loop through menu order and do some rearranging
		foreach ( $menu_order as $index => $item ) {

			if ( ( ( 'axisbuilder' ) == $item ) ) {
				$axisbuilder_menu_order[] = 'separator-axisbuilder';
				$axisbuilder_menu_order[] = $item;
				$axisbuilder_menu_order[] = 'edit.php?post_type=portfolio';
				unset( $menu_order[ $axisbuilder_separator ] );
				unset( $menu_order[ $axisbuilder_portfolio ] );
			} elseif ( ! in_array( $item, array( 'separator-axisbuilder' ) ) ) {
				$axisbuilder_menu_order[] = $item;
			}

		}

		// Return order
		return $axisbuilder_menu_order;
	}

	/**
	 * Custom menu order
	 * @return bool
	 */
	public function custom_menu_order() {
		return current_user_can( 'manage_axisbuilder' );
	}

	/**
	 * Init the iconfonts page
	 */
	public function iconfonts_page() {
		AB_Admin_Iconfonts::output();
	}

	/**
	 * Init the settings page
	 */
	public function settings_page() {
		AB_Admin_Settings::output();
	}

	/**
	 * Init the addons page
	 */
	public function addons_page() {
		// AB_Admin_Addons::output();
	}
}

return new AB_Admin_Menu();
