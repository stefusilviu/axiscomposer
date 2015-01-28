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
			'add_iconfont'            => false,
			'delete_iconfont'         => false,
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
	 * AJAX Add Zipped Icon Fonts.
	 */
	public function add_iconfont() {
		check_ajax_referer( 'add-custom-iconfont', 'security' );
		exit( 'axisbuilder_iconfont_added' );
	}

	/**
	 * AJAX Add Zipped Icon Fonts.
	 */
	public function delete_iconfont() {
		check_ajax_referer( 'delete-custom-iconfont', 'security' );
	}

	/**
	 * AJAX Delete Custom Sidebar on Widgets Page.
	 */
	public static function delete_custom_sidebar() {

		check_ajax_referer( 'delete-custom-sidebar', 'security' );

		$sidebar = esc_attr( $_POST['sidebar'] );

		if ( isset( $sidebar ) || ! empty( $sidebar ) ) {

			$name = stripslashes( $_POST['sidebar'] );
			$data = get_option( 'axisbuilder_sidebars' );
			$keys = array_search( $name, $data );

			if ( $keys !== false ) {
				unset( $data[$keys] );
				update_option( 'axisbuilder_sidebars', $data );
				wp_send_json( true );
			}
		}

		die();
	}

	/**
	 * AJAX Shortcodes to interface.
	 */
	public static function shortcodes_to_interface( $text = null ) {
		$allowed = false;

		if ( isset( $_POST['text'] ) ) {
			$text = $_POST['text'];
		}

		// Only build the pattern with a subset of shortcodes.
		if ( isset( $_POST['params'] ) && isset( $_POST['params']['allowed'] ) ) {
			$allowed = explode( ',', $_POST['params']['allowed'] );
		}

		// Build the shortcode pattern to check if the text that we want to check uses any of the builder shortcodes.
		axisbuilder_shortcode_pattern( $allowed );

		$text = do_shortcode_builder( $text );

		if ( isset( $_POST['text'] ) ) {
			echo $text;

			die();
		} else {
			return $text;
		}
	}
}

AB_AJAX::init();
