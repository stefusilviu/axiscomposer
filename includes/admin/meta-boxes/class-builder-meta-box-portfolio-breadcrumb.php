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

		?>
		<ul class="breadcrumb_data">

			<?php
				do_action( 'axisbuilder_breadcrumb_data_start', $post->ID );

				// Breadcrumb Parent Page
				axisbuilder_wp_select( array( 'id' => 'breadcrumb_parent', 'label' => __( 'Breadcrumb Parent Page', 'axisbuilder' ), 'options' => array(), 'desc_side' => true, 'desc_tip' => false, 'desc_class' => 'side', 'description' => __( 'Select a parent page for this entry. If no page is selected the them will use session data to build the breadcrumb.', 'axisbuilder' ) ) );

				do_action( 'axisbuilder_breadcrumb_data_end', $post->ID );
			?>
		</ul>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id ) {

		// Save the breadcrumb settings ;)
		$layout_post_meta = array( 'breadcrumb_parent' );

		foreach ( $layout_post_meta as $post_meta ) {
			if ( isset( $_POST[ $post_meta ] ) ) {
				update_post_meta( $post_id, $post_meta, $_POST[ $post_meta ] );
			}
		}
	}

}
