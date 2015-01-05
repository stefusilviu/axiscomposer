<?php
/**
 * Section Shortcode
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
 * AB_Shortcode_Section Class
 */
class AB_Shortcode_Section extends AB_Shortcode {

	public static $section_count = 0;

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
		$this->id        = 'axisbuilder_section';
		$this->title     = __( 'Color Section', 'axisbuilder' );
		$this->tooltip   = __( 'Creates a section with unique background image and colors', 'axisbuilder' );
		$this->shortcode = array(
			'sort'        => 11,
			'type'        => 'layout',
			'name'        => 'ab_section',
			'icon'        => 'icon-section',
			'image'       => AB()->plugin_url() . '/assets/images/layouts/section.png', // Fallback if icon is missing :)
			'target'      => 'axisbuilder-target-insert',
			'tinyMCE'     => array( 'disable' => true ),
			'drag-level'  => 1,
			'drop-level'  => 1,
			'html-render' => false
		);
	}


	/**
	 * Popup Elements
	 *
	 * If this method is defined the elements automatically gets an edit button.
	 * When pressed opens a popup modal window that allows to edit the element properties.
	 */
	public function popup_elements() {
		$this->elements = array(
			array(
				'name'     => __( 'Custom Background Color', 'axisbuilder' ),
				'desc'     => __( 'Select a custom background color for your Section here. Leave empty to use the default.', 'axisbuilder' ),
				'id'       => 'background_color',
				'type'     => 'colorpicker',
				'std'      => ''
			),
			array(
				'name'     => __( 'Custom Background Image', 'axisbuilder' ),
				'desc'     => __( 'Either upload a new, or choose an existing image from your media library. Leave empty if you want to use the background image of the color scheme defined above.', 'axisbuilder' ),
				'title'    => __( 'Insert Image', 'axisbuilder' ),
				'button'   => __( 'Insert', 'axisbuilder' ),
				'id'       => 'src',
				'std'      => '',
				'type'     => 'image'
			),
			array(
				'name'     => __( 'Background Attachment', 'axisbuilder' ),
				'desc'     => __( 'Background can either scroll with the page, be fixed or scroll with a parallax motion.', 'axisbuilder' ),
				'id'       => 'background_attachment',
				'std'      => 'scroll',
				'type'     => 'select',
				'required' => array( 'src', 'not', '' ),
				'subtype'  => array(
					__( 'Scroll', 'axisbuilder' )   => 'scroll',
					__( 'Fixed', 'axisbuilder' )    => 'fixed',
					__( 'Parallax', 'axisbuilder' ) => 'parallax'
				)
			),
			array(
				'name'     => __( 'Background Position', 'axisbuilder' ),
				'id'       => 'background_position',
				'std'      => 'top left',
				'type'     => 'select',
				'required' => array( 'src', 'not', '' ),
				'subtype'  => array(
					__( 'Top Left', 'axisbuilder' )       =>'top left',
					__( 'Top Center', 'axisbuilder' )     =>'top center',
					__( 'Top Right', 'axisbuilder' )      =>'top right',
					__( 'Bottom Left', 'axisbuilder' )    =>'bottom left',
					__( 'Bottom Center', 'axisbuilder' )  =>'bottom center',
					__( 'Bottom Right', 'axisbuilder' )   =>'bottom right',
					__( 'Center Left', 'axisbuilder' )    =>'center left',
					__( 'Center Center', 'axisbuilder' )  =>'center center',
					__( 'Center Right', 'axisbuilder' )   =>'center right'
				)
			),
			array(
				'name'     => __( 'Background Repeat', 'axisbuilder' ),
				'id'       => 'background_repeat',
				'std'      => 'no-repeat',
				'type'     => 'select',
				'required' => array( 'src', 'not', '' ),
				'subtype'  => array(
					__( 'No Repeat', 'axisbuilder' )         => 'no-repeat',
					__( 'Tile', 'axisbuilder' )              => 'repeat',
					__( 'Tile Horizontally', 'axisbuilder' ) => 'repeat-x',
					__( 'Tile Vertically', 'axisbuilder' )   => 'repeat-y',
					__( 'Stretch to Fit', 'axisbuilder' )    => 'stretch'
				)
			),
			array(
				'name'     => __( 'Background Video', 'axisbuilder' ),
				'desc'     => __( 'You can also place a video as background for your section. Enter the URL to the Video. Currently supported are Youtube, Vimeo and direct linking of web-video files (mp4, webm, ogv)', 'axisbuilder' ) . '<br /><br />' . __( 'Working examples Vimeo &amp; YouTube:', 'axisbuilder' ) . '<br /><strong>http://vimeo.com/1084537</strong><br/><strong>https://www.youtube.com/watch?v=NJtPPbgdt7A</strong><br/><br/>',
				'id'       => 'video',
				'std'      => '',
				'type'     => 'input'
			),
			array(
				'name'     => __( 'Video Aspect Ratio', 'axisbuilder' ),
				'desc'     => __( 'In order to calculate the correct height and width for the video slide you need to enter a aspect ratio (width:height). usually: 16:9 or 4:3.', 'axisbuilder' ),
				'id'       => 'video_ratio',
				'std'      => '16:9',
				'type'     => 'input',
				'required' => array( 'video', 'not', '' ),
			),
			array(
				'name'     => __( 'Hide video on Mobile Devices?', 'axisbuilder' ),
				'desc'     => __( 'You can chose to hide the video entirely on Mobile devices and instead display the Section Background image', 'axisbuilder' ) . '<br /><small>' . __( 'Most smartphones can\'t autoplay videos to prevent bandwidth problems for the user', 'axisbuilder' ) . '</small>',
				'id'       => 'video_mobile_disabled',
				'std'      => '',
				'type'     => 'checkbox',
				'required' => array( 'video', 'not', '' ),
			),
			array(
				'name'     => __( 'Section Minimum Height', 'axisbuilder' ),
				'desc'     => __( 'Define a minimum height for the section. Content within the section will be centered vertically within the section', 'axisbuilder' ),
				'id'       => 'min_height',
				'std'      => '',
				'type'     => 'select',
				'subtype'  => array(
					__( 'Use content within section to define Section height', 'axisbuilder' ) => 'default',
					__( 'At least 100&percnt; of Browser Window height', 'axisbuilder' )       => '100',
					__( 'At least 75&percnt; of Browser Window height', 'axisbuilder' )        => '75',
					__( 'At least 50&percnt; of Browser Window height', 'axisbuilder' )        => '50',
					__( 'At least 25&percnt; of Browser Window height', 'axisbuilder' )        => '25',
					__( 'Custom height in pixel', 'axisbuilder' )                              => 'custom'
				)
			),
			array(
				'name'     => __( 'Section custom height', 'axisbuilder' ),
				'desc'     => __( 'Define a minimum height for the section. Use a pixel value. eg: 500px', 'axisbuilder' ),
				'id'       => 'custom_min_height',
				'std'      => '500px',
				'type'     => 'input',
				'required' => array( 'min_height', 'equals', 'custom' ),
			),
			array(
				'name'     => __( 'Section Padding', 'axisbuilder' ),
				'desc'     => __( 'Define the sections top and bottom padding', 'axisbuilder' ),
				'id'       => 'padding',
				'std'      => 'default',
				'type'     => 'select',
				'subtype'  => array(
					__( 'No Padding', 'axisbuilder' )      => 'none',
					__( 'Small Padding', 'axisbuilder' )   => 'small',
					__( 'Default Padding', 'axisbuilder' ) => 'default',
					__( 'Large Padding', 'axisbuilder' )   => 'large'
				)
			),
			array(
				'name'     => __( 'Section Top Border Styling', 'axisbuilder' ),
				'desc'     => __( 'Chose a border styling for the top of your section', 'axisbuilder' ),
				'id'       => 'shadow',
				'std'      => 'no-shadow',
				'type'     => 'select',
				'subtype'  => array(
					__( 'Display simple top border', 'axisbuilder' ) => 'no-shadow',
					__( 'Display a small styling shadow at the top of the section', 'axisbuilder' ) => 'shadow',
					__( 'No border styling', 'axisbuilder' ) => 'no-border-styling'
				)
			),
			array(
				'name'     => __( 'Section Bottom Border Styling', 'axisbuilder' ),
				'desc'     => __( 'Chose a border styling for the bottom of your section', 'axisbuilder' ),
				'id'       => 'bottom_border',
				'std'      => 'none',
				'type'     => 'select',
				'subtype'  => array(
					__( 'No border styling', 'axisbuilder' ) => 'none',
					__( 'Display a small arrow that points down to the next section', 'axisbuilder' ) => 'arrow-down',
				)
			),
			array(
				'name'     => __( 'For Developers: Section ID', 'axisbuilder' ),
				'desc'     => __( 'Apply a custom ID Attribute to the section, so you can apply a unique style via CSS. This option is also helpful if you want to use anchor links to scroll to a sections when a link is clicked', 'axisbuilder' ) . '<br /><br />' . __( 'Use with caution and make sure to only use allowed characters. No special characters can be used.', 'axisbuilder' ),
				'id'       => 'id',
				'std'      => '',
				'type'     => 'input'
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

		$data['modal-title']       = $this->title;
		$data['modal-action']      = $this->shortcode['name'];
		$data['dragdrop-level']    = $this->shortcode['drag-level'];
		$data['shortcode-handler'] = $this->shortcode['name'];
		$data['shortcode-allowed'] = $this->shortcode['name'];

		if ( ! empty( $this->shortcode['modal-on-load'] ) ) {
			$data['modal-on-load'] = $this->shortcode['modal-on-load'];
		}

		$output = '<div class="axisbuilder-layout-section popup-animation axisbuilder-no-visual-updates axisbuilder-drag ' . $this->shortcode['name'] . '"' . axisbuilder_html_data_string( $data ) . '>';
			$output .= '<div class="axisbuilder-sorthandle menu-item-handle">';
				$output .= '<span class="axisbuilder-element-title">' . $this->title . '</span>';
				if ( isset( $this->shortcode['popup_editor'] ) ) {
					$output .= '<a class="axisbuilder-edit edit-element-icon" href="#edit" title="' . __( 'Edit Section', 'axisbuilder' ) . '">' . __( 'Edit Section', 'axisbuilder' ) . '</a>';
				}
				$output .= '<a class="axisbuilder-trash trash-element-icon" href="#trash" title="' . __( 'Delete Section', 'axisbuilder' ) . '">' . __( 'Delete Section', 'axisbuilder' ) . '</a>';
				$output .= '<a class="axisbuilder-clone clone-element-icon" href="#clone" title="' . __( 'Clone Section',  'axisbuilder' ) . '">' . __( 'Clone Section',  'axisbuilder' ) . '</a>';
			$output .= '</div>';
			$output .= '<div class="axisbuilder-inner-shortcode axisbuilder-connect-sort axisbuilder-drop" data-dragdrop-level="' . $data['dragdrop-level'] . '">';
				$output .= '<textarea data-name="text-shortcode" rows="4" cols="20">' . ab_create_shortcode_data( $this->shortcode['name'], $content, $args ) . '</textarea>';
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
		$output = '';
		self::$section_count++;

		$shortcode_atts = array(
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
			'bottom_border'         => 'none',
			'id'                    => '',
			'custom_markup'         => ''
		);

		$atts = shortcode_atts( $shortcode_atts, $atts, $this->shortcode['name'] );

		extract( $atts );

		$class      = 'axisbuilder-section section-padding-' . $padding . ' section-' . $shadow . ' background-' . $background_attachment . '';
		$background = empty( $background_color ) ? '' : 'style="background-color: ' . $background_color . '"';

		// Set Background Image
		if ( $src != '' ) {
			$background .= 'background-image: url(' . $src . ');';

			if ( $repeat == 'stretch' ) {
				$class      .= 'axisbuilder-full-stretch';
				$background .= 'background-repeat: no-repeat';
			} else {
				$background .= 'background-repeat: ' . $repeat;
			}
		}

		$params = array();

		$params['attach'] = '';
		$params['custom_markup'] = $meta['custom_markup'];
		$params['id'] = empty( $id ) ? 'axisbuilder-section-' . self::$section_count : $id;
	}
}
