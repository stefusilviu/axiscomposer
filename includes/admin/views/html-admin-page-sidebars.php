<?php
/**
 * Admin View: Page - Sidebars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<script id="axisbuilder-sidebar-tmpl" type="text/html">
	<form class="axisbuilder-add-sidebar" action="widgets.php" method="post">
		<h3><?php _e( 'Custom Widget Area Builder', 'axisbuilder' ) ?></h3>
		<input name="axisbuilder-add-sidebar" type="text" id="axisbuilder-add-sidebar" class="widefat" autocomplete="off" value="" placeholder="<?php _e( 'Enter New Widget Area Name', 'axisbuilder' ) ?>" />
		<button id="create" class="button button-primary button-large create">
			<?php _e( 'Add Widget Area', 'axisbuilder' ); ?>
		</button>
	</form>
</script>
