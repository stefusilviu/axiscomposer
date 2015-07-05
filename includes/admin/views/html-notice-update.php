<?php
/**
 * Admin View: Notice - Update
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated axiscomposer-message ac-connect">
	<p><?php _e( '<strong>AxisComposer Data Update Required</strong> &#8211; We just need to update your install to the latest version', 'axiscomposer' ); ?></p>
	<p class="submit"><a href="<?php echo esc_url( add_query_arg( 'do_update_axiscomposer', 'true', admin_url( 'admin.php?page=ac-settings' ) ) ); ?>" class="ac-update-now button-primary"><?php _e( 'Run the updater', 'axiscomposer' ); ?></a></p>
</div>
<script type="text/javascript">
	jQuery( '.ac-update-now' ).click( 'click', function() {
		var answer = confirm( '<?php _e( 'It is strongly recommended that you backup your database before proceeding. Are you sure you wish to run the updater now?', 'axiscomposer' ); ?>' );
		return answer;
	});
</script>
