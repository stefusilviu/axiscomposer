<?php
/**
 * Section Shortcode
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
 * AC_Shortcode_Section Class
 */
class AC_Shortcode_Section extends AC_Shortcode {

	public static $section_close;
	public static $section_count = 0;

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
		$this->id                 = 'section';
		$this->method_title       = __( 'Color Section', 'axiscomposer' );
		$this->method_description = __( 'Creates a color section with custom styles', 'axiscomposer' );
		$this->shortcode = array(
			'sort'        => 11,
			'type'        => 'layout',
			'name'        => 'ac_section',
			'icon'        => 'icon-section',
			'image'       => AC()->plugin_url() . '/assets/images/layouts/section.png', // Fallback if icon is missing :)
			'target'      => 'ac-target-insert',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 1,
			'drop-level'  => 1,
			'html-render' => false
		);
	}

	/**
	 * Initialise Shortcode Settings Form Fields.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'background_color' => array(
				'title'             => __( 'Background Color', 'axiscomposer' ),
				'description'       => __( 'This sets the background color for your section. Leave empty to use the default.', 'axiscomposer' ),
				'type'              => 'color',
				'desc_tip'          => true,
				'default'           => ''
			),
			'background_image' => array(
				'title'             => __( 'Background Image', 'axiscomposer' ),
				'description'       => __( 'Either upload a new, or choose an existing image from your media library.', 'axiscomposer' ),
				'label'             => __( 'Insert Image', 'axiscomposer' ),
				'type'              => 'image',
				'desc_tip'          => true,
				'default'           => ''
			),
			'background_attachment' => array(
				'title'             => __( 'Background Attachment', 'axiscomposer' ),
				'description'       => __( 'Background can either scroll with the page, be fixed or scroll with a parallax motion.', 'axiscomposer' ),
				'default'           => 'scroll',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'scroll'   => __( 'Scroll', 'axiscomposer' ),
					'repeat'   => __( 'Fixed', 'axiscomposer' ),
					'repeat-x' => __( 'Parallax', 'axiscomposer' )
				)
			),
			'background_position' => array(
				'title'             => __( 'Background Position', 'axiscomposer' ),
				'default'           => 'top left',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'top left'      => __( 'Top Left', 'axiscomposer' ),
					'top center'    => __( 'Top Center', 'axiscomposer' ),
					'top right'     => __( 'Top Right', 'axiscomposer' ),
					'bottom left'   => __( 'Bottom Left', 'axiscomposer' ),
					'bottom center' => __( 'Bottom Center', 'axiscomposer' ),
					'bottom right'  => __( 'Bottom Right', 'axiscomposer' ),
					'center left'   => __( 'Center Left', 'axiscomposer' ),
					'center center' => __( 'Center Center', 'axiscomposer' ),
					'center right'  => __( 'Center Right', 'axiscomposer' )
				)
			),
			'background_repeat' => array(
				'title'             => __( 'Background Repeat', 'axiscomposer' ),
				'default'           => 'no-repeat',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'no-repeat' => __( 'No Repeat', 'axiscomposer' ),
					'repeat'    => __( 'Tile', 'axiscomposer' ),
					'repeat-x'  => __( 'Tile Horizontally', 'axiscomposer' ),
					'repeat-y'  => __( 'Tile Vertically', 'axiscomposer' ),
					'stretch'   => __( 'Stretch to Fit', 'axiscomposer' )
				)
			),
			'min_height' => array(
				'title'             => __( 'Section Minimum Height', 'axiscomposer' ),
				'description'       => __( 'This option lets you choose minimum height for the section. Content within the section will be centered vertically.', 'axiscomposer' ),
				'default'           => 'default',
				'type'              => 'select',
				'class'             => 'availability ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'default'  => __( 'Default section height', 'axiscomposer' ),
					'specific' => __( 'Custom height in pixel', 'axiscomposer' )
				)
			),
			'custom_min_height' => array(
				'title'             => __( 'Section Custom Height', 'axiscomposer' ),
				'description'       => __( 'Define a minimum height for the section. Use a pixel value. eg: 500px', 'axiscomposer' ),
				'type'              => 'text',
				'desc_tip'          => true,
				'default'           => '500px'
			),
			'padding' => array(
				'title'             => __( 'Section Padding', 'axiscomposer' ),
				'description'       => __( 'This option lets you define top and bottom padding for the section.', 'axiscomposer' ),
				'default'           => 'default',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'none'     => __( 'No Padding', 'axiscomposer' ),
					'small'    => __( 'Small Padding', 'axiscomposer' ),
					'large'    => __( 'Large Padding', 'axiscomposer' ),
					'default'  => __( 'Default Padding', 'axiscomposer' )
				)
			),
			'shadow' => array(
				'title'             => __( 'Section Top Border', 'axiscomposer' ),
				'description'       => __( 'This option lets you choose a top border style for the section.', 'axiscomposer' ),
				'default'           => 'border',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'none'    => __( 'No top border style', 'axiscomposer' ),
					'shadow'  => __( 'Display small top shadow', 'axiscomposer' ),
					'border'  => __( 'Display simple top border', 'axiscomposer' )
				)
			),
			'bottom_border' => array(
				'title'             => __( 'Section Bottom Border', 'axiscomposer' ),
				'description'       => __( 'This option lets you choose a bottom border style for the section.', 'axiscomposer' ),
				'default'           => 'none',
				'type'              => 'select',
				'class'             => 'ac-enhanced-select',
				'css'               => 'min-width: 350px;',
				'desc_tip'          => true,
				'options'           => array(
					'none'    => __( 'No bottom border style', 'axiscomposer' ),
					'arrow'   => __( 'Display small pointed arrow to next section', 'axiscomposer' )
				)
			),
			'id' => array(
				'title'             => __( 'Custom Section ID', 'axiscomposer' ),
				'description'       => __( 'This option lets you set custom section ID you are willing to use for customization.', 'axiscomposer' ),
				'class'             => 'ac_input_id',
				'type'              => 'text',
				'default'           => ''
			)
		);
	}

	/**
	 * Editor Elements.
	 *
	 * This method defines the visual appearance of an element on the Builder canvas.
	 */
	public function editor_element( $params ) {
		extract( $params );

		$data['modal-title']       = $this->method_title;
		$data['modal-action']      = $this->shortcode['name'];
		$data['dragdrop-level']    = $this->shortcode['drag-level'];
		$data['shortcode-handler'] = $this->shortcode['name'];
		$data['shortcode-allowed'] = $this->shortcode['name'];

		$output = '<div class="ac-layout-section modal-animation ac-no-visual-updates ac-drag ' . $this->shortcode['name'] . '"' . ac_html_data_string( $data ) . '>';
			$output .= '<div class="ac-sorthandle menu-item-handle">';
				$output .= '<span class="ac-element-title">' . $this->method_title . '</span>';
				if ( isset( $this->shortcode['has_fields'] ) ) {
					$output .= '<a class="axiscomposer-edit edit-element-icon" href="#edit" title="' . __( 'Edit Section', 'axiscomposer' ) . '">' . __( 'Edit Section', 'axiscomposer' ) . '</a>';
				}
				$output .= '<a class="axiscomposer-trash trash-element-icon" href="#trash" title="' . __( 'Delete Section', 'axiscomposer' ) . '">' . __( 'Delete Section', 'axiscomposer' ) . '</a>';
				$output .= '<a class="axiscomposer-clone clone-element-icon" href="#clone" title="' . __( 'Clone Section',  'axiscomposer' ) . '">' . __( 'Clone Section',  'axiscomposer' ) . '</a>';
			$output .= '</div>';
			$output .= '<div class="ac-inner-shortcode ac-connect-sort ac-drop" data-dragdrop-level="' . $data['dragdrop-level'] . '">';
				$output .= '<textarea data-name="text-shortcode" rows="4" cols="20">' . ac_shortcode_data( $this->shortcode['name'], $content, $args ) . '</textarea>';
				if ( $content ) {
					$content = do_shortcode_builder( $content );
					$output .= $content;
				}
			$output .= '</div>';
		$output .= '</div>';

		return $output;
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
		global $axiscomposer_config;

		$params = array();
		$output = $background = '';

		self::$section_count ++;

		// Entire list of supported attributes and their defaults
		$pairs = array(
			'background_color'      => '',
			'src'                   => '',
			'background_attachment' => 'scroll',
			'background_position'   => 'top left',
			'background_repeat'     => 'no-repeat',
			'video'                 => '',
			'video_ratio'           => '16:9',
			'video_mobile_disabled' => '',
			'min_height'            => '',
			'custom_min_height'     => '500px',
			'padding'               => 'default',
			'shadow'                => 'no-shadow',
			'bottom_border'         => '',
			'id'                    => '',
			'custom_markup'         => '',
			'attachment'            => '',
			'attachment_size'       => ''
		);

		$atts = shortcode_atts( $pairs, $atts, $this->shortcode['name'] );

		extract( $atts );

		$class = 'ac-section section-padding-' . $padding . ' ' . $shadow . ' section-background-' . $background_attachment . '';

		$params['attach'] = '';
		$params['custom_markup'] = $meta['custom_markup'];
		$params['id'] = empty( $id ) ? 'ac-section-' . self::$section_count : sanitize_html_class( $id );

		// Set Attachment Image
		if ( ! empty( $attachment ) && ! empty( $attachment_size ) ) {
			$attachment_entry = get_post( $attachment );

			if ( ! empty( $attachment_size ) ) {
				$src = wp_get_attachment_image_src( $attachment_entry->ID, $attachment_size );
				$src = empty( $src[0] ) ? '' : $src[0];
			}
		} else {
			$attachment = false;
		}

		// Set Background Image
		if ( $src != '' ) {
			$background .= 'background-image: url(' . $src . '); ';
			$background .= 'background-position: ' . $background_position . '; ';
			$background .= ( $background_attachment == 'parallax' ) ? "background-attachment: scroll; " : 'background-attachment: ' . $background_attachment . '; ';

			if ( $background_repeat == 'stretch' ) {
				$class      .= 'ac-full-stretch';
				$background .= 'background-repeat: no-repeat; ';
			} else {
				$background .= 'background-repeat: ' . $background_repeat . '; ';
			}

			if ( $background_attachment == 'parallax' ) {
				$class .= 'ac-parallax-section';
				$speed  = apply_filters( 'axiscomposer_parallax_speed', '0.3', $params['id'] );
				$attachment_class  = ( $background_repeat == 'stretch' || $background_repeat == 'stretch' ) ? 'ac-full-stretch' : '';
				$params['attach'] .= '<div class="ac-parallax ' . $attachment_class . '" data-ac-parallax-ratio="' . $speed . '" style="' . $background . '"></div>';
				$background = '';
			}

			$params['data'] = 'data-section-background-repeat="' . $background_repeat . '"';
		}

		if ( $background_color != '' ) {
			$background .= 'background-color: ' . $background_color . ';';
		}

		if ( $background ) {
			$background = 'style="' . $background . '"';
		}

		$params['class'] = $class . ' ' . $meta['el_class'];
		$params['background'] = $background;
		$params['min_height'] = $min_height;
		$params['custom_min_height'] = $custom_min_height;
		$params['video'] = $video;
		$params['video_ratio'] = $video_ratio;
		$params['video_mobile_disabled'] = $video_mobile_disabled;

		if ( isset( $meta['counter'] ) ) {
			if ( $meta['counter'] == 0 ) {
				$params['main_container'] = true;
			}

			if ( $meta['counter'] == 0 ) {
				$params['close'] = false;
			}
		}

		$axiscomposer_config['layout_container'] = 'section';

		$output .= ac_new_section( $params );
		$output .= ac_remove_autop( $content, true );

		// Set Extra arrow element
		if ( strpos( $bottom_border, 'border-extra' ) !== false ) {
			$arrow_bg = empty( $background_color ) ? apply_filters( 'axiscomposer_background_color', '#fff' ) : $background_color;
			self::$section_close = '<div class="ac-extra-border-element ' . $bottom_border . '"><div class="arrow-wrap"><div class="arrow-inner" style="background-color: ' . $arrow_bg . '"></div></div></div>';
		} else {
			self::$section_close = '';
		}

		unset( $axiscomposer_config['layout_container'] );

		return $output;
	}
}

if ( ! function_exists( 'ac_new_section' ) ) :

/**
 * Structure New Section.
 */
function ac_new_section( $params = array() ) {
	global  $axiscomposer_config, $_axiscomposer_section_markup;
	$output = $post_class = $container_style = '';

	$defaults = array(
		'close'                 => true,
		'open'                  => true,
		'open_structure'        => true,
		'open_color_wrap'       => true,
		'main_container'        => false,
		'id'                    => '',
		'class'                 => '',
		'data'                  => '',
		'style'                 => '',
		'background'            => '',
		'video'                 => '',
		'video_ratio'           => '16:9',
		'video_mobile_disabled' => '',
		'min_height'            => '',
		'custom_min_height'     => '500px',
		'attach'                => '',
		'before_new'            => '',
		'custom_markup'         => ''
	);

	$defaults = array_merge( $defaults, $params );
	extract( $defaults );

	if ( $id ) {
		$id = 'id="' . $id . '"';
	}

	// Close the Section structure when previous element was a section ;)
	if ( $close ) {
		$output .= '</div></div>' . ac_section_markup_close() . '</div>' . AC_Shortcode_Section::$section_close . '</div>';
	}

	// Open the Section Structure
	if ( $open ) {
		$post_class = 'post-entry-' . get_the_ID();

		if ( $open_color_wrap ) {
			if ( ! empty( $min_height ) ) {
				$class .= ' section-min-height-' . $min_height;

				if ( $min_height == 'custom' && $custom_min_height != '' ) {
					$custom_min_height = (int) $custom_min_height;
					$container_style   = 'style="height: ' . $custom_min_height . 'px"';
				}
			}

			$output .= $before_new;
			$output .= '<div ' . $id . ' class="' . $class . ' container-wrap" ' . $background . $data . $style . '>';
			$output .= $attach;
			$output .= apply_filters( 'axiscomposer_add_section_container', '', $defaults );
		}
	}

	// This applies only for the sections. Other fullwidth elements don't need the container for centering ;)
	if ( $open_structure ) {
		if ( ! empty( $main_container ) ) {
			$markup = 'main';
			$_axiscomposer_section_markup = 'main';
		} else {
			$markup = 'div';
		}

		$output .= '<div class="container" ' . $container_style . '>';
		$output .= '<' . $markup . ' class="template-page content ac-content-full alpha units">';
		$output .= '<div class="post-entry post-entry-type-page ' . $post_class . '">';
		$output .= '<div class="entry-content-wrapper clearfix">';
	}

	return $output;
}

endif;

if ( ! function_exists( 'ac_section_markup_close' ) ) :

/**
 * Close Section Markup.
 */
function ac_section_markup_close() {
	global  $axiscomposer_config, $_axiscomposer_section_markup;

	if ( ! empty( $_axiscomposer_section_markup ) ) {
		$_axiscomposer_section_markup = false;
		$close_markup = '</main><!-- close content main element -->';
	} else {
		$close_markup = '</div><!-- close content main div -->';
	}

	return $close_markup;
}

endif;

if ( ! function_exists( 'ac_section_after_element_content' ) ) :

/**
 * Section after Element Content.
 * @param string $meta
 */
function ac_section_after_element_content( $meta, $second_id = '', $skip_second = false, $extra = '' ) {
	$output  = '</div><!-- Close Section -->';
	$output .= $extra;

	if ( empty( $skip_second ) ) {
		$output .= ac_new_section( array( 'close' => false, 'id' => $second_id ) );
	}

	return $output;
}

endif;
