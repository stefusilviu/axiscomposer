<?php
/**
 * AxisBuilder General Settings
 *
 * @class       AB_Settings_General
 * @package     AxisBuilder/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Settings_General CLass
 */
class AB_Settings_General extends AB_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'general';
		$this->label = __( 'General', 'axisbuilder' );

		add_filter( 'axisbuilder_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'axisbuilder_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'axisbuilder_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings
	 * @return array
	 */
	public function get_settings() {

		$settings = apply_filters( 'axisbuilder_general_settings', array(

			array(
				'title' => __( 'General Options', 'axisbuilder' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'general_options'
			),

			array(
				'title'    => __( 'Custom Screen(s)', 'axisbuilder' ),
				'desc'     => __( 'This option lets you limit which screens you are willing to display to.', 'axisbuilder' ),
				'id'       => 'axisbuilder_allowed_screens',
				'default'  => 'all',
				'type'     => 'select',
				'class'    => 'axisbuilder-enhanced-select',
				'css'      => 'min-width: 350px;',
				'desc_tip' =>  true,
				'options'  => array(
					'all'      => __( 'Enable to all screens', 'axisbuilder' ),
					'specific' => __( 'Enable to specific screens only', 'axisbuilder' )
				)
			),

			array(
				'title'   => __( 'Specific Screens', 'axisbuilder' ),
				'desc'    => '',
				'id'      => 'axisbuilder_specific_allowed_screens',
				'css'     => 'min-width: 350px;',
				'default' => '',
				'type'    => 'multi_select_screens'
			),

			array(
				'title'   => __( 'TinyMCE Shortcodes', 'axisbuilder' ),
				'desc'    => __( 'Enable the TinyMCE Shortcodes', 'axisbuilder' ),
				'id'      => 'axisbuilder_tinymce_enabled',
				'default' => 'yes',
				'type'    => 'checkbox',
				'desc_tip' =>  __( 'Allows to insert shortcodes from the default editor.', 'axisbuilder' ),
				'autoload' => false
			),

			array(
				'title'   => __( 'Custom Widgets Area', 'axisbuilder' ),
				'desc'    => __( 'Enable Custom Widgets Area Builder', 'axisbuilder' ),
				'id'      => 'axisbuilder_sidebar_enabled',
				'default' => 'yes',
				'type'    => 'checkbox',
				'desc_tip' =>  __( 'Allows to register custom Sidebars or Widgets Area from the Widgets Page.', 'axisbuilder' ),
				'autoload' => false
			),

			array(
				'type' => 'sectionend',
				'id'   => 'general_options'
			)

		) );

		return apply_filters( 'axisbuilder_get_settings_' . $this->id, $settings );
	}

	/**
	 * Save settings
	 */
	public function save() {
		$settings = $this->get_settings();
		AB_Admin_Settings::save_fields( $settings );
	}
}

return new AB_Settings_General();
