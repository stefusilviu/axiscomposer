<?php
/**
 * AxisComposer Sidebars
 *
 * Handles the building of the Sidebars on the fly.
 *
 * @class       AC_Sidebars
 * @package     AxisComposer/Classes
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Sidebars Class
 */
class AC_Sidebars {

	private $sidebars;

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'widgets_admin_page', array( $this, 'output_sidebar_tmpl' ) );
		add_action( 'load-widgets.php', array( $this, 'add_custom_sidebar' ), 100 );
		add_action( 'widgets_init', array( $this, 'register_custom_sidebar' ), 1000 );
	}

	/**
	 * Output Sidebar Templates.
	 */
	public function output_sidebar_tmpl() {
		include_once( 'admin/views/html-admin-tmpl-sidebars.php' );
	}

	/**
	 * Add Custom Widget Area (Sidebar).
	 */
	public function add_custom_sidebar() {

		if ( ! empty( $_POST['axiscomposer-add-sidebar'] ) ) {

			$this->sidebars = get_option( 'axiscomposer_custom_sidebars' );
			$sidebar_name	= $this->check_sidebar_name( $_POST['axiscomposer-add-sidebar'] );

			if ( empty( $this->sidebars ) ) {
				$this->sidebars = array( $sidebar_name );
			} else {
				$this->sidebars = array_merge( $this->sidebars, array( $sidebar_name ) );
			}

			update_option( 'axiscomposer_custom_sidebars', $this->sidebars );
			wp_redirect( admin_url( 'widgets.php' ) );
			die();
		}
	}

	/**
	 * Checks submitted sidebar name for collisions.
	 * @param  string $sidebar_name Raw sidebar name
	 * @return string $sidebar_name Valid sidebar name
	 */
	public function check_sidebar_name( $sidebar_name ) {

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
			$sidebar_name = $this->check_sidebar_name( $rename );
		}

		return $sidebar_name;
	}

	/**
	 * Register Custom Widget Area (Sidebar).
	 */
	public function register_custom_sidebar() {

		if ( empty( $this->sidebars ) ) {
			$this->sidebars = get_option( 'axiscomposer_custom_sidebars', array() );
		}

		$args = array(
			'before_widget' => '<aside id="%1$s" class="widget clearfix %2$s">',
			'after_widget'  => '<span class="seperator extralight-border"></span></aside>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>'
		);

		$args = apply_filters( 'axiscomposer_custom_widget_args', $args );

		if ( is_array( $this->sidebars ) ) {
			foreach ( (array) $this->sidebars as $id => $name ) {
				$args['name']        = $name;
				$args['id']          = 'axiscomposer-sidebar-' . ++$id;
				$args['class']       = 'axiscomposer-custom-widgets-area';
				$args['description'] = sprintf( __( 'Custom Widget Area of the site - %s ', 'axiscomposer' ), $name );
				register_sidebar( $args );
			}
		}
	}
}

new AC_Sidebars();
