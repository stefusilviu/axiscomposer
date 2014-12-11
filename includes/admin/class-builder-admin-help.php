<?php
/**
 * AxisBuilder Admin Help
 *
 * Handles the Contextual help tabs.
 *
 * @class       AB_Admin_Help
 * @package     AxisBuilder/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'AB_Admin_Help' ) ) :

/**
 * AB_Admin_Help Class
 */
class AB_Admin_Help {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'current_screen', array( $this, 'add_tabs' ), 50 );
	}

	/**
	 * Add Contextual help tabs.
	 */
	public function add_tabs() {
		$screen = get_current_screen();

		if ( ! in_array( $screen->id, get_builder_core_supported_screens() ) ) {
			return;
		}
	}
}

endif;

return new AB_Admin_Help();
