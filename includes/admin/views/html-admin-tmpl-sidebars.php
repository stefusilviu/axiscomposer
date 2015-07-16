<?php
/**
 * Admin View: Template - Sidebars
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<script type="text/template" id="tmpl-axiscomposer-form-create-sidebar">
	<form class="axiscomposer-add-sidebar" action="<?php echo self_admin_url( 'widgets.php' ); ?>" method="post">
		<h3><?php _e( 'Custom Widget Area Builder', 'axiscomposer' ) ?></h3>
		<?php wp_nonce_field( 'axiscomposer_add_sidebar', '_ac_sidebar_nonce' ); ?>
		<input name="axiscomposer-add-sidebar" type="text" id="axiscomposer-add-sidebar" class="widefat" autocomplete="off" value="" placeholder="<?php _e( 'Enter New Widget Area Name', 'axiscomposer' ) ?>" />
		<button id="create" class="button button-primary button-large create">
			<?php _e( 'Add Widget Area', 'axiscomposer' ); ?>
		</button>
	</form>
</script>

<script type="text/template" id="tmpl-axiscomposer-modal-delete-sidebar">
	<div class="ac-backbone-modal">
		<div class="ac-backbone-modal-content modal-animation">
			<section class="ac-backbone-modal-main" role="main">
				<header class="ac-backbone-modal-header">
					<h1><?php _e( 'Last warning, are you sure?', 'axiscomposer' ); ?></h1>
					<button class="modal-close modal-close-link dashicons dashicons-no-alt">
						<span class="screen-reader-text">Close modal panel</span>
					</button>
				</header>
				<article class="ac-backbone-modal-article">
					<form action="" method="post">
						<div class="message">
							<?php printf( __( 'Delete this Sidebar Permanently and store all widgets in Inactive Sidebar. %sAre you positive you want to delete this Sidebar?%s', 'axiscomposer' ), '<br /><mark class="no">', '</mark>' ); ?>
						</div>
					</form>
				</article>
				<footer class="ac-backbone-modal-footer">
					<div class="inner">
						<button id="btn-ok" class="button button-large button-primary"><?php _e( 'Delete' , 'axiscomposer' ); ?></button>
					</div>
				</footer>
			</section>
		</div>
	</div>
	<div class="ac-backbone-modal-backdrop modal-close"></div>
</script>
