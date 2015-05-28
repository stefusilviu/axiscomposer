<?php
/**
 * AxisComposer Integrations class
 *
 * Loads Integrations into AxisComposer.
 *
 * @class       AC_Integrations
 * @package     AxisComposer/Classes/Integrations
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Integrations Class
 */
class AC_Integrations {

	/** Array of integration classes */
	public $integrations = array();

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {

		do_action( 'axiscomposer_integrations_init' );

		$load_integrations = apply_filters( 'axiscomposer_integrations', array() );

		// Load integration classes
		foreach ( $load_integrations as $integration ) {

			$load_integration = new $integration();

			$this->integrations[ $load_integration->id ] = $load_integration;
		}
	}

	/**
	 * Return loaded integrations.
	 * @return array
	 */
	public function get_integrations() {
		return $this->integrations;
	}
}
