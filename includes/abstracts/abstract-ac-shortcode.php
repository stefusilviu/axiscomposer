<?php
/**
 * Abstract Shortcode Class
 *
 * Extended by individual shortcodes to handle shortcode data etc.
 *
 * @class       AC_Shortcode
 * @extends     AC_Settings_API
 * @package     AxisComposer/Abstracts
 * @category    Abstract Class
 * @author      AxisThemes
 * @since       1.0.0
 */
abstract class AC_Shortcode extends AC_Settings_API {

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
	 * Shortcode Counter
	 * @var int
	 */
	protected $counter = 0;

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		$this->init_shortcode();
		$this->config_shortcode();

		/**
		 * Shortcode AJAX Events
		 * @todo Include in AC_AJAX Class as soon.
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
	 * Abstract method for shortcode initialize.
	 */
	abstract public function init_shortcode();

	/**
	 * Abstract method for frontend shortcode handle.
	 */
	abstract public function shortcode_handle( $atts, $content = '', $shortcode = '', $meta = '' );

	/**
	 * AJAX Events for shortcodes.
	 */
	public function shortcode_action() {
		if ( ! empty( $this->shortcode['has_fields'] ) ) {
			add_action( 'wp_ajax_axiscomposer_' . $this->shortcode['name'], array( $this, 'load_modal_items' ) );

			// If available nested shortcode define them.
			if ( isset( $this->shortcode['nested'] ) ) {
				foreach ( $this->shortcode['nested'] as $shortcode ) {
					if ( method_exists( $this, $shortcode ) ) {
						add_action( 'wp_ajax_axiscomposer_' . $shortcode, array( $this, 'load_modal_items' ) );
					}
				}
			}
		}
	}

	/**
	 * AJAX Load Backbone modal items.
	 */
	public function load_modal_items() {

		check_ajax_referer( 'modal-item', 'security' );

		if ( empty( $this->elements ) ) {
			die();
		}

		// Display Custom CSS element
		if ( apply_filters( 'axiscomposer_show_css_element', true ) ) {
			$this->elements = $this->custom_css( $this->elements );
		}

		$elements = apply_filters( 'axiscomposer_shortcodes_elements', $this->elements );

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

		// Get modal settings fragment
		ob_start();
		?><table class="form-table"><?php
			$this->generate_settings_html( $elements );
		?></table><?php
		$axiscomposer_modal_settings = ob_get_clean();

		$data = array(
			'result'    => 'success',
			'fragments' => apply_filters( 'axiscomposer_update_modal_settings_fragments', array(
				'.ac-enhanced-settings' => $axiscomposer_modal_settings
			) )
		);

		wp_send_json( $data );

		die();
	}

	/**
	 * Define shortcodes.
	 */
	protected function add_shortcode() {
		if ( ! is_admin() ) {
			add_shortcode( $this->shortcode['name'], array( $this, 'shortcode_wrapper' ) );

			// If availabe nested shortcode define them.
			if ( isset( $this->shortcode['nested'] ) ) {
				foreach ( $this->shortcode['nested'] as $shortcode ) {
					if ( method_exists( $this, $shortcode ) ) {
						add_shortcode( $shortcode, array( $this, $shortcode ) );
					} elseif ( ! shortcode_exists( $shortcode ) ) {
						add_shortcode( $shortcode, '__return_false' );
					}
				}
			}
		}
	}

	/**
	 * Prefix key for settings.
	 *
	 * @param  mixed $key
	 * @return string
	 */
	public function get_field_key( $key ) {
		return $this->plugin_id . $key;
	}

	/**
	 * Generate TinyMCE Input HTML.
	 *
	 * @param  mixed $key
	 * @param  mixed $data
	 * @since  1.0.0
	 * @return string
	 */
	public function generate_tinymce_html( $key, $data ) {

		$field    = $this->get_field_key( $key );
		$defaults = array(
			'title'       => '',
			'class'       => '',
			'desc_tip'    => false,
			'description' => ''
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top" class="full-width">
			<td colspan="3" class="forminp">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<?php
						echo $this->get_description_html( $data );

						$settings = array(
							'editor_css'    => '<style>#wp-tinymce-content-editor-container .wp-editor-area{height:auto; display:block; border:none !important;}</style>',
							'editor_class'  => esc_attr( $data['class'] ),
							'textarea_name' => esc_attr( $field )
						);

						wp_editor( htmlspecialchars_decode( $this->get_option( $key ) ), esc_attr( $field ), apply_filters( 'axiscomposer_backbone_modal_editor_settings', $settings ) );
					?>
				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Generate Color Picker Input HTML.
	 *
	 * @param  mixed $key
	 * @param  mixed $data
	 * @since  1.0.0
	 * @return string
	 */
	public function generate_color_html( $key, $data ) {
		$data['type']  = 'text';
		$data['class'] = 'color-picker';
		return $this->generate_text_html( $key, $data );
	}

	/**
	 * Generate Image Upload Button HTML.
	 *
	 * @param  mixed $key
	 * @param  mixed $data
	 * @since  1.0.0
	 * @return string
	 */
	public function generate_image_html( $key, $data ) {

		$field    = $this->get_field_key( $key );
		$defaults = array(
			'title'             => '',
			'disabled'          => false,
			'class'             => '',
			'css'               => '',
			'type'              => 'image',
			'desc_tip'          => false,
			'description'       => '',
			'custom_attributes' => array()
		);

		$data = wp_parse_args( $data, $defaults );

		ob_start();
		?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<button class="button button-large insert-media ac-image-upload ac-image-insert <?php echo esc_attr( $data['class'] ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> ><?php echo esc_attr( $data['label'] ); ?></button>
					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}

	/**
	 * Shortcode Wrapper.
	 */
	public function shortcode_wrapper( $atts, $content = '', $shortcode = '' ) {
		$meta = array();

		// Inline shortcodes like dropcaps are basically nested shortcode and shouldn't be counted ;)
		if ( empty( $this->shortcode['inline'] ) ) {
			$meta = array(
				'class'    => 'axiscomposer',
				'counter'  => $this->counter,
				'el_class' => 'el-class-' . $this->counter
			);

			$this->counter ++;
		}

		if ( isset( $atts['custom_class'] ) ) {
			$meta['el_class']    .= ' ' . $atts['custom_class'];
			$meta['custom_class'] = $atts['custom_class'];
		}

		if ( ! isset( $meta['custom_markup'] ) ) {
			$meta['custom_markup'] = '';
		}

		$meta    = apply_filters( 'axiscomposer_shortcodes_meta', $meta, $atts, $content, $shortcode );
		$content = $this->shortcode_handle( $atts, $content, $shortcode, $meta );

		return $content;
	}

	/**
	 * Auto-set shortcode configurations.
	 */
	protected function config_shortcode() {
		$load_shortcode_data = array(
			'class'      => '',
			'target'     => '',
			'drag-level' => 3,
			'drop-level' => -1,
			'href-class' => get_class( $this )
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

		// Activate modal if settings exists.
		if ( method_exists( $this, 'get_settings' ) ) {
			$this->get_settings();
			if ( isset( $this->elements ) ) {
				$this->shortcode['has_fields'] = true;
			}
		}
	}

	/**
	 * Editor Elements.
	 *
	 * This method defines the visual appearance of an element on the pagebuilder canvas.
	 */
	public function editor_element( $params ) {
		$params['innerHtml']  = '';
		$params['innerHtml'] .= ( isset( $this->shortcode['image'] ) && ! empty( $this->shortcode['image'] ) ) ? '<img src="' . $this->shortcode['image'] . '" alt="' . $this->title . '" />' : '<i class="' . $this->shortcode['icon'] . '"></i>';
		$params['innerHtml'] .= '<div class="ac-element-label">' . $this->title . '</div>';

		return (array) $params;
	}

	/**
	 * Add-on for Custom CSS class to each element.
	 */
	public function custom_css( $elements ) {
		$elements['custom_class'] = array(
			'title'       => __( 'Custom CSS Class', 'axiscomposer' ),
			'description' => __( 'This option lets you set custom css class you are willing to use for customization.', 'axiscomposer' ),
			'class'       => 'ac_input_class',
			'type'        => 'text',
			'default'     => ''
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
			'class'     => 'ac-default-container'
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

		$output = '<div class="ac-sortable-element modal-animation ac-drag ' . $this->shortcode['name'] . ' ' . $class . '"' . ac_html_data_string( $data ) . '>';
			$output .= '<div class="ac-sorthandle menu-item-handle">';
				if ( isset( $this->shortcode['has_fields'] ) ) {
					$extra_class = 'axiscomposer-edit';
					$output .= '<a class="' . $extra_class . ' edit-element-icon" href="#edit" title="' . __( 'Edit Element', 'axiscomposer' ) . '">' . __( 'Edit Element', 'axiscomposer' ) . '</a>';
				}
				$output .= '<a class="axiscomposer-trash trash-element-icon" href="#trash" title="' . __( 'Delete Element', 'axiscomposer' ) . '">' . __( 'Delete Element', 'axiscomposer' ) . '</a>';
				$output .= '<a class="axiscomposer-clone clone-element-icon" href="#clone" title="' . __( 'Clone Element',  'axiscomposer' ) . '">' . __( 'Clone Element',  'axiscomposer' ) . '</a>';
			$output .= '</div>';
			$output .= '<div class="ac-inner-shortcode ' . $extra_class . '">';
				$output .= $innerHtml;
				$output .= '<textarea data-name="text-shortcode" rows="4" cols="20">' . ac_shortcode_data( $this->shortcode['name'], $content, $args ) . '</textarea>';
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
			AC_AJAX::shortcodes_to_interface( $shortcode );

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
				foreach ( $elements as $key => &$value ) {

					if ( isset( $key ) && isset( $extracted_shortcode['attr'][$key] ) ) {

						// Ensure each popup element can access the other values of the shortcode. Necessary for hidden elements.
						$element['shortcode_data'] = $extracted_shortcode['attr'];

						// If we got a item with subelements
						if ( isset( $value['subelements'] ) ) {
							$value['default'] = array();

							for ( $i = 0; $i < ( $multi_content - 1 ); $i++ ) {
								$value['default'][$i]            = $_POST['extracted_shortcode'][$i]['attr'];
								$value['default'][$i]['content'] = $_POST['extracted_shortcode'][$i]['content'];
							}
						} else {
							$value['default'] = stripslashes( $extracted_shortcode['attr'][$key] );
						}
					} else {
						if ( $value['type'] == 'checkbox' ) {
							$value['default'] = '';
						}
					}
				}
			}
		}

		return $elements;
	}

	/**
	 * Create shortcode content from default values.
	 * @return array $content
	 */
	public function get_default_content() {
		$content = '';

		if ( ! empty( $this->elements ) ) {

			// Fetch arguments
			if ( empty( $this->arguments ) ) {
				$this->get_default_arguments();
			}

			if ( ! isset( $this->arguments['content'] ) ) {
				foreach ( $this->elements as $key => $value ) {
					if ( isset( $key ) && isset( $value['default'] ) && $key == 'content' ) {
						$content = $value['default'];
					}
				}
			} else {
				$content = $this->arguments['content'];
			}

			// We got a nested shortcode
			if ( is_array( $content ) ) {
				$nested_content = '';

				foreach ( $content as $data ) {
					$nested_content .= trim( ac_shortcode_data( $this->shortcode['shortcode_nested'][0], null, $data ) . "\n" );
				}

				$content = $nested_content;
			}
		}

		return $content;
	}

	/**
	 * Create shortcode arguments from default values.
	 * @return array $arguments
	 */
	public function get_default_arguments() {
		$arguments = array();

		if ( ! empty( $this->elements ) ) {
			foreach ( $this->elements as $key => $value ) {
				if ( isset( $key ) && isset( $value['default'] ) ) {
					$arguments[$key] = $value['default'];
				}
			}

			$this->arguments = $arguments;
		}

		return $arguments;
	}

	/**
	 * Output a view template which can used with pagebuilder elements.
	 */
	public function print_media_templates() {
		$class    = $this->shortcode['href-class'];
		$template = $this->prepare_editor_element();

		if ( is_array( $template ) ) {
			foreach ( $template as $value ) {
				$template = $value;
				continue;
			}
		}

		?>

<script type="text/html" id="tmpl-axiscomposer-<?php echo strtolower( $class ); ?>">
<?php echo $template; ?>

</script>

		<?php
	}
}
