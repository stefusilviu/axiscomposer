<?php
/**
 * Admin View: Page - Sidebars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<script id="axisbuilder-add-widget-tmpl" type="text/html">
	<form class="axisbuilder-add-widget" action="" method="post">
		<?php wp_nonce_field( 'delete-custom-sidebar', '_axisbuilder_custom_sidebar_nonce', false ); ?>
		<h3><?php _e( 'Custom Widget Area Builder', 'axisbuilder' ) ?></h3>
		<input name="axisbuilder-add-widget" type="text" id="axisbuilder-add-widget" class="widefat" autocomplete="off" value="" placeholder = "<?php _e( 'Enter New Widget Area Name', 'axisbuilder' ) ?>" />
		<button id="create" class="button button-primary button-large create">
			<?php _e( 'Add Widget Area', 'axisbuilder' ); ?>
		</button>
	</form>
</script>
