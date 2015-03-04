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
	 * Intro text/links shown on all about pages.
	 */
	private function intro() {

		// Flush after upgrades
		if ( ! empty( $_GET['ab-updated'] ) || ! empty( $_GET['ab-installed'] ) ) {
			flush_rewrite_rules();
		}

		// Drop minor version if 0
		$major_version = substr( AB()->version, 0, 3 );
		?>
		<h1><?php printf( __( 'Welcome to AxisBuilder %s', 'axisbuilder' ), $major_version ); ?></h1>

		<div class="about-text axisbuilder-about-text">
			<?php
				if ( ! empty( $_GET['ab-installed'] ) ) {
					$message = __( 'Thanks, all done!', 'axisbuilder' );
				} elseif ( ! empty( $_GET['ab-updated'] ) ) {
					$message = __( 'Thank you for updating to the latest version!', 'axisbuilder' );
				} else {
					$message = __( 'Thanks for installing!', 'axisbuilder' );
				}

				printf( __( '%s AxisBuilder %s is more powerful, stable and secure than ever before. We hope you enjoy using it.', 'axisbuilder' ), $message, $major_version );
			?>
		</div>

		<div class="axisbuilder-badge"><?php printf( __( 'Version %s', 'axisbuilder' ), AB()->version ); ?></div>

		<p class="axisbuilder-actions">
			<a href="<?php echo admin_url('admin.php?page=axisbuilder-settings'); ?>" class="button button-primary"><?php _e( 'Settings', 'axisbuilder' ); ?></a>
			<a href="<?php echo esc_url( apply_filters( 'axisbuilder_docs_url', 'http://docs.axisthemes.com/documentation/plugins/axisbuilder/', 'axisbuilder' ) ); ?>" class="button button-secondary"><?php _e( 'Documentation', 'axisbuilder' ); ?></a>
			<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://axisthemes.com/axisbuilder/" data-text="A open-source (free) #builder plugin for #WordPress that helps you build unique and modern page layouts smartly. Beautifully." data-via="AxisThemes" data-size="large" data-hashtags="AxisThemes">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</p>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( $_GET['page'] == 'axisbuilder-about' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'axisbuilder-about' ), 'index.php' ) ) ); ?>">
				<?php _e( "What's New", 'axisbuilder' ); ?>
			</a><a class="nav-tab <?php if ( $_GET['page'] == 'axisbuilder-credits' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'axisbuilder-credits' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Credits', 'axisbuilder' ); ?>
			</a><a class="nav-tab <?php if ( $_GET['page'] == 'axisbuilder-translators' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'axisbuilder-translators' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Translators', 'axisbuilder' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Output the about screen.
	 */
	public function about_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<!--<div class="changelog point-releases"></div>-->

		</div>
		<?php
	}

	/**
	 * Output the credits screen.
	 */
	public function credits_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php printf( __( 'AxisBuilder is developed and maintained by a worldwide team of passionate individuals and backed by an awesome developer community. Want to see your name? <a href="%s">Contribute to AxisBuilder</a>.', 'axisbuilder' ), 'https://github.com/axisthemes/axisbuilder/blob/master/CONTRIBUTING.md' ); ?></p>

			<?php echo $this->contributors(); ?>
		</div>
		<?php
	}

	/**
	 * Output the translators screen.
	 */
	public function translators_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php printf( __( 'AxisBuilder has been kindly translated into several other languages thanks to our translation team. Want to see your name? <a href="%s">Translate AxisBuilder</a>.', 'axisbuilder' ), 'https://www.transifex.com/projects/p/axisbuilder/' ); ?></p>

			<?php
				// Have to use this to get the list until the API is open...
				/*
				$contributor_json = json_decode( 'string from https://www.transifex.com/api/2/project/axisbuilder/languages/', true );

				$contributors = array();

				foreach ( $contributor_json as $group ) {
					$contributors = array_merge( $contributors, $group['coordinators'], $group['reviewers'], $group['translators'] );
				}

				$contributors = array_filter( array_unique( $contributors ) );

				natsort( $contributors );

				foreach ( $contributors as $contributor ) {
					echo htmlspecialchars( '<a href="https://www.transifex.com/accounts/profile/' . $contributor . '">' . $contributor . '</a>, ' );
				}
				*/
			?>

			<p class="wp-credits-list">
				<a href="https://www.transifex.com/accounts/profile/axisthemes">AxisThemes</a>
			</p>
		</div>
		<?php
	}

	/**
	 * Render Contributors List.
	 * @return string $contributor_list HTML formatted list of contributors.
	 */
	public function contributors() {
		$contributors = $this->get_contributors();

		if ( empty( $contributors ) ) {
			return '';
		}

		$contributor_list = '<ul class="wp-people-group">';

		foreach ( $contributors as $contributor ) {
			$contributor_list .= '<li class="wp-person">';
			$contributor_list .= sprintf( '<a href="%s" title="%s">',
				esc_url( 'https://github.com/' . $contributor->login ),
				esc_html( sprintf( __( 'View %s', 'axisbuilder' ), $contributor->login ) )
			);
			$contributor_list .= sprintf( '<img src="%s" width="64" height="64" class="gravatar" alt="%s" />', esc_url( $contributor->avatar_url ), esc_html( $contributor->login ) );
			$contributor_list .= '</a>';
			$contributor_list .= sprintf( '<a class="web" href="%s">%s</a>', esc_url( 'https://github.com/' . $contributor->login ), esc_html( $contributor->login ) );
			$contributor_list .= '</a>';
			$contributor_list .= '</li>';
		}

		$contributor_list .= '</ul>';

		return $contributor_list;
	}

	/**
	 * Retrieve list of contributors from GitHub.
	 * @return mixed
	 */
	public function get_contributors() {
		$contributors = get_transient( 'axisbuilder_contributors' );

		if ( false !== $contributors ) {
			return $contributors;
		}

		$response = wp_remote_get( 'https://api.github.com/repos/axisthemes/axisbuilder/contributors', array( 'sslverify' => false ) );

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
			return array();
		}

		$contributors = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! is_array( $contributors ) ) {
			return array();
		}

		set_transient( 'axisbuilder_contributors', $contributors, HOUR_IN_SECONDS );

		return $contributors;
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
