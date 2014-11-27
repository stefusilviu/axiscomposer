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
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		// Save builder Meta Boxes
		add_action( 'axisbuilder_layout_editor_meta', array( $this, 'save_layout_editor_meta' ), 10, 2 );

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
		foreach ( $screens as $screen ) {
			add_meta_box( 'axisbuilder-editor', __( 'Axis Page Builder', 'axisbuilder' ), array( $this, 'create_meta_box' ), $screen, 'normal', 'high' );
			add_filter( 'postbox_classes_' . $screen . '_axisbuilder-editor', array( $this, 'custom_postbox_classes' ) );
		}

		// Load Configurations
		self::add_meta_config();

		// Additional
		if ( ! empty( self::$add_meta_boxes ) && ! empty( self::$add_meta_elements ) ) {

			foreach ( self::$add_meta_boxes as $key => $meta_box ) {

				foreach ( $meta_box['page'] as $screen ) {
					add_meta_box( $meta_box['id'], $meta_box['title'], array( $this, 'create_meta_box' ), $screen, $meta_box['context'], $meta_box['priority'], array( 'axisbuilder_current_meta_box' => $meta_box ) );
				}
			}
		}
	}

	public function create_meta_box() {
		echo "string";
	}

	/**
	 * Filter the postbox classes for a specific screen and screen ID combo.
	 * @param  array $classes An array of postbox classes.
	 * @return array
	 */
	public function custom_postbox_classes( $classes ) {

		// Class for hidden items
		if ( empty( $_GET['post'] ) || ( isset( $_GET['post'] ) && get_post_meta( $_GET['post'], '_axisLayoutBuilder_active', true ) != 'active' ) ) {
			$classes[] = 'axisbuilder-hidden';
		}

		// Class for expanded items
		if ( ! empty( $_GET['axisbuilder-expanded'] ) && ( 'axisbuilder-editor' === $_GET['axisbuilder-expanded'] ) ) {
			$classes[] = 'axisbuilder-expanded';
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
	public function save_layout_editor_meta() {
		if ( isset( $_POST['post_ID'] ) ) {

			// Save if the page builder is active
			if ( isset( $_POST['axisLayoutBuilder_active'] ) ) {
				update_post_meta( (int) $_POST['post_ID'], '_axisLayoutBuilder_active', $_POST['axisLayoutBuilder_active']);
			}
		}
	}
}

new AB_Admin_Meta_Boxes();
