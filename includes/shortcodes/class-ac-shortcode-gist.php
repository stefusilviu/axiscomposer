<?php
/**
 * Gist Shortcode
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
 * AC_Shortcode_Gist Class
 */
class AC_Shortcode_Gist extends AC_Shortcode {

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
		$this->id                 = 'gist';
		$this->method_title       = __( 'Gist Snippet', 'axiscomposer' );
		$this->method_description = __( 'Embed a gist snippet with one or multiple files display.', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 350,
			'type'    => 'content',
			'name'    => 'ac_gist',
			'icon'    => 'icon-gist',
			'image'   => AC()->plugin_url() . '/assets/images/content/gist.png', // Fallback if icon is missing :)
			'target'  => 'ac-target-insert',
			'tinyMCE' => array( 'disable' => false ),
		);
	}

	/**
	 * Initialise Shortcode Settings Form Fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
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
			)
		);
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
		extract( shortcode_atts( array(
			'wrapper_element'    => '',
			'element_attributes' => '',
			'escape_html'        => '',
			'disable_markup'     => '',
			'disable_shortcode'  => ''
		), $atts, $this->shortcode['name'] ) );

		$custom_class = empty( $meta['custom_class'] ) ? '' : $meta['custom_class'];

		ob_start();
		?>
		<section class="axiscomposer codeblock-section">
			<div class="ac-codeblock <?php echo esc_attr( $custom_class ); ?>">
				<pre><?php echo $content; ?></pre>
			</div>
		</section>
		<?php

		return ob_get_clean();
	}
}
