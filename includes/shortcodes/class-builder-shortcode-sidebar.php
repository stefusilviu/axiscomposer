<?php
/**
 * Sidebar or Widget-Area Shortcode
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
 * AB_Shortcode_Sidebar Class
 */
class AB_Shortcode_Sidebar extends AB_Shortcode {

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
		$this->id        = 'axisbuilder_sidebar';
		$this->title     = __( 'Widget Area', 'axisbuilder' );
		$this->tooltip   = __( 'Display one of the themes widget areas', 'axisbuilder' );
		$this->shortcode = array(
			'sort'    => 330,
			'type'    => 'content',
			'name'    => 'ab_sidebar',
			'icon'    => 'icon-sidebar',
			'image'   => AB()->plugin_url() . '/assets/images/content/sidebar.png', // Fallback if icon is missing :)
			'target'  => 'axisbuilder-target-insert',
			'tinymce' => array( 'instantInsert' => '[ab_sidebar widget_area="Displayed Everywhere"]' ),
		);
	}

	/**
	 * Editor Elements.
	 *
	 * This method defines the visual appearance of an element on the Builder canvas.
	 */
	public function editor_element( $params ) {

		// Fetch all registered sidebars
		$sidebars = axisbuilder_get_registered_sidebars();

		if ( empty( $params['args']['widget_area'] ) ) {
			$params['args']['widget_area'] = reset( $sidebars );
		}

		$element = array(
			'type'    => 'select',
			'subtype' => $sidebars,
			'data'    => array( 'attr' => 'widget_area' ),
			'class'   => 'axisbuilder-recalculate-shortcode',
			'std'     => htmlspecialchars_decode( $params['args']['widget_area'] )
		);

		$params['innerHtml']  = '';
		$params['innerHtml'] .= ( isset( $this->shortcode['image'] ) && ! empty( $this->shortcode['image'] ) ) ? '<img src="' . $this->shortcode['image'] . '" alt="' . $this->title . '" />' : '<i class="' . $this->shortcode['icon'] . '"></i>';
		$params['innerHtml'] .= '<div class="axisbuilder-element-label">' . $this->title . '</div>';
		$params['innerHtml'] .= AB_HTML_Helper::render_element( $element );

		return (array) $params;
	}
}
