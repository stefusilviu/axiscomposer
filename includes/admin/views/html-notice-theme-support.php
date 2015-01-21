<?php
/**
 * Admin View: Notice - Theme Support
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="message" class="updated axisbuilder-message">
	<p><?php printf( __( '<strong>Your theme does not declare Axis Builder support</strong> &#8211; Please read our integration guide or check out our %sofficial themes%s which are designed specifically for use with Axis Builder.', 'axisbuilder' ), '<a href="http://axisthemes.com/themes">', '</a>' ); ?></p>
	<p class="submit">
		<a href="http://axisthemes.com/themes" class="button-primary" target="_blank"><?php _e( 'Official Themes', 'axisbuilder' ); ?></a>
		<a href="<?php echo esc_url( apply_filters( 'axisbuilder_plugin_theme_compatibility', 'http://docs.axisthemes.com/documentation/plugins/axis-builder/third-party-custom-theme-compatibility/', 'theme-compatibility' ) ); ?>" class="button-secondary"><?php _e( 'Theme Integration Guide', 'axisbuilder' ); ?></a>
		<a class="button-secondary skip" href="<?php echo esc_url( add_query_arg( 'axisbuilder-hide-notice', 'theme_support' ) ); ?>"><?php _e( 'Dismiss this notice', 'axisbuilder' ); ?></a>
	</p>
</div>
