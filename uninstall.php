<?php
/**
 * AxisBuilder Uninstall
 *
 * Uninstalling AxisBuilder deletes user roles and options.
 *
 * @package     AxisBuilder/Uninstaller
 * @category    Core
 * @author      AxisThemes
 * @since       1.0.0
 */

if( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$status_options = get_option( 'axisbuilder_status_options', array() ) );

if ( ! empty( $status_options['uninstall_data'] ) ) {

	global $wpdb;

	// Roles + caps
	include_once( 'includes/class-builder-install.php' );
	AB_Install::remove_roles();

	// Delete options
	$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'axisbuilder_%';" );

	// Delete posts + data
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'portfolio' );" );
	$wpdb->query( "DELETE FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );
}
