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

<script type="text/javascript">

	/**
	 * axis_strpad
	 *
	 * @param  {var} i string default
	 * @param  {var} l how many repeat s
	 * @param  {var} s string to repeat
	 * @param  {var} w where s should indent
	 * @return {var}   string modified
	 */
	jQuery.axis_strPad = function( i, l, s, w ) {
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

					var the_name    = jQuery.axis_strPad( jQuery.trim( jQuery( this ).find( 'td:eq(0)' ).text() ), 25, ' ' );
					var the_value   = jQuery.trim( jQuery( this ).find( 'td:eq(2)' ).text() );
					var value_array = the_value.split( ', ' );

					if ( value_array.length > 1 ) {

						// If value have a list of plugins ','
						// Split to add new line
						var output = '';
						var temp_line ='';
						jQuery.each( value_array, function( key, line ){
							var tab = ( key == 0 ) ? 0:25;
							temp_line = temp_line + jQuery.axis_strPad( '', tab, ' ', 'f' ) + line +'\n';
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
