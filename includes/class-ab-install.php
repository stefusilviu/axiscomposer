<?php
/**
 * AxisBuilder Install
 *
 * Installation related functions and actions.
 *
 * @class       AB_Install
 * @package     AxisBuilder/Classes
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'AB_Install' ) ) :

/**
 * AB_Install Class
 */
class AB_Install {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		// Run this on activation.
		register_activation_hook( AB_FILENAME, array( $this, 'install' ) );

		// Hooks
		add_action( 'admin_init', array( $this, 'check_version' ), 5 );
		add_action( 'in_plugin_update_message-axis-builder/axis-builder.php', array( $this, 'in_plugin_update_message' ) );
		add_filter( 'plugin_action_links_' . AB_BASENAME, array( $this, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * check_version function.
	 */
	public function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'axisbuilder_version' ) != AB()->version ) ) {
			$this->install();

			do_action( 'axisbuilder_updated' );
		}
	}

	/**
	 * Install AB
	 */
	public function install() {
		$this->create_files();

		// Update version
		update_option( 'axisbuilder_version', AB()->version );

		// Flush rules after install
		flush_rewrite_rules();

		// Redirect to Welcome screen
		set_transient( '_ab_activation_redirect', 1, HOUR_IN_SECONDS );
	}

	/**
	 * Create files/directories
	 *
	 * @access private
	 */
	private function create_files() {
		// Install files and folders for uploading files and prevent hotlinking
		$upload_dir =  wp_upload_dir();

		$files = array(
			array(
				'base' 		=> $upload_dir['basedir'] . '/axisbuilder_uploads',
				'file' 		=> '.htaccess',
				'content' 	=> 'deny from all'
			),
			array(
				'base' 		=> $upload_dir['basedir'] . '/axisbuilder_uploads',
				'file' 		=> 'index.html',
				'content' 	=> ''
			)
		);

		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
					fwrite( $file_handle, $file['content'] );
					fclose( $file_handle );
				}
			}
		}
	}

	/**
	 * Show plugin changes. Code adapted from W3 Total Cache.
	 */
	public function in_plugin_update_message( $args ) {
		$transient_name = 'ab_upgrade_notice_' . $args['Version'];

		if ( false === ( $upgrade_notice = get_transient( $transient_name ) ) ) {

			$response = wp_remote_get( 'https://plugins.svn.wordpress.org/axis-builder/trunk/readme.txt' );

			if ( ! is_wp_error( $response ) && ! empty( $response['body'] ) ) {

				// Output Upgrade Notice
				$matches        = null;
				$regexp         = '~==\s*Upgrade Notice\s*==\s*=\s*(.*)\s*=(.*)(=\s*' . preg_quote( AB_VERSION ) . '\s*=|$)~Uis';
				$upgrade_notice = '';

				if ( preg_match( $regexp, $response['body'], $matches ) ) {
					$version		= trim( $matches[1] );
					$notices		= (array) preg_split('~[\r\n]+~', trim( $matches[2] ) );

					if ( version_compare( AB_VERSION, $version, '<' ) ) {

						$upgrade_notice .= '<div class="axis_plugin_upgrade_notice">';

						foreach ( $notices as $index => $line ) {
							$upgrade_notice .= wp_kses_post( preg_replace( '~\[([^\]]*)\]\(([^\)]*)\)~', '<a href="${2}">${1}</a>', $line ) );
						}

						$upgrade_notice .= '</div> ';
					}
				}

				set_transient( $transient_name, $upgrade_notice, DAY_IN_SECONDS );
			}
		}

		echo wp_kses_post( $upgrade_notice );
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @param	mixed $links Plugin Action links
	 * @return	array
	 */
	public function plugin_action_links( $links ) {
		$action_links = array(
			'settings'	=>	'<a href="' . admin_url( 'admin.php?page=ab-settings' ) . '" title="' . esc_attr( __( 'View Axis Builder Settings', 'axisbuilder' ) ) . '">' . __( 'Settings', 'axisbuilder' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param	mixed $links Plugin Row Meta
	 * @param	mixed $file  Plugin Base file
	 * @return	array
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( $file == AB_BASENAME ) {
			$row_meta = array(
				'docs'		=>	'<a href="' . esc_url( apply_filters( 'axisbuilder_docs_url', 'http://docs.axisthemes.com/documentation/plugins/axis-builder/' ) ) . '" title="' . esc_attr( __( 'View Axis Builder Documentation', 'axisbuilder' ) ) . '">' . __( 'Docs', 'axisbuilder' ) . '</a>',
				'apidocs'	=>	'<a href="' . esc_url( apply_filters( 'axisbuilder_apidocs_url', 'http://docs.axisthemes.com/apidocs/axis-builder/' ) ) . '" title="' . esc_attr( __( 'View Axis Builder API Docs', 'axisbuilder' ) ) . '">' . __( 'API Docs', 'axisbuilder' ) . '</a>',
				'support'	=>	'<a href="' . esc_url( apply_filters( 'axisbuilder_support_url', 'http://support.axisthemes.com/' ) ) . '" title="' . esc_attr( __( 'Visit Premium Customer Support Forum', 'axisbuilder' ) ) . '">' . __( 'Premium Support', 'axisbuilder' ) . '</a>',
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
}

endif;

return new AB_Install();
