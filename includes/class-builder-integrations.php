<?php
/**
 * AxisBuilder Integrations class
 *
 * Loads Integrations into AxisBuilder.
 *
 * @class       AB_Integrations
 * @package     AxisBuilder/Classes/Integrations
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Integrations Class
 */
class AB_Integrations {

	/** Array of integration classes */
	public $integrations = array();

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {

		do_action( 'axisbuilder_integrations_init' );

		$load_integrations = apply_filters( 'axisbuilder_integrations', array() );

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
