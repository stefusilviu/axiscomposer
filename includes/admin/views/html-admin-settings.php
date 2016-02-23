<?php
/**
 * Admin View: Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap axiscomposer">
	<form method="<?php echo esc_attr( apply_filters( 'axiscomposer_settings_form_method_tab_' . $current_tab, 'post' ) ); ?>" id="mainform" action="" enctype="multipart/form-data">
		<div class="icon32 icon32-axiscomposer-settings" id="icon-axiscomposer"><br /></div>
		<h1 class="screen-reader-text"><?php echo esc_html( $tabs[ $current_tab ] ); ?></h1>
		<h2 class="nav-tab-wrapper axis-nav-tab-wrapper">
			<?php
				foreach ( $tabs as $name => $label ) {
					echo '<a href="' . admin_url( 'admin.php?page=ac-settings&tab=' . $name ) . '" class="nav-tab ' . ( $current_tab == $name ? 'nav-tab-active' : '' ) . '">' . $label . '</a>';
				}

				do_action( 'axiscomposer_settings_tabs' );
			?>
		</h2>
		<?php
			do_action( 'axiscomposer_sections_' . $current_tab );

			self::show_messages();

			do_action( 'axiscomposer_settings_' . $current_tab );
		?>
		<p class="submit">
			<?php if ( ! isset( $GLOBALS['hide_save_button'] ) ) : ?>
				<input name="save" class="button-primary" type="submit" value="<?php esc_attr_e( 'Save Changes', 'axiscomposer' ); ?>" />
			<?php endif; ?>
			<input type="hidden" name="subtab" id="last_tab" />
			<?php wp_nonce_field( 'axiscomposer-settings' ); ?>
		</p>
	</form>
</div>
