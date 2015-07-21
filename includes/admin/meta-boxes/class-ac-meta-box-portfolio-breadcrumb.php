<?php
/**
 * Portfolio Breadcrumb Hierarchy
 *
 * @class    AC_Meta_Box_Portfolio_Breadcrumb
 * @version  1.0.0
 * @package  AxisComposer/Admin/Meta Boxes
 * @category Admin
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Meta_Box_Portfolio_Breadcrumb Class
 */
class AC_Meta_Box_Portfolio_Breadcrumb {

	/**
	 * Output the meta box
	 */
	public static function output( $post ) {
		wp_nonce_field( 'axiscomposer_save_data', 'axiscomposer_meta_nonce' );

		?>
		<ul class="breadcrumb_data">
			<p class="form-field">
				<label for="breadcrumb_parent"><?php _e( 'Breadcrumb Parent Page', 'axiscomposer' ) ?></label>
				<span class="description side"><?php _e( 'Select a parent page for this entry. If no page is selected then session data will be used to build the breadcrumb.', 'axiscomposer' ); ?></span>
				<input type="hidden" class="ac-page-search" id="breadcrumb_parent" name="breadcrumb_parent" data-placeholder="<?php _e( 'Search for a page&hellip;', 'axiscomposer' ); ?>" data-action="axiscomposer_json_search_pages" data-allow_clear="true" data-multiple="false" data-exclude="<?php echo intval( $post->ID ); ?>" data-selected="<?php
					$page_id = absint( get_post_meta( $post->ID, '_breadcrumb_parent', true ) );

					if ( $page_id ) {
						$page = get_post( $page_id );
						if ( is_object( $page ) ) {
							$page_title = sprintf( __( '%s &ndash; %s', 'axiscomposer' ), '#' . absint( $page->ID ), wp_kses_post( html_entity_decode( $page->post_title ) ) );
						}

						echo esc_attr( $page_title );
					}
				?>" value="<?php echo $page_id ? $page_id : ''; ?>" />
			</p>
			<?php do_action( 'axiscomposer_breadcrumb_data_end', $post->ID ); ?>
		</ul>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id ) {
		// Update meta
		update_post_meta( $post_id, '_breadcrumb_parent', absint( $_POST['breadcrumb_parent'] ) );
	}
}
