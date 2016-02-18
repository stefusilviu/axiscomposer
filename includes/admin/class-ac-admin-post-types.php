<?php
/**
 * Post Types Admin
 *
 * @class    AC_Admin_Post_Types
 * @version  1.0.0
 * @package  AxisComposer/Admin
 * @category Admin
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Admin_Post_Types Class
 *
 * Handles the edit posts views and some functionality on the edit post screen for AC post types.
 */
class AC_Admin_Post_Types {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );

		// Edit post screens
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );
		add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );
		add_action( 'edit_form_after_editor', array( $this, 'edit_form_after_editor' ) );
		add_filter( 'default_hidden_meta_boxes', array( $this, 'hidden_meta_boxes' ), 10, 2 );

		// Meta-Box Class
		include_once( 'class-ac-admin-meta-boxes.php' );

		// Disable DFW feature pointer
		add_action( 'admin_footer', array( $this, 'disable_dfw_feature_pointer' ) );
	}

	/**
	 * Change messages when a post type is updated.
	 * @param  array $messages
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		global $post, $post_ID;

		$messages['portfolio'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Project updated. <a href="%s">View Project</a>', 'axiscomposer' ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.', 'axiscomposer' ),
			3 => __( 'Custom field deleted.', 'axiscomposer' ),
			4 => __( 'Project updated.', 'axiscomposer' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Project restored to revision from %s', 'axiscomposer' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Project published. <a href="%s">View Project</a>', 'axiscomposer' ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Project saved.', 'axiscomposer' ),
			8 => sprintf( __( 'Project submitted. <a target="_blank" href="%s">Preview Project</a>', 'axiscomposer' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Project scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Project</a>', 'axiscomposer' ),
			  date_i18n( __( 'M j, Y @ G:i', 'axiscomposer' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Project draft updated. <a target="_blank" href="%s">Preview Project</a>', 'axiscomposer' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		return $messages;
	}

	/**
	 * Specify custom bulk actions messages for different post types.
	 * @param  array $bulk_messages
	 * @param  array $bulk_counts
	 * @return array
	 */
	public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {

		$bulk_messages['portfolio'] = array(
			'updated'   => _n( '%s project updated.', '%s projects updated.', $bulk_counts['updated'], 'axiscomposer' ),
			'locked'    => _n( '%s project not updated, somebody is editing it.', '%s projects not updated, somebody is editing them.', $bulk_counts['locked'], 'axiscomposer' ),
			'deleted'   => _n( '%s project permanently deleted.', '%s projects permanently deleted.', $bulk_counts['deleted'], 'axiscomposer' ),
			'trashed'   => _n( '%s project moved to the Trash.', '%s projects moved to the Trash.', $bulk_counts['trashed'], 'axiscomposer' ),
			'untrashed' => _n( '%s project restored from the Trash.', '%s projects restored from the Trash.', $bulk_counts['untrashed'], 'axiscomposer' ),
		);

		return $bulk_messages;
	}

	/**
	 * Change title boxes in admin.
	 * @param  string $text
	 * @param  object $post
	 * @return string
	 */
	public function enter_title_here( $text, $post ) {
		switch ( $post->post_type ) {
			case 'portfolio' :
				$text = __( 'Project name', 'axiscomposer' );
			break;
		}

		return $text;
	}

	/**
	 * Print after the title field.
	 * @param WP_Post $post
	 */
	public function edit_form_after_title( $post ) {
		if ( in_array( $post->post_type, ac_get_allowed_screen_types() ) ) {
			$params = apply_filters( 'axiscomposer_editors_toggle_params', array(
				'notice'        => '',
				'disabled'      => false,
				'builder_label' => __( 'Use Page Builder', 'axiscomposer' ),
				'default_label' => __( 'Use Default Editor', 'axiscomposer' ),
				'disable_label' => __( 'Page Builder Disabled', 'axiscomposer' )
			), $post );

			if ( is_pagebuilder_active( $post->ID ) ) {
				$button_label = $params['default_label'];
				$button_class = 'button-secondary';
				$editor_class = 'ac-hidden-editor';
			} elseif ( $params['disabled'] ) {
				$button_label = $params['disable_label'];
				$button_class = 'button-secondary disabled';
				$editor_class = 'ac-visible-editor';
			} else {
				$button_label = $params['builder_label'];
				$button_class = 'button-primary';
				$editor_class = 'ac-visible-editor';
			}

			echo '<a href="#" id="_toggle_editor" class="button button-large ' . $button_class . ' axiscomposer-toggle-editor" data-builder="' . esc_attr( $params['builder_label'] ) . '" data-editor="' . esc_attr( $params['default_label'] ) . '">' . esc_html( $button_label ) . '</a>';
			echo '<div id="postdivrich_wrap" class="axiscomposer ' . $editor_class . '">';
			if ( $params['notice'] ) {
				echo '<div class="ac_plugin_display_notice ' . esc_attr( $params['disabled'] ? 'inactive' : 'active' ) . '">' . esc_html( $params['notice'] ) . '</div>';
			}
		}
	}

	/**
	 * Print after the content editor.
	 * @param WP_Post $post
	 */
	public function edit_form_after_editor( $post ) {
		if ( in_array( $post->post_type, ac_get_allowed_screen_types() ) ) {
			echo '</div> <!-- #postdivrich_wrap -->';
		}
	}

	/**
	 * Hidden default Meta-Boxes.
	 * @param  array  $hidden
	 * @param  object $screen
	 * @return array
	 */
	public function hidden_meta_boxes( $hidden, $screen ) {
		if ( 'portfolio' === $screen->post_type && 'post' === $screen->base ) {
			$hidden = array_merge( $hidden, array( 'postcustom' ) );
		}

		return $hidden;
	}

	/**
	 * Disable DFW feature pointer.
	 */
	public function disable_dfw_feature_pointer() {
		$screen = get_current_screen();

		if ( $screen && 'portfolio' === $screen->id && 'post' === $screen->base ) {
			remove_action( 'admin_print_footer_scripts', array( 'WP_Internal_Pointers', 'pointer_wp410_dfw' ) );
		}
	}
}

new AC_Admin_Post_Types();
