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
	exit; // Exit if accessed directly
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
		include_once( 'class-builder-admin-assets.php' );
		include_once( 'class-builder-admin-editor.php' );
		include_once( 'class-builder-admin-meta-boxes.php' );
	}
}

return new AB_Admin();
