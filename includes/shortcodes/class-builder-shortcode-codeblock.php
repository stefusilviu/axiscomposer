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

class AB_Shortcode_Codeblock extends AB_Shortcode {

	/**
	 * Create the config array for the shortcode button.
	 */
	public function shortcode_button() {

		// Shortcode Button
		$this->config['name']      = __( 'Code Block', 'axisbuilder' );
		$this->config['desc']      = __( 'Add text or code to your website without any formatting or text optimization. Can be used for HTML/CSS/Javascript', 'axisbuilder' );
		$this->config['type']      = __( 'Content Elements', 'axisbuilder' );
		$this->config['icon']      = 'codeblock';
		$this->config['order']     = 1;
		$this->config['target']    = 'axisbuilder-target-insert';
		$this->config['tinyMCE']   = array( 'disable' => true );
		$this->config['shortcode'] = 'ab_codeblock';

		// Fallback if icon is missing )
		$this->config['image']     = AB()->plugin_url() . '/assets/images/content/codeblock.png';
	}

	/**
	 * Popup Elements
	 *
	 * If this function is defined in a child class the element automatically gets an edit button, that, when pressed
	 * opens a modal window that allows to edit the element properties
	 */
	public function popup_elements() {
		$this->elements =  array(
			array(
				"name"  => __("Code Block Content. Add your own HTML/CSS/Javascript here", 'axisbuilder'),
				"desc"  => __("Enter some text/code. You can also add plugin shortcodes here. (Adding theme shortcodes is not recommended though)", 'axisbuilder'),
				"id"    => "content",
				'container_class' =>"axisbuilder-element-fullwidth",
				"type"  => "textarea",
				"std"   => "",
			),

			array(
				"name"  => __("Code Wrapper Element", 'axisbuilder' ),
				"desc"  => __("Wrap your code into a html tag (i.e. pre or code tag). Insert the tag without <>", 'axisbuilder' ) ,
				"id"    => "wrapper_element",
				"std"   => '',
				"type"  => "input"
			),

			array(
				"name"  => __("Code Wrapper Element Attributes", 'axisbuilder' ),
				"desc"  => __("Enter one or more attribute values which should be applied to the wrapper element. Leave the field empty if no attributes are required.", 'axisbuilder' ) ,
				"id"    => "wrapper_element_attributes",
				"std"   => '',
				"required" => array('wrapper_element', 'not', ''),
				"type"  => "input"
			),

			array(
				"name"  => __("Escape HTML Code", 'axisbuilder' ),
				"desc"  => __("WordPress will convert the html tags to readable text.", 'axisbuilder' ) ,
				"id"    => "escape_html",
				"std"   => false,
				"type"  => "checkbox"
			),

			array(
				"name"  => __("Disable Shortcode Processing", 'axisbuilder' ),
				"desc"  => __("Check if you want to disable the shortcode processing for this code block", 'axisbuilder' ) ,
				"id"    => "deactivate_shortcode",
				"std"   => false,
				"type"  => "checkbox"
			),

			array(
				"name"  => __("Deactivate schema.org markup", 'axisbuilder' ),
				"desc"  => __("Output the code without any additional wrapper elements. (not recommended)", 'axisbuilder' ) ,
				"id"    => "deactivate_wrapper",
				"std"   => false,
				"type"  => "checkbox"
			),
		);
	}

}
