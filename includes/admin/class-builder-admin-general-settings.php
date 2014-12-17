<?php
/**
 * Adds settings to the general admin settings page.
 *
 * @class       AB_Admin_General_Settings
 * @package     AxisBuilder/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'AB_Admin_General_Settings' ) ) :

/**
 * AB_Admin_General_Settings Class
 */
class AB_Admin_General_Settings {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		$this->settings_init();
		$this->settings_save();
	}

	/**
	 * Init our Settings.
	 */
	public function settings_init() {

	}

	/**
	 * Save the Settings.
	 */
	public function settings_save() {

		if ( ! is_admin() ) {
			return;
		}
	}
}

endif;

return new AB_Admin_General_Settings();
