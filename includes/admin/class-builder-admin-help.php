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

		$screen->add_help_tab( array(
			'id'		=> 'axisbuilder-help-tab',
			'title'		=> __( 'Page Builder', 'axisbuilder' ),
			'content'	=>

				'<h4>' . __( 'General Info', 'axisbuilder' ) . '</h4>' .
					'<ul>' .
						'<li>' . __( 'To insert an Element either click the insert button for that element or drag the button onto the canvas.', 'axisbuilder' ) . '</li>' .
						'<li>' . __( 'If you place your mouse above the insert button a short info tooltip will appear.', 'axisbuilder' ) . '</li>' .
						'<li>' . __( 'To sort and arrange your elements just drag them to a position of your choice and release them.', 'axisbuilder' ) . '</li>' .
						'<li>' . __( 'Valid drop targets will be highlighted. Elements like fullwidth sliders &amp color section can\'t be dropped onto other elements.', 'axisbuilder' ) . '</li>' .
					'</ul>' .
				'<h4>' . __( 'Popup Elements', 'axisbuilder' ) . '</h4>' .
				'<ul>' .
					'<li>' . __( 'Most elements open a popup window if you click them.', 'axisbuilder' ) . '</li>' .
					'<li>' . __( 'Press TAB to navigate trough the various form fields of a popup window.', 'axisbuilder' ) . '</li>' .
					'<li>' . __( 'Press ESC on your keyboard or the Close Button to close popup window.', 'axisbuilder' ) . '</li>' .
					'<li>' . __( 'Press ENTER on your keyboard or the Save Button to save current state of a popup window.', 'axisbuilder' ) . '</li>' .
				'</ul>'
		) );
	}
}

endif;

return new AB_Admin_Help();
