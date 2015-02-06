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

global $wpdb;

$status_options = apply_filters( 'axisbuilder_status_options', array( 'uninstall_data' => false ) );

// Roles + caps
include_once( 'includes/class-builder-install.php' );
AB_Install::remove_roles();

// Delete options
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'axisbuilder_%';" );

if ( ! empty( $status_options['uninstall_data'] ) ) {
	// Delete posts + data
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'portfolio' );" );
	$wpdb->query( "DELETE FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE wp.ID IS NULL;" );
}
