<?php
/**
 * Admin View: Custom Notices
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated axiscomposer-message">
	<a class="axiscomposer-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'ac-hide-notice', $notice ), 'axiscomposer_hide_notices_nonce', '_ac_notice_nonce' ) ); ?>"><?php _e( 'Dismiss', 'axiscomposer' ); ?></a>
	<?php echo wp_kses_post( wpautop( $notice_html ) ); ?>
</div>

