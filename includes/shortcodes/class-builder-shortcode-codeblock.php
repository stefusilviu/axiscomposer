<?php
/**
 * Codeblock Shortcode
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

/**
 * AB_Shortcode_Codeblock Class
 */
class AB_Shortcode_Codeblock extends AB_Shortcode {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		$this->id        = 'axisbuilder_codeblock';
		$this->title     = __( 'Code Block', 'axisbuilder' );
		$this->tooltip   = __( 'Add text or code to your website without any formatting or text optimization. Can be used for HTML/CSS/Javascript', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 200,
			'type'    => 'content',
			'name'    => 'ab_codeblock',
			'icon'    => 'icon-codeblock',
			'image'   => AB()->plugin_url() . '/assets/images/content/codeblock.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'disable' => true ),
		);
		$this->settings = array(

		);
		parent::__construct();
	}
}
