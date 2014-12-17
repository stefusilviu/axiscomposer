<?php
/**
 * AxisBuilder Admin
 *
 * Central Admin Class.
 *
 * @class       AB_Admin
 * @package     AxisBuilder/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Admin Class
 */
class AB_Admin {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		// Classes
		include_once( 'class-builder-admin-editor.php' );
		include_once( 'class-builder-admin-meta-boxes.php' );

		// Classes we only need during non-ajax requests
		if ( ! defined( 'DOING_AJAX' ) ) {
			include( 'class-builder-admin-assets.php' );
			include( 'class-builder-admin-notices.php' );

			// Help
			if ( apply_filters( 'axisbuilder_enable_admin_help_tab', true ) ) {
				include( 'class-builder-admin-help.php' );
			}
		}
	}
}

return new AB_Admin();
