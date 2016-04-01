<?php
/**
 * AxisComposer Sidebars
 *
 * Handles the building of the Sidebars on the fly.
 *
 * @class    AC_Sidebars
 * @version  1.0.0
 * @package  AxisComposer/Classes
 * @category Class
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Sidebars Class
 */
class AC_Sidebars {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'widgets_admin_page', array( $this, 'output_sidebar_tmpl' ) );
		add_action( 'load-widgets.php', array( $this, 'add_custom_sidebars' ), 100 );
		add_action( 'widgets_init', array( $this, 'register_custom_sidebars' ), 1000 );
	}

	/**
	 * Add a sidebar.
	 * @param string $name
	 */
	public static function add_sidebar( $name ) {
		$sidebars = array_unique( array_merge( get_option( 'axiscomposer_custom_sidebars', array() ), array( $name ) ) );
		update_option( 'axiscomposer_custom_sidebars', $sidebars );
	}

	/**
	 * Remove a sidebar.
	 * @param string $name
	 */
	public static function remove_sidebar( $name ) {
		$sidebars = array_diff( get_option( 'axiscomposer_custom_sidebars', array() ), array( $name ) );
		update_option( 'axiscomposer_custom_sidebars', $sidebars );
	}

	/**
	 * Remove all sidebars.
	 */
	public static function remove_all_sidebars() {
		delete_option( 'axiscomposer_custom_sidebars' );
	}

	/**
	 * Validate sidebar name to prevent collisions.
	 * @param  string $sidebar_name Raw sidebar name.
	 * @return string $sidebar_name Valid sidebar name.
	 */
	public static function validate_sidebar_name( $sidebar_name ) {
		global $wp_registered_sidebars;

		// Get the existing sidebars.
		$existing_sidebars = array();
		foreach ( $wp_registered_sidebars as $sidebar ) {
			$existing_sidebars[] = $sidebar['name'];
		}

		// Rename if sidebar exists.
		if ( in_array( $sidebar_name, $existing_sidebars ) ) {
			$count        = substr( $sidebar_name, -1 );
			$rename       = is_numeric( $count ) ? ( substr( $sidebar_name, 0, -1 ) . ( (int) $count + 1 ) ) : ( $sidebar_name . ' - 1' );
			$sidebar_name = self::validate_sidebar_name( $rename );
		}

		return $sidebar_name;
	}

	/**
	 * Output Sidebar Templates.
	 */
	public function output_sidebar_tmpl() {
		include_once( 'admin/views/html-admin-tmpl-sidebars.php' );
	}

	/**
	 * Add a sidebar if the POST variable is set.
	 */
	public function add_custom_sidebars() {
		if ( ! empty( $_POST['axiscomposer-add-sidebar'] ) && isset( $_POST['_ac_sidebar_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_POST['_ac_sidebar_nonce'], 'axiscomposer_add_sidebar' ) ) {
				wp_die( __( 'Action failed. Please refresh the page and retry.', 'axiscomposer' ) );
			}

			if ( ! current_user_can( 'manage_axiscomposer' ) ) {
				wp_die( __( 'Cheatin&#8217; huh?', 'axiscomposer' ) );
			}

			$sidebar_name = ac_clean( $_POST['axiscomposer-add-sidebar'] );
			self::add_sidebar( self::validate_sidebar_name( $sidebar_name ) );
			wp_redirect( admin_url( 'widgets.php' ) );
		}
	}

	/**
	 * Register Custom Widgets Area (Sidebars).
	 */
	public function register_custom_sidebars() {
		$args = apply_filters( 'axiscomposer_custom_widget_args', array(
			'before_widget' => '<aside id="%1$s" class="widget clearfix %2$s">',
			'after_widget'  => '<span class="seperator extralight-border"></span></aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>'
		) );

		$sidebars = get_option( 'axiscomposer_custom_sidebars', array() );

		foreach ( (array) $sidebars as $id => $name ) {
			$args['name']        = $name;
			$args['id']          = 'axiscomposer-sidebar-' . ++$id;
			$args['class']       = 'axiscomposer-custom-widgets-area';
			$args['description'] = sprintf( __( 'Custom Widget Area of the site - %s ', 'axiscomposer' ), $name );
			register_sidebar( $args );
		}
	}
}

new AC_Sidebars();
