<?php
/**
 * Installation related functions and actions.
 *
 * @class       AB_Install
 * @package     AxisBuilder/Classes
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Install Class
 */
class AB_Install {

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'check_version' ), 5 );
		add_action( 'in_plugin_update_message-axisbuilder/axisbuilder.php', array( __CLASS__, 'in_plugin_update_message' ) );
		add_filter( 'plugin_action_links_' . AB_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * check_version function.
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'axisbuilder_version' ) != AB()->version ) ) {
			self::install();
			do_action( 'axisbuilder_updated' );
		}
	}

	/**
	 * Install AB
	 */
	public static function install() {
		if ( ! defined( 'AB_INSTALLING' ) ) {
			define( 'AB_INSTALLING', true );
		}

		// Ensure needed classes are loaded
		include_once( 'admin/class-builder-admin-notices.php' );

		self::create_options();
		self::create_roles();

		// Register post types
		AB_Post_Types::register_post_types();
		AB_Post_Types::register_taxonomies();

		self::create_files();

		// Update version
		update_option( 'axisbuilder_version', AB()->version );

		// Flush rules after install
		flush_rewrite_rules();

		// Redirect to welcome screen
		if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) {
			set_transient( '_ab_activation_redirect', 1, 30 );
		}

		// Trigger action
		do_action( 'axisbuilder_installed' );
	}

	/**
	 * Default options
	 *
	 * Sets up the default options used on the settings page
	 */
	private static function create_options() {
		// Include settings so that we can run through defaults
		include_once( 'admin/class-builder-admin-settings.php' );

		$settings = AB_Admin_Settings::get_settings_pages();

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
	 * Get capabilities for AxisBuilder.
	 * @return array
	 */
	 private static function get_core_capabilities() {
		$capabilities = array();

		$capabilities['core'] = array(
			'manage_axisbuilder'
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
	 * axisbuilder_remove_roles function.
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
				'base'    => AB_UPLOAD_DIR,
				'file'    => 'index.html',
				'content' => ''
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
		$transient_name = 'ab_upgrade_notice_' . $args['Version'];

		if ( false === ( $upgrade_notice = get_transient( $transient_name ) ) ) {
			$response = wp_remote_get( 'https://plugins.svn.wordpress.org/axisbuilder/trunk/readme.txt' );

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
		$regexp         = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( AB_VERSION ) . '\s*=|$)~Uis';
		$upgrade_notice = '';

		if ( preg_match( $regexp, $content, $matches ) ) {
			$version = trim( $matches[1] );
			$notices = (array) preg_split('~[\r\n]+~', trim( $matches[2] ) );

			if ( version_compare( AB_VERSION, $version, '<' ) ) {

				$upgrade_notice .= '<div class="axisbuilder_plugin_upgrade_notice">';

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
			'settings' => '<a href="' . admin_url( 'admin.php?page=axisbuilder-settings' ) . '" title="' . esc_attr( __( 'View AxisBuilder Settings', 'axisbuilder' ) ) . '">' . __( 'Settings', 'axisbuilder' ) . '</a>',
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
		if ( $file == AB_PLUGIN_BASENAME ) {
			$row_meta = array(
				'docs'    => '<a href="' . esc_url( apply_filters( 'axisbuilder_docs_url', 'http://docs.axisthemes.com/documentation/plugins/axisbuilder/' ) ) . '" title="' . esc_attr( __( 'View AxisBuilder Documentation', 'axisbuilder' ) ) . '">' . __( 'Docs', 'axisbuilder' ) . '</a>',
				'apidocs' => '<a href="' . esc_url( apply_filters( 'axisbuilder_apidocs_url', 'http://docs.axisthemes.com/apidocs/axisbuilder/' ) ) . '" title="' . esc_attr( __( 'View AxisBuilder API Docs', 'axisbuilder' ) ) . '">' . __( 'API Docs', 'axisbuilder' ) . '</a>',
				'support' => '<a href="' . esc_url( apply_filters( 'axisbuilder_support_url', 'http://support.axisthemes.com/' ) ) . '" title="' . esc_attr( __( 'Visit Premium Customer Support Forum', 'axisbuilder' ) ) . '">' . __( 'Premium Support', 'axisbuilder' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
}

AB_Install::init();
