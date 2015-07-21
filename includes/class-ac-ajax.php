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
	 * Hooks in ajax handlers
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'add_endpoint' ) );
		add_action( 'template_redirect', array( __CLASS__, 'do_ac_ajax' ), 0 );

		self::add_ajax_events();
	}

	/**
	 * Add our endpoint for frontend ajax requests
	 */
	public static function add_endpoint() {
		add_rewrite_tag( '%ac-ajax%', '([^/]*)' );
		add_rewrite_rule( 'ac-ajax/([^/]*)/?', 'index.php?ac-ajax=$matches[1]', 'top' );
		add_rewrite_rule( 'index.php/ac-ajax/([^/]*)/?', 'index.php?ac-ajax=$matches[1]', 'top' );
	}

	/**
	 * Get AC Ajax Endpoint
	 * @param  string $request Optional
	 * @return string
	 */
	public static function get_endpoint( $request = '' ) {
		if ( strstr( get_option( 'permalink_structure' ), '/index.php/' ) ) {
			$endpoint = trailingslashit( home_url( '/index.php/ac-ajax/' . $request, 'relative' ) );
		} elseif ( get_option( 'permalink_structure' ) ) {
			$endpoint = trailingslashit( home_url( '/ac-ajax/' . $request, 'relative' ) );
		} else {
			$endpoint = add_query_arg( 'ac-ajax=', $request, trailingslashit( home_url( '', 'relative' ) ) );
		}
		return esc_url_raw( $endpoint );
	}

	/**
	 * Check for AC Ajax request and fire action
	 */
	public static function do_ac_ajax() {
		global $wp_query;

		if ( ! empty( $_GET['ac-ajax'] ) ) {
			$wp_query->set( 'ac-ajax', sanitize_text_field( $_GET['ac-ajax'] ) );
		}

		if ( $action = $wp_query->get( 'ac-ajax' ) ) {
			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}
			do_action( 'ac_ajax_' . sanitize_text_field( $action ) );
			die();
		}
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax)
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

				// AC AJAX can be used for frontend ajax requests
				add_action( 'ac_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * AJAX Add Icon Font
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
	 * AJAX Delete Icon Font
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
	 * Search for pages and return json
	 * @param string $x (default: '')
	 * @param string $post_types (default: array( 'page' ))
	 */
	public static function json_search_pages( $x = '', $post_types = array( 'page' ) ) {
		ob_start();

		check_ajax_referer( 'search-post-types', 'security' );

		$term    = (string) ac_clean( stripslashes( $_GET['term'] ) );
		$exclude = array();

		if ( empty( $term ) ) {
			die();
		}

		if ( ! empty( $_GET['exclude'] ) ) {
			$exclude = array_map( 'intval', explode( ',', $_GET['exclude'] ) );
		}

		$args = array(
			'post_type'      => $post_types,
			'post_status'    => 'publish',
			'posts_per_page' => -1,
			's'              => $term,
			'fields'         => 'ids',
			'exclude'        => $exclude
		);

		if ( is_numeric( $term ) ) {

			if ( false === array_search( $term, $exclude ) ) {
				$posts2 = get_posts( array(
					'post_type'      => $post_types,
					'post_status'    => 'publish',
					'posts_per_page' => -1,
					'post__in'       => array( 0, $term ),
					'fields'         => 'ids'
				) );
			} else {
				$posts2 = array();
			}

			$posts3 = get_posts( array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'post_parent'    => $term,
				'fields'         => 'ids',
				'exclude'        => $exclude
			) );

			$posts = array_unique( array_merge( get_posts( $args ), $posts2, $posts3 ) );
		} else {
			$posts = array_unique( get_posts( $args ) );
		}

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
	 * Search for pages & portfolio projects and return json
	 * @see AC_AJAX::json_search_pages()
	 */
	public static function json_search_pages_and_portfolio() {
		self::json_search_pages( '', array( 'page', 'portfolio' ) );
	}

	/**
	 * AJAX Delete Custom Sidebar on Widgets Page
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
	 * AJAX Shortcodes to interface
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
