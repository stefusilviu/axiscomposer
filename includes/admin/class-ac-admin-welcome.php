<?php
/**
 * Welcome Page Class
 *
 * Shows a feature overview for the new version (major) and credits.
 *
 * Adapted from code in EDD (Copyright (c) 2012, Pippin Williamson) and WP.
 *
 * @class    AC_Admin_Welcome
 * @version  1.0.0
 * @package  AxisComposer/Admin
 * @category Admin
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Admin_Welcome Class
 */
class AC_Admin_Welcome {

	/** @var array Tweets user can optionally send after install */
	private $tweets = array(
		'AxisComposer kickstarts unique page layouts. It\'s free and has been downloaded over multiple times.',
		'Building a modern layout? AxisComposer is the leading #builder plugin for WordPress (and it\'s free).',
		'AxisComposer is a free #builder plugin for #WordPress for building #allthethings online, beautifully.',
		'Ready to ship your idea? AxisComposer is the fastest growing #builder plugin for WordPress on the web',
	);

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		shuffle( $this->tweets );
	}

	/**
	 * Add admin menus/screens.
	 */
	public function admin_menus() {
		$welcome_page_name  = __( 'About AxisComposer', 'axiscomposer' );
		$welcome_page_title = __( 'Welcome to AxisComposer', 'axiscomposer' );

		switch ( $_GET['page'] ) {
			case 'ac-about' :
				$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'ac-about', array( $this, 'about_screen' ) );
				add_action( 'admin_print_styles-' . $page, array( $this, 'admin_css' ) );
			break;
			case 'ac-credits' :
				$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'ac-credits', array( $this, 'credits_screen' ) );
				add_action( 'admin_print_styles-' . $page, array( $this, 'admin_css' ) );
			break;
			case 'ac-translators' :
				$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'ac-translators', array( $this, 'translators_screen' ) );
				add_action( 'admin_print_styles-' . $page, array( $this, 'admin_css' ) );
			break;
		}
	}

	/**
	 * admin_css function.
	 */
	public function admin_css() {
		wp_enqueue_style( 'axiscomposer-activation', AC()->plugin_url() . '/assets/css/activation.css', array(), AC_VERSION );
	}

	/**
	 * Add styles just for this page, and remove dashboard page links.
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'ac-about' );
		remove_submenu_page( 'index.php', 'ac-credits' );
		remove_submenu_page( 'index.php', 'ac-translators' );
		?>
		<style type="text/css">
			/*<![CDATA[*/
			.ac-badge:before {
				font-family: AxisComposer !important;
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
			.ac-badge {
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
			.about-wrap .ac-badge {
				position: absolute;
				top: 0;
				<?php echo is_rtl() ? 'left' : 'right'; ?>: 0;
			}
			.about-wrap .feature-section {
				margin-bottom: 40px;
			}
			.about-wrap .last-feature-section {
				border-bottom: 0;
				padding-bottom: 0;
				margin-bottom: 0;
			}
			.about-wrap .ac-feature {
				overflow: visible !important;
				*zoom:1;
			}
			.about-wrap h3 + .ac-feature {
				margin-top: 0;
			}
			.about-wrap .ac-feature:before,
			.about-wrap .ac-feature:after {
				content: " ";
				display: table;
			}
			.about-wrap .ac-feature:after {
				clear: both;
			}
			.about-wrap div.icon {
				width: 0 !important;
				padding: 0;
				margin: 20px 0 !important;
			}
			.about-integrations {
				background: #fff;
				margin: 20px 0;
				padding: 1px 20px 10px;
			}
			.about-integrations .feature-section {
				padding: 20px 0;
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
		// Drop minor version if 0
		$major_version = substr( AC()->version, 0, 3 );
		?>
		<h1><?php printf( __( 'Welcome to AxisComposer %s', 'axiscomposer' ), $major_version ); ?></h1>

		<div class="about-text axiscomposer-about-text">
			<?php
				if ( ! empty( $_GET['ac-installed'] ) ) {
					$message = __( 'Thanks, all done!', 'axiscomposer' );
				} elseif ( ! empty( $_GET['ac-updated'] ) ) {
					$message = __( 'Thank you for updating to the latest version!', 'axiscomposer' );
				} else {
					$message = __( 'Thanks for installing!', 'axiscomposer' );
				}

				printf( __( '%s AxisComposer %s is more powerful, stable and secure than ever before. We hope you enjoy using it.', 'axiscomposer' ), $message, $major_version );
			?>
		</div>

		<div class="ac-badge"><?php printf( __( 'Version %s', 'axiscomposer' ), AC()->version ); ?></div>

		<p class="axiscomposer-actions">
			<a href="<?php echo admin_url( 'admin.php?page=ac-settings' ); ?>" class="button button-primary"><?php _e( 'Settings', 'axiscomposer' ); ?></a>
			<a href="<?php echo esc_url( apply_filters( 'axiscomposer_docs_url', 'http://docs.axisthemes.com/documentation/plugins/axiscomposer/', 'axiscomposer' ) ); ?>" class="button button-secondary docs" target="_blank"><?php _e( 'Documentation', 'axiscomposer' ); ?></a>
			<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.axisthemes.com/axiscomposer/" data-text="<?php echo esc_attr( $this->tweets[0] ); ?>" data-via="AxisThemes" data-size="large">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</p>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( $_GET['page'] == 'ac-about' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ac-about' ), 'index.php' ) ) ); ?>">
				<?php _e( "What's New", 'axiscomposer' ); ?>
			</a><a class="nav-tab <?php if ( $_GET['page'] == 'ac-credits' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ac-credits' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Credits', 'axiscomposer' ); ?>
			</a><a class="nav-tab <?php if ( $_GET['page'] == 'ac-translators' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ac-translators' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Translators', 'axiscomposer' ); ?>
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

			<div class="changelog">
				<div class="changelog about-integrations">
					<div class="ac-feature feature-section last-feature-section col three-col">
						<div>
							<h4><?php _e( 'Frontend UI Improvements', 'axiscomposer' ); ?></h4>
							<p><?php _e( 'On the frontend there are several UX enhancements such as the responsive table design as well as a fresh, modern look which meshes more fluidly with the current design trends of default WordPress themes.', 'axiscomposer' ); ?></p>
						</div>
						<div>
							<h4><?php _e( 'Backend UI Improvements', 'axiscomposer' ); ?></h4>
							<p><?php _e( 'On the backend, settings have been re-organised and perform better on hand-held devices for an all round improved user experience. ', 'axiscomposer' ); ?></p>
						</div>
						<div class="last-feature">
							<h4><?php _e( 'Pagebuilder UI', 'axiscomposer' ); ?></h4>
							<p><?php _e( 'As part of the Builder, we\'ve introduced a UI for the Pagebuilder system in 1.0. This makes it easier for 3rd party apps to integrate with AxisComposer', 'axiscomposer' ); ?></p>
						</div>
					</div>
				</div>
			</div>
			<div class="changelog">
				<div class="feature-section last-feature-section col three-col">
					<div>
						<h4><?php _e( 'Custom Widgets Area Builder', 'axiscomposer' ); ?></h4>
						<p><?php printf( __( 'We have added a new option to create the "Custom Widgets Area". Coupled with ability to delete the unused Widget Area or Sidebar. Enable or disable this in the %ssettings%s.', 'axiscomposer' ), '<a href="' . admin_url( 'admin.php?page=ac-settings&tab=general' ) . '">', '</a>' ); ?></p>
					</div>
					<div>
						<h4><?php _e( 'Simplified Iconfonts Upload', 'axiscomposer' ); ?></h4>
						<p><?php printf( __( 'We have added a new option to upload the "Custom Iconfonts". Coupled with some of the additional hooks for developers to load and store your custom iconfonts. View %sIconfont Settings%s.', 'axiscomposer' ), '<a href="' . admin_url( 'admin.php?page=ac-iconfonts' ) . '">', '</a>' ); ?></p>
					</div>
					<div class="last-feature">
						<h4><?php _e( 'Color Customization', 'axiscomposer' ); ?></h4>
						<p><?php printf( __( 'If you\'re looking to customise the look and feel of the frontend in 1.0, take a look at the free %sAxisComposer Colors plugin%s. This lets you change the colors with a live preview.', 'axiscomposer' ), '<a href="https://wordpress.org/plugins/axiscomposer-colors/">', '</a>' ); ?></p>
					</div>
				</div>
			</div>

			<hr />

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ac-settings' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to AxisComposer Settings', 'axiscomposer' ); ?></a>
			</div>
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

			<p class="about-description"><?php printf( __( 'AxisComposer is developed and maintained by a worldwide team of passionate individuals and backed by an awesome developer community. Want to see your name? <a href="%s">Contribute to AxisComposer</a>.', 'axiscomposer' ), 'https://github.com/axisthemes/axiscomposer/blob/master/CONTRIBUTING.md' ); ?></p>

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

			<p class="about-description"><?php printf( __( 'AxisComposer has been kindly translated into several other languages thanks to our translation team. Want to see your name? <a href="%s">Translate AxisComposer</a>.', 'axiscomposer' ), 'https://www.transifex.com/projects/p/axiscomposer/' ); ?></p>

			<?php
				// Have to use this to get the list until the API is open...
				/*
				$contributor_json = json_decode( 'string from https://www.transifex.com/api/2/project/axiscomposer/languages/', true );

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
				esc_html( sprintf( __( 'View %s', 'axiscomposer' ), $contributor->login ) )
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
		$contributors = get_transient( 'axiscomposer_contributors' );

		if ( false !== $contributors ) {
			return $contributors;
		}

		$response = wp_safe_remote_get( 'https://api.github.com/repos/axisthemes/axiscomposer/contributors' );

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
			return array();
		}

		$contributors = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! is_array( $contributors ) ) {
			return array();
		}

		set_transient( 'axiscomposer_contributors', $contributors, HOUR_IN_SECONDS );

		return $contributors;
	}
}

new AC_Admin_Welcome();
