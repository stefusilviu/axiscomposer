<?php
/**
 * Portfolio Breadcrumb Hierarchy
 *
 * @class       AB_Meta_Box_Portfolio_Breadcrumb
 * @package     AxisBuilder/Admin/Meta Boxes
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Meta_Box_Portfolio_Breadcrumb Class
 */
class AB_Meta_Box_Portfolio_Breadcrumb {

	/**
	 * Output the meta box
	 */
	public static function output( $post ) {
		wp_nonce_field( 'axisbuilder_save_data', 'axisbuilder_meta_nonce' );

		// Breadcrumb Parent Page
		?>
		<ul class="breadcrumb_data">
			<p class="form-field"><label for="breadcrumb_parent"><?php _e( 'Breadcrumb Parent Page', 'axisbuilder' ) ?></label>
				<span class="description side"><?php _e( 'Select a parent page for this entry. If no page is selected then session data will be used to build the breadcrumb.', 'axisbuilder' ); ?></span>
				<?php
					$page_id     = '';
					$page_string = '';
					if ( ! empty( absint( get_post_meta( $post->ID, '_breadcrumb_parent', true ) ) ) ) {
						$page_id     = absint( get_post_meta( $post->ID, '_breadcrumb_parent', true ) );
						$page        = get_post( $page_id );
						$page_string = sprintf( __( '%s &ndash; %s', 'axisbuilder' ), '#' . absint( $page->ID ), wp_kses_post( $page->post_title ) );
					}
				?>
				<input type="hidden" class="axisbuilder-page-search" id="breadcrumb_parent" name="breadcrumb_parent" data-placeholder="<?php _e( 'Search for a page&hellip;', 'axisbuilder' ); ?>" data-action="axisbuilder_json_search_pages" data-selected="<?php echo esc_attr( $page_string ); ?>" value="<?php echo $page_id; ?>" data-allow_clear="true" />
			</p>
			<?php do_action( 'axisbuilder_breadcrumb_data_end', $post->ID ); ?>
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
