<?php
/**
 * Special Heading Shortcode
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
 * AC_Shortcode_Heading Class
 */
class AC_Shortcode_Heading extends AC_Shortcode {

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
		$this->id                 = 'heading';
		$this->method_title       = __( 'Special Heading', 'axiscomposer' );
		$this->method_description = __( 'Creates a Special Heading', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 80,
			'type'    => 'content',
			'name'    => 'ac_heading',
			'icon'    => 'icon-heading',
			'image'   => AC()->plugin_url() . '/assets/images/content/heading.png', // Fallback if icon is missing :)
			'target'  => 'ac-target-insert',
			'tinyMCE' => array( 'disable' => true ),
		);
	}

	/**
	 * Initialise Shortcode Settings Form Fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'heading' => array(
				'title'             => __( 'Heading Text', 'axiscomposer' ),
				'description'       => __( 'This option lets you enter heading text.', 'axiscomposer' ),
				'default'           => __( 'Add your heading text here.', 'axiscomposer' ),
				'type'              => 'text',
				'desc_tip'          => true
			),
			'tag' => array(
				'title'             => __( 'Heading Type', 'axiscomposer' ),
				'description'       => __( 'This sets the custom heading tag of the text.', 'axiscomposer' ),
				'default'           => 'H3',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'H1' => __( 'H1', 'axiscomposer' ),
					'H2' => __( 'H2', 'axiscomposer' ),
					'H3' => __( 'H3', 'axiscomposer' ),
					'H4' => __( 'H4', 'axiscomposer' ),
					'H5' => __( 'H5', 'axiscomposer' ),
					'H6' => __( 'H6', 'axiscomposer' )
				)
			),
			'position' => array(
				'title'             => __( 'Heading Style', 'axiscomposer' ),
				'description'       => __( 'This allows to set align of heading text.', 'axiscomposer' ),
				'default'           => 'ac-left',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'ac-left'    => __( 'Align Left', 'axiscomposer' ),
					'ac-center'  => __( 'Align Center', 'axiscomposer' ),
					'ac-right'   => __(' Align Right', 'axiscompoer' )
				)
			),
			'size' => array(
				'title'             => __( 'Heading Size', 'axiscomposer' ),
				'description'       => __( 'This sets the custom font size of the heading text.', 'axiscomposer' ),
				'type'              => 'number',
				'desc_tip'          => true,
				'default'           => 20,
				'custom_attributes' => array(
					'min' => 20,
					'max' => 90
				)
			),
			'subheading' => array(
				'title'             => __( 'Subheading', 'axiscomposer' ),
				'description'       => __( 'This option lets you control the display of subheading.', 'axiscomposer' ),
				'default'           => 'default',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'default'          => __( 'No Subheading', 'axiscomposer' ),
					'subheading-above' => __( 'Display Subheading Above', 'axiscomposer' ),
					'subheading-below' => __( 'Display Subheading Below', 'axiscomposer' )
				)
			),
			'subheading_text' => array(
				'title'             => __( 'Subheading Text', 'axiscomposer' ),
				'description'       => __( 'Enter an extra descriptive subheading here.', 'axiscomposer' ),
				'type'              => 'textarea',
				'desc_tip'          => true,
				'default'           => ''
			),
			'subheading_size' => array(
				'title'             => __( 'Subheading Size', 'axiscomposer' ),
				'description'       => __( 'This sets the custom font size of the subheading text.', 'axiscomposer' ),
				'type'              => 'number',
				'desc_tip'          => true,
				'default'           => 16,
				'custom_attributes' => array(
					'min' => 10,
					'max' => 40
				)
			),
			'padding' => array(
				'title'             => __( 'Padding Bottom', 'axiscomposer' ),
				'description'       => __( 'This sets the custom bottom padding in pixel.', 'axiscomposer' ),
				'type'              => 'number',
				'desc_tip'          => true,
				'default'           => 0,
				'custom_attributes' => array(
					'min'  => 0,
					'max'  => 120,
				)
			),
			'font_color' => array(
				'title'             => __( 'Font Color', 'axiscomposer' ),
				'description'       => __( 'This option lets you limit which color you are willing to use.', 'axiscomposer' ),
				'default'           => 'default',
				'type'              => 'select',
				'class'             => 'availability ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'default'  => __( 'Theme Default Color', 'axiscomposer' ),
					'specific' => __( 'Define Custom Color', 'axiscomposer' )
				)
			),
			'color' => array(
				'title'             => __( 'Custom Font Color', 'axiscomposer' ),
				'description'       => __( 'This sets the custom font color of the heading text.', 'axiscomposer' ),
				'type'              => 'color',
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
			'heading'         => '',
			'tag'             => '',
			'size'            => '',
			'position'        => '',
			'subheading'      => '',
			'subheading_text' => '',
			'subheading_size' => '',
			'padding'         => '',
			'font_color'      => '',
			'color'           => ''
		), $atts, $this->shortcode['name'] ) );

		// Don't display if the heading text is empty
		if ( empty( $heading ) ) {
			return;
		}

		$sub_heading  = "<div class='ac-subheading' style='font-size:{$subheading_size}px;'><p>";
		$sub_heading .= $subheading_text;
		$sub_heading .= "</p></div>";

		$above_heading = $below_heading = '';

		if ( $subheading === 'subheading-above' ) {
			$above_heading = $sub_heading;
		} elseif( $subheading === 'subheading-below' ) {
			$below_heading = $sub_heading;
		}

		$custom_style = "padding-bottom:{$padding}px;";

		if ( $font_color === "specific" ) {
			$custom_style .= "color:{$color};";
		}

		if ( ! empty( $custom_style ) ) {
			$custom_style = "style='{$custom_style}'";
		}

		$custom_class = empty( $meta['custom_class'] ) ? '' : $meta['custom_class'];
		$custom_class .= $position;

		$heading = apply_filters( 'axiscomposer_format_heading', wptexturize( $heading ) );

		ob_start();
		?>
		<div class="axiscomposer ac-special-heading <?php echo $custom_class; ?>" <?php echo $custom_style; ?>>
			<?php echo $above_heading; ?>
			<div class="ac-special-heading-tag" style="font-size:<?php echo $size; ?>px;">
				<<?php echo $tag; ?>><?php echo $heading; ?></<?php echo $tag; ?>>
			</div>
			<?php echo $below_heading; ?>
		</div>
		<?php

		return ob_get_clean();
	}
}
