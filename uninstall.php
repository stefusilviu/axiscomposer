<?php
/**
 * AxisBuilder Uninstall
 *
 * @package     AxisBuilder/Uninstaller
 * @category    Core
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // Exit if uninstall is not called from WordPress
}

global $wpdb;

// Delete options
$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'axisbuilder_%';" );
