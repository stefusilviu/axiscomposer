<?php
/**
 * Debug/Status page
 *
 * @class       AB_Admin_Status
 * @package     AxisBuilder/Admin/System Status
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Admin_Status Class
 */
class AB_Admin_Status {

	/**
	 * Handles output of the status page in admin.
	 */
	public static function output() {
		include_once( 'views/html-admin-page-status.php' );
	}

	/**
	 * Handles output of reports
	 */
	public static function status_report() {
		include_once( 'views/html-admin-page-status-report.php' );
	}

	/**
	 * Handles output of tools
	 */
	public static function status_tools() {
		global $wpdb;

		$tools = self::get_tools();

		if ( ! empty( $_GET['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'debug_action' ) ) {

			switch ( $_GET['action'] ) {
				case 'clear_expired_transients' :

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

					echo '<div class="updated"><p>' . sprintf( __( '%d Transients Rows Cleared', 'axisbuilder' ), $rows + $rows2 ) . '</p></div>';

				break;
				case 'reset_roles' :
					// Remove then re-add caps and roles
					AB_Install::remove_roles();
					AB_Install::create_roles();

					echo '<div class="updated"><p>' . __( 'Roles successfully reset', 'axisbuilder' ) . '</p></div>';
				break;
				default :
					$action = esc_attr( $_GET['action'] );
					if ( isset( $tools[ $action ]['callback'] ) ) {
						$callback = $tools[ $action ]['callback'];
						$return = call_user_func( $callback );
						if ( $return === false ) {
							if ( is_array( $callback ) ) {
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
					echo '<div class="error"><p>' . __( 'Failed to install/update the translation:', 'axisbuilder' ) . ' ' . __( 'Sorry but there is no translation available for your language =/', 'axisbuilder' ) . '</p></div>';
					break;

				default :
					// Force WordPress find for new updates and hide the AxisBuilder translation update
					set_site_transient( 'update_plugins', null );

					echo '<div class="updated"><p>' . __( 'Translations installed/updated successfully!', 'axisbuilder' ) . '</p></div>';
					break;
			}
		}

		// Display message if settings settings have been saved
		if ( isset( $_REQUEST['settings-updated'] ) ) {
			echo '<div class="updated notice"><p>' . __( 'Your changes have been saved.', 'axisbuilder' ) . '</p></div>';
		}

		include_once( 'views/html-admin-page-status-tools.php' );
	}

	/**
	 * Get tools
	 * @return array of tools
	 */
	public static function get_tools() {
		$tools = array(
			'clear_expired_transients' => array(
				'name'    => __( 'Expired Transients', 'axisbuilder' ),
				'button'  => __( 'Clear expired transients', 'axisbuilder' ),
				'desc'    => __( 'This tool will clear ALL expired transients from WordPress.', 'axisbuilder' ),
			),
			'reset_roles' => array(
				'name'    => __( 'Capabilities', 'axisbuilder'),
				'button'  => __( 'Reset capabilities', 'axisbuilder'),
				'desc'    => __( 'This tool will reset the admin roles to default. Use this if your users cannot access all of the AxisBuilder admin pages.', 'axisbuilder' ),
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
}
