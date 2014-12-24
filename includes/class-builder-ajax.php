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
	exit;
}

/**
 * AB_AJAX Class
 */
class AB_AJAX {

	/**
	 * Hooks in methods
	 */
	public static function init() {

		// axisbuilder_EVENT => nopriv
		$ajax_events = array(
			'delete_custom_sidebar'   => false,
			'shortcodes_to_interface' => false,
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

	/**
	 * Shortcodes to interface
	 */
	public static function shortcodes_to_interface( $text = null ) {

		// check_ajax_referer( 'shortcodes-to-interface', 'security' );

		$allowed = false;

		if ( isset( $_POST['text'] ) ) {
			$text = $_POST['text'];
		}

		// Only build the pattern with a subset of shortcodes.
		if ( isset( $_POST['params'] ) && isset( $_POST['params']['allowed'] ) ) {
			$allowed = explode( ',', $_POST['params']['allowed'] );
		}

		// Build the shortcode pattern to check if the text that we want to check uses any of the builder shortcodes.
		ab_build_shortcode_pattern( $allowed );

		$text = do_shortcode_builder( $text );

		if ( isset( $_POST['text'] ) ) {
			echo $text;

			// Quit out
			die();
		} else {
			return $text;
		}
	}
}

AB_AJAX::init();
