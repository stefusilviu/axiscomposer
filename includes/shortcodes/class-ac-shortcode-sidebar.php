<?php
/**
 * Sidebar or Widget-Area Shortcode
 *
 * @extends  AC_Shortcode
 * @version  1.0.0
 * @package  AxisComposer/Shortcodes
 * @category Shortcodes
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Shortcode_Sidebar Class
 */
class AC_Shortcode_Sidebar extends AC_Shortcode {

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
		$this->id                 = 'sidebar';
		$this->method_title       = __( 'Widget Area', 'axiscomposer' );
		$this->method_description = __( 'Display one of the themes widget areas', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 330,
			'type'    => 'content',
			'name'    => 'ac_sidebar',
			'icon'    => 'icon-sidebar',
			'image'   => AC()->plugin_url() . '/assets/images/content/sidebar.png', // Fallback if icon is missing :)
			'target'  => 'ac-target-insert',
			'tinyMCE' => array( 'instantInsert' => '[ac_sidebar widget_area="Displayed Everywhere"]' ),
		);
	}

	/**
	 * Editor Elements.
	 *
	 * This method defines the visual appearance of an element on the Builder canvas.
	 */
	public function editor_element( $params ) {

		// Get all active sidebars
		$sidebars = ac_get_sidebars();

		if ( empty( $params['args']['widget_area'] ) ) {
			list( $widget_area ) = array_keys( $sidebars );
			$params['args']['widget_area'] = esc_attr( $widget_area );
		}

		$params['innerHtml']  = '';
		$params['innerHtml'] .= ( isset( $this->shortcode['image'] ) && ! empty( $this->shortcode['image'] ) ) ? '<img src="' . $this->shortcode['image'] . '" alt="' . $this->method_title . '" />' : '<i class="' . $this->shortcode['icon'] . '"></i>';
		$params['innerHtml'] .= '<div class="ac-element-label">' . $this->method_title . '</div>';
		$params['innerHtml'] .= ac_select_html( 'axiscomposer_sidebar', array(
			'default'           => $params['args']['widget_area'],
			'class'             => 'ac-recalc-shortcode',
			'options'           => $sidebars,
			'custom_attributes' => array(
				'data-attr' => 'widget_area'
			)
		) );

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
		if ( ! isset( $atts['widget_area'] ) ) {
			return;
		}

		ob_start();
		?>
		<div class="axiscomposer ac-sidebar clearfix"><?php
			if ( is_dynamic_sidebar( $atts['widget_area'] ) ) {
				dynamic_sidebar( $atts['widget_area'] );
			}
		?></div>
		<?php

		return ob_get_clean();
	}
}
