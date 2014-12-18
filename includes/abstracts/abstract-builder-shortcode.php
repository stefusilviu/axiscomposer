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
	 * Shortcode Settings
	 * @var array
	 */
	public $settings;

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		$this->shortcode_button();
		$this->shortcode_config();

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

		// Activate popup editor if settings exist.
		if ( method_exists( $this, 'popup_elements' ) ) {
			$this->popup_elements();
			if ( isset( $this->settings ) ) {
				$this->shortcode['popup_editor'] = true;
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
	 * Add-on for custom CSS class to each element.
	 */
	public function custom_css( $elements ) {
		$elements[] = array(
			'id'   => 'custom_class',
			'name' => __( 'custom CSS Class', 'axisbuilder' ),
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

		// Set default content unless it was already passed
		// if ( $content === false ) {
		// 	$content = $this->fetch_default_content( $content );
		// }

		// Set default arguments unless it was already passed
		// if ( empty( $args ) ) {
		// 	$args = $this->fetch_default_args( $args );
		// }

		if ( isset( $args['content'] ) ) {
			unset( $args['content'] );
		}

		$params['content'] = $content;
		$params['args']    = $args;
		$params['data']    = isset( $this->shortcode['modal_data'] ) ? $this->shortcode['modal_data'] : '';

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
		$data_string = $extra_class = '';

		$defaults = array(
			'innerHtml' => '',
			'class'     => 'axisbuilder-default-container',
		);

		$params = array_merge( $defaults, $params );

		extract( $params );

		$data['modal-title']       = $this->title;
		$data['modal-action']      = $this->shortcode['name'];
		$data['dragdrop-level']    = $this->shortcode['drag-level'];
		$data['shortcode-handler'] = $this->shortcode['name'];
		$data['shortcode-allowed'] = $this->shortcode['name'];

		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$value = implode( ', ', $value );
			}

			$data_string .= ' data-' . $key . '="' . $value . '"';
		}

		$output = '<div class="axisbuilder-sortable-element popup-animation axisbuilder-drag ' . $this->shortcode['name'] . ' ' . $class . '" ' . $data_string . '>';
			$output .= '<div class="axisbuilder-sorthandle menu-item-handle">';
				if ( isset( $this->shortcode['popup_editor'] ) ) {
					$extra_class = 'axisbuilder-edit-element';
					$output .= '<a class="' . $extra_class . '" href="#editor-element" title="' . __( 'Edit Element', 'axisbuilder' ) . '">' . __( 'Edit Element', 'axisbuilder' ) . '</a>';
				}
				$output .= '<a class="axisbuilder-trash" href="#trash" title="' . __( 'Delete Element', 'axisbuilder' ) . '">' . __( 'Delete Element', 'axisbuilder' ) . '</a>';
				$output .= '<a class="axisbuilder-clone" href="#clone" title="' . __( 'Clone Element',  'axisbuilder' ) . '">' . __( 'Clone Element',  'axisbuilder' ) . '</a>';
			$output .= '</div>';
			$output .= '<div class="axisbuilder-inner-shortcode ' . $extra_class . '">';
				$output .= $innerHtml;
				$output .= '<textarea data-name="text-shortcode" rows="4" cols="20">' . ab_create_shortcode_data( $this->shortcode['name'], $content, $args ) . '</textarea>';
			$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Fetch default content
	 */
	// public function fetch_default_content() {

	// }

	/**
	 * Fetch default args
	 */
	// public function fetch_default_args() {

	// }

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
