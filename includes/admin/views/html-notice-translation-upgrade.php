<?php
/**
 * Admin View: Notice - Translation Upgrade
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated axisbuilder-message">
	<p><?php printf( __( '<strong>AxisBuilder Translation Available</strong> &#8211; Install or update your <code>%s</code> translation to version <code>%s</code>.', 'axisbuilder' ), get_locale(), AB_VERSION ); ?></p>

	<p>
		<?php if ( is_multisite() ) : ?>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=axisbuilder-status&tab=tools&action=translation_upgrade' ), 'debug_action' ) ); ?>" class="button-primary"><?php _e( 'Update Translation', 'axisbuilder' ); ?></a>
		<?php else : ?>
			<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'do-translation-upgrade' ), admin_url( 'update-core.php' ) ), 'upgrade-translations' ) ); ?>" class="button-primary"><?php _e( 'Update Translation', 'axisbuilder' ); ?></a>
			<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=axisbuilder-status&tab=tools&action=translation_upgrade' ), 'debug_action' ) ); ?>" class="button-secondary"><?php _e( 'Force Update Translation', 'axisbuilder' ); ?></a>
		<?php endif; ?>
		<a href="<?php echo esc_url( add_query_arg( 'axisbuilder-hide-notice', 'translation_upgrade' ) ); ?>" class="button-secondary skip"><?php _e( 'Hide This Notice', 'axisbuilder' ); ?></a>
	</p>
</div>
