<?php
/**
 * Admin View: Notice - Install
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated axiscomposer-message ac-connect">
	<p><?php _e( '<strong>Welcome to AxisComposer</strong> &#8211; You&lsquo;re almost ready to start creating beautiful content :)', 'axiscomposer' ); ?></p>
	<p class="submit"><a href="<?php echo esc_url( admin_url( 'admin.php?page=ac-settings' ) ); ?>" class="button-primary"><?php _e( 'Run the Setup Wizard', 'axiscomposer' ); ?></a> <a class="button-secondary skip" href="<?php echo esc_url( add_query_arg( 'ac-hide-notice', 'install' ) ); ?>"><?php _e( 'Skip Setup', 'axiscomposer' ); ?></a></p>
</div>
