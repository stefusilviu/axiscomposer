<?php
/**
 * Installation related functions and actions.
 *
 * @class    AC_Install
 * @version  1.0.0
 * @package  AxisComposer/Classes
 * @category Admin
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Install Class
 */
class AC_Install {

	/** @var array DB updates that need to be run */
	private static $db_updates = array(
		'1.0.0' => 'updates/axiscomposer-update-1.0.php'
	);

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'admin_init', array( __CLASS__, 'install_actions' ) );
		add_action( 'in_plugin_update_message-axiscomposer/axiscomposer.php', array( __CLASS__, 'in_plugin_update_message' ) );
		add_filter( 'plugin_action_links_' . AC_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * check_version function.
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'axiscomposer_version' ) != AC()->version || get_option( 'axiscomposer_db_version' ) != AC()->version ) ) {
			self::install();
			do_action( 'axiscomposer_updated' );
		}
	}

	/**
	 * Install actions when a update button is clicked.
	 */
	public static function install_actions() {
		if ( ! empty( $_GET['do_update_axiscomposer'] ) ) {
			self::update();
			AC_Admin_Notices::remove_notice( 'update' );
			add_action( 'admin_notices', array( __CLASS__, 'updated_notice' ) );
		}
	}

	/**
	 * Show notice stating update was successful.
	 */
	public static function updated_notice() {
		?>
		<div id="message" class="notice notice-info">
			<p><?php _e( 'AxisComposer data update complete. Thank you for updating to the latest version!', 'axiscomposer' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Install AC
	 */
	public static function install() {
		global $wpdb;

		if ( ! defined( 'AC_INSTALLING' ) ) {
			define( 'AC_INSTALLING', true );
		}

		// Ensure needed classes are loaded
		include_once( 'admin/class-ac-admin-notices.php' );

		self::create_options();
		self::create_roles();
		self::create_files();

		// Register post types
		AC_Post_Types::register_post_types();
		AC_Post_Types::register_taxonomies();

		// Queue upgrades
		$current_db_version = get_option( 'axiscomposer_db_version', null );

		AC_Admin_Notices::remove_all_notices();

		if ( ! is_null( $current_db_version ) && version_compare( $current_db_version, max( array_keys( self::$db_updates ) ), '<' ) ) {
			AC_Admin_Notices::add_notice( 'update' );
		} else {
			self::update_db_version();
		}

		self::update_ac_version();

		// Flush rules after install
		flush_rewrite_rules();

		/*
		 * Deletes all expired transients. The multi-table delete syntax is used
		 * to delete the transient record from table a, and the corresponding
		 * transient_timeout record from table b.
		 *
		 * Based on code inside core's upgrade_network() function.
		 */
		$sql = "DELETE a, b FROM $wpdb->options a, $wpdb->options b
			WHERE a.option_name LIKE %s
			AND a.option_name NOT LIKE %s
			AND b.option_name = CONCAT( '_transient_timeout_', SUBSTRING( a.option_name, 12 ) )
			AND b.option_value < %d";
		$wpdb->query( $wpdb->prepare( $sql, $wpdb->esc_like( '_transient_' ) . '%', $wpdb->esc_like( '_transient_timeout_' ) . '%', time() ) );

		// Trigger action
		do_action( 'axiscomposer_installed' );
	}

	/**
	 * Update AC version to current
	 */
	private static function update_ac_version() {
		delete_option( 'axiscomposer_version' );
		add_option( 'axiscomposer_version', AC()->version );
	}

	/**
	 * Update DB version to current
	 */
	private static function update_db_version( $version = null ) {
		delete_option( 'axiscomposer_db_version' );
		add_option( 'axiscomposer_db_version', is_null( $version ) ? AC()->version : $version );
	}

	/**
	 * Handle updates
	 */
	private static function update() {
		$current_db_version = get_option( 'axiscomposer_db_version' );

		foreach ( self::$db_updates as $version => $updater ) {
			if ( version_compare( $current_db_version, $version, '<' ) ) {
				include( $updater );
				self::update_db_version( $version );
			}
		}

		self::update_db_version();
	}

	/**
	 * Default options
	 *
	 * Sets up the default options used on the settings page
	 */
	private static function create_options() {
		// Include settings so that we can run through defaults
		include_once( 'admin/class-ac-admin-settings.php' );

		$settings = AC_Admin_Settings::get_settings_pages();

		foreach ( $settings as $section ) {
			if ( ! method_exists( $section, 'get_settings' ) ) {
				continue;
			}
			$subsections = array_unique( array_merge( array( '' ), array_keys( $section->get_sections() ) ) );

			foreach ( $subsections as $subsection ) {
				foreach ( $section->get_settings( $subsection ) as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						$autoload = isset( $value['autoload'] ) ? (bool) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
		}
	}

	/**
	 * Create roles and capabilities.
	 */
	public static function create_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$capabilities = self::get_core_capabilities();

		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->add_cap( 'administrator', $cap );
			}
		}
	}

	/**
	 * Get capabilities for AxisComposer.
	 * @return array
	 */
	 private static function get_core_capabilities() {
		$capabilities = array();

		$capabilities['core'] = array(
			'manage_axiscomposer'
		);

		$capability_types = array( 'portfolio' );

		foreach ( $capability_types as $capability_type ) {

			$capabilities[ $capability_type ] = array(
				// Post type
				"edit_{$capability_type}",
				"read_{$capability_type}",
				"delete_{$capability_type}",
				"edit_{$capability_type}s",
				"edit_others_{$capability_type}s",
				"publish_{$capability_type}s",
				"read_private_{$capability_type}s",
				"delete_{$capability_type}s",
				"delete_private_{$capability_type}s",
				"delete_published_{$capability_type}s",
				"delete_others_{$capability_type}s",
				"edit_private_{$capability_type}s",
				"edit_published_{$capability_type}s",

				// Terms
				"manage_{$capability_type}_terms",
				"edit_{$capability_type}_terms",
				"delete_{$capability_type}_terms",
				"assign_{$capability_type}_terms"
			);
		}

		return $capabilities;
	}

	/**
	 * axiscomposer_remove_roles function.
	 */
	public static function remove_roles() {
		global $wp_roles;

		if ( ! class_exists( 'WP_Roles' ) ) {
			return;
		}

		if ( ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}

		$capabilities = self::get_core_capabilities();

		foreach ( $capabilities as $cap_group ) {
			foreach ( $cap_group as $cap ) {
				$wp_roles->remove_cap( 'administrator', $cap );
			}
		}
	}

	/**
	 * Create files/directories
	 */
	private static function create_files() {
		// Install files and folders for uploading files and prevent hotlinking
		$files = array(
			array(
				'base'    => AC_ICONFONT_DIR,
				'file'    => 'index.html',
				'content' => ''
			),
			array(
				'base' 		=> AC_LOG_DIR,
				'file' 		=> '.htaccess',
				'content' 	=> 'deny from all'
			),
			array(
				'base' 		=> AC_LOG_DIR,
				'file' 		=> 'index.html',
				'content' 	=> ''
			)
		);

		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
	}

	/**
	 * Show plugin changes. Code adapted from W3 Total Cache.
	 */
	public static function in_plugin_update_message( $args ) {
		$transient_name = 'ac_upgrade_notice_' . $args['Version'];

		if ( false === ( $upgrade_notice = get_transient( $transient_name ) ) ) {
			$response = wp_safe_remote_get( 'https://plugins.svn.wordpress.org/axiscomposer/trunk/readme.txt' );

			if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {
				$upgrade_notice = self::parse_update_notice( $response['body'] );
				set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
			}
		}

		echo wp_kses_post( $upgrade_notice );
	}

	/**
	 * Parse update notice from readme file
	 * @param  string $content
	 * @return string
	 */
	private static function parse_update_notice( $content ) {
		// Output Upgrade Notice
		$matches        = null;
		$regexp         = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( AC_VERSION ) . '\s*=|$)~Uis';
		$upgrade_notice = '';

		if ( preg_match( $regexp, $content, $matches ) ) {
			$version = trim( $matches[1] );
			$notices = (array) preg_split('~[\r\n]+~', trim( $matches[2] ) );

			if ( version_compare( AC_VERSION, $version, '<' ) ) {

				$upgrade_notice .= '<div class="ac_plugin_upgrade_notice">';

				foreach ( $notices as $index => $line ) {
					$upgrade_notice .= wp_kses_post( preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line ) );
				}

				$upgrade_notice .= '</div> ';
			}
		}

		return wp_kses_post( $upgrade_notice );
	}

	/**
	 * Show action links on the plugin screen.
	 * @param  mixed $links Plugin Action links
	 * @return array
	 */
	public static function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=ac-settings' ) . '" title="' . esc_attr( __( 'View AxisComposer Settings', 'axiscomposer' ) ) . '">' . __( 'Settings', 'axiscomposer' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Show row meta on the plugin screen.
	 * @param  mixed $links Plugin Row Meta
	 * @param  mixed $file  Plugin Base file
	 * @return array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( $file == AC_PLUGIN_BASENAME ) {
			$row_meta = array(
				'docs'    => '<a href="' . esc_url( apply_filters( 'axiscomposer_docs_url', 'http://docs.axisthemes.com/documentation/plugins/axiscomposer/' ) ) . '" title="' . esc_attr( __( 'View AxisComposer Documentation', 'axiscomposer' ) ) . '">' . __( 'Docs', 'axiscomposer' ) . '</a>',
				'apidocs' => '<a href="' . esc_url( apply_filters( 'axiscomposer_apidocs_url', 'http://docs.axisthemes.com/ac-apidocs/' ) ) . '" title="' . esc_attr( __( 'View AxisComposer API Docs', 'axiscomposer' ) ) . '">' . __( 'API Docs', 'axiscomposer' ) . '</a>',
				'support' => '<a href="' . esc_url( apply_filters( 'axiscomposer_support_url', 'http://support.axisthemes.com/' ) ) . '" title="' . esc_attr( __( 'Visit Premium Customer Support Forum', 'axiscomposer' ) ) . '">' . __( 'Premium Support', 'axiscomposer' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
}

AC_Install::init();
