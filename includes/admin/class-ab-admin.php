<?php
/**
 * AxisBuilder Admin
 *
 * Central Admin Class.
 *
 * @class		AB_Admin
 * @package		AxisBuilder/Admin
 * @category	Admin
 * @author		AxisThemes
 * @since		1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * AB_Admin Class
 */
class AB_Admin {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		// Functions
		include_once( 'ab-admin-functions.php' );

		// Classes we only need during non-ajax requests
		if ( ! is_ajax() ) {
			include( 'class-ab-admin-assets.php' );

			// Help
			if ( apply_filters( 'axisbuilder_enable_admin_help_tab', true ) ) {
				include( 'class-ab-admin-help.php' );
			}
		}
	}
}

return new AB_Admin();
