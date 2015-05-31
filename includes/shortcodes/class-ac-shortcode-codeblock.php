<?php
/**
 * Codeblock Shortcode
 *
 * @extends     AC_Shortcode
 * @package     AxisComposer/Shortcodes
 * @category    Shortcodes
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Shortcode_Codeblock Class
 */
class AC_Shortcode_Codeblock extends AC_Shortcode {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id        = 'codeblock';
		$this->title     = __( 'Code Block', 'axiscomposer' );
		$this->tooltip   = __( 'Add text or code to your website without any formatting or text optimization. Can be used for HTML/CSS/Javascript', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 350,
			'type'    => 'content',
			'name'    => 'ac_codeblock',
			'icon'    => 'icon-codeblock',
			'image'   => AC()->plugin_url() . '/assets/images/content/codeblock.png', // Fallback if icon is missing :)
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
				'name'            => __( 'Code Block Element. Add your own HTML/Javascript here', 'axiscomposer' ),
				'desc'            => __( 'Enter some text/code. You can also add plugin shortcode here. (Adding theme shortcode is not recommended though)', 'axiscomposer' ),
				'id'              => 'content',
				'std'             => '',
				'type'            => 'textarea',
				'class'           => 'code',
				'container_class' => 'field-fullwidth',
			),

			array(
				'name'    => __( 'Code Wrapper Element', 'axiscomposer' ),
				'desc'    => __( 'Wrap your code into a html tag (i.e. pre or code tag). Insert the tag without <>', 'axiscomposer' ),
				'id'      => 'wrapper_element',
				'type'    => 'input',
				'std'     => ''
			),

			array(
				'name'     => __( 'Code Wrapper Element Attributes', 'axiscomposer' ),
				'desc'     => __( 'Enter one or more attribute values which should be applied to the wrapper element. Leave the field empty if no attributes are required.', 'axiscomposer' ),
				'id'       => 'wrapper_element_attributes',
				'std'      => '',
				'required' => array( 'wrapper_element', 'not', '' ),
				'type'     => 'input'
			),

			array(
				'name'  => __( 'Escape HTML Code', 'axiscomposer' ),
				'desc'  => __( 'WordPress will convert the html tags to readable text.', 'axiscomposer' ),
				'id'    => 'escape_html',
				'std'   => false,
				'type'  => 'checkbox'
			),

			array(
				'name'  => __( 'Disable Shortcode Processing', 'axiscomposer' ),
				'desc'  => __( 'Check if you want to disable the shortcode processing for this code block.', 'axiscomposer' ),
				'id'    => 'deactivate_shortcode',
				'std'   => false,
				'type'  => 'checkbox'
			),

			array(
				'name'  => __( 'Deactivate schema.org markup', 'axiscomposer' ),
				'desc'  => __( 'Output the code without any additional wrapper elements. (not recommended)', 'axiscomposer' ),
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
