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
			'id' => array(
				'title'             => __( 'Gist ID', 'axiscomposer' ),
				'description'       => __( 'This option lets you add the public or secret Gist ID.', 'axiscomposer' ),
				'class'             => 'code ac_input_gist',
				'type'              => 'text',
				'desc_tip'          => true,
				'default'           => ''
			),
			'display' => array(
				'title'             => __( 'Gist File', 'axiscomposer' ),
				'description'       => __( 'This option lets you limit which file you are willing to display.', 'axiscomposer' ),
				'default'           => 'default',
				'type'              => 'select',
				'class'             => 'availability ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'default'  => __( 'Display all files', 'axiscomposer' ),
					'specific' => __( 'Display Specific file', 'axiscomposer' )
				)
			),
			'file' => array(
				'title'             => __( 'Specific file', 'axiscomposer' ),
				'description'       => __( 'This option lets you set the file names you want to display.', 'axiscomposer' ),
				'class'             => 'code',
				'type'              => 'text',
				'desc_tip'          => true,
				'default'           => ''
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
			'id'      => '',
			'file'    => '',
			'display' => ''
		), $atts, $this->shortcode['name'] ) );

		$specific_file = ( $display !== 'default' && ! empty( $file ) ) ? '?file=' . esc_attr( $file ) : '';

		return sprintf( '<script src="https://gist.github.com/%s.js%s"></script>', esc_attr( $id ), $specific_file );
	}
}
