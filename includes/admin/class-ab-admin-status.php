<?php
/**
 * AxisBuilder Admin Status Page
 *
 * Handles Debug/Status page.
 *
 * @class       AB_Admin_Status
 * @package     AxisBuilder/Admin/System Status
 * @category    Admin
 * @author      AxisThemes
 * @since       Version 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'AB_Admin_Status' ) ) :

/**
 * AB_Admin_Status Class
 */
class AB_Admin_Status {

	/**
	 * Handles output of the status page in admin.
	 */
	public static function output() {
		$current_tab = ! empty( $_REQUEST['tab'] ) ? sanitize_title( $_REQUEST['tab'] ) : 'status';

		include_once( 'views/html-admin-page-status.php' );
	}

	/**
	 * Handles output of report
	 */
	public static function status_report() {
		global $axisbuilder, $wpdb;

		include_once( 'views/html-admin-page-status-report.php' );
	}

	/**
	 * Handles output of tools
	 */
	public static function status_tools() {
		global $axisbuilder, $wpdb;

		$tools = self::get_tools();

		if ( ! empty( $_GET['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'debug_action' ) ) {

			switch ( $_GET['action'] ) {
				case "clear_expired_transients" :

					// http://w-shadow.com/blog/2012/04/17/delete-stale-transients/
					$rows = $wpdb->query( "
						DELETE
							a, b
						FROM
							{$wpdb->options} a, {$wpdb->options} b
						WHERE
							a.option_name LIKE '_transient_%' AND
							a.option_name NOT LIKE '_transient_timeout_%' AND
							b.option_name = CONCAT(
								'_transient_timeout_',
								SUBSTRING(
									a.option_name,
									CHAR_LENGTH('_transient_') + 1
								)
							)
							AND b.option_value < UNIX_TIMESTAMP()
					" );

					$rows2 = $wpdb->query( "
						DELETE
							a, b
						FROM
							{$wpdb->options} a, {$wpdb->options} b
						WHERE
							a.option_name LIKE '_site_transient_%' AND
							a.option_name NOT LIKE '_site_transient_timeout_%' AND
							b.option_name = CONCAT(
								'_site_transient_timeout_',
								SUBSTRING(
									a.option_name,
									CHAR_LENGTH('_site_transient_') + 1
								)
							)
							AND b.option_value < UNIX_TIMESTAMP()
					" );

					echo '<div class="updated axisbuilder-message"><p>' . sprintf( __( '%d Transients Rows Cleared', 'axisbuilder' ), $rows + $rows2 ) . '</p></div>';

				break;
				case 'dismiss_translation_upgrade' :
					update_option( 'axisbuilder_language_pack_version', array( AB_VERSION , get_locale() ) );
					$notices = get_option( 'axisbuilder_admin_notices', array() );
					$notices = array_diff( $notices, array( 'translation_upgrade' ) );
					update_option( 'axisbuilder_admin_notices', $notices );

					echo '<div class="updated axisbuilder-message"><p>' . __( 'Translation update message hidden successfully !', 'axisbuilder' ) . '</p></div>';
				break;
				default:
					$action = esc_attr( $_GET['action'] );
					if( isset( $tools[ $action ]['callback'] ) ) {
						$callback = $tools[ $action ]['callback'];
						$return = call_user_func( $callback );
						if( $return === false ) {
							if( is_array( $callback ) ) {
								echo '<div class="error"><p>' . sprintf( __( 'There was an error calling %s::%s', 'axisbuilder' ), get_class( $callback[0] ), $callback[1] ) . '</p></div>';

							} else {
								echo '<div class="error"><p>' . sprintf( __( 'There was an error calling %s', 'axisbuilder' ), $callback ) . '</p></div>';
							}
						}
					}
				break;
			}
		}


		// Manual translation update messages
		if ( isset( $_GET['translation_updated'] ) ) {

			switch ( $_GET['translation_updated'] ) {
				case 2 :
					echo '<div class="error"><p>' . __( 'Failed to install/update the translation:', 'axisbuilder' ) . ' ' . __( 'Seems you don\'t have permission to do this!', 'axisbuilder' ) . '</p></div>';
				break;
				case 3 :
					echo '<div class="error"><p>' . __( 'Failed to install/update the translation:', 'axisbuilder' ) . ' ' . sprintf( __( 'An authentication error occurred while updating the translation. Please try again or configure your %sUpgrade Constants%s.', 'axisbuilder' ), '<a href="http://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants">', '</a>' ) . '</p></div>';
				break;
				case 4 :
					echo '<div class="error"><p>' . __( 'Failed to install/update the translation:', 'axisbuilder' ) . ' ' . __( 'Sorry but there is no translation available for your language.', 'axisbuilder' ) . '</p></div>';
				break;
				default :
					// Force WordPress find for new updates and hide the Axis Builder translation update
					set_site_transient( 'update_plugins', null );

					echo '<div class="updated axisbuilder-message"><p>' . __( 'Translations installed/updated successfully!', 'axisbuilder' ) . '</p></div>';
				break;
			}
		}

	    // Display message if settings settings have been saved
	    if ( isset( $_REQUEST['settings-updated'] ) ) {
			echo '<div class="updated axisbuilder-message"><p>' . __( 'Your changes have been saved.', 'axisbuilder' ) . '</p></div>';
		}

		include_once( 'views/html-admin-page-status-tools.php' );
	}

	/**
	 * Get tools
	 *
	 * @return array of tools
	 */
	public static function get_tools() {
		$tools = array(
			'clear_expired_transients' => array(
				'name'		=> __( 'Clear Expired Transients', 'axisbuilder'),
				'button'	=> __( 'Clear Expired Transients', 'axisbuilder'),
				'desc'		=> __( 'This tool will clear ALL expired transients from WordPress.', 'axisbuilder' ),
			)
		);

		if ( get_locale() !== 'en_US' ) {
			$tools['translation_upgrade'] = array(
				'name'    => __( 'Translation Upgrade', 'axisbuilder' ),
				'button'  => __( 'Force Translation Upgrade', 'axisbuilder' ),
				'desc'    => __( '<strong class="red">Note:</strong> This option will force the translation upgrade for your language if a translation is available.', 'axisbuilder' ),
			);
		}

		return apply_filters( 'axisbuilder_debug_tools', $tools );
	}

	/**
	 * Show the logs page
	 */
	public static function status_logs() {
		$logs = self::scan_log_files();
		if ( ! empty( $_POST['log_file'] ) && isset( $logs[ sanitize_title( $_POST['log_file'] ) ] ) ) {
			$viewed_log = $logs[ sanitize_title( $_POST['log_file'] ) ];
		} elseif ( $logs ) {
			$viewed_log = current( $logs );
		}
		include_once( 'views/html-admin-page-status-logs.php' );
	}

	/**
	 * Retrieve metadata from a file. Based on WP Core's get_file_data function
	 *
	 * @param string $file Path to the file
	 * @param array  $all_headers List of headers, in the format array('HeaderKey' => 'Header Name')
	 */
	public static function get_file_version( $file ) {
		// Avoid notices if file does not exist
		if ( ! file_exists( $file ) ) {
			return '';
		}

		// We don't need to write to the file, so just open for reading.
		$fp = fopen( $file, 'r' );

		// Pull only the first 8kiB of the file in.
		$file_data = fread( $fp, 8192 );

		// PHP will close file handle, but we are good citizens.
		fclose( $fp );

		// Make sure we catch CR-only line endings.
		$file_data = str_replace( "\r", "\n", $file_data );
		$version   = '';

		if ( preg_match( '/^[ \t\/*#@]*' . preg_quote( '@version', '/' ) . '(.*)$/mi', $file_data, $match ) && $match[1] ) {
			$version = _cleanup_header_comment( $match[1] );
		}

		return $version;
	}

	/**
	 * Scan the log files
	 *
	 * @return array
	 */
	public static function scan_log_files() {
		$files         = @scandir( AB_LOG_DIR );
		$result        = array();
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
}

endif;

return new AB_Admin_Status();
