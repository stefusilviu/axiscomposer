<?php
/**
 * AxisComposer AC_AJAX
 *
 * AJAX Event Handler
 *
 * @class    AC_AJAX
 * @version  1.0.0
 * @package  AxisComposer/Classes
 * @category Class
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_AJAX Class
 */
class AC_AJAX {

	/**
	 * Hooks in ajax handlers.
	 */
	public static function init() {
		self::add_ajax_events();
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 */
	public static function add_ajax_events() {
		// axiscomposer_EVENT => nopriv
		$ajax_events = array(
			'add_iconfont'                    => false,
			'delete_iconfont'                 => false,
			'json_search_pages'               => false,
			'json_search_pages_and_portfolio' => false,
			'delete_custom_sidebar'           => false,
			'shortcodes_to_interface'         => false,
			'rated'                           => false
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_axiscomposer_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_axiscomposer_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * AJAX Add Icon Font.
	 */
	public static function add_iconfont() {

		check_ajax_referer( 'add-custom-iconfont', 'security' );

		if ( ! current_user_can( 'manage_axiscomposer' ) ) {
			die(-1);
		}

		// Get the zip file path.
		$value    = $_POST['value'];
		$zip_file = realpath( get_attached_file( $value['id'] ) );

		// Unpack a compressed package file.
		$unpack = AC_Iconfont::unpack_package( $zip_file );
	}

	/**
	 * AJAX Delete Icon Font.
	 */
	public static function delete_iconfont() {

		check_ajax_referer( 'delete-custom-iconfont', 'security' );

		if ( ! current_user_can( 'manage_axiscomposer' ) ) {
			die(-1);
		}

		$term = (string) ac_clean( stripslashes( $_POST['term'] ) );

		if ( empty( $term ) ) {
			die();
		}
	}

	/**
	 * Search for pages and return json.
	 *
	 * @param string $x (default: '')
	 * @param string $post_types (default: array( 'page' ))
	 */
	public static function json_search_pages( $x = '', $post_types = array( 'page' ) ) {
		global $wpdb;

		ob_start();

		check_ajax_referer( 'search-post-types', 'security' );

		$term = (string) ac_clean( stripslashes( $_GET['term'] ) );

		if ( empty( $term ) ) {
			die();
		}

		$like_term = '%' . $wpdb->esc_like( $term ) . '%';

		if ( is_numeric( $term ) ) {
			$query = $wpdb->prepare( "
				SELECT ID FROM {$wpdb->posts} posts LEFT JOIN {$wpdb->postmeta} postmeta ON posts.ID = postmeta.post_id
				WHERE posts.post_status = 'publish'
				AND (
					posts.post_parent = %s
					OR posts.ID = %s
					OR posts.post_title LIKE %s
					OR (
						postmeta.meta_key = '_sku' AND postmeta.meta_value LIKE %s
					)
				)
			", $term, $term, $term, $like_term );
		} else {
			$query = $wpdb->prepare( "
				SELECT ID FROM {$wpdb->posts} posts LEFT JOIN {$wpdb->postmeta} postmeta ON posts.ID = postmeta.post_id
				WHERE posts.post_status = 'publish'
				AND (
					posts.post_title LIKE %s
					or posts.post_content LIKE %s
					OR (
						postmeta.meta_key = '_sku' AND postmeta.meta_value LIKE %s
					)
				)
			", $like_term, $like_term, $like_term );
		}

		$query .= " AND posts.post_type IN ('" . implode( "','", array_map( 'esc_sql', $post_types ) ) . "')";

		if ( ! empty( $_GET['exclude'] ) ) {
			$query .= " AND posts.ID NOT IN (" . implode( ',', array_map( 'intval', explode( ',', $_GET['exclude'] ) ) ) . ")";
		}

		if ( ! empty( $_GET['include'] ) ) {
			$query .= " AND posts.ID IN (" . implode( ',', array_map( 'intval', explode( ',', $_GET['include'] ) ) ) . ")";
		}

		if ( ! empty( $_GET['limit'] ) ) {
			$query .= " LIMIT " . intval( $_GET['limit'] );
		}

		$posts       = array_unique( $wpdb->get_col( $query ) );
		$found_pages = array();

		if ( ! empty( $posts ) ) {
			foreach ( $posts as $post ) {
				$page = get_post( $post );

				if ( ! current_user_can( 'read_page', $post ) ) {
					continue;
				}

				$found_pages[ $post ] = sprintf( __( '%s &ndash; %s', 'axiscomposer' ), '#' . absint( $page->ID ), rawurldecode( $page->post_title ) );
			}
		}

		$found_pages = apply_filters( 'axiscomposer_json_search_found_pages', $found_pages );

		wp_send_json( $found_pages );
	}

	/**
	 * Search for pages & portfolio projects and return json.
	 *
	 * @see AC_AJAX::json_search_pages()
	 */
	public static function json_search_pages_and_portfolio() {
		self::json_search_pages( '', array( 'page', 'portfolio' ) );
	}

	/**
	 * AJAX Delete Custom Sidebar on Widgets Page.
	 */
	public static function delete_custom_sidebar() {
		ob_start();

		check_ajax_referer( 'delete-custom-sidebar', 'security' );

		if ( ! current_user_can( 'manage_axiscomposer' ) ) {
			die(-1);
		}

		$sidebar = ac_clean( stripslashes( $_POST['sidebar'] ) );

		if ( ! empty( $sidebar ) ) {
			AC_Sidebars::remove_sidebar( $sidebar );
			wp_send_json_success( array( $sidebar ) );
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
		ac_shortcode_pattern( $allowed );

		$text = do_shortcode_builder( $text );

		if ( isset( $_POST['text'] ) ) {
			echo $text;

			die();
		} else {
			return $text;
		}
	}

	/**
	 * Triggered when clicking the rating footer.
	 */
	public static function rated() {
		if ( ! current_user_can( 'manage_axiscomposer' ) ) {
			die(-1);
		}

		update_option( 'axiscomposer_admin_footer_text_rated', 1 );
		die();
	}
}

AC_AJAX::init();
