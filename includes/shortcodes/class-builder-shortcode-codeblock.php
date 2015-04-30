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
			'sort'    => 350,
			'type'    => 'content',
			'name'    => 'ab_codeblock',
			'icon'    => 'icon-codeblock',
			'image'   => AB()->plugin_url() . '/assets/images/content/codeblock.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinyMCE' => array( 'disable' => false ),
		);
	}

	/**
	 * Get Settings.
	 * @return array
	 */
	public function get_settings() {

		$this->elements = array(
			array(
				'name'            => __( 'Code Block Element. Add your own HTML/Javascript here', 'axisbuilder' ),
				'desc'            => __( 'Enter some text/code. You can also add plugin shortcode here. (Adding theme shortcode is not recommended though)', 'axisbuilder' ),
				'id'              => 'content',
				'std'             => '',
				'type'            => 'textarea',
				'class'           => 'code',
				'container_class' => 'field-fullwidth',
			),

			array(
				'name'    => __( 'Code Wrapper Element', 'axisbuilder' ),
				'desc'    => __( 'Wrap your code into a html tag (i.e. pre or code tag). Insert the tag without <>', 'axisbuilder' ),
				'id'      => 'wrapper_element',
				'type'    => 'input',
				'std'     => ''
			),

			array(
				'name'     => __( 'Code Wrapper Element Attributes', 'axisbuilder' ),
				'desc'     => __( 'Enter one or more attribute values which should be applied to the wrapper element. Leave the field empty if no attributes are required.', 'axisbuilder' ),
				'id'       => 'wrapper_element_attributes',
				'std'      => '',
				'required' => array( 'wrapper_element', 'not', '' ),
				'type'     => 'input'
			),

			array(
				'name'  => __( 'Escape HTML Code', 'axisbuilder' ),
				'desc'  => __( 'WordPress will convert the html tags to readable text.', 'axisbuilder' ),
				'id'    => 'escape_html',
				'std'   => false,
				'type'  => 'checkbox'
			),

			array(
				'name'  => __( 'Disable Shortcode Processing', 'axisbuilder' ),
				'desc'  => __( 'Check if you want to disable the shortcode processing for this code block.', 'axisbuilder' ),
				'id'    => 'deactivate_shortcode',
				'std'   => false,
				'type'  => 'checkbox'
			),

			array(
				'name'  => __( 'Deactivate schema.org markup', 'axisbuilder' ),
				'desc'  => __( 'Output the code without any additional wrapper elements. (not recommended)', 'axisbuilder' ),
				'id'    => 'deactivate_wrapper',
				'std'   => false,
				'type'  => 'checkbox'
			)
		);
	}

	/**
	 * Editor Elements.
	 *
	 * This method defines the visual appearance of an element on the Builder canvas.
	 */
	public function editor_element( $params ) {
		$params['innerHtml']  = '';
		$params['innerHtml'] .= ( isset( $this->shortcode['image'] ) && ! empty( $this->shortcode['image'] ) ) ? '<img src="' . $this->shortcode['image'] . '" alt="' . $this->title . '" />' : '<i class="' . $this->shortcode['icon'] . '"></i>';
		$params['innerHtml'] .= '<div class="axisbuilder-element-label">' . $this->title . '</div>';

		return (array) $params;
	}

	/**
	 * Frontend Shortcode Handle.
	 * @param  array  $atts      Array of attributes.
	 * @param  string $content   Text within enclosing form of shortcode element.
	 * @param  string $shortcode The shortcode found, when == callback name.
	 * @param  string $meta      Meta data.
	 * @return string            Returns the modified html string.
	 */
	public function shortcode_handle( $atts, $content = '', $shortcode = '', $meta = '' ) {

	}
}
