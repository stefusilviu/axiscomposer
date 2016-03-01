<?php
/**
 * Debug/Status page
 *
 * @class    AC_Admin_Status
 * @version  1.0.0
 * @package  AxisComposer/Admin/System Status
 * @category Admin
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Admin_Status Class
 */
class AC_Admin_Status {

	/**
	 * Handles output of the status page in admin.
	 */
	public static function output() {
		include_once( 'views/html-admin-page-status.php' );
	}

	/**
	 * Handles output of reports.
	 */
	public static function status_report() {
		include_once( 'views/html-admin-page-status-report.php' );
	}

	/**
	 * Handles output of tools.
	 */
	public static function status_tools() {
		global $wpdb;

		$tools = self::get_tools();

		if ( ! empty( $_GET['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'debug_action' ) ) {

			switch ( $_GET['action'] ) {
				case 'clear_expired_transients' :

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
					$rows = $wpdb->query( $wpdb->prepare( $sql, $wpdb->esc_like( '_transient_' ) . '%', $wpdb->esc_like( '_transient_timeout_' ) . '%', time() ) );

					$sql = "DELETE a, b FROM $wpdb->options a, $wpdb->options b
						WHERE a.option_name LIKE %s
						AND a.option_name NOT LIKE %s
						AND b.option_name = CONCAT( '_site_transient_timeout_', SUBSTRING( a.option_name, 17 ) )
						AND b.option_value < %d";
					$rows2 = $wpdb->query( $wpdb->prepare( $sql, $wpdb->esc_like( '_site_transient_' ) . '%', $wpdb->esc_like( '_site_transient_timeout_' ) . '%', time() ) );

					echo '<div class="updated inline"><p>' . sprintf( __( '%d Transients Rows Cleared', 'axiscomposer' ), $rows + $rows2 ) . '</p></div>';
				break;
				case 'reset_roles' :
					// Remove then re-add caps and roles
					AC_Install::remove_roles();
					AC_Install::create_roles();

					echo '<div class="updated inline"><p>' . __( 'Roles successfully reset', 'axiscomposer' ) . '</p></div>';
				break;
				default :
					$action = esc_attr( $_GET['action'] );
					if ( isset( $tools[ $action ]['callback'] ) ) {
						$callback = $tools[ $action ]['callback'];
						$return = call_user_func( $callback );
						if ( $return === false ) {
							if ( is_array( $callback ) ) {
								echo '<div class="error inline"><p>' . sprintf( __( 'There was an error calling %s::%s', 'axiscomposer' ), get_class( $callback[0] ), $callback[1] ) . '</p></div>';
							} else {
								echo '<div class="error inline"><p>' . sprintf( __( 'There was an error calling %s', 'axiscomposer' ), $callback ) . '</p></div>';
							}
						}
					}
				break;
			}
		}

		// Display message if settings settings have been saved
		if ( isset( $_REQUEST['settings-updated'] ) ) {
			echo '<div class="updated inline"><p>' . __( 'Your changes have been saved.', 'axiscomposer' ) . '</p></div>';
		}

		include_once( 'views/html-admin-page-status-tools.php' );
	}

	/**
	 * Get tools.
	 * @return array of tools
	 */
	public static function get_tools() {
		$tools = array(
			'clear_expired_transients' => array(
				'name'    => __( 'Expired Transients', 'axiscomposer' ),
				'button'  => __( 'Clear expired transients', 'axiscomposer' ),
				'desc'    => __( 'This tool will clear ALL expired transients from WordPress.', 'axiscomposer' ),
			),
			'reset_roles' => array(
				'name'    => __( 'Capabilities', 'axiscomposer'),
				'button'  => __( 'Reset capabilities', 'axiscomposer'),
				'desc'    => __( 'This tool will reset the admin roles to default. Use this if your users cannot access all of the AxisComposer admin pages.', 'axiscomposer' ),
			)
		);

		return apply_filters( 'axiscomposer_debug_tools', $tools );
	}

	/**
	 * Show the logs page.
	 */
	public static function status_logs() {

		$logs = self::scan_log_files();

		if ( ! empty( $_REQUEST['log_file'] ) && isset( $logs[ sanitize_title( $_REQUEST['log_file'] ) ] ) ) {
			$viewed_log = $logs[ sanitize_title( $_REQUEST['log_file'] ) ];
		} elseif ( ! empty( $logs ) ) {
			$viewed_log = current( $logs );
		}

		include_once( 'views/html-admin-page-status-logs.php' );
	}

	/**
	 * Scan the log files.
	 * @return array
	 */
	public static function scan_log_files() {
		$files  = @scandir( AC_LOG_DIR );
		$result = array();

		if ( $files ) {

			foreach ( $files as $key => $value ) {

				if ( ! in_array( $value, array( '.', '..' ) ) ) {
					if ( ! is_dir( $value ) && strstr( $value, '.log' ) ) {
						$result[ sanitize_title( $value ) ] = $value;
					}
				}
			}

		}

		return $result;
	}

	/**
	 * Get latest version of a theme by slug.
	 * @param  object $theme WP_Theme object.
	 * @return string Version number if found.
	 */
	public static function get_latest_theme_version( $theme ) {
		$api = themes_api( 'theme_information', array(
			'slug'     => $theme->get_stylesheet(),
			'fields'   => array(
				'sections' => false,
				'tags'     => false,
			)
		) );

		$update_theme_version = 0;

		// Check .org for updates.
		if ( is_object( $api ) && ! is_wp_error( $api ) ) {
			$update_theme_version = $api->version;

		// Check AxisThemes Theme Version.
		} elseif ( strstr( $theme->{'Author URI'}, 'axisthemes' ) ) {
			$theme_dir = substr( strtolower( str_replace( ' ','', $theme->Name ) ), 0, 45 );

			if ( false === ( $theme_version_data = get_transient( $theme_dir . '_version_data' ) ) ) {
				$theme_changelog = wp_safe_remote_get( 'http://www.axisthemes.com/changelogs/' . $theme_dir . '/changelog.txt' );
				$cl_lines  = explode( "\n", wp_remote_retrieve_body( $theme_changelog ) );
				if ( ! empty( $cl_lines ) ) {
					foreach ( $cl_lines as $line_num => $cl_line ) {
						if ( preg_match( '/^[0-9]/', $cl_line ) ) {
							$theme_date         = str_replace( '.' , '-' , trim( substr( $cl_line , 0 , strpos( $cl_line , '-' ) ) ) );
							$theme_version      = preg_replace( '~[^0-9,.]~' , '' ,stristr( $cl_line , "version" ) );
							$theme_update       = trim( str_replace( "*" , "" , $cl_lines[ $line_num + 1 ] ) );
							$theme_version_data = array( 'date' => $theme_date , 'version' => $theme_version , 'update' => $theme_update , 'changelog' => $theme_changelog );
							set_transient( $theme_dir . '_version_data', $theme_version_data , DAY_IN_SECONDS );
							break;
						}
					}
				}
			}

			if ( ! empty( $theme_version_data['version'] ) ) {
				$update_theme_version = $theme_version_data['version'];
			}
		}

		return $update_theme_version;
	}
}
