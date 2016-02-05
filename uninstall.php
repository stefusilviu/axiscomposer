<?php
/**
 * AxisComposer Uninstall
 *
 * Uninstalling the plugin deletes user roles and options.
 *
 * @author   AxisThemes
 * @category Core
 * @package  AxisComposer/Uninstaller
 * @version  1.0.0
 */

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$status_options = get_option( 'axiscomposer_status_options', array() );

if ( ! empty( $status_options['uninstall_data'] ) ) {
	// Roles + caps.
	include_once( 'includes/class-ac-install.php' );
	AC_Install::remove_roles();

	// Delete options.
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'axiscomposer\_%';" );

	// Delete posts + data.
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'portfolio' );" );
	$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );
}
