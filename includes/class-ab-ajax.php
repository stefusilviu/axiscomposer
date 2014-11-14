<?php
/**
 * AxisBuilder AB_AJAX
 *
 * AJAX Event Handler
 *
 * @class       AB_AJAX
 * @package     AxisBuilder/Classes
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * AB_AJAX Class
 */
class AB_AJAX {

	/**
	 * Hooks in methods
	 */
	public static function init() {

		// axisthemes_EVENT => nopriv
		$ajax_events = array(
			'delete_custom_sidebar' => false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_axisbuilder_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_axisbuilder_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Output headers for JSON requests.
	 *
	 * @access private
	 * @return void
	 */
	private function json_headers() {
		header( 'Content-Type: application/json; charset=utf-8' );
	}

	/**
	 * AJAX Delete Custom Sidebar on Widgets Page.
	 */
	public static function delete_custom_sidebar() {

		check_ajax_referer( 'delete-custom-sidebar', 'security' );

		// Get post name
		$post = esc_attr( $_POST['name'] );

		if ( ! empty( $post ) ) {

			self::json_headers();

			$name = stripslashes( $_POST['name'] );
			$data = get_option( 'axisbuilder_sidebars' );
			$keys = array_search( $name, $data );

			if ( $keys !== false ) {

				unset( $data[ $keys ] );
				update_option( 'axisbuilder_sidebars', $data );
				echo json_encode( 'axisbuilder-sidebar-deleted' );
			}
		}

		// Quit out
		die();
	}
}

AB_AJAX::init();
