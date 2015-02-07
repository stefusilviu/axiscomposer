<?php
/**
 * Layout Data
 *
 * Display the layout data meta box.
 *
 * @class       AB_Meta_Box_Layout_Data
 * @package     AxisBuilder/Admin/Meta Boxes
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Meta_Box_Layout_Data Class
 */
class AB_Meta_Box_Layout_Data {

	/**
	 * Output the meta box
	 */
	public static function output( $post ) {
		wp_nonce_field( 'axisbuilder_save_data', 'axisbuilder_meta_nonce' );

		?>
		<ul class="layout_data">

			<?php
				do_action( 'axisbuilder_layout_data_start', $post->ID );

				// Layout
				axisbuilder_wp_select( array( 'id' => 'layout', 'label' => __( 'Layout Settings', 'axisbuilder' ), 'options' => array(
					'default'       => __( 'Default Layout', 'axisbuilder' ),
					'fullsize'      => __( 'No Sidebar', 'axisbuilder' ),
					'sidebar_left'  => __( 'Left Sidebar', 'axisbuilder' ),
					'sidebar_right' => __( 'Right Sidebar', 'axisbuilder' )
				), 'desc_side' => true, 'desc_tip' => false, 'description' => __( 'Select the specific layout for this entry.', 'axisbuilder' ) ) );

				// Sidebar
				axisbuilder_wp_select( array( 'id' => 'sidebar', 'label' => __( 'Sidebar Settings', 'axisbuilder' ), 'desc_side' => true, 'desc_tip' => false, 'description' => __( 'Choose a custom sidebar for this entry.', 'axisbuilder' ), 'options' => axisbuilder_get_registered_sidebars() ) );

				// Title Bar
				axisbuilder_wp_select( array( 'id' => 'header_title_bar', 'label' => __( 'Title Bar Settings', 'axisbuilder' ), 'options' => array(
					'default'              => __( 'Default Layout', 'axisbuilder' ),
					'title_bar_breadcrumb' => __( 'Display title and breadcrumbs', 'axisbuilder' ),
					'title_bar'            => __( 'Display only title', 'axisbuilder' ),
					'hidden_title_bar'     => __( 'Hide both', 'axisbuilder' )
				), 'desc_side' => true, 'desc_tip' => false, 'description' => __( 'Display the Title Bar with Page Title and Breadcrumb Navigation?', 'axisbuilder' ) ) );

				// Footer Settings
				axisbuilder_wp_select( array( 'id' => 'footer', 'label' => __( 'Footer Settings', 'axisbuilder' ), 'options' => array(
					'footer_both' => __( 'Both Widgets and Socket', 'axisbuilder' ),
					'widget_only' => __( 'Only Widgets (No Socket)', 'axisbuilder' ),
					'socket_only' => __( 'Only Socket (No Widgets)', 'axisbuilder' ),
					'footer_none' => __( 'Don\'t Display Both', 'axisbuilder' )
				), 'desc_side' => true, 'desc_tip' => false, 'description' => __( 'Display the footer widgets?', 'axisbuilder' ) ) );

				// Header Transparency
				axisbuilder_wp_select( array( 'id' => 'header_transparency', 'label' => __( 'Header visibility and transparency', 'axisbuilder' ), 'options' => array(
					'default'                          => __( 'No transparency', 'axisbuilder' ),
					'header_transparent'               => __( 'Transparent Header', 'axisbuilder' ),
					'header_transparent header_glassy' => __( 'Transparent & Glassy Header', 'axisbuilder' )
				), 'desc_side' => true, 'desc_tip' => false, 'description' => __( 'Several options to change the header transparency and visibility on this page.', 'axisbuilder' ) ) );

				do_action( 'axisbuilder_layout_data_end', $post->ID );
			?>
		</ul>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id ) {

		// Save the layout settings ;)
		$layout_post_meta = array( 'layout', 'sidebar', 'header_title_bar', 'footer', 'header_transparency' );

		foreach ( $layout_post_meta as $post_meta ) {
			if ( isset( $_POST[ $post_meta ] ) ) {
				update_post_meta( $post_id, $post_meta, $_POST[ $post_meta ] );
			}
		}
	}
}
