<?php
/**
 * Abstract Shortcode Class
 *
 * The AxisBuilder shortcode class handles individual shortcode data.
 *
 * @class       AB_Shortcode
 * @package     AxisBuilder/Abstracts
 * @category    Shortcodes
 * @author      AxisThemes
 * @since       1.0.0
 */
abstract class AB_Shortcode {

	/**
	 * Shortcode ID
	 * @var string
	 */
	public $id;

	/**
	 * Shortcode Title
	 * @var string
	 */
	public $title;

	/**
	 * Shortcode Tooltip
	 * @var string
	 */
	public $tooltip;

	/**
	 * Shortcode Configs
	 * @var array
	 */
	public $shortcode;

	/**
	 * Shortcode Elements
	 * @var array
	 */
	public $elements;

	/**
	 * Shortcode Arguments
	 * @var array
	 */
	protected $arguments;

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		$this->shortcode_button();
		$this->shortcode_config();

		/**
		 * Shortcode AJAX Events
		 * @todo Include in AB_AJAX Class as soon.
		 */
		$this->shortcode_action();

		// Define shortcodes
		$this->add_shortcode();

		// Hooks
		if ( is_admin() ) {
			add_action( 'print_media_templates', array( $this, 'print_media_templates' ) );
		}
	}

	/**
	 * Abstract method for builder shortcode button configuration.
	 */
	abstract function shortcode_button();

	/**
	 * Abstract method for Frontend Shortcode Handle.
	 */
	abstract function shortcode_handle( $atts, $content = '', $shortcode = '', $meta = '' );

	/**
	 * AJAX Events for shortcodes.
	 */
	public function shortcode_action() {
		if ( ! empty( $this->shortcode['popup_editor'] ) ) {
			add_action( 'wp_ajax_axisbuilder_' . $this->shortcode['name'], array( $this, 'popup_editor' ) );

			// If availabe nested shortcode define them.
			if ( isset( $this->shortcode['nested'] ) ) {
				foreach ( $this->shortcode['nested'] as $shortcode ) {
					if ( method_exists( $this, $shortcode ) ) {
						add_action( 'wp_ajax_axisbuilder_' . $shortcode, array( $this, 'popup_editor' ) );
					}
				}
			}
		}
	}

	/**
	 * AJAX Popup Editor.
	 */
	public function popup_editor() {

		if ( empty( $this->elements ) ) {
			die();
		}

		check_ajax_referer( 'get-modal-elements', 'security' );

		// Display Custom CSS element
		if ( apply_filters( 'axisbuilder_show_css_element', true ) ) {
			$this->elements = $this->custom_css( $this->elements );
		}

		$elements = apply_filters( 'axisbuilder_shortcodes_elements', $this->elements );

		// If the ajax request told us that we are fetching the sub-function iterate over the array elements :)
		if ( ! empty( $_POST['params']['subelements'] ) ) {
			foreach ( $elements as $element ) {
				if ( isset( $element['subelements'] ) ) {
					$elements = $element['subelements'];
					break;
				}
			}
		}

		$elements = $this->set_defaults_value( $elements );
		echo AB_HTML_Helper::render_multiple_elements( $elements );

		die();
	}

	/**
	 * Define shortcodes.
	 */
	protected function add_shortcode() {
		if ( ! is_admin() ) {
			add_shortcode( $this->shortcode['name'], array( $this, 'prepare_shortcode_wrapper' ) );

			// If availabe nested shortcode define them.
			if ( isset( $this->shortcode['nested'] ) ) {
				foreach ( $this->shortcode['nested'] as $shortcode ) {
					if ( method_exists( $this, $shortcode ) ) {
						add_shortcode( $shortcode, array( $this, $shortcode ) );
					}
				}
			}
		}
	}

	/**
	 * Prepare Shortcode Wrapper.
	 */
	public function prepare_shortcode_wrapper( $atts, $content = '', $shortcode = '', $fake = false ) {
		$meta = array();

		// Inline shortcodes like dropcaps are basically nested shortcode and shouldn't be counted ;)
		if ( empty( $this->shortcode['inline'] ) ) {
			$meta = array(

			);
		}

		if ( isset( $atts['custom_class'] ) ) {
			// $meta['el_class']    .= ' ' . $atts['custom_class'];
			$meta['custom_class'] = $atts['custom_class'];
		}

		if ( ! isset( $meta['custom_markup'] ) ) {
			$meta['custom_markup'] = '';
		}

		$meta = apply_filters( 'axisbuilder_shortcodes_meta', $meta, $atts, $content, $shortcode );

		$content = $this->shortcode_handle( $atts, $content, $shortcode, $meta );

		return $content;
	}

	/**
	 * Auto-set shortcode configurations.
	 */
	protected function shortcode_config() {
		$load_shortcode_data = array(
			'class'       => '',
			'target'      => '',
			'drag-level'  => 3,
			'drop-level'  => -1,
			'href-class'  => get_class( $this )
		);

		// Load the default shortcode data.
		foreach ( $load_shortcode_data as $key => $data ) {
			if ( empty( $this->shortcode[$key] ) ) {
				$this->shortcode[$key] = $data;
			}
		}

		// Activate sortable editor element.
		if ( ! isset( $this->shortcode['html-render'] ) ) {
			$this->shortcode['html-render'] = 'sortable_editor_element';
		}

		// Activate popup editor if elements exist.
		if ( method_exists( $this, 'popup_elements' ) ) {
			$this->popup_elements();
			if ( isset( $this->elements ) ) {
				$this->shortcode['popup_editor'] = true;

				$this->element_iterator( $this->elements );

				// Remove any duplicate values
				if ( ! empty( $this->shortcode['modal-on-load'] ) ) {
					$this->shortcode['modal-on-load'] = array_unique( $this->shortcode['modal-on-load'] );
				}
			}
		}
	}

	/**
	 * Iterate recursively over element and sub-element trees.
	 */
	protected function element_iterator( $elements ) {
		// Check for JS callbacks to be executed on popup modal load ;)
		foreach ( $elements as $element ) {
			switch ( $element['type'] ) {
				case 'tinymce':
					$this->shortcode['modal-on-load'][] = 'modal_load_tinymce';
				break;

				case 'colorpicker':
					$this->shortcode['modal-on-load'][] = 'modal_load_colorpicker';
				break;

				case 'datepicker':
					$this->shortcode['modal-on-load'][] = 'modal_load_datepicker';
				break;
			}
		}
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
	 * Add-on for Custom CSS class to each element.
	 */
	public function custom_css( $elements ) {
		$elements[] = array(
			'id'   => 'custom_class',
			'name' => __( 'Custom CSS Class', 'axisbuilder' ),
			'desc' => __( 'Add a custom css class for the element here. Ensure the use of allowed characters (latin characters, underscores, dashes and numbers)', 'axisbuilder' ),
			'type' => 'input',
			'std'  => ''
		);

		return $elements;
	}

	/**
	 * Render shortcode canvas elements.
	 */
	public function prepare_editor_element( $content = false, $args = array() ) {

		// Extract default content unless it was already passed
		if ( $content === false ) {
			$content = $this->get_default_content();
		}

		// Extract default arguments unless it was already passed
		if ( empty( $args ) ) {
			$args = $this->get_default_arguments();
		}

		// Unset content key that resides in arguments passed
		if ( isset( $args['content'] ) ) {
			unset( $args['content'] );
		}

		// Let's initialized params as an array
		$params = array();

		$params['args']    = $args;
		$params['data']    = isset( $this->shortcode['modal'] ) ? $this->shortcode['modal'] : '';
		$params['content'] = $content;

		// Fetch the parameters array from the child classes visual_appearance which should describe the html code :)
		$params = $this->editor_element( $params );

		// Render the sortable or default editor elements.
		if ( $this->shortcode['html-render'] !== false ) {
			$callback = array( $this, $this->shortcode['html-render'] );

			if ( is_callable( $callback ) ) {
				$output = call_user_func( $callback, $params );
			}
		} else {
			$output = $params;
		}

		return $output;
	}

	/**
	 * Callback for default sortable elements.
	 */
	public function sortable_editor_element( $params ) {
		$extra_class = '';

		$defaults = array(
			'innerHtml' => '',
			'class'     => 'axisbuilder-default-container'
		);

		$params = array_merge( $defaults, $params );

		extract( $params );

		$data['modal-title']       = $this->title;
		$data['modal-action']      = $this->shortcode['name'];
		$data['dragdrop-level']    = $this->shortcode['drag-level'];
		$data['shortcode-handler'] = $this->shortcode['name'];
		$data['shortcode-allowed'] = $this->shortcode['name'];

		if ( isset( $this->shortcode['shortcode-nested'] ) ) {
			$data['shortcode-allowed']   = $this->shortcode['shortcode-nested'];
			$data['shortcode-allowed'][] = $this->shortcode['name'];
			$data['shortcode-allowed']   = implode( ',', $data['shortcode-allowed'] );
		}

		if ( ! empty( $this->shortcode['modal-on-load'] ) ) {
			$data['modal-on-load'] = $this->shortcode['modal-on-load'];
		}

		$output = '<div class="axisbuilder-sortable-element popup-animation axisbuilder-drag ' . $this->shortcode['name'] . ' ' . $class . '"' . axisbuilder_html_data_string( $data ) . '>';
			$output .= '<div class="axisbuilder-sorthandle menu-item-handle">';
				if ( isset( $this->shortcode['popup_editor'] ) ) {
					$extra_class = 'axisbuilder-edit';
					$output .= '<a class="' . $extra_class . ' edit-element-icon" href="#edit" title="' . __( 'Edit Element', 'axisbuilder' ) . '">' . __( 'Edit Element', 'axisbuilder' ) . '</a>';
				}
				$output .= '<a class="axisbuilder-trash trash-element-icon" href="#trash" title="' . __( 'Delete Element', 'axisbuilder' ) . '">' . __( 'Delete Element', 'axisbuilder' ) . '</a>';
				$output .= '<a class="axisbuilder-clone clone-element-icon" href="#clone" title="' . __( 'Clone Element',  'axisbuilder' ) . '">' . __( 'Clone Element',  'axisbuilder' ) . '</a>';
			$output .= '</div>';
			$output .= '<div class="axisbuilder-inner-shortcode ' . $extra_class . '">';
				$output .= $innerHtml;
				$output .= '<textarea data-name="text-shortcode" rows="4" cols="20">' . ab_create_shortcode_data( $this->shortcode['name'], $content, $args ) . '</textarea>';
			$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Extracts the shortcode attributes and merge the values into the options array.
	 * @param  array $elements
	 * @return array $elements
	 */
	public function set_defaults_value( $elements ) {
		$shortcode = empty( $_POST['params']['shortcode'] ) ? '' : $_POST['params']['shortcode'];

		if ( $shortcode ) {

			// Will extract the shortcode into $_POST['extracted_shortcode']
			AB_AJAX::shortcodes_to_interface( $shortcode );

			// The main shortcode (which is always the last array item) will be stored in $extracted_shortcode
			$extracted_shortcode = end( $_POST['extracted_shortcode'] );

			// If the $_POST['extracted_shortcode'] has more than one items we are dealing with nested shortcodes
			$multi_content = count( $_POST['extracted_shortcode'] );

			// Proceed if the main shortcode has either arguments or content
			if ( ! empty( $extracted_shortcode['attr'] ) || ! empty( $extracted_shortcode['content'] ) ) {

				if ( empty( $extracted_shortcode['attr'] ) ) {
					$extracted_shortcode['attr'] = '';
				}

				if ( isset( $extracted_shortcode['content'] ) ) {
					$extracted_shortcode['attr']['content'] = $extracted_shortcode['content'];
				}

				// Iterate over each elements and check if we already got a value
				foreach ( $elements as &$element ) {

					if ( isset( $element['id'] ) && isset( $extracted_shortcode['attr'][$element['id']] ) ) {

						// Ensure each popup element can access the other values of the shortcode. Necessary for hidden elements.
						$element['shortcode_data'] = $extracted_shortcode['attr'];

						// If the item has subelements then std value should be an array
						if ( isset( $element['subelements'] ) ) {
							$element['std'] = array();

							for ( $i = 0; $i < ( $multi_content - 1 ); $i++ ) {
								$element['std'][$i]            = $_POST['extracted_shortcode'][$i]['attr'];
								$element['std'][$i]['content'] = $_POST['extracted_shortcode'][$i]['content'];
							}
						} else {
							$element['std'] = stripslashes( $extracted_shortcode['attr'][$element['id']] );
						}
					} else {
						if ( $element['type'] == 'checkbox' ) {
							$element['std'] = '';
						}
					}
				}
			}
		}

		return $elements;
	}

	/**
	 * Extract the default values of the content element.
	 * @return array $content
	 */
	public function get_default_content() {
		$content = '';

		if ( ! empty( $this->elements ) ) {

			// If we didn't iterate over the arguments array yet do it now !
			if ( empty( $this->arguments ) ) {
				$this->get_default_arguments();
			}

			if ( ! isset( $this->arguments['content'] ) ) {
				foreach ( $this->elements as $element ) {
					if ( isset( $element['std'] ) && isset( $element['id'] ) && $element['id'] == 'content' ) {
						$content = $element['std'];
					}
				}
			} else {
				$content = $this->arguments['content'];
			}

			// If content is an array we got a nested shortcode :)
			if ( is_array( $content ) ) {
				$nested_content = '';

				foreach ( $content as $data ) {
					$nested_content .= trim( ab_create_shortcode_data( $this->shortcode['shortcode_nested'][0], null, $data ) . "\n" );
				}

				$content = $nested_content;
			}
		}

		return $content;
	}

	/**
	 * Extract the std values from the options array and create a shortcode arguments array.
	 * @return array $arguments
	 */
	public function get_default_arguments() {
		$arguments = array();

		if ( ! empty( $this->elements ) ) {
			foreach ( $this->elements as $element ) {
				if ( isset( $element['std'] ) && isset( $element['id'] ) ) {
					$arguments[$element['id']] = $element['std'];
				}
			}

			$this->arguments = $arguments;
		}

		return $arguments;
	}

	/**
	 * Output a view template which can used with builder elements.
	 */
	public function print_media_templates() {
		$class    = $this->shortcode['href-class'];
		$template = $this->prepare_editor_element();

		if ( is_array( $template ) ) {
			foreach ($template as $value) {
				$template = $value;
				continue;
			}
		}

		?>

<!-- <?php echo $class ?> Templates -->
<script type="text/html" id="axisbuilder-tmpl-<?php echo strtolower( $class ); ?>">
<?php echo $template ?>

</script>

		<?php
	}
}
