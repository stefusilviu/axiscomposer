<?php
/**
 * Post Types Admin
 *
 * @class       AB_Admin_Post_Types
 * @package     AxisBuilder/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Admin_Post_Types Class
 *
 * Handles the edit posts views and some functionality on the edit post screen for AB post types.
 */
class AB_Admin_Post_Types {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );

		// WP List table columns. Defined here so they are always available for events such as inline editing.
		add_filter( 'manage_edit-portfolio_columns', array( $this, 'portfolio_columns' ) );
		add_action( 'manage_portfolio_posts_custom_column', array( $this, 'render_portfolio_columns' ), 2 );
		add_filter( 'manage_edit-portfolio_sortable_columns', array( $this, 'portfolio_sortable_columns' ) );

		// Edit post screens
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );
		add_filter( 'media_view_strings', array( $this, 'change_insert_into_post' ) );

		// Meta-Box Class
		include_once( 'class-builder-admin-meta-boxes.php' );
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
			1 => sprintf( __( 'Project updated. <a href="%s">View Project</a>', 'axisbuilder' ), esc_url( get_permalink( $post_ID ) ) ),
			2 => __( 'Custom field updated.', 'axisbuilder' ),
			3 => __( 'Custom field deleted.', 'axisbuilder' ),
			4 => __( 'Project updated.', 'axisbuilder' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Project restored to revision from %s', 'axisbuilder' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Project published. <a href="%s">View Project</a>', 'axisbuilder' ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Project saved.', 'axisbuilder' ),
			8 => sprintf( __( 'Project submitted. <a target="_blank" href="%s">Preview Project</a>', 'axisbuilder' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Project scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Project</a>', 'axisbuilder' ),
			  date_i18n( __( 'M j, Y @ G:i', 'axisbuilder' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Project draft updated. <a target="_blank" href="%s">Preview Project</a>', 'axisbuilder' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		return $messages;
	}

	/**
	 * Define custom columns for products
	 * @param  array $existing_columns
	 * @return array
	 */
	public function portfolio_columns( $existing_columns ) {

		$current_screen = get_current_screen();

		// Check we're on the correct post type
		if ( 'portfolio' != $current_screen->post_type ) {
			return $existing_columns;
		}

		if ( empty( $existing_columns ) && ! is_array( $existing_columns ) ) {
			$existing_columns = array();
		}

		unset( $existing_columns['title'], $existing_columns['comments'], $existing_columns['date'] );

		$columns                   = array();
		$columns['cb']             = $existing_columns['cb'];
		$columns['thumb']          = '<span class="axisbuilder-image tips" data-tip="' . __( 'Image', 'axisbuilder' ) . '">' . __( 'Image', 'axisbuilder' ) . '</span>';
		$columns['name']           = __( 'Name', 'axisbuilder' );
		$columns['portfolio_cat']  = __( 'Categories', 'axisbuilder' );
		$columns['portfolio_tag']  = __( 'Tags', 'axisbuilder' );
		$columns['portfolio_type'] = __( 'Types', 'axisbuilder' );
		$columns['featured']       = '<span class="axisbuilder-featured parent-tips" data-tip="' . __( 'Featured', 'axisbuilder' ) . '">' . __( 'Featured', 'axisbuilder' ) . '</span>';
		$columns['date']           = __( 'Date', 'axisbuilder' );

		return array_merge( $columns, $existing_columns );
	}

	/**
	 * Ouput custom columns for products
	 * @param  string $column
	 */
	public function render_product_columns( $column ) {
		global $post;

		switch ( $columns ) {
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
				$text = __( 'Project name', 'axisbuilder' );
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

			$strings['insertIntoPost']     = sprintf( __( 'Insert into %s', 'axisbuilder' ), $obj->labels->singular_name );
			$strings['uploadedToThisPost'] = sprintf( __( 'Uploaded to this %s', 'axisbuilder' ), $obj->labels->singular_name );
		}

		return $strings;
	}
}

new AB_Admin_Post_Types();
