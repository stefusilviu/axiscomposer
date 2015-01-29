<?php
/**
 * Admin View: Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="wrap axisbuilder">
	<form method="<?php echo esc_attr( apply_filters( 'axisbuilder_settings_form_method_tab_' . $current_tab, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">
		<div class="icon32 icon32-axisbuilder-settings" id="icon-axisbuilder"><br /></div><h2 class="nav-tab-wrapper axisbuilder-nav-tab-wrapper">
			<?php
				foreach ( $tabs as $name => $label ) {
					echo '<a href="' . admin_url( 'admin.php?page=axisbuilder-settings&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
				}

				do_action( 'axisbuilder_settings_tabs' );
			?>
		</h2>

		<?php
			do_action( 'axisbuilder_sections_' . $current_tab );
			do_action( 'axisbuilder_settings_' . $current_tab );
			do_action( 'axisbuilder_settings_tabs_' . $current_tab ); // @deprecated hook
		?>

		<p class="submit">
			<?php if ( ! isset( $GLOBALS['hide_save_button'] ) ) : ?>
				<input name="save" class="button-primary" type="submit" value="<?php _e( 'Save changes', 'axisbuilder' ); ?>" />
			<?php endif; ?>
			<input type="hidden" name="subtab" id="last_tab" />
			<?php wp_nonce_field( 'axisbuilder-settings' ); ?>
		</p>
	</form>
</div>
