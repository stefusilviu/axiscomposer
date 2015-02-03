<?php
/**
 * AxisBuilder Uninstall
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

// Roles + caps
include_once( 'includes/class-builder-install.php' );
AB_Install::remove_roles();

// Delete options
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'axisbuilder_%';" );
