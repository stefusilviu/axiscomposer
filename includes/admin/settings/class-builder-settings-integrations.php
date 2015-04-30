<?php
/**
 * AxisBuilder Integration Settings
 *
 * @class       AB_Settings_Integrations
 * @package     AxisBuilder/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'AB_Settings_Integrations' ) ) :

/**
 * AB_Settings_Integrations Class
 */
class AB_Settings_Integrations extends AB_Settings_Page {

	/**
	 * Constructor.
	 */
	public function __construct() {

		$this->id    = 'integration';
		$this->label = __( 'Integration', 'axisbuilder' );

		if ( isset( AB()->integrations ) && AB()->integrations->get_integrations() ) {
			add_filter( 'axisbuilder_settings_tabs_array', array( $this, 'add_settings_page' ), 20 );
			add_action( 'axisbuilder_sections_' . $this->id, array( $this, 'output_sections' ) );
			add_action( 'axisbuilder_settings_' . $this->id, array( $this, 'output' ) );
			add_action( 'axisbuilder_settings_save_' . $this->id, array( $this, 'save' ) );
		}
	}

	/**
	 * Get sections.
	 * @return array
	 */
	public function get_sections() {
		global $current_section;

		$sections = array();

		if ( ! defined( 'AB_INSTALLING' ) ) {
			$integrations = AB()->integrations->get_integrations();

			if ( ! $current_section && ! empty( $integrations ) ) {
				$current_section = current( $integrations )->id;
			}

			if ( sizeof( $integrations ) > 1 ) {
				foreach ( $integrations as $integration ) {
					$title = empty( $integration->method_title ) ? ucfirst( $integration->id ) : $integration->method_title;
					$sections[ strtolower( $integration->id ) ] = esc_html( $title );
				}
			}
		}

		return apply_filters( 'axisbuilder_get_sections_' . $this->id, $sections );
	}

	/**
	 * Output the settings.
	 */
	public function output() {
		global $current_section;

		$integrations = AB()->integrations->get_integrations();

		if ( isset( $integrations[ $current_section ] ) )
			$integrations[ $current_section ]->admin_options();
	}
}

endif;

return new AB_Settings_Integrations();
