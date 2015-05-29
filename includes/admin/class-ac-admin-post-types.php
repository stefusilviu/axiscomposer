<?php
/**
 * Post Types Admin
 *
 * @class       AC_Admin_Post_Types
 * @package     AxisComposer/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
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
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );

		// WP List table columns. Defined here so they are always available for events such as inline editing.
		// add_filter( 'manage_portfolio_posts_columns', array( $this, 'portfolio_columns' ) );
		// add_action( 'manage_portfolio_posts_custom_column', array( $this, 'render_portfolio_columns' ), 2 );
		// add_filter( 'manage_edit-portfolio_sortable_columns', array( $this, 'portfolio_sortable_columns' ) );

		// Edit post screens
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );
		add_filter( 'media_view_strings', array( $this, 'change_insert_into_post' ) );
		add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );
		add_action( 'edit_form_after_editor', array( $this, 'edit_form_after_editor' ) );

		// Meta-Box Class
		include_once( 'class-ac-admin-meta-boxes.php' );
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
	 * Define custom columns for products
	 * @param  array $existing_columns
	 * @return array
	 */
	public function portfolio_columns( $existing_columns ) {
		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}

		unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

		$columns                   = array();
		$columns['cb']             = $existing_columns['cb'];
		$columns['thumb']          = '<span class="ac-image tips" data-tip="' . __( 'Image', 'axiscomposer' ) . '">' . __( 'Image', 'axiscomposer' ) . '</span>';
		$columns['name']           = __( 'Name', 'axiscomposer' );
		$columns['portfolio_cat']  = __( 'Categories', 'axiscomposer' );
		$columns['portfolio_tag']  = __( 'Tags', 'axiscomposer' );
		$columns['portfolio_type'] = __( 'Types', 'axiscomposer' );
		$columns['featured']       = '<span class="ac-featured parent-tips" data-tip="' . __( 'Featured', 'axiscomposer' ) . '">' . __( 'Featured', 'axiscomposer' ) . '</span>';
		$columns['date']           = __( 'Date', 'axiscomposer' );

		return array_merge( $columns, $existing_columns );
	}

	/**
	 * Ouput custom columns for products
	 * @param  string $column
	 */
	public function render_portfolio_columns( $column ) {
		global $post;

		switch ( $column ) {
			case 'thumb' :
				# code...
				break;
			case 'name':
				# code...
				break;
			case 'portfolio_cat':
				# code...
				break;
			case 'portfolio_tag':
				# code...
				break;
			case 'portfolio_type':
				# code...
				break;
			case 'featured':
				# code...
				break;
			default :
				break;
		}
	}

	/**
	 * Make columns sortable - https://gist.github.com/906872
	 * @param  array $columns
	 * @return array
	 */
	public function portfolio_sortable_columns( $columns ) {
		$custom = array(
			'featured' => 'featured',
			'name'     => 'title'
		);
		return wp_parse_args( $custom, $columns );
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
	 * Change label for insert buttons.
	 * @param  array $strings
	 * @return array
	 */
	public function change_insert_into_post( $strings ) {
		global $post_type;

		if ( in_array( $post_type, array( 'portfolio' ) ) ) {
			$obj = get_post_type_object( $post_type );

			$strings['insertIntoPost']     = sprintf( __( 'Insert into %s', 'axiscomposer' ), $obj->labels->singular_name );
			$strings['uploadedToThisPost'] = sprintf( __( 'Uploaded to this %s', 'axiscomposer' ), $obj->labels->singular_name );
		}

		return $strings;
	}

	/**
	 * edit_form_after_title function.
	 * @return string
	 */
	public function edit_form_after_title() {
		$screen = get_current_screen();

		if ( in_array( $screen->id, ac_get_allowed_screen_types() ) ) {
			global $post_ID;

			$params = apply_filters( 'axiscomposer_editors_toggle_params', array(
				'notice'        => '',
				'disabled'      => false,
				'builder_label' => __( 'Use Page Builder', 'axiscomposer' ),
				'default_label' => __( 'Use Default Editor', 'axiscomposer' ),
				'disable_label' => __( 'Page Builder Disabled', 'axiscomposer' )
			) );

			if ( is_pagebuilder_active( $post_ID ) ) {
				$active_label = $params['default_label'];
				$button_class = 'button-secondary';
				$editor_class = 'ac-hidden-editor';
			} elseif ( $params['disabled'] ) {
				$active_label = $params['disable_label'];
				$button_class = 'button-secondary disabled';
				$editor_class = 'ac-visible-editor';
			} else {
				$active_label = $params['builder_label'];
				$button_class = 'button-primary';
				$editor_class = 'ac-visible-editor';
			}

			echo '<a href="#" class="button button-large ' . $button_class . ' axiscomposer-toggle-editor" data-builder="' . $params['builder_label'] . '" data-editor="' . $params['default_label'] . '">' . $active_label . '</a>';
			echo '<div id="postdivrich_wrap" class="axiscomposer ' . $editor_class . '">';
			if ( $params['notice'] ) {
				echo '<div class="axiscomposer-plugin-display-notice">' . $params['notice'] . '</div>';
			}
		}
	}

	/**
	 * edit_form_after_editor function.
	 * @return string
	 */
	public function edit_form_after_editor() {
		$screen = get_current_screen();

		if ( in_array( $screen->id, ac_get_allowed_screen_types() ) ) {
			echo '</div> <!-- #postdivrich_wrap -->';
		}
	}
}

new AC_Admin_Post_Types();
