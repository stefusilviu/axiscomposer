<?php
/**
 * Admin View: Notice - Translation Upgrade
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( isset( $_GET['action'] ) && 'hide_ab_translation_upgrade' == $_GET['action'] ) {
	return;
}

?>

<div id="message" class="updated axisbuilder-message">
	<p><?php printf( __( '<strong>Axis Builder Translation Available</strong> &#8211; Install or update your <code>%s</code> translation to version <code>%s</code>.', 'axisbuilder' ), get_locale(), AB_VERSION ); ?></p>

	<p>
		<?php if ( is_multisite() ) : ?>
			<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=ab-status&tab=tools&action=ab_translation_upgrade' ), 'debug_action' ); ?>" class="button-primary"><?php _e( 'Update Translation', 'axisbuilder' ); ?></a>
		<?php else : ?>
			<a href="<?php echo wp_nonce_url( add_query_arg( array( 'action' => 'do-translation-upgrade' ), admin_url( 'update-core.php' ) ), 'upgrade-translations' ); ?>" class="button-primary"><?php _e( 'Update Translation', 'axisbuilder' ); ?></a>
			<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=ab-status&tab=tools&action=ab_translation_upgrade' ), 'debug_action' ); ?>" class="button-primary"><?php _e( 'Force Update Translation', 'axisbuilder' ); ?></a>
		<?php endif; ?>
		<a href="<?php echo wp_nonce_url( admin_url( 'admin.php?page=ab-status&tab=tools&action=dismiss_ab_translation_upgrade' ), 'debug_action' ); ?>" class="button"><?php _e( 'Dismiss this notice', 'axisbuilder' ); ?></a>
	</p>
</div>
