<?php
/**
 * Debug/Status page
 *
 * @class       AC_Admin_Status
 * @package     AxisComposer/Admin/System Status
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
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
							a.option_name LIKE '\_transient\_%' AND
							a.option_name NOT LIKE '\_transient\_timeout\_%' AND
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
							a.option_name LIKE '\_site\_transient\_%' AND
							a.option_name NOT LIKE '\_site\_transient\_timeout_%' AND
							b.option_name = CONCAT(
								'_site_transient_timeout_',
								SUBSTRING(
									a.option_name,
									CHAR_LENGTH('_site_transient_') + 1
								)
							)
							AND b.option_value < UNIX_TIMESTAMP()
					" );

					echo '<div class="updated"><p>' . sprintf( __( '%d Transients Rows Cleared', 'axiscomposer' ), $rows + $rows2 ) . '</p></div>';

				break;
				case 'reset_roles' :
					// Remove then re-add caps and roles
					AC_Install::remove_roles();
					AC_Install::create_roles();

					echo '<div class="updated"><p>' . __( 'Roles successfully reset', 'axiscomposer' ) . '</p></div>';
				break;
				default :
					$action = esc_attr( $_GET['action'] );
					if ( isset( $tools[ $action ]['callback'] ) ) {
						$callback = $tools[ $action ]['callback'];
						$return = call_user_func( $callback );
						if ( $return === false ) {
							if ( is_array( $callback ) ) {
								echo '<div class="error"><p>' . sprintf( __( 'There was an error calling %s::%s', 'axiscomposer' ), get_class( $callback[0] ), $callback[1] ) . '</p></div>';

							} else {
								echo '<div class="error"><p>' . sprintf( __( 'There was an error calling %s', 'axiscomposer' ), $callback ) . '</p></div>';
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
					echo '<div class="error"><p>' . __( 'Failed to install/update the translation:', 'axiscomposer' ) . ' ' . __( 'Seems you don\'t have permission to do this!', 'axiscomposer' ) . '</p></div>';
					break;
				case 3 :
					echo '<div class="error"><p>' . __( 'Failed to install/update the translation:', 'axiscomposer' ) . ' ' . sprintf( __( 'An authentication error occurred while updating the translation. Please try again or configure your %sUpgrade Constants%s.', 'axiscomposer' ), '<a href="http://codex.wordpress.org/Editing_wp-config.php#WordPress_Upgrade_Constants">', '</a>' ) . '</p></div>';
					break;
				case 4 :
					echo '<div class="error"><p>' . __( 'Failed to install/update the translation:', 'axiscomposer' ) . ' ' . __( 'Sorry but there is no translation available for your language =/', 'axiscomposer' ) . '</p></div>';
					break;

				default :
					// Force WordPress find for new updates and hide the AxisComposer translation update
					set_site_transient( 'update_plugins', null );

					echo '<div class="updated"><p>' . __( 'Translations installed/updated successfully!', 'axiscomposer' ) . '</p></div>';
					break;
			}
		}

		// Display message if settings settings have been saved
		if ( isset( $_REQUEST['settings-updated'] ) ) {
			echo '<div class="updated"><p>' . __( 'Your changes have been saved.', 'axiscomposer' ) . '</p></div>';
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

		if ( get_locale() !== 'en_US' ) {
			$tools['translation_upgrade'] = array(
				'name'    => __( 'Translation Upgrade', 'axiscomposer' ),
				'button'  => __( 'Force Translation Upgrade', 'axiscomposer' ),
				'desc'    => __( '<strong class="red">Note:</strong> This option will force the translation upgrade for your language if a translation is available.', 'axiscomposer' ),
			);
		}

		return apply_filters( 'axiscomposer_debug_tools', $tools );
	}
}
