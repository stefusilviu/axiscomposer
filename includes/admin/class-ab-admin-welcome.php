<?php
/**
 * AxisBuilder Admin Welcome Page
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
	exit; // Exit if accessed directly
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

		$welcome_page_name  = __( 'About Axis Builder', 'axisbuilder' );
		$welcome_page_title = __( 'Welcome to Axis Builder', 'axisbuilder' );

		switch ( $_GET['page'] ) {
			case 'ab-about' :
				$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'ab-about', array( $this, 'about_screen' ) );
				add_action( 'admin_print_styles-' . $page, array( $this, 'admin_css' ) );
			break;
			case 'ab-credits' :
				$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'ab-credits', array( $this, 'credits_screen' ) );
				add_action( 'admin_print_styles-' . $page, array( $this, 'admin_css' ) );
			break;
			case 'ab-translators' :
				$page = add_dashboard_page( $welcome_page_title, $welcome_page_name, 'manage_options', 'ab-translators', array( $this, 'translators_screen' ) );
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
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'ab-about' );
		remove_submenu_page( 'index.php', 'ab-credits' );
		remove_submenu_page( 'index.php', 'ab-translators' );

		?>
		<style type="text/css">
			/*<![CDATA[*/
			.ab-badge:before {
				font-family: axisicons !important;
				content: "\e601";
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
			.ab-badge {
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
			.about-wrap .ab-badge {
				position: absolute;
				top: 0;
				<?php echo is_rtl() ? 'left' : 'right'; ?>: 0;
			}
			/*]]>*/
		</style>
		<?php
	}

	/**
	 * Intro text/links shown on all about pages.
	 *
	 * @access private
	 */
	private function intro() {

		// Flush after upgrades
		if ( ! empty( $_GET['ac-updated'] ) || ! empty( $_GET['ac-installed'] ) ) {
			flush_rewrite_rules();
		}

		// Drop minor version if 0
		$major_version = substr( AB()->version, 0, 3 );
		?>
		<h1><?php printf( __( 'Welcome to Axis Builder %s', 'axisbuilder' ), $major_version ); ?></h1>

		<div class="about-text">
			<?php
				if ( ! empty( $_GET['ab-installed'] ) ) {
					$message = __( 'Thanks, all done!', 'axisbuilder' );
				} elseif ( ! empty( $_GET['ab-updated'] ) ) {
					$message = __( 'Thank you for updating to the latest version!', 'axisbuilder' );
				} else {
					$message = __( 'Thanks for installing!', 'axisbuilder' );
				}

				printf( __( '%s Axis Builder %s is more powerful, stable, and secure than ever before. We hope you enjoy using it.', 'axisbuilder' ), $message, $major_version );
			?>
		</div>

		<div class="ab-badge"><?php printf( __( 'Version %s', 'axisbuilder' ), AB()->version ); ?></div>

		<p class="axisbuilder-actions">
			<a href="<?php echo admin_url( 'admin.php?page=ab-settings' ); ?>" class="button button-primary"><?php _e( 'Settings', 'axisbuilder' ); ?></a>
			<a href="<?php echo esc_url( apply_filters( 'axisbuilder_docs_url', 'http://docs.axisthemes.com/documentation/plugins/axis-builder/' ) ); ?>" class="button button-secondary"><?php _e( 'Documentation', 'axisbuilder' ); ?></a>
			<a href="https://twitter.com/share" class="twitter-share-button" data-url="http://www.axisthemes.com/axis-builder" data-text="A open-source (free) drag and drop page builder for #WordPress that helps you build modern and unique page layouts smartly. Beautifully." data-via="AxisThemes" data-size="large" data-hashtags="Axis Builder">Tweet</a>
			<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
		</p>

		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php if ( $_GET['page'] == 'ab-about' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ab-about' ), 'index.php' ) ) ); ?>">
				<?php _e( "What's New", 'axisbuilder' ); ?>
			</a><a class="nav-tab <?php if ( $_GET['page'] == 'ab-credits' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ab-credits' ), 'index.php' ) ) ); ?>">
				<?php _e( 'Credits', 'axisbuilder' ); ?>
			</a><a class="nav-tab <?php if ( $_GET['page'] == 'ab-translators' ) echo 'nav-tab-active'; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ab-translators' ), 'index.php' ) ) ); ?>">
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

			<div class="changelog">
				<div class="about-overview">
					<h2 class="about-headline-callout"><?php _e( 'Introducing a Modern New Page Builder', 'axisbuilder' ); ?></h2>
					<!-- media-grid-cropped.png -->
					<img class="about-overview-img" src="https://i.cloudup.com/gFjdHZjkbI.png" />
					<p><?php _e( 'A drag and drop builder that helps you build modern and unique page layouts smartly. Beautifully.', 'axisbuilder' ); ?></p>
				</div>

				<hr />

				<div class="feature-section col two-col">
					<div class="col-1">
						<!-- oembed.mp4 -->
						<?php
							echo wp_video_shortcode( array(
								'mp4'      => 'http://axisthemes.com/wp-content/uploads/2014/08/AF-intro.mp4',
								// 'ogv'      => '//s.w.org/images/core/3.9/widgets.ogv',
								// 'webm'     => '//s.w.org/images/core/3.9/widgets.webm',
								'loop'     => false,
								'autoplay' => true,
								'width'    => 500
							) );
						?>
					</div>
					<div class="col-2 last-feature">
						<h3><?php _e( 'Building Pages has never been easier', 'axisbuilder' ); ?></h3>
						<p><?php _e( 'Paste in a YouTube URL on a new line, and watch it magically become an embedded video. Now try it with a tweet. Oh yeah &#8212; embedding has become a visual experience. The editor shows a true preview of your embedded content, saving you time and giving you confidence.', 'axisbuilder' ); ?></p>
						<p><?php _e( 'We&#8217;ve expanded the services supported by default, too &#8212; you can embed videos from CollegeHumor, playlists from YouTube, and talks from TED. <a href="http://codex.wordpress.org/Embeds">Check out all of the embeds</a> that WordPress supports.', 'axisbuilder' ); ?></p>
					</div>
				</div>

				<hr />

				<div class="feature-section col two-col">
					<div class="col-1">
						<h3><?php _e( 'Focus on your Theme Frontend', 'axisbuilder' ); ?></h3>
						<p><?php _e( 'Writing and editing is smoother and more immersive with an editor that expands to fit your content as you write, and keeps the formatting tools available at all times.', 'axisbuilder' ); ?></p>
					</div>
					<div class="col-2 last-feature">
						<!-- focus.png -->
						<img src="https://i.cloudup.com/DhokGXMLmR.png" />
					</div>
				</div>

				<hr />

				<div class="feature-section col two-col">
					<div class="col-1">
						<!-- plugins.png -->
						<img src="https://i.cloudup.com/6hlYGuLiTq.png" />
					</div>
					<div class="col-2 last-feature">
						<h3><?php _e( 'Add Features via Extension', 'axisbuilder' ); ?></h3>
						<p><?php _e( 'axisbuilder 1.0 makes it easier to extend the features via extensions. You can create a custom extension or grab the one developed by AxisThemes community.', 'axisbuilder' ); ?></p>
						<a href="<?php echo admin_url( 'admin.php?page=ab-addons' ); ?>" class="button button-large button-primary"><?php _e( 'Grab Extensions', 'axisbuilder' ); ?></a>
					</div>
				</div>
			</div>

			<hr />

			<div class="changelog under-the-hood">
				<h3><?php _e( 'Under the Hood', 'axisbuilder' ); ?></h3>

				<div class="feature-section col three-col">
					<div class="col-1">
						<h4><?php _e( 'Dashboard Localization', 'axisbuilder' ); ?></h4>
						<p><?php _e( 'International users can update their translation files easily from the dashboard.', 'axisbuilder' ); ?></p>
					</div>
					<div class="col-2">
						<h4><?php _e( 'Iconfonts Manager', 'axisbuilder' ); ?></h4>
						<p><?php _e( 'Now the users have the right to upload custom iconfonts generated by IcoMoon too.', 'axisbuilder' ); ?></p>
					</div>
					<div class="col-3 last-feature">
						<h4><?php _e( 'Buy Extensions', 'axisbuilder' ); ?></h4>
						<p><?php _e( 'Premium slider extensions can be bought from our marketplace.', 'axisbuilder' ); ?></p>
					</div>
				</div>
			</div>

			<hr />

			<div class="return-to-dashboard">
				<a href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'ab-settings' ), 'admin.php' ) ) ); ?>"><?php _e( 'Go to Axis Builder Settings', 'axisbuilder' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Output the credits.
	 */
	public function credits_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php printf( __( 'Axis Builder is developed and maintained by a worldwide team of passionate individuals and backed by an awesome developer community. Want to see your name? <a href="%s">Contribute to Axis Builder</a>.', 'axisbuilder' ), 'https://github.com/axisthemes/axis-builder/blob/master/CONTRIBUTING.md' ); ?></p>

			<?php echo $this->contributors(); ?>
		</div>
		<?php
	}

	/**
	 * Output the translators screens.
	 */
	public function translators_screen() {
		?>
		<div class="wrap about-wrap">

			<?php $this->intro(); ?>

			<p class="about-description"><?php printf( __( 'Axis Builder has been kindly translated into several other languages thanks to our translation team. Want to see your name? <a href="%s">Translate Axis Builder</a>.', 'axisbuilder' ), 'https://www.transifex.com/projects/p/axis-builder/' ); ?></p>

			<?php
				// Have to use this to get the list until the API is open...
				/*
				$contributor_json = json_decode( 'string from https://www.transifex.com/api/2/project/axis-builder/languages/', true );

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
	 *
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
	 *
	 * @return mixed
	 */
	public function get_contributors() {
		$contributors = get_transient( 'axisbuilder_contributors' );

		if ( false !== $contributors ) {
			return $contributors;
		}

		$response = wp_remote_get( 'https://api.github.com/repos/axisthemes/axis-builder/contributors', array( 'sslverify' => false ) );

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

		if ( ( isset( $_GET['action'] ) && 'upgrade-plugin' == $_GET['action'] ) && ( isset( $_GET['plugin'] ) && strstr( $_GET['plugin'], 'axis-builder.php' ) ) ) {
			return;
		}

		wp_redirect( admin_url( 'index.php?page=ab-about' ) );
		exit;
	}
}

new AB_Admin_Welcome();
