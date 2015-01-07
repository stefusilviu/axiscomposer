<?php
/**
 * Handle frontend scripts.
 *
 * @class       AB_Frontend_Scripts
 * @package     AxisBuilder/Classes
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Frontend_Scripts Class
 */
class AB_Frontend_Scripts {

	/**
	 * Contains an array of script handles registered by AB
	 * @var array
	 */
	private static $scripts = array();

	/**
	 * Contains an array of script handles localized by AB
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
	 * Get styles for the frontend
	 * @access private
	 * @return array
	 */
	public static function get_styles() {
		return apply_filters( 'axisbuilder_enqueue_styles', array(
			'axisbuilder-layout' => array(
				'src'     => str_replace( array( 'http:', 'https:' ), '', AB()->plugin_url() ) . '/assets/styles/axisbuilder-layout.css',
				'deps'    => '',
				'version' => AB_VERSION,
				'media'   => 'all'
			),
			'axisbuilder-smallscreen' => array(
				'src'     => str_replace( array( 'http:', 'https:' ), '', AB()->plugin_url() ) . '/assets/styles/axisbuilder-smallscreen.css',
				'deps'    => 'axisbuilder-layout',
				'version' => AB_VERSION,
				'media'   => 'only screen and (max-width: ' . apply_filters( 'axisbuilder_style_smallscreen_breakpoint', $breakpoint = '768px' ) . ')'
			),
			'axisbuilder-general' => array(
				'src'     => str_replace( array( 'http:', 'https:' ), '', AB()->plugin_url() ) . '/assets/styles/axisbuilder.css',
				'deps'    => '',
				'version' => AB_VERSION,
				'media'   => 'all'
			),
		) );
	}

	/**
	 * Register a script for use
	 *
	 * @uses   wp_register_script()
	 * @access private
	 * @param  string  $handle    [description]
	 * @param  string  $path      [description]
	 * @param  array   $deps      [description]
	 * @param  string  $version   [description]
	 * @param  boolean $in_footer [description]
	 */
	private static function register_script( $handle, $path, $deps = array( 'jquery' ), $version = AB_VERSION, $in_footer = true ) {
		self::$scripts[] = $handle;
		wp_register_script( $handle, $path, $deps, $version, $in_footer );
	}

	/**
	 * Register and enqueue a script for use
	 *
	 * @uses   wp_enqueue_script()
	 * @access private
	 * @param  string  $handle    [description]
	 * @param  string  $path      [description]
	 * @param  array   $deps      [description]
	 * @param  string  $version   [description]
	 * @param  boolean $in_footer [description]
	 */
	private static function enqueue_script( $handle, $path = '', $deps = array( 'jquery' ), $version = AB_VERSION, $in_footer = true ) {
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
		$assets_path          = str_replace( array( 'http:', 'https:' ), '', AB()->plugin_url() ) . '/assets/';
		$frontend_script_path = $assets_path . 'scripts/frontend/';

		// Register any scripts for later use, or used as dependencies
		self::register_script( 'jquery-blockui', $assets_path . 'scripts/jquery-blockui/jquery.blockUI' . $suffix . '.js', array( 'jquery' ), '2.66' );

		// Global frontend scripts
		self::enqueue_script( 'axisbuilder', $frontend_script_path . 'axisbuilder' . $suffix . '.js', array( 'jquery', 'jquery-blockui' ) );

		// CSS Styles
		if ( $enqueue_styles = self::get_styles() ) {
			foreach ( $enqueue_styles as $handle => $args ) {
				wp_enqueue_style( $handle, $args['src'], $args['deps'], $args['version'], $args['media'] );
			}
		}
	}

	/**
	 * Localize a AB script once.
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
			case 'axisbuilder' :
				return array(
					'ajax_url' => AB()->ajax_url(),
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

AB_Frontend_Scripts::init();
