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
	 * Shortcode Counter.
	 * @var int
	 */
	protected $counter = 0;

	/**
	 * Shortcode Arguments.
	 * @var array
	 */
	protected $arguments = array();

	/**
	 * Shortcode Configurations.
	 * @var array
	 */
	public $shortcode = array();

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

		if ( empty( $this->form_fields ) ) {
			die();
		}

		// Add-on Custom CSS class field
		if ( apply_filters( 'axiscomposer_show_custom_class_field', true ) ) {
			$this->form_fields = $this->custom_css_field();
		}

		// If we got a field with subelements
		if ( ! empty( $_POST['params']['subelements'] ) ) {
			foreach ( $this->form_fields as $field ) {
				if ( isset( $field['subelements'] ) ) {
					$this->form_fields = $field['subelements'];
					break;
				}
			}
		}

		// Get the default fields values
		$this->form_fields = $this->get_default_values();

		// Get modal settings fragment
		ob_start();
		?><table class="form-table">
			<?php $this->generate_settings_html(); ?>
		</table><?php
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
	 * Generate TinyMCE HTML.
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
		if ( method_exists( $this, 'init_form_fields' ) ) {
			$this->init_form_fields();
			if ( ! empty( $this->form_fields ) ) {
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
		$params['innerHtml'] .= ( isset( $this->shortcode['image'] ) && ! empty( $this->shortcode['image'] ) ) ? '<img src="' . $this->shortcode['image'] . '" alt="' . $this->method_title . '" />' : '<i class="' . $this->shortcode['icon'] . '"></i>';
		$params['innerHtml'] .= '<div class="ac-element-label">' . $this->method_title . '</div>';

		return (array) $params;
	}

	/**
	 * Add-on for Custom CSS class field.
	 * @param  array $form_fields (default: array())
	 * @return array $form_fields
	 */
	public function custom_css_field( $form_fields = array() ) {

		if ( empty( $form_fields ) ) {
			$form_fields = $this->get_form_fields();
		}

		// Hide for specific shortcodes
		$ac_shortcode = apply_filters( 'axiscomposer_hide_custom_class_field', array( 'gist' ) );

		// Check to make sure we've excluded shortcodes
		if ( isset( $this->id ) && current_user_can( 'manage_axiscomposer' ) && ! in_array( $this->id, $ac_shortcode ) ) {
			$form_fields['custom_class'] = array(
				'title'       => __( 'Custom CSS Class', 'axiscomposer' ),
				'description' => __( 'This option lets you set custom css class you are willing to use for customization.', 'axiscomposer' ),
				'class'       => 'ac_input_css',
				'type'        => 'text',
				'default'     => ''
			);
		}

		return $form_fields;
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
	 * Get the default field values.
	 * @param  array $form_fields (default: array())
	 * @return array $form_fields
	 */
	public function get_default_values( $form_fields = array() ) {

		if ( empty( $form_fields ) ) {
			$form_fields = $this->get_form_fields();
		}

		$shortcode = empty( $_POST['params']['shortcode'] ) ? '' : $_POST['params']['shortcode'];

		if ( $shortcode ) {

			// Extract and store the main shortcode
			AC_AJAX::shortcodes_to_interface( $shortcode );
			$main_shortcode = end( $_POST['extracted_shortcode'] );

			// Proceed if the main shortcode has either arguments or content
			if ( ! empty( $main_shortcode['attr'] ) || ! empty( $main_shortcode['content'] ) ) {

				if ( empty( $main_shortcode['attr'] ) ) {
					$main_shortcode['attr'] = '';
				}

				if ( isset( $main_shortcode['content'] ) ) {
					$main_shortcode['attr']['content'] = $main_shortcode['content'];
				}

				// Check if we already got a value?
				foreach ( $form_fields as $key => &$value ) {
					if ( isset( $key ) && isset( $main_shortcode['attr'][$key] ) ) {
						$value['shortcode_data'] = $main_shortcode['attr'];

						// If we got a item with subelements
						if ( isset( $value['subelements'] ) ) {
							$value['default'] = array();
							$count_shortcodes = count( $_POST['extracted_shortcode'] );

							for ( $i = 0; $i < ( $count_shortcodes - 1 ); $i++ ) {
								$value['default'][$i]            = $_POST['extracted_shortcode'][$i]['attr'];
								$value['default'][$i]['content'] = $_POST['extracted_shortcode'][$i]['content'];
							}
						} elseif ( $value['type'] == 'checkbox' ) {
							if ( 1 == $main_shortcode['attr'][$key] ) {
								$value['default'] = 'yes';
							}
						} else {
							$value['default'] = stripslashes( $main_shortcode['attr'][$key] );
						}
					} elseif ( $value['type'] == 'checkbox' ) {
						$value['default'] = 'no';
					}
				}
			}
		}

		return $form_fields;
	}

	/**
	 * Create shortcode content from default values.
	 * @return array $content
	 */
	public function get_default_content() {
		$content = '';

		if ( ! empty( $this->form_fields ) ) {

			// Fetch arguments
			if ( empty( $this->arguments ) ) {
				$this->get_default_arguments();
			}

			if ( ! isset( $this->arguments['content'] ) ) {
				foreach ( $this->form_fields as $key => $value ) {
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

		if ( ! empty( $this->form_fields ) ) {
			foreach ( $this->form_fields as $key => $value ) {
				if ( isset( $key ) && isset( $value['default'] ) ) {
					$arguments[$key] = $value['default'];
				}
			}

			$this->arguments = $arguments;
		}

		return $arguments;
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

		$data['modal-title']       = $this->method_title;
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
	 * Output a Pagebuilder Shortcode Templates.
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
