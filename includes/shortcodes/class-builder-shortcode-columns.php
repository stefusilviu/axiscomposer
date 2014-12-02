<?php
/**
 * Columns Shortcode
 *
 * @extends     AB_Shortcode
 * @package     AxisBuilder/Shortcodes
 * @category    Shortcodes
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AB_Shortcode_Columns extends AB_Shortcode {

	public function shortcode_button() {

		// Shortcode Button
		$this->config['name']      = __( '1/1', 'axisbuilder' );
		$this->config['desc']      = __( 'Creates a single full width column', 'axisbuilder' );
		$this->config['type']      = __( 'Layout Elements', 'axisbuilder' );
		$this->config['icon']      = 'one-full';
		$this->config['order']     = 100;
		$this->config['target']    = 'axisbuilder-section-drop';
		$this->config['tinyMCE']   = array( 'instantInsert' => '[ab_one_full first]Add Content here[/ab_one_full]' );
		$this->config['shortcode'] = 'ab_one_full';

		// Fallback if icon is missing )
		$this->config['image']     = AB()->plugin_url() . '/assets/images/columns/one-full.png';
	}

}

class AB_Shortcode_Columns_One_Half extends AB_Shortcode_Columns {

	public function shortcode_button() {

		// Shortcode Button
		$this->config['name']      = __( '1/2', 'axisbuilder' );
		$this->config['desc']      = __( 'Creates a single column with 50&percnt; width', 'axisbuilder' );
		$this->config['type']      = __( 'Layout Elements', 'axisbuilder' );
		$this->config['icon']      = 'one-half';
		$this->config['order']     = 90;
		$this->config['target']    = 'axisbuilder-section-drop';
		$this->config['tinyMCE']   = array( 'name' => '1/2 + 1/2', 'instantInsert' => '[ab_one_half first]Add Content here[/ab_one_half]\n\n\n[ab_one_half]Add Content here[/ab_one_half]' );
		$this->config['shortcode'] = 'ab_one_half';

		// Fallback if icon is missing )
		$this->config['image']     = AB()->plugin_url() . '/assets/images/columns/one-half.png';
	}
}

class AB_Shortcode_Columns_One_Third extends AB_Shortcode_Columns {

	public function shortcode_button() {

		// Shortcode Button
		$this->config['name']      = __( '1/3', 'axisbuilder' );
		$this->config['desc']      = __( 'Creates a single column with 33&percnt; width', 'axisbuilder' );
		$this->config['type']      = __( 'Layout Elements', 'axisbuilder' );
		$this->config['icon']      = 'one-third';
		$this->config['order']     = 80;
		$this->config['target']    = 'axisbuilder-section-drop';
		$this->config['tinyMCE']   = array( 'name' => '1/3 + 1/3 + 1/3', 'instantInsert' => '[ab_one_third first]Add Content here[/ab_one_third]\n\n\n[ab_one_third]Add Content here[/ab_one_third]\n\n\n[ab_one_third]Add Content here[/ab_one_third]' );
		$this->config['shortcode'] = 'ab_one_third';

		// Fallback if icon is missing )
		$this->config['image']     = AB()->plugin_url() . '/assets/images/columns/one-third.png';
	}
}

class AB_Shortcode_Columns_Two_Third extends AB_Shortcode_Columns {

	public function shortcode_button() {

		// Shortcode Button
		$this->config['name']      = __( '2/3', 'axisbuilder' );
		$this->config['desc']      = __( 'Creates a single column with 67&percnt; width', 'axisbuilder' );
		$this->config['type']      = __( 'Layout Elements', 'axisbuilder' );
		$this->config['icon']      = 'two-third';
		$this->config['order']     = 70;
		$this->config['target']    = 'axisbuilder-section-drop';
		$this->config['tinyMCE']   = array( 'name' => '2/3 + 1/3', 'instantInsert' => '[ab_two_third first]Add Content here[/ab_two_third]\n\n\n[ab_one_third]Add Content here[/ab_one_third]' );
		$this->config['shortcode'] = 'ab_one_third';

		// Fallback if icon is missing )
		$this->config['image']     = AB()->plugin_url() . '/assets/images/columns/two-third.png';
	}
}

// Load Columns
$load_columns = array(
	'AB_Shortcode_Columns_One_Half',
	'AB_Shortcode_Columns_One_Third',
	'AB_Shortcode_Columns_Two_Third',
);

foreach ( $load_columns as $column ) {
	$load_column = new $column();
}
