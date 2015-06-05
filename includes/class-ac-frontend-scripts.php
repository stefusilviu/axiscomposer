<?php
/**
 * Handle frontend scripts.
 *
 * @class       AC_Frontend_Scripts
 * @package     AxisComposer/Classes
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Frontend_Scripts Class
 */
class AC_Frontend_Scripts {

	/**
	 * Contains an array of script handles registered by AC
	 * @var array
	 */
	private static $scripts = array();

	/**
	 * Contains an array of script handles localized by AC
	 * @var array
	 */
	private static $wp_localize_scripts = array();

	/**
	 * Hooks in methods.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );
		add_action( 'wp_print_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
		add_action( 'wp_print_footer_scripts', array( __CLASS__, 'localize_printed_scripts' ), 5 );
	}

	/**
	 * Get styles for the frontend.
	 * @access private
	 * @return array
	 */
	public static function get_styles() {
		return apply_filters( 'axiscomposer_enqueue_styles', array(
			'axiscomposer-layout' => array(
				'src'     => str_replace( array( 'http:', 'https:' ), '', AC()->plugin_url() ) . '/assets/css/axiscomposer-layout.css',
				'deps'    => '',
				'version' => AC_VERSION,
				'media'   => 'all'
			),
			'axiscomposer-smallscreen' => array(
				'src'     => str_replace( array( 'http:', 'https:' ), '', AC()->plugin_url() ) . '/assets/css/axiscomposer-smallscreen.css',
				'deps'    => 'axiscomposer-layout',
				'version' => AC_VERSION,
				'media'   => 'only screen and (max-width: ' . apply_filters( 'axiscomposer_style_smallscreen_breakpoint', $breakpoint = '768px' ) . ')'
			),
			'axiscomposer-general' => array(
				'src'     => str_replace( array( 'http:', 'https:' ), '', AC()->plugin_url() ) . '/assets/css/axiscomposer.css',
				'deps'    => '',
				'version' => AC_VERSION,
				'media'   => 'all'
			),
		) );
	}

	/**
	 * Register a script for use.
	 *
	 * @uses   wp_register_script()
	 * @access private
	 * @param  string   $handle    [description]
	 * @param  string   $path      [description]
	 * @param  string[] $deps      [description]
	 * @param  string   $version   [description]
	 * @param  boolean  $in_footer [description]
	 */
	private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = AC_VERSION, $in_footer = true ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $path, $deps, $version, $in_footer );
	}

	/**
	 * Register and enqueue a script for use.
	 *
	 * @uses   wp_enqueue_script()
	 * @access private
	 * @param  string   $handle    [description]
	 * @param  string   $path      [description]
	 * @param  string[] $deps      [description]
	 * @param  string   $version   [description]
	 * @param  boolean  $in_footer [description]
	 */
	private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = AC_VERSION, $in_footer = true ) {
		if ( ! in_array( $handle, self::$scripts ) && $path ) {
			self::register_script( $handle, $path, $deps, $version, $in_footer );
		}
		wp_enqueue_script( $handle );
	}

	/**
	 * Register/enqueue frontend scripts.
	 */
	public static function load_scripts() {
		$suffix               = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$assets_path          = str_replace( array( 'http:', 'https:' ), '', AC()->plugin_url() ) . '/assets/';
		$frontend_script_path = $assets_path . 'scripts/frontend/';

		// Register any scripts for later use, or used as dependencies
		self::register_script( 'select2', $assets_path . 'js/select2/select2' . $suffix . '.js', array( 'jquery' ), '3.5.2' );
		self::register_script( 'jquery-blockui', $assets_path . 'scripts/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.70' );

		// Global frontend scripts
		self::enqueue_script( 'axiscomposer', $frontend_script_path . 'axiscomposer' . $suffix . '.js', array( 'jquery', 'jquery-blockui' ) );

		// CSS Styles
		if ( $enqueue_styles = self::get_styles() ) {
			foreach ( $enqueue_styles as $handle => $args ) {
				wp_enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
			}
		}
	}

	/**
	 * Localize a AC script once.
	 * @access private
	 * @since  1.0.0 this needs less wp_script_is() calls due to https://core.trac.wordpress.org/ticket/28404 being added in WP 4.0.
	 * @param  string $handle
	 */
	private static function localize_script( $handle ) {
		if ( ! in_array( $handle, self::$wp_localize_scripts ) && wp_script_is( $handle ) && ( $data = self::get_script_data( $handle ) ) ) {
			$name                        = str_replace( '-', '_', $handle ) . '_params';
			self::$wp_localize_scripts[] = $handle;
			wp_localize_script( $handle, $name, apply_filters( $name, $data ) );
		}
	}

	/**
	 * Return data for script handles
	 * @access private
	 * @param  string $handle
	 * @return array|bool
	 */
	private static function get_script_data( $handle ) {

		switch ( $handle ) {
			case 'axiscomposer' :
				return array(
					'ajax_url'    => AC()->ajax_url(),
					'ac_ajax_url' => AC_AJAX::get_endpoint(),
				);
			break;
			case 'axiscomposer-enhanced-select' :
				return array(
					'i18n_select_option_text'   => esc_attr__( 'Select an option&hellip;', 'axiscomposer' ),
					'i18n_matches_1'            => _x( 'One result is available, press enter to select it.', 'enhanced select', 'axiscomposer' ),
					'i18n_matches_n'            => _x( '%qty% results are available, use up and down arrow keys to navigate.', 'enhanced select', 'axiscomposer' ),
					'i18n_no_matches'           => _x( 'No matches found', 'enhanced select', 'axiscomposer' ),
					'i18n_ajax_error'           => _x( 'Loading failed', 'enhanced select', 'axiscomposer' ),
					'i18n_input_too_short_1'    => _x( 'Please enter 1 or more characters', 'enhanced select', 'axiscomposer' ),
					'i18n_input_too_short_n'    => _x( 'Please enter %qty% or more characters', 'enhanced select', 'axiscomposer' ),
					'i18n_input_too_long_1'     => _x( 'Please delete 1 character', 'enhanced select', 'axiscomposer' ),
					'i18n_input_too_long_n'     => _x( 'Please delete %qty% characters', 'enhanced select', 'axiscomposer' ),
					'i18n_selection_too_long_1' => _x( 'You can only select 1 item', 'enhanced select', 'axiscomposer' ),
					'i18n_selection_too_long_n' => _x( 'You can only select %qty% items', 'enhanced select', 'axiscomposer' ),
					'i18n_load_more'            => _x( 'Loading more results&hellip;', 'enhanced select', 'axiscomposer' ),
					'i18n_searching'            => _x( 'Searching&hellip;', 'enhanced select', 'axiscomposer' )
				);
			break;
		}
		return false;
	}

	/**
	 * Localize scripts only when enqueued.
	 */
	public static function localize_printed_scripts() {
		foreach ( self::$scripts as $handle ) {
			self::localize_script( $handle );
		}
	}
}

AC_Frontend_Scripts::init();