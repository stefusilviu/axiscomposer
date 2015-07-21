<?php
/**
 * Call To Action Shortcode
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
 * AC_Shortcode_Calltoaction Class
 */
class AC_Shortcode_Calltoaction extends AC_Shortcode {

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
		$this->id                 = 'calltoaction';
		$this->method_title       = __( 'Call To Action', 'axiscomposer' );
		$this->method_description = __( 'Creates a call to action button', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 130,
			'type'    => 'content',
			'name'    => 'ac_calltoaction',
			'icon'    => 'icon-calltoaction',
			'image'   => AC()->plugin_url() . '/assets/images/content/calltoaction.png', // Fallback if icon is missing :)
			'target'  => 'ac-target-insert',
			'tinyMCE' => array( 'disable' => true ),
		);
	}

	/**
	 * Initialise Shortcode Settings Form Fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'label' => array(
				'title'             => __( 'Button Label', 'axiscomposer' ),
				'description'       => __( 'This option lets you define button label.', 'axiscomposer' ),
				'default'           => __( 'Add your button label here.', 'axiscomposer' ),
				'type'              => 'text',
				'desc_tip'          => true
			),
			'link' => array(
				'title'             => __( 'Button Link', 'axiscomposer' ),
				'description'       => __( 'This option lets you enter button link.', 'axiscomposer' ),
				'type'              => 'text',
				'desc_tip'          => true,
				'default'           => ''
			),
			'cta_maintext' => array(
				'title'             => __( 'CTA Main Text', 'axiscomposer' ),
				'description'       => __( 'Enter main text description for call to action.' ),
				'type'              => 'text',
				'desc_tip'          => true,
				'default'           => ''
			),
			'cta_extratext' => array(
				'title'             => __( 'CTA Addtional Text', 'axiscomposer' ),
				'description'       => __( 'Enter addtional text description for call to action.' ),
				'type'              => 'text',
				'desc_tip'          => true,
				'default'           => ''
			),
			'iconfont' => array(
				'title'             => __( 'Button Icon', 'axiscomposer' ),
				'description'       => __( 'Select an icon for your Button below.', 'axiscomposer' ),
				'type'              => 'iconfont',
				'default'           => 'entypo-fontello',
				'options'           => ac_get_iconfont_charlist()
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
			'label'         => '',
			'link'          => '',
			'cta_maintext'  => '',
			'cta_extratext' => '',
			'iconfont'      => ''
		), $atts, $this->shortcode['name'] ) );

		// Don't display if button label is missing
		if ( empty( $label ) || empty( $cta_maintext )  ) {
			return;
		}

		$custom_class = empty( $meta['custom_class'] ) ? '' : $meta['custom_class'];

		ob_start();
		?>
		<section class="axiscomposer calltoaction-section">
			<div class="ac-calltoaction <?php echo esc_attr( $custom_class ); ?>">
				<div class="ac-calltoaction-content">
					<h3><?php echo esc_attr( $cta_maintext ); ?></h3>
					<p><?php echo esc_attr( $cta_extratext ); ?></p>
				</div>
				<a class="ac-calltoaction-button" href="<?php echo esc_attr( $link ); ?>" title="<?php echo esc_attr( $label ); ?>"><?php echo esc_attr( $label ); ?></a>
			</div>
		</section>
		<?php

		return ob_get_clean();
	}
}
