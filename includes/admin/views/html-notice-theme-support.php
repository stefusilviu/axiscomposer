<?php
/**
 * Admin View: Notice - Theme Support
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated axiscomposer-message ac-connect">
	<p><?php printf( __( '<strong>Your theme does not declare AxisComposer support</strong> &#8211; Please read our %sintegration%s guide or check out our %sofficial themes%s which are designed specifically for use with AxisComposer.', 'axiscomposer' ), '<a href="' . esc_url( apply_filters( 'axiscomposer_docs_url', 'http://docs.axisthemes.com/document/third-party-custom-theme-compatibility/', 'theme-compatibility' ) ) . '">', '</a>', '<a href="' . esc_url( 'http://axisthemes.com/themes/' ) . '">', '</a>' ); ?></p>
	<p class="submit">
		<a href="http://axisthemes.com/themes" class="button-primary" target="_blank"><?php _e( 'Official Themes', 'axiscomposer' ); ?></a>
		<a href="<?php echo esc_url( apply_filters( 'axiscomposer_docs_url', 'http://docs.axisthemes.com/document/plugins/axiscomposer/third-party-custom-theme-compatibility/', 'theme-compatibility' ) ); ?>" class="button-secondary" target="_blank"><?php _e( 'Theme Integration Guide', 'axiscomposer' ); ?></a>
		<a class="button-secondary skip" href="<?php echo esc_url( add_query_arg( 'ac-hide-notice', 'theme_support' ) ); ?>"><?php _e( 'Hide This Notice', 'axiscomposer' ); ?></a>
	</p>
</div>
