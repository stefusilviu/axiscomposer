<?php
/**
 * AxisBuilder Meta Boxes
 *
 * Sets up the write panels used by builder and custom post types.
 *
 * @class       AB_Admin_Meta_Boxes
 * @package     AxisBuilder/Admin/Meta Boxes
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Admin_Meta_Boxes Class
 */
class AB_Admin_Meta_Boxes {

	private static $add_meta_boxes    = array();
	private static $add_meta_elements = array();
	private static $meta_box_errors   = array();

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10 );
		add_action( 'save_post', array( $this, 'save_layout_editor_meta' ), 1, 2 );

		// Save builder Meta Boxes
		// add_action( 'axisbuilder_layout_editor_meta', array( $this, 'save_layout_editor_meta' ), 10, 2 );

		// Error handling (for showing errors from meta boxes on next page load)
		add_action( 'admin_notices', array( $this, 'output_errors' ) );
		add_action( 'shutdown', array( $this, 'save_errors' ) );
	}

	/**
	 * Add Meta-box configurations.
	 */
	public static function add_meta_config() {
		require( AB_CONFIG_DIR . 'meta-boxes.php' );

		if ( isset( $boxes ) ) {
			self::$add_meta_boxes = apply_filters( 'axisbuilder_add_meta_boxes', $boxes );
		}

		if ( isset( $elements ) ) {
			self::$add_meta_elements = apply_filters( 'axisbuilder_add_meta_elements', $elements );
		}
	}

	/**
	 * Add an error message.
	 * @param string $text
	 */
	public static function add_error( $text ) {
		self::$meta_box_errors[] = $text;
	}

	/**
	 * Save errors to an option.
	 */
	public function save_errors() {
		update_option( 'axisbuilder_meta_box_errors', self::$meta_box_errors );
	}

	/**
	 * Show any stored error messages.
	 */
	public function output_errors() {
		$errors = maybe_unserialize( get_option( 'axisbuilder_meta_box_errors' ) );

		if ( ! empty( $errors ) ) {

			echo '<div id="axisbuilder_errors" class="error fade">';
			foreach ( $errors as $error ) {
				echo '<p>' . esc_html( $error ) . '</p>';
			}
			echo '</div>';

			// Clear
			delete_option( 'axisbuilder_meta_box_errors' );
		}
	}

	/**
	 * Add AB Meta boxes.
	 */
	public function add_meta_boxes() {
		$screens = get_builder_core_supported_screens();

		// Page Builder
		foreach ( $screens as $type ) {
			add_meta_box( 'axisbuilder-editor', __( 'Page Builder', 'axisbuilder' ), array( $this, 'create_page_builder' ), $type, 'normal', 'high' );
			add_filter( 'postbox_classes_' . $type . '_axisbuilder-editor', array( $this, 'custom_postbox_classes' ) );
		}

		// Load Configurations
		self::add_meta_config();

		// Additional
		if ( ! empty( self::$add_meta_boxes ) && ! empty( self::$add_meta_elements ) ) {

			foreach ( self::$add_meta_boxes as $key => $meta_box ) {

				foreach ( $meta_box['page'] as $type ) {
					add_meta_box( $meta_box['id'], $meta_box['title'], array( $this, 'create_meta_box' ), $type, $meta_box['context'], $meta_box['priority'], array( 'axisbuilder_current_meta_box' => $meta_box ) );
				}
			}
		}
	}

	public function create_meta_box() {
		return "Metabox Elements Goes Here :)";
	}

	public function create_page_builder() {
		global $axisbuilder_shortcodes;
		$title  = $content = '';

		// Builder Post Meta
		$builder_canvas = get_post_meta( get_the_ID(), '_axisbuilder_canvas', true );
		$builder_status = get_post_meta( get_the_ID(), '_axisbuilder_status', true );

		$loop = 0;

		// Let's bail if shortcode exists.
		if ( ! empty( $axisbuilder_shortcodes ) ) {

			// Shortcode tabs
			$load_shortcode_tabs = get_builder_core_shortcode_tabs();
			$load_shortcode_tabs = empty( $load_shortcode_tabs ) ? array() : array_flip( $load_shortcode_tabs );

			// Will hide the PHP warnings :)
			foreach ( $load_shortcode_tabs as &$empty_tabs ) {
				$empty_tabs = array();
			}

			foreach ( $axisbuilder_shortcodes as $shortcode ) {
				if ( empty( $shortcode['tinyMCE']['tiny_only'] ) ) {
					if ( ! isset( $shortcode['type'] ) ) {
						$shortcode['type'] = __( 'Custom Elements', 'axisbuilder' );
					}
				}

				$load_shortcode_tabs[$shortcode['type']][] = $shortcode;
			}

			foreach ( $load_shortcode_tabs as $key => $tab ) {
				if ( empty( $tab ) ) {
					continue;
				}

				usort( $tab, array( $this, 'sort_by_order' ) );

				$loop ++;
				$title   .= '<a href="#axisbuilder-tab-' . $loop . '">' . $key . '</a>';
				$content .= '<div class="axisbuilder-tab-shortcodes axisbuilder-tab-' . $loop . '">';

				foreach ( $tab as $shortcode ) {
					if ( empty( $shortcode['invisible'] ) ) {
						$content .= $this->create_shortcode_button( $shortcode );
					}
				}

				$content .= '</div>';
			}

			$html = '<div id="axisbuilder-wrapper" class="wrap-pagebuilder">';

				// Builder Loader
				$html .= '<div id="axisbuilder-loader" class="axisbuilder-meta-box axisbuilder-editor-custom">';
					$html .= '<div class="axisbuilder-shortcodes axisbuilder-tab-container"><div class="axisbuilder-tab-title">' . $title . '</div>' . $content . '</div>';
				$html .= '</div>';

				// Builder Handle
				$html .= '<div id="axisbuilder-handle" class="handle-bar">';

					$html .= '<div class="control-bar">';

						// History Sections
						$html .= '<div class="history-sections">';
							$html .= '<div class="history-action" data-axis-tooltip="History">';
								$html .= '<a href="#" class="undo-icon undo-data" title="Undo"></i></a>';
								$html .= '<a href="#" class="redo-icon redo-data" title="Redo"></i></a>';
							$html .= '</div>';
							$html .= '<div class="delete-action">';
								$html .= '<a href="#" class="trash-icon trash-data" data-axis-tooltip="Permanently delete all canvas elements"></a>';
							$html .= '</div>';
						$html .= '</div>';

						// Content Sections
						$html .= '<div class="content-sections">';
							$html .= '<div class="template-action">';
								$html .= '<a href="#" class="button button-secondary" data-axis-tooltip="Save or Load templates">Templates</a>';
							$html .= '</div>';
							$html .= '<div class="fullscreen-action">';
								$html .= '<a href="#" class="expand-icon axisbuilder-attach-expand">Close</a>';
							$html .= '</div>';
						$html .= '</div>';

					$html .= '</div>';

				$html .= '</div>';

				// Builder Canvas
				$html .= '<div id="axisbuilder-canvas" class="visual-editor">';
					$html .= '<div class="canvas-area loader axisbuilder-drop"></div>';
					$html .= '<textarea readonly="readonly" name="canvas-data" id="canvas-data" class="canvas-data">' . $builder_canvas . '</textarea>';
				$html .= '</div>';

			$html .= '</div>';

			// Builder Status
			$html .= '<input type="hidden" name="axisbuilder_status" value="' . esc_attr( $builder_status ? $builder_status : 'inactive' ) . '"/>';

			echo $html;
		}
	}

	/**
	 * Create a shortcode button
	 */
	protected function create_shortcode_button( $shortcode ) {
		$icon     = isset( $shortcode['image'] ) ? '<img src="' . $shortcode['image'] . '" alt="' . $shortcode['name'] . '" />' : '<i class="' . $shortcode['icon'] . '" /></i>';
		$desc     = empty( $shortcode['desc'] ) ? '' : 'data-axis-tooltip="' . $shortcode['desc'] . '"';
		$class    = isset( $shortcode['class'] ) ? $shortcode['class'] : 'empty-class ';
		$target   = empty( $shortcode['target'] ) ? '' : $shortcode['target'];
		$dragdrop = empty( $shortcode['drag-level'] ) ? '' : 'data-dragdrop-level="' . $shortcode['drag-level'] . '"';

		return '<a href="#' . $shortcode['php_class'] . '" class="insert-shortcode ' . $class . $target . '" ' . $desc . $dragdrop . '>' . $icon . '<span>' . $shortcode['name'] . '</span></a>';
	}

	/**
	 * Helper function to sort the shortcode buttons.
	 */
	protected function sort_by_order( $a, $b ) {
		if ( empty( $a['order'] ) ) {
			$a['order'] = 10;
		}

		if ( empty( $b['order'] ) ) {
			$b['order'] = 10;
		}

		return $b['order'] >= $a['order'];
	}

	/**
	 * Filter the postbox classes for a specific screen and screen ID combo.
	 * @param  array $classes An array of postbox classes.
	 * @return array
	 */
	public function custom_postbox_classes( $classes ) {

		// Class for hidden items
		if ( empty( $_GET['post'] ) || ( isset( $_GET['post'] ) && get_post_meta( $_GET['post'], '_axisbuilder_status', true ) != 'active' ) ) {
			$classes[] = 'axisbuilder-hidden';
		}

		// Class for expanded items
		if ( ! empty( $_GET['axisbuilder-expanded'] ) && ( 'axisbuilder-editor' === $_GET['axisbuilder-expanded'] ) ) {
			$classes[] = 'axisbuilder-expanded';
		}

		// Class for Debug or Test-mode
		if ( defined( 'AB_DEBUG' ) && AB_DEBUG ) {
			$classes[] = 'axisbuilder-debug';
		}

		return $classes;
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type
	 *
	 * @param  int $post_id
	 * @param  object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) ) {
			return;
		}

		// Dont' save meta boxes for revisions or autosaves
		if ( defined( 'DOING_AUTOSAVE' ) || is_int( wp_is_post_revision( $post ) ) || is_int( wp_is_post_autosave( $post ) ) ) {
			return;
		}

		// Check the nonce
		if ( empty( $_POST['axisbuilder_meta_nonce'] ) || ! wp_verify_nonce( $_POST['axisbuilder_meta_nonce'], 'axisbuilder_save_data' ) ) {
			return;
		}

		// Check the post being saved == the $post_id to prevent triggering this call for other save_post events
		if ( empty( $_POST['post_ID'] ) || $_POST['post_ID'] != $post_id ) {
			return;
		}

		// Check user has permission to edit
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Provide a hook for some additional data manipulation where users can modify the $_POST array or save additional information
		do_action( 'axisbuilder_layout_editor_meta', $post_id, $post );
	}


	/**
	 * Set status of builder (open/closed) and save the shortcodes that are used in the post
	 */
	public function save_layout_editor_meta( $post_id ) {

		// Save if the page builder is active
		if ( isset( $_POST['axisbuilder_status'] ) ) {
			update_post_meta( $post_id, '_axisbuilder_status', $_POST['axisbuilder_status']);
		}

		// Filter the redirect url in case we got a Meta-Box that is expanded. In that case append some POST Paramas.
		if ( ! empty( $_POST['axisbuilder-expanded-hidden'] ) ) {
			add_filter( 'redirect_post_location', array( __CLASS__, 'set_builder_expanded_param' ), 10, 2 );
		}
	}

	/**
	 * Set the correct Builder Expanded POST Params.
	 *
	 * @param  $location
	 * @static
	 * @return string
	 */
	public static function set_builder_expanded_param( $location ) {
		return add_query_arg( 'axisbuilder-expanded', $_POST['axisbuilder-expanded-hidden'], $location );
	}
}

new AB_Admin_Meta_Boxes();

