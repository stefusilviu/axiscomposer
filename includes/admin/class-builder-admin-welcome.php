<?php
/**
 * Welcome Page Class
 *
 * Shows a feature overview for the new version (major) and credits.
 *
 * Adapted from code in EDD (Copyright (c) 2012, Pippin Williamson) and WP.
 *
 * @class       AB_Admin_Welcome
 * @package     AxisBuilder/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Admin_Welcome Class
 */
class AB_Admin_Welcome {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {

		if ( empty( $_GET['page'] ) ) {
			return;
		}

		$welcome_page_name  = __( 'About AxisBuilder', 'axisbuilder' );
		$welcome_page_title = __( 'Welcome to AxisBuilder', 'axisbuilder' );

		switch ( $_GET['page'] ) {
			case 'axisbuilder-about' :
				$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'axisbuilder-about', array( $this, 'about_screen' ) );
				add_action( 'admin_print_styles-' . $page, array( $this, 'admin_css' ) );
			break;
			case 'axisbuilder-credits' :
				$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'axisbuilder-credits', array( $this, 'credits_screen' ) );
				add_action( 'admin_print_styles-' . $page, array( $this, 'admin_css' ) );
			break;
			case 'axisbuilder-translators' :
				$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'axisbuilder-translators', array( $this, 'translators_screen' ) );
				add_action( 'admin_print_styles-' . $page, array( $this, 'admin_css' ) );
			break;
		}
	}

	/**
	 * admin_css function.
	 */
	public function admin_css() {
		wp_enqueue_style( 'axisbuilder-activation', AB()->plugin_url() . '/assets/styles/activation.css', array(), AB_VERSION );
	}

	/**
	 * Add styles just for this page, and remove dashboard page links.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'axisbuilder-about' );
		remove_submenu_page( 'index.php', 'axisbuilder-credits' );
		remove_submenu_page( 'index.php', 'axisbuilder-translators' );

		?>
		<style type="text/css">
			/*<![CDATA[*/
			.axisbuilder-badge:before {
				font-family: AxisBuilder !important;
				content: "\e002";
				color: #fff;
				-webkit-font-smoothing: antialiased;
				-moz-osx-font-smoothing: grayscale;
				font-size: 80px;
				font-weight: normal;
				width: 165px;
				height: 165px;
				line-height: 165px;
				text-align: center;
				position: absolute;
				top: 0;
				<?php echo is_rtl() ? 'right' : 'left'; ?>: 0;
				margin: 0;
				vertical-align: middle;
			}
			.axisbuilder-badge {
				position: relative;
				background: #0074a2;
				text-rendering: optimizeLegibility;
				padding-top: 150px;
				height: 52px;
				width: 165px;
				font-weight: 600;
				font-size: 14px;
				text-align: center;
				color: #78c8e6;
				margin: 5px 0 0 0;
				-webkit-box-shadow: 0 1px 3px rgba(0,0,0,.2);
				box-shadow: 0 1px 3px rgba(0,0,0,.2);
			}
			.about-wrap .axisbuilder-badge {
				position: absolute;
				top: 0;
				<?php echo is_rtl() ? 'left' : 'right'; ?>: 0;
			}
			.about-integrations {
				background: #fff;
				margin: 20px 0;
				padding: 1px 20px 10px;
			}
			.changelog h4 {
				line-height: 1.4;
			}
			/*]]>*/
		</style>
		<?php
	}

	/**
	 * Sends user to the welcome page on first activation.
	 */
	public function welcome() {

		// Bail if no activation redirect transient is set
		if ( ! get_transient( '_ab_activation_redirect' ) ) {
			return;
		}

		// Delete the redirect transient
		delete_transient( '_ab_activation_redirect' );

		// Bail if activating from network, or bulk, or within an iFrame
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) || defined( 'IFRAME_REQUEST' ) ) {
			return;
		}

		if ( ( isset( $_GET['action'] ) && 'upgrade-plugin' == $_GET['action'] ) || ( ! empty( $_GET['page'] ) && $_GET['page'] === 'axisbuilder-about' ) ) {
			return;
		}

		wp_redirect( admin_url( 'index.php?page=axisbuilder-about' ) );
		exit;
	}
}

new AB_Admin_Welcome();
