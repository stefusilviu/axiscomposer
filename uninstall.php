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

global $wpdb, $wp_version;

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

	// Delete terms if > WP 4.2 (term splitting was added in 4.2)
	if ( version_compare( $wp_version, '4.2', '>=' ) ) {
		// Delete term taxonomies
		foreach ( array( 'portfolio_cat', 'portfolio_tag', 'portfolio_type' ) as $taxonomy ) {
			$wpdb->delete(
				$wpdb->term_taxonomy,
				array(
					'taxonomy' => $taxonomy,
				)
			);
		}

		// Delete orphan relationships
		$wpdb->query( "DELETE tr FROM {$wpdb->term_relationships} tr LEFT JOIN {$wpdb->posts} posts ON posts.ID = tr.object_id WHERE posts.ID IS NULL;" );

		// Delete orphan terms
		$wpdb->query( "DELETE t FROM {$wpdb->terms} t LEFT JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id WHERE tt.term_id IS NULL;" );

		// Delete orphan term meta
		if ( ! empty( $wpdb->termmeta ) ) {
			$wpdb->query( "DELETE tm FROM {$wpdb->termmeta} tm LEFT JOIN {$wpdb->term_taxonomy} tt ON tm.term_id = tt.term_id WHERE tt.term_id IS NULL;" );
		}
	}

	// Clear any cached data that has been removed.
	wp_cache_flush();
}
