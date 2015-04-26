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

	private static $saved_meta_boxes = false;
	private static $meta_box_errors  = array();

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ), 10 );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 20 );
		add_action( 'save_post', array( $this, 'save_meta_boxes' ), 1, 2 );

		// Save Portfolio Meta Boxes
		add_action( 'axisbuilder_process_portfolio_meta', 'AB_Meta_Box_Portfolio_Breadcrumb::save', 10, 2 );

		// Save Layout Meta Boxes
		add_action( 'axisbuilder_process_layout_meta', 'AB_Meta_Box_Layout_Data::save', 10, 2 );

		// Save Builder Meta Boxes
		add_action( 'axisbuilder_process_builder_meta', 'AB_Meta_Box_Builder_Data::save', 10, 2 );

		// Restores a post to the specified revision
		add_action( 'wp_restore_post_revision', array( $this, 'restore_post_revision' ), 10, 2 );

		// Error handling (for showing errors from meta boxes on next page load)
		add_action( 'admin_notices', array( $this, 'output_errors' ) );
		add_action( 'shutdown', array( $this, 'save_errors' ) );
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
		// Portfolio
		add_meta_box( 'postexcerpt', __( 'Portfolio Short Description', 'axisbuilder' ), 'AB_Meta_Box_Portfolio_Short_Description::output', 'portfolio', 'normal' );
		add_meta_box( 'axisbuilder-portfolio-breadcrumb', __( 'Breadcrumb Hierarchy', 'axisbuilder' ), 'AB_Meta_Box_Portfolio_Breadcrumb::output', 'portfolio', 'side', 'default' );

		// Layouts
		foreach ( axisbuilder_get_layout_supported_screens() as $type ) {
			if ( post_type_exists( $type ) ) {
				$post_type_object = get_post_type_object( $type );
				add_meta_box( 'axisbuilder-layout-data', sprintf( __( '%s Layout', 'axisbuilder' ), $post_type_object->labels->singular_name ), 'AB_Meta_Box_Layout_Data::output', $type, 'side', 'default' );
			}
		}

		// Page Builder
		foreach ( axisbuilder_get_allowed_screen_types() as $type ) {
			add_meta_box( 'axisbuilder-editor', __( 'Page Builder', 'axisbuilder' ), 'AB_Meta_Box_Builder_Data::output', $type, 'normal', 'high' );
			add_filter( 'postbox_classes_' . $type . '_axisbuilder-editor', 'AB_Meta_Box_Builder_Data::postbox_classes' );
		}
	}

	/**
	 * Remove bloat.
	 */
	public function remove_meta_boxes() {
		remove_meta_box( 'postexcerpt', 'portfolio', 'normal' );
	}

	/**
	 * Check if we're saving, the trigger an action based on the post type
	 * @param int $post_id
	 * @param object $post
	 */
	public function save_meta_boxes( $post_id, $post ) {
		// $post_id and $post are required
		if ( empty( $post_id ) || empty( $post ) || self::$saved_meta_boxes ) {
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

		// We need this save event to run once to avoid potential endless loops. This would have been perfect:
		//	remove_action( current_filter(), __METHOD__ );
		self::$saved_meta_boxes = true;

		// Check the post type
		if ( in_array( $post->post_type, array( 'portfolio' ) ) ) {
			do_action( 'axisbuilder_process_' . $post->post_type . '_meta', $post_id, $post );
		}

		// Trigger action
		$process_actions = array( 'layout', 'builder' );
		foreach ( $process_actions as $process_action ) {
			do_action( 'axisbuilder_process_' . $process_action . '_meta', $post_id, $post );
		}
	}

	/**
	 * Function to restore post meta along with revision.
	 * @param  int $post_id     Post ID.
	 * @param  int $revision_id Post revision ID.
	 * @return null
	 */
	public function restore_post_revision( $post_id, $revision_id ) {
		$revision = get_post( $revision_id );
		$metadata = array( '_axisbuilder_canvas' );

		foreach ( $metadata as $metafield ) {
			$builder_metadata = get_metadata( 'post', $revision->ID, $metafield, true );

			if ( ! empty( $builder_metadata ) ) {
				update_post_meta( $post_id, $metafield, $builder_metadata );
			} else {
				delete_post_meta( $post_id, $metafield );
			}
		}
	}
}

new AB_Admin_Meta_Boxes();
