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
			'target'  => 'ac-target-insert',
			'tinyMCE' => array( 'disable' => false ),
		);
	}

	/**
	 * Get Settings.
	 * @return array
	 */
	public function get_settings() {

		$this->elements = array(
			'content' => array(
				'title'             => __( 'Content', 'axiscomposer' ),
				'description'       => __( 'Enter some text/code. You can also add plugin shortcode here. (Adding theme shortcode is not recommended though)', 'axiscomposer' ),
				'type'              => 'textarea',
				'desc_tip'          => true,
				'default'           => ''
			),
			'wrapper_element' => array(
				'title'             => __( 'Wrapper Element', 'axiscomposer' ),
				'description'       => __( 'This option lets you wrap code into a html tag (i.e. pre|code).', 'axiscomposer' ),
				'class'             => 'availability',
				'type'              => 'text',
				'desc_tip'          => true,
				'default'           => ''
			),
			'element_attributes' => array(
				'title'             => __( 'Element Attributes', 'axiscomposer' ),
				'description'       => __( 'Enter one or more attribute values which should be applied to the wrapper element. Leave the field empty if no attributes are required.', 'axiscomposer' ),
				'type'              => 'text',
				'desc_tip'          => true,
				'default'           => ''
			),
			'escape_html' => array(
				'title'             => __( 'Escape HTML', 'axiscomposer' ),
				'label'             => __( 'Enable to convert the html tags to readable text.', 'axiscomposer' ),
				'type'              => 'checkbox',
				'checkboxgroup'     => '',
				'default'           => 'no'
			),
			'disable_markup' => array(
				'title'             => __( 'Disable Markup', 'axiscomposer' ),
				'label'             => __( 'Disable the schema.org markup for this code block.', 'axiscomposer' ),
				'type'              => 'checkbox',
				'checkboxgroup'     => '',
				'default'           => 'no'
			),
			'disable_shortcode' => array(
				'title'             => __( 'Disable Shortcode', 'axiscomposer' ),
				'label'             => __( 'Disable the shortcode processing for this code block.', 'axiscomposer' ),
				'type'              => 'checkbox',
				'checkboxgroup'     => '',
				'default'           => 'no'
			),
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
		$params['innerHtml'] .= '<div class="ac-element-label">' . $this->title . '</div>';

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
