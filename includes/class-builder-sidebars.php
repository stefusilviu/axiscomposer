<?php
/**
 * AxisBuilder Sidebars
 *
 * Handles the building of the Sidebars on the fly.
 *
 * @class       AB_Sidebars
 * @package     AxisBuilder/Classes
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Sidebars Class
 */
class AB_Sidebars {

	private $sidebars;

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'load-widgets.php', array( $this, 'load_widgets' ), 5 );
		add_action( 'widgets_init', array( $this, 'register_custom_sidebars' ), 1000 );
	}

	/**
	 * Load Necessary assets and hooks to the widgets page.
	 */
	public function load_widgets() {
		add_action( 'admin_print_scripts', array( $this, 'output' ) );
		add_action( 'load-widgets.php', array( $this, 'add_sidebar_option' ), 100 );
	}

	/**
	 * Handles output of the Widget Area (Sidebar) Builder page in admin.
	 */
	public function output() {
		include_once( 'admin/views/html-admin-page-sidebars.php' );
	}

	/**
	 * Add Custom Widget Area (Sidebar) to the database.
	 */
	public function add_sidebar_option() {

		if ( ! empty( $_POST['axisbuilder-add-sidebar'] ) ) {

			$this->sidebars = get_option( 'axisbuilder_sidebars' );
			$sidebar_name	= $this->get_sidebar_name( $_POST['axisbuilder-add-sidebar'] );

			if ( empty( $this->sidebars ) ) {
				$this->sidebars = array( $sidebar_name );
			} else {
				$this->sidebars = array_merge( $this->sidebars, array( $sidebar_name ) );
			}

			update_option( 'axisbuilder_sidebars', $this->sidebars );
			wp_redirect( admin_url( 'widgets.php' ) );
			die();
		}
	}

	/**
	 * Checks the user Submitted name and makes sure that there are no collisions.
	 *
	 * @param  string $sidebar_name Raw Sidebar name
	 * @return string $sidebar_name Valid Sidebar name without collisions.
	 */
	public function get_sidebar_name( $sidebar_name ) {

		if ( empty( $GLOBALS['wp_registered_sidebars'] ) ) {
			return $sidebar_name;
		}

		$sidebar_exists = array();
		foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) {
			$sidebar_exists[] = $sidebar['name'];
		}

		if ( empty( $this->sidebars ) ) {
			$this->sidebars = array();
		}

		$sidebar_exists = array_merge( $sidebar_exists, $this->sidebars );

		if ( in_array( $sidebar_name, $sidebar_exists ) ) {
			$count        = substr( $sidebar_name, -1 );
			$rename       = is_numeric( $count ) ? ( substr( $sidebar_name, 0, -1 ) . ( (int) $count + 1 ) ) : ( $sidebar_name . ' - 1' );
			$sidebar_name = $this->get_sidebar_name( $rename );
		}

		return $sidebar_name;
	}

	/**
	 * Register Custom Widget Areas (Sidebars).
	 */
	public function register_custom_sidebars() {

		if ( empty( $this->sidebars ) ) {
			$this->sidebars = get_option( 'axisbuilder_sidebars' );
		}

		$args = array(
			'before_widget' => '<aside id="%1$s" class="widget clearfix %2$s">',
			'after_widget'  => '<span class="seperator extralight-border"></span></aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>'
		);

		$args = apply_filters( 'axisbuilder_custom_widget_args', $args );

		if ( is_array( $this->sidebars ) ) {
			foreach ( (array) $this->sidebars as $sidebar ) {
				$args['name']        = $sidebar;
				$args['class']       = 'axisbuilder-custom';
				$args['description'] = sprintf( __( 'Custom Widget Area of the site - %s ', 'axisbuilder' ), $sidebar );
				register_sidebar( $args );
			}
		}
	}
}

return new AB_Sidebars();
