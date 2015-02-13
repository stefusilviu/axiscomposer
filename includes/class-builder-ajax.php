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
			'add_iconfont'                    => false,
			'delete_iconfont'                 => false,
			'json_search_pages'               => false,
			'json_search_pages_and_portfolio' => false,
			'delete_custom_sidebar'           => false,
			'shortcodes_to_interface'         => false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_axisbuilder_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_axisbuilder_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * AJAX Add Icon Font
	 */
	public static function add_iconfont() {

		check_ajax_referer( 'add-custom-iconfont', 'security' );

		AB_Iconfonts::check_capability();

		// Get the file path if the zip file
		$attachment = $_POST['value'];
		$zipfile    = realpath( get_attached_file( $attachment['id'] ) );
		$flatten    = AB_Iconfonts::zip_flatten( $zipfile, array( '\.eot', '\.svg', '\.ttf', '\.woff', '\.json' ) );

		// If zip is flatten, save it to our temp folder and extract the svg file.
		if ( $flatten ) {
			AB_Iconfonts::create_config();
		}

		// If we got no name for the font don't add it and delete the temp folder.
		$tempdir = AB_UPLOAD_DIR . '/axisfonts-temp';
		if ( AB_Iconfonts::$font_name == 'unknown' ) {
			AB_Iconfonts::delete_files( $tempdir );
			die( 'Was not able to retrieve the Font name from your Uploaded Folder' );
		}

		die( 'axisbuilder_iconfont_added:' . AB_Iconfonts::$font_name );
	}

	/**
	 * AJAX Delete Icon Fonts
	 */
	public static function delete_iconfont() {

		check_ajax_referer( 'delete-custom-iconfont', 'security' );

		AB_Iconfonts::check_capability();

		die( 'Was not able to remove Font' );
	}

	/**
	 * Search for pages and return json
	 * @param string $x (default: '')
	 * @param string $post_types (default: array( 'page' ))
	 */
	public static function json_search_pages( $x = '', $post_types = array( 'page' ) ) {
		ob_start();

		check_ajax_referer( 'search-post-types', 'security' );

		$term = axisbuilder_clean( stripslashes( $_GET['term'] ) );

		if ( empty( $term ) ) {
			die();
		}

		if ( is_numeric( $term ) ) {

			$args = array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'post__in'       => array(0, $term),
				'fields'         => 'ids'
			);

			$args2 = array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				'post_parent'    => $term,
				'fields'         => 'ids'
			);

			$posts = array_unique( array_merge( get_posts( $args ), get_posts( $args2 ) ) );

		} else {

			$args = array(
				'post_type'      => $post_types,
				'post_status'    => 'publish',
				'posts_per_page' => -1,
				's'              => $term,
				'fields'         => 'ids'
			);

			$posts = array_unique( get_posts( $args ) );

		}

		$found_pages = array();

		if ( $posts ) {
			foreach ( $posts as $post ) {
				$page = get_post( $post );
				$found_pages[ $post ] = sprintf( __( '%s &ndash; %s', 'axisbuilder' ), '#' . absint( $page->ID ), wp_kses_post( $page->post_title ) );
			}
		}

		$found_pages = apply_filters( 'axisbuilder_json_search_found_pages', $found_pages );

		wp_send_json( $found_pages );
	}

	/**
	 * Search for pages & portfolio projects and return json
	 * @see AB_AJAX::json_search_pages()
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

		$sidebar = esc_attr( $_POST['sidebar'] );

		if ( empty( $sidebar ) ) {
			die();
		}

		if ( $sidebar ) {
			$name = stripslashes( $_POST['sidebar'] );
			$data = (array) get_option( 'axisbuilder_custom_sidebars' );
			$keys = array_search( $name, $data );

			if ( $keys !== false ) {
				unset( $data[$keys] );
				update_option( 'axisbuilder_custom_sidebars', $data );
				wp_send_json( true );
			}
		}
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
