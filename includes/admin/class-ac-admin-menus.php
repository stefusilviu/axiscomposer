<?php
/**
 * Setup menus in WP admin.
 *
 * @class    AC_Admin_Menu
 * @version  1.0.0
 * @package  AxisComposer/Admin
 * @category Admin
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Admin_Menu Class
 */
class AC_Admin_Menu {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Add menus
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'settings_menu' ), 50 );
		add_action( 'admin_menu', array( $this, 'status_menu' ), 60 );
		add_action( 'admin_head', array( $this, 'menu_order_count' ) );
		add_filter( 'menu_order', array( $this, 'menu_order' ) );
		add_filter( 'custom_menu_order', array( $this, 'custom_menu_order' ) );

		if ( apply_filters( 'axiscomposer_show_iconfont_page', false ) ) {
			add_action( 'admin_menu', array( $this, 'iconfont_menu' ), 20 );
		}

		// Admin bar menus
		if ( apply_filters( 'axiscomposer_show_admin_bar_visit_settings', false ) ) {
			add_action( 'admin_bar_menu', array( $this, 'admin_bar_menus' ), 31 );
		}
	}

	/**
	 * Add menu items
	 */
	public function admin_menu() {
		global $menu;

		if ( current_user_can( 'manage_axiscomposer' ) ) {
			$menu[] = array( '', 'read', 'separator-axiscomposer', '', 'wp-menu-separator axiscomposer' );
		}

		add_menu_page( __( 'AxisComposer', 'axiscomposer' ), __( 'AxisComposer', 'axiscomposer' ), 'manage_axiscomposer', 'axiscomposer', null, null, '56.5' );
	}

	/**
	 * Add menu item
	 */
	public function iconfont_menu() {
		add_submenu_page( 'axiscomposer', __( 'Iconfont', 'axiscomposer' ),  __( 'Iconfont', 'axiscomposer' ) , 'manage_axiscomposer', 'ac-iconfont', array( $this, 'iconfont_page' ) );
	}

	/**
	 * Add menu item
	 */
	public function settings_menu() {
		$settings_page = add_submenu_page( 'axiscomposer', __( 'AxisComposer Settings', 'axiscomposer' ),  __( 'Settings', 'axiscomposer' ) , 'manage_axiscomposer', 'ac-settings', array( $this, 'settings_page' ) );

		add_action( 'load-' . $settings_page, array( $this, 'settings_page_init' ) );
	}

	/**
	 * Loads shortcodes methods into memory for use within settings.
	 */
	public function settings_page_init() {
		AC()->shortcodes();
	}

	/**
	 * Add menu item
	 */
	public function status_menu() {
		add_submenu_page( 'axiscomposer', __( 'AxisComposer Status', 'axiscomposer' ),  __( 'System Status', 'axiscomposer' ) , 'manage_axiscomposer', 'ac-status', array( $this, 'status_page' ) );
		register_setting( 'axiscomposer_status_settings_fields', 'axiscomposer_status_options' );
	}

	/**
	 * Adds the iconfont processing count to the menu
	 */
	public function menu_order_count() {
		global $submenu;

		if ( isset( $submenu['axiscomposer'] ) ) {
			// Remove 'AxisComposer' sub menu item
			unset( $submenu['axiscomposer'][0] );

			// Add count if user has access
			if ( current_user_can( 'manage_axiscomposer' ) && ( $iconfont_count = 0 ) ) {
				foreach ( $submenu['axiscomposer'] as $key => $menu_item ) {
					if ( 0 === strpos( $menu_item[0], _x( 'Iconfonts', 'Admin menu name', 'axiscomposer' ) ) ) {
						$submenu['axiscomposer'][ $key ][0] .= ' <span class="awaiting-mod update-plugins count-' . $iconfont_count . '"><span class="processing-count">' . number_format_i18n( $iconfont_count ) . '</span></span>';
						break;
					}
				}
			}
		}
	}

	/**
	 * Reorder the AC menu items in admin.
	 * @param  mixed $menu_order
	 * @return array
	 */
	public function menu_order( $menu_order ) {
		// Initialize our custom order array
		$axiscomposer_menu_order = array();

		// Get the index of our custom separator
		$axiscomposer_separator = array_search( 'separator-axiscomposer', $menu_order );

		// Get index of portfolio menu
		$axiscomposer_portfolio = array_search( 'edit.php?post_type=portfolio', $menu_order );

		// Loop through menu order and do some rearranging
		foreach ( $menu_order as $index => $item ) {

			if ( ( ( 'axiscomposer' ) == $item ) ) {
				$axiscomposer_menu_order[] = 'separator-axiscomposer';
				$axiscomposer_menu_order[] = $item;
				$axiscomposer_menu_order[] = 'edit.php?post_type=portfolio';
				unset( $menu_order[ $axiscomposer_separator ] );
				unset( $menu_order[ $axiscomposer_portfolio ] );
			} elseif ( ! in_array( $item, array( 'separator-axiscomposer' ) ) ) {
				$axiscomposer_menu_order[] = $item;
			}

		}

		// Return order
		return $axiscomposer_menu_order;
	}

	/**
	 * Custom menu order
	 * @return bool
	 */
	public function custom_menu_order() {
		return current_user_can( 'manage_axiscomposer' );
	}

	/**
	 * Init the iconfont page
	 */
	public function iconfont_page() {
		AC_Admin_Iconfont::output();
	}

	/**
	 * Init the settings page
	 */
	public function settings_page() {
		AC_Admin_Settings::output();
	}

	/**
	 * Init the status page
	 */
	public function status_page() {
		AC_Admin_Status::output();
	}

	/**
	 * Add the "Visit Settings" link in admin bar main menu
	 * @param WP_Admin_Bar $wp_admin_bar
	 */
	public function admin_bar_menus( $wp_admin_bar ) {
		if ( ! is_admin() || ! is_user_logged_in() ) {
			return;
		}

		// Show only when the user is a member of this site, or they're a super admin
		if ( ! is_user_member_of_blog() && ! is_super_admin() ) {
			return;
		}

		// Don't display when user cannot manage AC
		if ( ! current_user_can( 'manage_axiscomposer' ) ) {
			return;
		}

		// Add an option to visit the settings
		$wp_admin_bar->add_node( array(
			'parent' => 'site-name',
			'id'     => 'view-settings',
			'title'  => __( 'Visit Settings', 'axiscomposer' ),
			'href'   => admin_url( 'admin.php?page=ac-settings' )
		) );
	}
}

new AC_Admin_Menu();
