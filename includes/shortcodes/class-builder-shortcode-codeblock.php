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
		parent::__construct();
	}

	/**
	 * Configuration for builder shortcode button.
	 */
	public function shortcode_button() {
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
	}

	/**
	 * Popup Elements
	 *
	 * If this method is defined the elements automatically gets an edit button.
	 * When pressed opens a popup modal window that allows to edit the element properties.
	 */
	public function popup_elements() {
		$this->settings = array(
			array(
				'name'  => __( 'Code Block Element. Add your own HTML/Javascript here', 'axisbuilder' ),
				'desc'  => __( 'Enter some text/code. You can also add plugin shortcode here. (Adding theme shortcode is not recommended though)', 'axisbuilder' ),
				'id'    => 'content',
				'class' => 'axisbuilder-element-fullwidth',
				'type'  => 'textarea',
				'std'   => ''
			)
		);
	}
}
