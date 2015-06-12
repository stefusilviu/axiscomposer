<?php
/**
 * Layout Data
 *
 * Display the layout data meta box.
 *
 * @class       AC_Meta_Box_Layout_Data
 * @package     AxisComposer/Admin/Meta Boxes
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Meta_Box_Layout_Data Class
 */
class AC_Meta_Box_Layout_Data {

	/**
	 * Output the meta box
	 */
	public static function output( $post ) {
		wp_nonce_field( 'axiscomposer_save_data', 'axiscomposer_meta_nonce' );

		?>
		<ul class="layout_data">

			<?php
				do_action( 'axiscomposer_layout_data_start', $post->ID );

				// Layout
				axiscomposer_wp_select( array( 'id' => 'layout', 'class' => 'select side show_if_sidebar', 'label' => __( 'Layout Settings', 'axiscomposer' ), 'options' => array(
					'default'       => __( 'Default Layout', 'axiscomposer' ),
					'fullsize'      => __( 'No Sidebar', 'axiscomposer' ),
					'sidebar_left'  => __( 'Left Sidebar', 'axiscomposer' ),
					'sidebar_right' => __( 'Right Sidebar', 'axiscomposer' )
				), 'desc_side' => true, 'desc_tip' => false, 'desc_class' => 'side', 'description' => __( 'Select the specific layout for this entry.', 'axiscomposer' ) ) );

				// Sidebar
				axiscomposer_wp_select( array( 'id' => 'sidebar', 'class' => 'select side', 'label' => __( 'Sidebar Settings', 'axiscomposer' ), 'desc_side' => true, 'desc_tip' => false, 'desc_class' => 'side', 'description' => __( 'Choose a custom sidebar for this entry.', 'axiscomposer' ), 'options' => ac_get_sidebars( array( 'default' => 'Default Sidebar' ), array( 'Display Everywhere' ) ) ) );

				// Footer
				axiscomposer_wp_select( array( 'id' => 'footer', 'class' => 'select side', 'label' => __( 'Footer Settings', 'axiscomposer' ), 'options' => array(
					'default'     => __( 'Default Socket and Widgets', 'axiscomposer' ),
					'footer_both' => __( 'Both Socket and Widgets', 'axiscomposer' ),
					'widget_only' => __( 'Only Widgets (No Socket)', 'axiscomposer' ),
					'socket_only' => __( 'Only Socket (No Widgets)', 'axiscomposer' ),
					'footer_hide' => __( 'Hide Socket and Widgets', 'axiscomposer' )
				), 'desc_side' => true, 'desc_tip' => false, 'desc_class' => 'side', 'description' => __( 'Display the socket and footer widgets?', 'axiscomposer' ) ) );

				/**
				 * @todo Display a Conditional Header Notice
				 * Below Header settings are only available for layouts with a main menu placed at the top ;)
				 */

				// Header Title and Breadcrumbs
				axiscomposer_wp_select( array( 'id' => 'header_title_bar', 'class' => 'select side', 'label' => __( 'Header Title and Breadcrumb', 'axiscomposer' ), 'options' => array(
					'default'          => __( 'Default Title and Breadcrumb', 'axiscomposer' ),
					'header_crumb_bar' => __( 'Display Title and Breadcrumb', 'axiscomposer' ),
					'header_title_bar' => __( 'Display Title (No Breadcrumb)', 'axiscomposer' ),
					'hidden_title_bar' => __( 'Hide both Title and Breadcrumb', 'axiscomposer' )
				), 'desc_side' => true, 'desc_tip' => false, 'desc_class' => 'side', 'description' => __( 'Display the Title Bar with Page Title and Breadcrumb Navigation?', 'axiscomposer' ) ) );

				// Header Transparency
				axiscomposer_wp_select( array( 'id' => 'header_transparency', 'class' => 'select side', 'label' => __( 'Header Transparency and Visibility', 'axiscomposer' ), 'options' => array(
					'default'                          => __( 'No Transparency', 'axiscomposer' ),
					'header_transparent'               => __( 'Transparent Header', 'axiscomposer' ),
					'header_transparent header_glassy' => __( 'Transparent & Glassy Header', 'axiscomposer' ),
					'header_transparent header_scroll' => __( 'Display Header on scroll down', 'axiscomposer' ),
					'header_transparent header_hidden' => __( 'Hide Header on this page', 'axiscomposer' )
				), 'desc_side' => true, 'desc_tip' => false, 'desc_class' => 'side', 'description' => __( 'Several options to change the header transparency and visibility on this page.', 'axiscomposer' ) ) );

				do_action( 'axiscomposer_layout_data_end', $post->ID );
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
