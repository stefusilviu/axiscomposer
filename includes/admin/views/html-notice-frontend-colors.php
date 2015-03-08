<?php
/**
 * Admin View: Notice - Frontend Colors
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$plugin_slug = 'axisbuilder-colors';

if ( current_user_can( 'install_plugins' ) ) {
	$url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . $plugin_slug ), 'install-plugin_' . $plugin_slug );
} else {
	$url = 'http://wordpress.org/plugins/' . $plugin_slug;
}

?>
<div id="message" class="updated axisbuilder-message">
	<p><?php _e( '<strong>The Frontend Style Options</strong> &#8211; If you want to continue editing the colors of your Page Builder we recommended that you install the AxisBuilder Colors plugin from WordPress.org.', 'axisbuilder' ); ?></p>
	<p class="submit"><a href="<?php echo esc_url( $url ); ?>" class="button-primary"><?php _e( 'Install the AxisBuilder Colors plugin', 'axisbuilder' ); ?></a> <a class="button-secondary skip" href="<?php echo esc_url( add_query_arg( 'axisbuilder-hide-notice', 'frontend_colors' ) ); ?>"><?php _e( 'Hide This Notice', 'axisbuilder' ); ?></a></p>
</div>
