<?php
/**
 * AxisComposer General Settings
 *
 * @class    AC_Settings_General
 * @version  1.0.0
 * @package  AxisComposer/Admin
 * @category Admin
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'AC_Settings_General' ) ) :

/**
 * AC_Settings_General Class
 */
class AC_Settings_General extends AC_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'general';
		$this->label = __( 'General', 'axiscomposer' );

		add_filter( 'axiscomposer_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
		add_action( 'axiscomposer_settings_' . $this->id, array( $this, 'output' ) );
		add_action( 'axiscomposer_settings_save_' . $this->id, array( $this, 'save' ) );
	}

	/**
	 * Get settings.
	 * @return array
	 */
	public function get_settings() {

		$settings = apply_filters( 'axiscomposer_general_settings', array(

			array(
				'title' => __( 'General Options', 'axiscomposer' ),
				'type'  => 'title',
				'desc'  => '',
				'id'    => 'general_options'
			),

			array(
				'title'    => __( 'Custom Screen(s)', 'axiscomposer' ),
				'desc'     => __( 'This option lets you limit which screens you are willing to display to.', 'axiscomposer' ),
				'id'       => 'axiscomposer_allowed_screens',
				'default'  => 'all',
				'type'     => 'select',
				'class'    => 'availability ac-enhanced-select',
				'css'      => 'min-width: 350px;',
				'desc_tip' =>  true,
				'options'  => array(
					'all'      => __( 'Enable to all screens', 'axiscomposer' ),
					'specific' => __( 'Enable to specific screens only', 'axiscomposer' )
				)
			),

			array(
				'title'   => __( 'Allow Specific Screens', 'axiscomposer' ),
				'desc'    => '',
				'id'      => 'axiscomposer_specific_allowed_screens',
				'css'     => 'min-width: 350px;',
				'default' => '',
				'type'    => 'multi_select_screens'
			),

			array(
				'title'   => __( 'TinyMCE Shortcodes', 'axiscomposer' ),
				'desc'    => __( 'Enable the TinyMCE Shortcodes', 'axiscomposer' ),
				'id'      => 'axiscomposer_tinymce_enabled',
				'default' => 'yes',
				'type'    => 'checkbox',
				'desc_tip' =>  __( 'Allows to insert shortcodes from the default editor.', 'axiscomposer' ),
				'autoload' => false
			),

			array(
				'title'   => __( 'Custom Widgets Area', 'axiscomposer' ),
				'desc'    => __( 'Enable Custom Widgets Area Builder', 'axiscomposer' ),
				'id'      => 'axiscomposer_sidebar_enabled',
				'default' => 'yes',
				'type'    => 'checkbox',
				'desc_tip' =>  __( 'Allows to register custom Sidebars or Widgets Area from the Widgets Page.', 'axiscomposer' ),
				'autoload' => false
			),

			array(
				'type' => 'sectionend',
				'id'   => 'general_options'
			)

		) );

		return apply_filters( 'axiscomposer_get_settings_' . $this->id, $settings );
	}

	/**
	 * Save settings.
	 */
	public function save() {
		$settings = $this->get_settings();
		AC_Admin_Settings::save_fields( $settings );
	}
}

endif;

return new AC_Settings_General();
