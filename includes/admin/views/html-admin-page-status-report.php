<?php
/**
 * Admin View: Page - Status Report
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="updated axisbuilder-message">
	<p><?php _e( 'Please copy and paste this information in your ticket when contacting support:', 'axisbuilder' ); ?> </p>
	<p class="submit"><a href="#" class="button-primary debug-report"><?php _e( 'Get System Report', 'axisbuilder' ); ?></a></p>
	<div id="debug-report">
		<textarea readonly="readonly"></textarea>
		<p class="submit"><button id="copy-for-axis-support" class="button-primary" href="#" data-tip="<?php _e( 'Copied!', 'axisbuilder' ); ?>"><?php _e( 'Copy for Support', 'axisbuilder' ); ?></button></p>
	</div>
</div>

<table class="axisbuilder-list-table widefat" cellspacing="0" id="status">
	<thead>
		<tr>
			<th colspan="3"><?php _e( 'WordPress Environment', 'axisbuilder' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php _e( 'Home URL', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The URL of your site\'s homepage.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
			<td><?php echo home_url(); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Site URL', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The root URL of your site.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
			<td><?php echo site_url(); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'AB Version', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The version of Axis Builder installed on your site.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
			<td><?php echo esc_html( AB()->version ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'AB Logging', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Several Axis Builder extension can write logs which makes debugging problems easier. The directory must be writable for this to happen.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
			<td>
			<?php
				if ( @fopen( AB_LOG_DIR . 'test-log.log', 'a' ) ) {
					printf( '<mark class="yes">' . __( 'Log directory (%s) is writable.', 'axisbuilder' ) . '</mark>', AB_LOG_DIR );
				} else {
					printf( '<mark class="error">' . __( 'Log directory (<code>%s</code>) is not writable. To allow logging, make this writable or define a custom <code>AB_LOG_DIR</code>.', 'axisbuilder' ) . '</mark>', AB_LOG_DIR );
				}
			?>
			</td>
		</tr>
		<tr>
			<td><?php _e( 'WP Version', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The version of WordPress installed on your site.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
			<td><?php bloginfo( 'version' ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Multisite Enabled', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Whether or not you have WordPress Multisite enabled.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
			<td><?php if ( is_multisite() ) echo __( 'Yes', 'axisbuilder' ); else echo __( 'No', 'axisbuilder' ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Active Plugins', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The number of active plugins on your website.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
			<td><?php echo count( (array) get_option( 'active_plugins' ) ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Memory Limit', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The maximum amount of memory (RAM) that your site can use at one time.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
			<td>
			<?php
				$memory = ab_let_to_num( WP_MEMORY_LIMIT );

				if ( $memory < 67108864 ) {
					echo '<mark class="error">' . sprintf( __( '%s - We recommend setting memory to at least 64MB. See: <a href="%s">Increasing memory allocated to PHP</a>', 'axisbuilder' ), size_format( $memory ), 'http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP' ) . '</mark>';
				} else {
					echo '<mark class="yes">' . size_format( $memory ) . '</mark>';
				}
			?>
			</td>
		</tr>
		<tr>
			<td><?php _e( 'WP Debug Mode', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Displays whether or not WordPress is in Debug Mode.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
			<td><?php if ( defined('WP_DEBUG') && WP_DEBUG ) echo '<mark class="yes">' . __( 'Yes', 'axisbuilder' ) . '</mark>'; else echo '<mark class="no">' . __( 'No', 'axisbuilder' ) . '</mark>'; ?></td>
		</tr>
		<tr>
			<td><?php _e( 'WP Language', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The current language used by WordPress. Default = English', 'axisbuilder' ) . '">[?]</a>'; ?></td>
			<td><?php echo get_locale() ?></td>
		</tr>
	</tbody>
</table>
<table class="axisbuilder-list-table widefat" cellspacing="0" id="status">
	<thead>
		<tr>
			<th colspan="3"><?php _e( 'Server Environment', 'axisbuilder' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td><?php _e( 'Web Server Info', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Information about the web server that is currently hosting your site.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
			<td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'PHP Version', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The version of PHP installed on your hosting server.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
			<td><?php if ( function_exists( 'phpversion' ) ) echo esc_html( phpversion() ); ?></td>
		</tr>
		<?php if ( function_exists( 'ini_get' ) ) : ?>
			<tr>
				<td><?php _e('PHP Post Max Size', 'axisbuilder' ); ?>:</td>
				<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The largest filesize that can be contained in one post.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
				<td><?php echo size_format( ab_let_to_num( ini_get('post_max_size') ) ); ?></td>
			</tr>
			<tr>
				<td><?php _e('PHP Time Limit', 'axisbuilder' ); ?>:</td>
				<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups)', 'axisbuilder' ) . '">[?]</a>'; ?></td>
				<td><?php echo ini_get('max_execution_time'); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'PHP Max Input Vars', 'axisbuilder' ); ?>:</td>
				<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The maximum number of variables your server can use for a single function to avoid overloads.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
				<td><?php echo ini_get('max_input_vars'); ?></td>
			</tr>
			<tr>
				<td><?php _e( 'SUHOSIN Installed', 'axisbuilder' ); ?>:</td>
				<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Suhosin is an advanced protection system for PHP installations. It was designed to protect your servers on the one hand against a number of well known problems in PHP applications and on the other hand against potential unknown vulnerabilities within these applications or the PHP core itself. If enabled on your server, Suhosin may need to be configured to increase its data submission limits.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
				<td><?php echo extension_loaded( 'suhosin' ) ? __( 'Yes', 'axisbuilder' ) : __( 'No', 'axisbuilder' ); ?></td>
			</tr>
		<?php endif; ?>
		<tr>
			<td><?php _e( 'MySQL Version', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The version of MySQL installed on your hosting server.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
			<td>
				<?php
				/** @global wpdb $wpdb */
				global $wpdb;
				echo $wpdb->db_version();
				?>
			</td>
		</tr>
		<tr>
			<td><?php _e( 'Max Upload Size', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The largest filesize that can be uploaded to your WordPress installation.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
			<td><?php echo size_format( wp_max_upload_size() ); ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Default Timezone', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The default timezone for your server.', 'axisbuilder' ) . '">[?]</a>'; ?></td>
			<td><?php
				$default_timezone = date_default_timezone_get();
				if ( 'UTC' !== $default_timezone ) {
					echo '<mark class="error">' . sprintf( __( 'Default timezone is %s - it should be UTC', 'axisbuilder' ), $default_timezone ) . '</mark>';
				} else {
					echo '<mark class="yes">' . sprintf( __( 'Default timezone is %s', 'axisbuilder' ), $default_timezone ) . '</mark>';
				} ?>
			</td>
		</tr>
		<?php
			$posting = array();

			// WP Remote Get Check
			$posting['wp_remote_get']['name'] = __( 'Remote Get', 'axisbuilder');
			$posting['wp_remote_get']['help'] = '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Axis Builder plugins may use this method of communication when checking for plugin updates.', 'axisbuilder' ) . '">[?]</a>';

			$response = wp_remote_get( 'http://www.woothemes.com/wc-api/product-key-api?request=ping&network=' . ( is_multisite() ? '1' : '0' ) );

			if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
				$posting['wp_remote_get']['note']    = __( 'wp_remote_get() was successful - The Axis Builder plugin updater should work.', 'axisbuilder' );
				$posting['wp_remote_get']['success'] = true;
			} else {
				$posting['wp_remote_get']['note']    = __( 'wp_remote_get() failed. The Axis Builder plugin updater won\'t work with your server. Contact your hosting provider.', 'axisbuilder' ) . ' ' . $response->get_error_message();
				if ( $response->get_error_message() ) {
					$posting['wp_remote_get']['note'] .= ' ' . sprintf( __( 'Error: %s', 'axisbuilder' ), $response->get_error_message() );
				}
				$posting['wp_remote_get']['success'] = false;
			}

			$posting = apply_filters( 'axisbuilder_debug_posting', $posting );

			foreach( $posting as $post ) { $mark = ( isset( $post['success'] ) && $post['success'] == true ) ? 'yes' : 'error';
				?>
				<tr>
					<td><?php echo esc_html( $post['name'] ); ?>:</td>
					<td><?php echo isset( $post['help'] ) ? $post['help'] : ''; ?></td>
					<td>
						<mark class="<?php echo $mark; ?>">
							<?php echo wp_kses_data( $post['note'] ); ?>
						</mark>
					</td>
				</tr>
				<?php
			}
		?>
	</tbody>
</table>
<table class="axisbuilder-list-table widefat" cellspacing="0" id="status">
	<thead>
		<tr>
			<th colspan="3"><?php _e( 'Active Plugins', 'axisbuilder' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}

		foreach ( $active_plugins as $plugin ) {

			$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
			$dirname        = dirname( $plugin );
			$version_string = '';
			$network_string = '';

			if ( ! empty( $plugin_data['Name'] ) ) {

				// link the plugin name to the plugin url if available
				$plugin_name = esc_html( $plugin_data['Name'] );

				if ( ! empty( $plugin_data['PluginURI'] ) ) {
					$plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . __( 'Visit plugin homepage' , 'axisbuilder' ) . '">' . $plugin_name . '</a>';
				}

				if ( strstr( $dirname, 'axis-builder' ) ) {

					if ( false === ( $version_data = get_transient( md5( $plugin ) . '_version_data' ) ) ) {
						$changelog = wp_remote_get( 'http://dzv365zjfbd8v.cloudfront.net/changelogs/' . $dirname . '/changelog.txt' );
						$cl_lines  = explode( "\n", wp_remote_retrieve_body( $changelog ) );
						if ( ! empty( $cl_lines ) ) {
							foreach ( $cl_lines as $line_num => $cl_line ) {
								if ( preg_match( '/^[0-9]/', $cl_line ) ) {

									$date         = str_replace( '.' , '-' , trim( substr( $cl_line , 0 , strpos( $cl_line , '-' ) ) ) );
									$version      = preg_replace( '~[^0-9,.]~' , '' ,stristr( $cl_line , "version" ) );
									$update       = trim( str_replace( "*" , "" , $cl_lines[ $line_num + 1 ] ) );
									$version_data = array( 'date' => $date , 'version' => $version , 'update' => $update , 'changelog' => $changelog );
									set_transient( md5( $plugin ) . '_version_data', $version_data, DAY_IN_SECONDS );
									break;
								}
							}
						}
					}

					if ( ! empty( $version_data['version'] ) && version_compare( $version_data['version'], $plugin_data['Version'], '>' ) ) {
						$version_string = ' &ndash; <strong style="color:red;">' . esc_html( sprintf( _x( '%s is available', 'Version info', 'axisbuilder' ), $version_data['version'] ) ) . '</strong>';
					}

					if ( $plugin_data['Network'] != false ) {
						$network_string = ' &ndash; <strong style="color:black;">' . __( 'Network enabled', 'axisbuilder' ) . '</strong>';
					}
				}

				?>
				<tr>
					<td><?php echo $plugin_name; ?></td>
					<td class="help">&nbsp;</td>
					<td><?php echo sprintf( _x( 'By %s', 'By author', 'axisbuilder' ), $plugin_data['Author'] ) . ' &ndash; ' . esc_html( $plugin_data['Version'] ) . $version_string . $network_string; ?></td>
				</tr>
				<?php
			}
		}
		?>
	</tbody>
</table>
<table class="axisbuilder-list-table widefat" cellspacing="0" id="status">
	<thead>
		<tr>
			<th colspan="3"><?php _e( 'Theme', 'axisbuilder' ); ?></th>
		</tr>
	</thead>
		<?php
		$active_theme = wp_get_theme();
		if ( $active_theme->{'Author URI'} == 'http://www.woothemes.com' ) :

			$theme_dir = substr( strtolower( str_replace( ' ','', $active_theme->Name ) ), 0, 45 );

			if ( false === ( $theme_version_data = get_transient( $theme_dir . '_version_data' ) ) ) :

				$theme_changelog = wp_remote_get( 'http://dzv365zjfbd8v.cloudfront.net/changelogs/' . $theme_dir . '/changelog.txt' );
				$cl_lines  = explode( "\n", wp_remote_retrieve_body( $theme_changelog ) );
				if ( ! empty( $cl_lines ) ) :

					foreach ( $cl_lines as $line_num => $cl_line ) {
						if ( preg_match( '/^[0-9]/', $cl_line ) ) :

							$theme_date         = str_replace( '.' , '-' , trim( substr( $cl_line , 0 , strpos( $cl_line , '-' ) ) ) );
							$theme_version      = preg_replace( '~[^0-9,.]~' , '' ,stristr( $cl_line , "version" ) );
							$theme_update       = trim( str_replace( "*" , "" , $cl_lines[ $line_num + 1 ] ) );
							$theme_version_data = array( 'date' => $theme_date , 'version' => $theme_version , 'update' => $theme_update , 'changelog' => $theme_changelog );
							set_transient( $theme_dir . '_version_data', $theme_version_data , DAY_IN_SECONDS );
							break;

						endif;
					}

				endif;

			endif;

		endif;
		?>
	<tbody>
		<tr>
			<td><?php _e( 'Theme Name', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The name of the current active theme.', 'axisbuilder'  ) . '">[?]</a>'; ?></td>
			<td><?php echo $active_theme->Name; ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Theme Version', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The installed version of the current active theme.', 'axisbuilder'  ) . '">[?]</a>'; ?></td>
			<td><?php
				echo $active_theme->Version;

				if ( ! empty( $theme_version_data['version'] ) && version_compare( $theme_version_data['version'], $active_theme->Version, '!=' ) ) {
					echo ' &ndash; <strong style="color:red;">' . $theme_version_data['version'] . ' ' . __( 'is available', 'axisbuilder' ) . '</strong>';
				}
			?></td>
		</tr>
		<tr>
			<td><?php _e( 'Theme Author URL', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The developer or plugin\'s URL.', 'axisbuilder'  ) . '">[?]</a>'; ?></td>
			<td><?php echo $active_theme->{'Author URI'}; ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Is Child Theme', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Displays whether or not the current theme is a child theme.', 'axisbuilder'  ) . '">[?]</a>'; ?></td>
			<td><?php
				echo is_child_theme() ? '<mark class="yes">' . __( 'Yes', 'axisbuilder' ) . '</mark>' : '<mark class="no">' . __( 'No', 'axisbuilder' ) . '</mark> &ndash; <mark class="error">' . sprintf( __( 'We recommend using a child theme. See: <a href="%s" target="_blank">How to create a child theme</a>', 'axisbuilder' ), 'http://codex.wordpress.org/Child_Themes' ) . '</mark>';
			?></td>
		</tr>
		<?php
		if( is_child_theme() ) :
			$parent_theme = wp_get_theme( $active_theme->Template );
		?>
		<tr>
			<td><?php _e( 'Parent Theme Name', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The name of the parent theme.', 'axisbuilder'  ) . '">[?]</a>'; ?></td>
			<td><?php echo $parent_theme->Name; ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Parent Theme Version', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The installed version of the parent theme.', 'axisbuilder'  ) . '">[?]</a>'; ?></td>
			<td><?php echo  $parent_theme->Version; ?></td>
		</tr>
		<tr>
			<td><?php _e( 'Parent Theme Author URL', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'The developer or plugi\'s URL', 'axisbuilder'  ) . '">[?]</a>'; ?></td>
			<td><?php echo $parent_theme->{'Author URI'}; ?></td>
		</tr>
		<?php endif ?>
		<tr>
			<td><?php _e( 'WooCommerce Support', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Displays whether or not the current active theme declare Axis Builder support.', 'axisbuilder'  ) . '">[?]</a>'; ?></td>
			<td><?php
				if ( ! current_theme_supports( 'axisbuilder' ) && ! in_array( $active_theme->template, ab_get_core_supported_themes() ) ) {
					echo '<mark class="error">' . __( 'Not Declared', 'axisbuilder' ) . '</mark>';
				} else {
					echo '<mark class="yes">' . __( 'Yes', 'axisbuilder' ) . '</mark>';
				}
			?></td>
		</tr>
			<td><?php _e( 'Has custom.css', 'axisbuilder' ); ?>:</td>
			<td class="help"><?php echo '<a href="#" class="help_tip" data-tip="' . esc_attr__( 'Displays whether or not the current active theme has the file custom.css.', 'axisbuilder'  ) . '">[?]</a>'; ?></td>
			<td><?php
				if ( file_exists( get_template_directory( $active_theme->template ) . '/custom.css' ) ) {
					echo '<mark class="yes">' . __( 'Yes', 'axisbuilder' ) . '</mark>';
				} else {
					echo '<mark class="no">' . __( 'No', 'axisbuilder' ) . '</mark>';
				}
			?></td>
		</tr>
	</tbody>
</table>

<script type="text/javascript">

	/**
	 * ab_strpad
	 *
	 * @param  {var} i string default
	 * @param  {var} l how many repeat s
	 * @param  {var} s string to repeat
	 * @param  {var} w where s should indent
	 * @return {var}   string modified
	 */
	jQuery.ab_strpad = function( i, l, s, w ) {
		var o = i.toString();
		if (! s) { s = '0'; }
		while ( o.length < l ) {
			// empty
			if ( w == 'undefined' ){
				o = s + o;
			} else {
				o = o + s;
			}
		}
		return o;
	};

	jQuery( 'a.help_tip' ).click(function() { return false; });

	jQuery( 'a.debug-report' ).click(function(){

		var report = "";

		jQuery( '#status thead, #status tbody' ).each(function(){

			if ( jQuery( this ).is('thead') ) {

				report = report + "\n### " + jQuery.trim( jQuery( this ).text() ) + " ###\n\n";

			} else {

				jQuery('tr', jQuery( this ) ).each(function(){

					var the_name    = jQuery.ab_strpad( jQuery.trim( jQuery( this ).find( 'td:eq(0)' ).text() ), 25, ' ' );
					var the_value   = jQuery.trim( jQuery( this ).find( 'td:eq(2)' ).text() );
					var value_array = the_value.split( ', ' );

					if ( value_array.length > 1 ) {

						// If value have a list of plugins ','
						// Split to add new line
						var output = '';
						var temp_line ='';
						jQuery.each( value_array, function( key, line ){
							var tab = ( key == 0 ) ? 0:25;
							temp_line = temp_line + jQuery.ab_strpad( '', tab, ' ', 'f' ) + line +'\n';
						});

						the_value = temp_line;
					}

					report = report +''+ the_name + the_value + "\n";
				});

			}
		});

		try {
			jQuery( "#debug-report" ).slideDown();
			jQuery( "#debug-report textarea" ).val( report ).focus().select();
			jQuery( this ).fadeOut();
			return false;
		} catch( e ){
			console.log( e );
		}

		return false;
	});

	jQuery( document ).ready( function ( $ ) {
		$( '#copy-for-axis-support' ).tipTip({
			'attribute':  'data-tip',
			'activation': 'click',
			'fadeIn':     50,
			'fadeOut':    50,
			'delay':      0
		});

		$( 'body' ).on( 'copy', '#copy-for-axis-support', function ( e ) {
			e.clipboardData.clearData();
			e.clipboardData.setData( 'text/plain', $( '#debug-report textarea' ).val() );
			e.preventDefault();
		});

		// Tooltips
		var tiptip_args = {
			'attribute' : 'data-tip',
			'fadeIn' : 50,
			'fadeOut' : 50,
			'delay' : 200
		};
		$(".tips, .help_tip").tipTip( tiptip_args );

		// Add tiptip to parent element for widefat tables
		$(".parent-tips").each(function(){
			$(this).closest( 'a, th' ).attr( 'data-tip', $(this).data( 'tip' ) ).tipTip( tiptip_args ).css( 'cursor', 'help' );
		});
	});

</script>
