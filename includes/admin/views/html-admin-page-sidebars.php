<?php
/**
 * Admin View: Page - Sidebars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<script type="text/template" id="tmpl-axisbuilder-form-delete-sidebar">
	<form class="axisbuilder-add-sidebar" action="widgets.php" method="post">
		<h3><?php _e( 'Custom Widget Area Builder', 'axisbuilder' ) ?></h3>
		<input name="axisbuilder-add-sidebar" type="text" id="axisbuilder-add-sidebar" class="widefat" autocomplete="off" value="" placeholder="<?php _e( 'Enter New Widget Area Name', 'axisbuilder' ) ?>" />
		<button id="create" class="button button-primary button-large create">
			<?php _e( 'Add Widget Area', 'axisbuilder' ); ?>
		</button>
	</form>
</script>

<script type="text/template" id="tmpl-axisbuilder-modal-delete-sidebar">
	<div class="axisbuilder-backbone-modal">
		<div class="axisbuilder-backbone-modal-content modal-animation">
			<section class="axisbuilder-backbone-modal-main" role="main">
				<header class="axisbuilder-backbone-modal-header">
					<a class="modal-close modal-close-link" href="#"><span class="close-icon"><span class="screen-reader-text">Close media panel</span></span></a>
					<h1><?php _e( 'Last warning, are you sure?', 'axisbuilder' ); ?></h1>
				</header>
				<article class="axisbuilder-backbone-modal-article">
					<form action="" method="post">
						<div class="message">
							<?php printf( __( 'Permanantly delete Sidebar and store all widgets in Inactive Sidebar. %sAre you sure you want to delete the sidebar now?%s', 'axisbuilder' ), '<br /><mark class="no">', '</mark>' ); ?>
						</div>
					</form>
				</article>
				<footer class="axisbuilder-backbone-modal-footer">
					<div class="inner">
						<button id="btn-ok" class="button button-large button-primary"><?php _e( 'Delete' , 'axisbuilder' ); ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="axisbuilder-backbone-modal-backdrop modal-close">&nbsp;</div>
</script>
