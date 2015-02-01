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

		// Edit post screens
		add_filter( 'enter_title_here', array( $this, 'enter_title_here' ), 1, 2 );

		// Meta-Box Class
		include_once( 'class-builder-admin-meta-boxes.php' );
	}

	/**
	 * Change messages when a post type is updated.
	 *
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
	 * Change title boxes in admin.
	 *
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
}

new AB_Admin_Post_Types();
