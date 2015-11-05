<?php
/**
 * Admin View: Page - Status Tools
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form method="post" action="options.php">
	<?php settings_fields( 'axiscomposer_status_settings_fields' ); ?>
	<?php $options = wp_parse_args( get_option( 'axiscomposer_status_options', array() ), array( 'uninstall_data' => 0, 'builder_debug_mode' => 0 ) ); ?>
	<table class="ac_status_table widefat" cellspacing="0">
		<thead class="tools">
			<tr>
				<th colspan="2"><?php _e( 'Tools', 'axiscomposer' ); ?></th>
			</tr>
		</thead>
		<tbody class="tools">
			<?php foreach ( $tools as $action => $tool ) : ?>
				<tr class="<?php echo sanitize_html_class( $action ); ?>">
					<td><?php echo esc_html( $tool['name'] ); ?></td>
					<td>
						<p>
							<a href="<?php echo wp_nonce_url( admin_url('admin.php?page=ac-status&tab=tools&action=' . $action ), 'debug_action' ); ?>" class="button <?php echo esc_attr( $action ); ?>"><?php echo esc_html( $tool['button'] ); ?></a>
						</p>
						<p>
							<span class="description"><?php echo wp_kses_post( $tool['desc'] ); ?></span>
						</p>
					</td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td><?php _e( 'Builder Debug Mode', 'axiscomposer' ); ?></td>
				<td>
					<p>
						<label><input type="checkbox" class="checkbox" name="axiscomposer_status_options[builder_debug_mode]" value="1" <?php checked( '1', $options['builder_debug_mode'] ); ?> /> <?php _e( 'Enabled', 'axiscomposer' ); ?></label>
					</p>
					<p>
						<span class="description"><?php _e( 'This tool will log all the shortcode attributes and content via elements textarea field for debugging purposes.', 'axiscomposer' ); ?></span>
					</p>
				</td>
			</tr>
			<tr>
				<td><?php _e( 'Remove All Data', 'axiscomposer' ); ?></td>
				<td>
					<p>
						<label><input type="checkbox" class="checkbox" name="axiscomposer_status_options[uninstall_data]" value="1" <?php checked( '1', $options['uninstall_data'] ); ?> /> <?php _e( 'Enabled', 'axiscomposer' ); ?></label>
					</p>
					<p>
						<span class="description"><?php _e( 'This tool will remove all AxisComposer and Portfolio data when using the "Delete" link on the plugins screen. It will also remove any setting/option prepended with "axiscomposer_" so may also affect installed AxisComposer Extensions.', 'axiscomposer' ); ?></span>
					</p>
				</td>
			</tr>
		</tbody>
	</table>
	<p class="submit">
		<input type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', 'axiscomposer' ) ?>" />
	</p>
</form>
