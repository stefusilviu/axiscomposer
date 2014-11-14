<?php
/**
 * Admin View: Page - Sidebars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<script id="axis-add-widget-template" type="text/html">
	<form class="axis-add-widget" action="" method="post">
		<?php wp_nonce_field( 'delete-custom-sidebar', '_axis_nonce_custom_sidebar', false ); ?>
		<h3><?php _e( 'Custom Widget Area Builder', 'axisbuilder' ) ?></h3>
		<input name="axis-add-widget" type="text" id="axis-add-widget" class="widefat" autocomplete="off" value="" placeholder = "<?php _e( 'Enter New Widget Area Name', 'axisbuilder' ) ?>" />
		<button id="create" class="button button-primary button-large create">
			<?php _e( 'Add Widget Area', 'axisbuilder' ); ?>
		</button>
	</form>
</script>
