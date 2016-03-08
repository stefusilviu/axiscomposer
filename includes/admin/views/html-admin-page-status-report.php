<?php
/**
 * Admin View: Page - Status Report.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="updated axiscomposer-message inline">
	<p><?php _e( 'Please copy and paste this information in your ticket when contacting support:', 'axiscomposer' ); ?> </p>
	<p class="submit"><a href="#" class="button-primary debug-report"><?php _e( 'Get System Report', 'axiscomposer' ); ?></a>
	<a class="button-secondary docs" href="http://docs.axisthemes.com/document/understanding-the-axiscomposer-system-status-report/" target="_blank"><?php _e( 'Understanding the Status Report', 'axiscomposer' ); ?></a></p>
	<div id="debug-report">
		<textarea readonly="readonly"></textarea>
		<p class="submit"><button id="copy-for-support" class="button-primary" href="#" data-tip="<?php esc_attr_e( 'Copied!', 'axiscomposer' ); ?>"><?php _e( 'Copy for Support', 'axiscomposer' ); ?></button></p>
		<p class="copy-error hidden"><?php _e( 'Copying to clipboard failed. Please press Ctrl/Cmd+C to copy.', 'axiscomposer' ); ?></p>
	</div>
</div>
<table class="ac_status_table widefat" cellspacing="0" id="status">
	<thead>
		<tr>
			<th colspan="3" data-export-label="WordPress Environment"><h2><?php _e( 'WordPress Environment', 'axiscomposer' ); ?></h2></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td data-export-label="Home URL"><?php _e( 'Home URL', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The URL of your site\'s homepage.', 'axiscomposer' ) ); ?></td>
			<td><?php form_option( 'home' ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Site URL"><?php _e( 'Site URL', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The root URL of your site.', 'axiscomposer' ) ); ?></td>
			<td><?php form_option( 'siteurl' ); ?></td>
		</tr>
		<tr>
			<td data-export-label="AC Version"><?php _e( 'AC Version', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The version of AxisComposer installed on your site.', 'axiscomposer' ) ); ?></td>
			<td><?php echo esc_html( AC()->version ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Log Directory Writable"><?php _e( 'Log Directory Writable', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'Several AxisComposer extensions can write logs which makes debugging problems easier. The directory must be writable for this to happen.', 'axiscomposer' ) ); ?></td>
			<td><?php
				if ( @fopen( AC_LOG_DIR . 'test-log.log', 'a' ) ) {
					echo '<mark class="yes"><span class="dashicons dashicons-yes"></span> <code class="private">' . AC_LOG_DIR . '</code></mark> ';
				} else {
					printf( '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . __( 'To allow logging, make <code>%s</code> writable or define a custom <code>AC_LOG_DIR</code>.', 'axiscomposer' ) . '</mark>', AC_LOG_DIR );
				}
			?></td>
		</tr>
		<tr>
			<td data-export-label="WP Version"><?php _e( 'WP Version', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The version of WordPress installed on your site.', 'axiscomposer' ) ); ?></td>
			<td><?php bloginfo('version'); ?></td>
		</tr>
		<tr>
			<td data-export-label="WP Multisite"><?php _e( 'WP Multisite', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'Whether or not you have WordPress Multisite enabled.', 'axiscomposer' ) ); ?></td>
			<td><?php if ( is_multisite() ) echo '<span class="dashicons dashicons-yes"></span>'; else echo '&ndash;'; ?></td>
		</tr>
		<tr>
			<td data-export-label="WP Memory Limit"><?php _e( 'WP Memory Limit', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The maximum amount of memory (RAM) that your site can use at one time.', 'axiscomposer' ) ); ?></td>
			<td><?php
				$memory = ac_let_to_num( WP_MEMORY_LIMIT );

				if ( function_exists( 'memory_get_usage' ) ) {
					$system_memory = ac_let_to_num( @ini_get( 'memory_limit' ) );
					$memory        = max( $memory, $system_memory );
				}

				if ( $memory < 67108864 ) {
					echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( __( '%s - We recommend setting memory to at least 64MB. See: %s', 'axiscomposer' ), size_format( $memory ), '<a href="http://codex.wordpress.org/Editing_wp-config.php#Increasing_memory_allocated_to_PHP" target="_blank">' . __( 'Increasing memory allocated to PHP', 'axiscomposer' ) . '</a>' ) . '</mark>';
				} else {
					echo '<mark class="yes">' . size_format( $memory ) . '</mark>';
				}
			?></td>
		</tr>
		<tr>
			<td data-export-label="WP Debug Mode"><?php _e( 'WP Debug Mode', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'Displays whether or not WordPress is in Debug Mode.', 'axiscomposer' ) ); ?></td>
			<td>
				<?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) : ?>
					<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
				<?php else : ?>
					<mark class="no">&ndash;</mark>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td data-export-label="WP Cron"><?php _e( 'WP Cron', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'Displays whether or not WP Cron Jobs are enabled.', 'axiscomposer' ) ); ?></td>
			<td>
				<?php if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) : ?>
					<mark class="no">&ndash;</mark>
				<?php else : ?>
					<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td data-export-label="Language"><?php _e( 'Language', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The current language used by WordPress. Default = English', 'axiscomposer' ) ); ?></td>
			<td><?php echo get_locale(); ?></td>
		</tr>
	</tbody>
</table>
<table class="ac_status_table widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="3" data-export-label="Server Environment"><h2><?php _e( 'Server Environment', 'axiscomposer' ); ?></h2></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td data-export-label="Server Info"><?php _e( 'Server Info', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'Information about the web server that is currently hosting your site.', 'axiscomposer' ) ); ?></td>
			<td><?php echo esc_html( $_SERVER['SERVER_SOFTWARE'] ); ?></td>
		</tr>
		<tr>
			<td data-export-label="PHP Version"><?php _e( 'PHP Version', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The version of PHP installed on your hosting server.', 'axiscomposer' ) ); ?></td>
			<td><?php
				// Check if phpversion function exists.
				if ( function_exists( 'phpversion' ) ) {
					$php_version = phpversion();

					if ( version_compare( $php_version, '5.4', '<' ) ) {
						echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( __( '%s - We recommend a minimum PHP version of 5.4. See: %s', 'axiscomposer' ), esc_html( $php_version ), '<a href="http://docs.axisthemes.com/document/how-to-update-your-php-version/" target="_blank">' . __( 'How to update your PHP version', 'axiscomposer' ) . '</a>' ) . '</mark>';
					} else {
						echo '<mark class="yes">' . esc_html( $php_version ) . '</mark>';
					}
				} else {
					_e( "Couldn't determine PHP version because phpversion() doesn't exist.", 'axiscomposer' );
				}
			?></td>
		</tr>
		<?php if ( function_exists( 'ini_get' ) ) : ?>
			<tr>
				<td data-export-label="PHP Post Max Size"><?php _e( 'PHP Post Max Size', 'axiscomposer' ); ?>:</td>
				<td class="help"><?php echo ac_help_tip( __( 'The largest filesize that can be contained in one post.', 'axiscomposer' ) ); ?></td>
				<td><?php echo size_format( ac_let_to_num( ini_get( 'post_max_size' ) ) ); ?></td>
			</tr>
			<tr>
				<td data-export-label="PHP Time Limit"><?php _e( 'PHP Time Limit', 'axiscomposer' ); ?>:</td>
				<td class="help"><?php echo ac_help_tip( __( 'The amount of time (in seconds) that your site will spend on a single operation before timing out (to avoid server lockups)', 'axiscomposer' ) ); ?></td>
				<td><?php echo ini_get( 'max_execution_time' ); ?></td>
			</tr>
			<tr>
				<td data-export-label="PHP Max Input Vars"><?php _e( 'PHP Max Input Vars', 'axiscomposer' ); ?>:</td>
				<td class="help"><?php echo ac_help_tip( __( 'The maximum number of variables your server can use for a single function to avoid overloads.', 'axiscomposer' ) ); ?></td>
				<td><?php echo ini_get( 'max_input_vars' ); ?></td>
			</tr>
			<tr>
				<td data-export-label="SUHOSIN Installed"><?php _e( 'SUHOSIN Installed', 'axiscomposer' ); ?>:</td>
				<td class="help"><?php echo ac_help_tip( __( 'Suhosin is an advanced protection system for PHP installations. It was designed to protect your servers on the one hand against a number of well known problems in PHP applications and on the other hand against potential unknown vulnerabilities within these applications or the PHP core itself. If enabled on your server, Suhosin may need to be configured to increase its data submission limits.', 'axiscomposer' ) ); ?></td>
				<td><?php echo extension_loaded( 'suhosin' ) ? '<span class="dashicons dashicons-yes"></span>' : '&ndash;'; ?></td>
			</tr>
		<?php endif; ?>
		<tr>
			<td data-export-label="MySQL Version"><?php _e( 'MySQL Version', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The version of MySQL installed on your hosting server.', 'axiscomposer' ) ); ?></td>
			<td><?php
				/** @global wpdb $wpdb */
				global $wpdb;
				echo $wpdb->db_version();
			?></td>
		</tr>
		<tr>
			<td data-export-label="Max Upload Size"><?php _e( 'Max Upload Size', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The largest filesize that can be uploaded to your WordPress installation.', 'axiscomposer' ) ); ?></td>
			<td><?php echo size_format( wp_max_upload_size() ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Default Timezone is UTC"><?php _e( 'Default Timezone is UTC', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The default timezone for your server.', 'axiscomposer' ) ); ?></td>
			<td><?php
				$default_timezone = date_default_timezone_get();
				if ( 'UTC' !== $default_timezone ) {
					echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . sprintf( __( 'Default timezone is %s - it should be UTC', 'axiscomposer' ), $default_timezone ) . '</mark>';
				} else {
					echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
				}
			?></td>
		</tr>
		<?php
			$posting = array();

			// Multibyte String.
			$posting['mbstring']['name'] = 'Multibyte String';
			$posting['mbstring']['help'] = ac_help_tip( __( 'Multibyte String (mbstring) is used to convert character encoding, like for emails or converting characters to lowercase.', 'axiscomposer' ) );

			if ( extension_loaded( 'mbstring' ) ) {
				$posting['mbstring']['success'] = true;
			} else {
				$posting['mbstring']['success'] = false;
				$posting['mbstring']['note']    = sprintf( __( 'Your server does not support the %s functions - this is required for better character encoding. Some fallbacks will be used instead for it.', 'axiscomposer' ), '<a href="http://php.net/manual/en/mbstring.installation.php">mbstring</a>' );
			}

			// WP Remote Post Check.
			$posting['wp_remote_post']['name'] = __( 'Remote Post', 'axiscomposer' );
			$posting['wp_remote_post']['help'] = ac_help_tip( __( 'AxisComposer plugins may uses this method of communication when sending back information.', 'axiscomposer' ) );

			$response = wp_safe_remote_post( 'https://www.paypal.com/cgi-bin/webscr', array(
				'timeout'    => 60,
				'user-agent' => 'AxisComposer/' . AC()->version,
				'body'       => array(
					'cmd'    => '_notify-validate'
				)
			) );

			if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
				$posting['wp_remote_post']['success'] = true;
			} else {
				$posting['wp_remote_post']['note']    = __( 'wp_safe_remote_post() failed. Contact your hosting provider.', 'axiscomposer' );
				if ( is_wp_error( $response ) ) {
					$posting['wp_remote_post']['note'] .= ' ' . sprintf( __( 'Error: %s', 'axiscomposer' ), ac_clean( $response->get_error_message() ) );
				} else {
					$posting['wp_remote_post']['note'] .= ' ' . sprintf( __( 'Status code: %s', 'axiscomposer' ), ac_clean( $response['response']['code'] ) );
				}
				$posting['wp_remote_post']['success'] = false;
			}

			// WP Remote Get Check.
			$posting['wp_remote_get']['name'] = __( 'Remote Get', 'axiscomposer' );
			$posting['wp_remote_get']['help'] = ac_help_tip( __( 'AxisComposer plugins may use this method of communication when checking for plugin updates.', 'axiscomposer' ) );

			$response = wp_safe_remote_get( 'https://api.github.com/repos/axisthemes/axiscomposer/contributors' );

			if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 ) {
				$posting['wp_remote_get']['success'] = true;
			} else {
				$posting['wp_remote_get']['note']    = __( 'wp_safe_remote_get() failed. The AxisComposer plugin updater won\'t work with your server. Contact your hosting provider.', 'axiscomposer' );
				if ( is_wp_error( $response ) ) {
					$posting['wp_remote_get']['note'] .= ' ' . sprintf( __( 'Error: %s', 'axiscomposer' ), ac_clean( $response->get_error_message() ) );
				} else {
					$posting['wp_remote_get']['note'] .= ' ' . sprintf( __( 'Status code: %s', 'axiscomposer' ), ac_clean( $response['response']['code'] ) );
				}
				$posting['wp_remote_get']['success'] = false;
			}

			$posting = apply_filters( 'axiscomposer_debug_posting', $posting );

			foreach ( $posting as $post ) {
				$mark = ! empty( $post['success'] ) ? 'yes' : 'error';
				?>
				<tr>
					<td data-export-label="<?php echo esc_html( $post['name'] ); ?>"><?php echo esc_html( $post['name'] ); ?>:</td>
					<td class="help"><?php echo isset( $post['help'] ) ? $post['help'] : ''; ?></td>
					<td>
						<mark class="<?php echo $mark; ?>">
							<?php echo ! empty( $post['success'] ) ? '<span class="dashicons dashicons-yes"></span>' : '<span class="dashicons dashicons-no-alt"></span>'; ?> <?php echo ! empty( $post['note'] ) ? wp_kses_data( $post['note'] ) : ''; ?>
						</mark>
					</td>
				</tr>
				<?php
			}
		?>
	</tbody>
</table>
<table class="ac_status_table widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="3" data-export-label="Database"><h2><?php _e( 'Database', 'axiscomposer' ); ?></h2></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td data-export-label="AC Database Version"><?php _e( 'AC Database Version', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The version of AxisComposer that the database is formatted for. This should be the same as your AxisComposer Version.', 'axiscomposer' ) ); ?></td>
			<td><?php echo esc_html( get_option( 'axiscomposer_db_version' ) ); ?></td>
		</tr>
		<tr><?php
			$tables = array();

			foreach ( $tables as $table ) {
				?>
				<tr>
					<td><?php echo esc_html( $table ); ?></td>
					<td class="help">&nbsp;</td>
					<td><?php echo $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s;", $wpdb->prefix . $table ) ) !== $wpdb->prefix . $table ? '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . __( 'Table does not exist', 'woocommerce' ) . '</mark>' : '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>'; ?></td>
				</tr>
				<?php
			}
		?></tr>
	</tbody>
</table>
<table class="ac_status_table widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="3" data-export-label="Active Plugins (<?php echo count( (array) get_option( 'active_plugins' ) ); ?>)"><h2><?php _e( 'Active Plugins', 'axiscomposer' ); ?> (<?php echo count( (array) get_option( 'active_plugins' ) ); ?>)</h2></th>
		</tr>
	</thead>
	<tbody><?php
		$active_plugins = (array) get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$network_activated_plugins = array_keys( get_site_option( 'active_sitewide_plugins', array() ) );
			$active_plugins            = array_merge( $active_plugins, $network_activated_plugins );
		}

		foreach ( $active_plugins as $plugin ) {

			$plugin_data    = @get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
			$dirname        = dirname( $plugin );
			$version_string = '';
			$network_string = '';

			if ( ! empty( $plugin_data['Name'] ) ) {

				// Link the plugin name to the plugin url if available.
				$plugin_name = esc_html( $plugin_data['Name'] );

				if ( ! empty( $plugin_data['PluginURI'] ) ) {
					$plugin_name = '<a href="' . esc_url( $plugin_data['PluginURI'] ) . '" title="' . esc_attr__( 'Visit plugin homepage' , 'axiscomposer' ) . '" target="_blank">' . $plugin_name . '</a>';
				}

				if ( strstr( $dirname, 'axiscomposer-' ) && strstr( $plugin_data['PluginURI'], 'axisthemes.com' ) ) {

					if ( false === ( $version_data = get_transient( md5( $plugin ) . '_version_data' ) ) ) {
						$changelog = wp_safe_remote_get( 'http://www.axisthemes.com/changelogs/' . $dirname . '/changelog.txt' );
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
						$version_string = ' &ndash; <strong style="color:red;">' . esc_html( sprintf( _x( '%s is available', 'Version info', 'axiscomposer' ), $version_data['version'] ) ) . '</strong>';
					}

					if ( $plugin_data['Network'] != false ) {
						$network_string = ' &ndash; <strong style="color:black;">' . __( 'Network enabled', 'axiscomposer' ) . '</strong>';
					}
				}

				?>
				<tr>
					<td><?php echo $plugin_name; ?></td>
					<td class="help">&nbsp;</td>
					<td><?php echo sprintf( _x( 'by %s', 'by author', 'axiscomposer' ), $plugin_data['Author'] ) . ' &ndash; ' . esc_html( $plugin_data['Version'] ) . $version_string . $network_string; ?></td>
				</tr>
				<?php
			}
		}
	?></tbody>
</table>
<table class="ac_status_table widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="3" data-export-label="Settings"><h2><?php _e( 'Settings', 'axiscomposer' ); ?></h2></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td data-export-label="TinyMCE Enabled"><?php _e( 'TinyMCE Enabled', 'axiscomposer' ) ?></td>
			<td class="help"><?php echo ac_help_tip( __( 'Does your site have tinyMCE enabled?', 'axiscomposer' ) ); ?></td>
			<td><?php echo 'yes' === get_option( 'axiscomposer_tinymce_enabled' ) ? '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>' : '<mark class="no">&ndash;</mark>'; ?></td>
		</tr>
		<tr>
			<td data-export-label="Sidebar Builder Enabled"><?php _e( 'Sidebar Builder Enabled', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'Does your site have Sidebar Builder enabled?', 'axiscomposer' ) ); ?></td>
			<td><?php echo 'yes' === get_option( 'axiscomposer_sidebar_enabled' ) ? '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>' : '<mark class="no">&ndash;</mark>'; ?></td>
		</tr>
	</tbody>
</table>
<table class="ac_status_table widefat" cellspacing="0">
	<thead>
		<tr>
			<th colspan="3" data-export-label="Theme"><h2><?php _e( 'Theme', 'axiscomposer' ); ?></h2></th>
		</tr>
	</thead><?php
		include_once( ABSPATH . 'wp-admin/includes/theme-install.php' );

		$active_theme         = wp_get_theme();
		$theme_version        = $active_theme->Version;
		$update_theme_version = AC_Admin_Status::get_latest_theme_version( $active_theme );
	?><tbody>
		<tr>
			<td data-export-label="Name"><?php _e( 'Name', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The name of the current active theme.', 'axiscomposer' ) ); ?></td>
			<td><?php echo esc_html( $active_theme->Name ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Version"><?php _e( 'Version', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The installed version of the current active theme.', 'axiscomposer' ) ); ?></td>
			<td><?php
				echo esc_html( $theme_version );

				if ( version_compare( $theme_version, $update_theme_version, '<' ) ) {
					echo ' &ndash; <strong style="color:red;">' . sprintf( __( '%s is available', 'axiscomposer' ), esc_html( $update_theme_version ) ) . '</strong>';
				}
			?></td>
		</tr>
		<tr>
			<td data-export-label="Author URL"><?php _e( 'Author URL', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The theme developers URL.', 'axiscomposer' ) ); ?></td>
			<td><?php echo $active_theme->{'Author URI'}; ?></td>
		</tr>
		<tr>
			<td data-export-label="Child Theme"><?php _e( 'Child Theme', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'Displays whether or not the current theme is a child theme.', 'axiscomposer' ) ); ?></td>
			<td><?php
				echo is_child_theme() ? '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>' : '<span class="dashicons dashicons-no-alt"></span> &ndash; ' . sprintf( __( 'If you\'re modifying AxisComposer on a parent theme you didn\'t build personally, then we recommend using a child theme. See: <a href="%s" target="_blank">How to create a child theme</a>', 'axiscomposer' ), 'http://codex.wordpress.org/Child_Themes' );
			?></td>
		</tr>
		<?php
		if ( is_child_theme() ) :
			$parent_theme         = wp_get_theme( $active_theme->Template );
			$update_theme_version = WC_Admin_Status::get_latest_theme_version( $parent_theme );
		?>
		<tr>
			<td data-export-label="Parent Theme Name"><?php _e( 'Parent Theme Name', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The name of the parent theme.', 'axiscomposer' ) ); ?></td>
			<td><?php echo esc_html( $parent_theme->Name ); ?></td>
		</tr>
		<tr>
			<td data-export-label="Parent Theme Version"><?php _e( 'Parent Theme Version', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The installed version of the parent theme.', 'axiscomposer' ) ); ?></td>
			<td><?php
				echo esc_html( $parent_theme->Version );

				if ( version_compare( $parent_theme->Version, $update_theme_version, '<' ) ) {
					echo ' &ndash; <strong style="color:red;">' . sprintf( __( '%s is available', 'axiscomposer' ), esc_html( $update_theme_version ) ) . '</strong>';
				}
			?></td>
		</tr>
		<tr>
			<td data-export-label="Parent Theme Author URL"><?php _e( 'Parent Theme Author URL', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'The parent theme developers URL.', 'axiscomposer' ) ); ?></td>
			<td><?php echo $parent_theme->{'Author URI'}; ?></td>
		</tr>
		<?php endif ?>
		<tr>
			<td data-export-label="AxisComposer Support"><?php _e( 'AxisComposer Support', 'axiscomposer' ); ?>:</td>
			<td class="help"><?php echo ac_help_tip( __( 'Displays whether or not the current active theme declares AxisComposer support.', 'axiscomposer' ) ); ?></td>
			<td><?php
				if ( ! current_theme_supports( 'axiscomposer' ) && ! in_array( $active_theme->template, ac_get_core_supported_themes() ) ) {
					echo '<mark class="error"><span class="dashicons dashicons-warning"></span> ' . __( 'Not Declared', 'axiscomposer' ) . '</mark>';
				} else {
					echo '<mark class="yes"><span class="dashicons dashicons-yes"></span></mark>';
				}
			?></td>
		</tr>
	</tbody>
</table>

<?php do_action( 'axiscomposer_system_status_report' ); ?>

<script type="text/javascript">

	jQuery( 'a.axiscomposer-help-tip' ).click( function() {
		return false;
	});

	jQuery( 'a.debug-report' ).click( function() {

		var report = '';

		jQuery( '.ac_status_table thead, .ac_status_table tbody' ).each( function() {

			if ( jQuery( this ).is( 'thead' ) ) {

				var label = jQuery( this ).find( 'th:eq(0)' ).data( 'export-label' ) || jQuery( this ).text();
				report = report + '\n### ' + jQuery.trim( label ) + ' ###\n\n';

			} else {

				jQuery( 'tr', jQuery( this ) ).each( function() {

					var label       = jQuery( this ).find( 'td:eq(0)' ).data( 'export-label' ) || jQuery( this ).find( 'td:eq(0)' ).text();
					var the_name    = jQuery.trim( label ).replace( /(<([^>]+)>)/ig, '' ); // Remove HTML.

					// Find value
					var $value_html = jQuery( this ).find( 'td:eq(2)' ).clone();
					$value_html.find( '.private' ).remove();
					$value_html.find( '.dashicons-yes' ).replaceWith( '&#10004;' );
					$value_html.find( '.dashicons-no-alt, .dashicons-warning' ).replaceWith( '&#10060;' );

					// Format value
					var the_value   = jQuery.trim( $value_html.text() );
					var value_array = the_value.split( ', ' );

					if ( value_array.length > 1 ) {
						// If value have a list of plugins ','.
						// Split to add new line.
						var temp_line ='';
						jQuery.each( value_array, function( key, line ) {
							temp_line = temp_line + line + '\n';
						});

						the_value = temp_line;
					}

					report = report + '' + the_name + ': ' + the_value + '\n';
				});

			}
		});

		try {
			jQuery( '#debug-report' ).slideDown();
			jQuery( '#debug-report textarea' ).val( '`' + report + '`' ).focus().select();
			jQuery( this ).fadeOut();
			return false;
		} catch( e ) {
			/* jshint devel: true */
			console.log( e );
		}

		return false;
	});

	jQuery( document ).ready( function( $ ) {

		$( document.body ).on( 'copy', '#copy-for-support', function( e ) {
			e.clipboardData.clearData();
			e.clipboardData.setData( 'text/plain', $( '#debug-report' ).find( 'textarea' ).val() );
			e.preventDefault();
		});

		$( document.body ).on( 'aftercopy', '#copy-for-support', function( e ) {
			if ( true === e.success['text/plain'] ) {
				$( '#copy-for-support' ).tipTip({
					'attribute':  'data-tip',
					'activation': 'focus',
					'fadeIn':     50,
					'fadeOut':    50,
					'delay':      0
				}).focus();
			} else {
				$( '.copy-error' ).removeClass( 'hidden' );
				$( '#debug-report' ).find( 'textarea' ).focus().select();
			}
		});

	});

</script>
