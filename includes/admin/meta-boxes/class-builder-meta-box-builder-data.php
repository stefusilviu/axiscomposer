<?php
/**
 * Page Builder Data
 *
 * Displays the page builder data box, tabbed, with several drag and drop canvas elements.
 *
 * @class       AB_Meta_Box_Builder_Data
 * @package     AxisBuilder/Admin/Meta Boxes
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Meta_Box_Builder_Data Class
 */
class AB_Meta_Box_Builder_Data {

	/**
	 * Output the Metabox
	 */
	public static function output( $post ) {
		wp_nonce_field( 'axisbuilder_save_data', 'axisbuilder_meta_nonce' );

		?>
		<div class="panel-wrap builder_data">
			<?php echo '<input type="hidden" name="axisbuilder_status" value="' . esc_attr( get_post_meta( $post->ID, '_axisbuilder_status', true ) ) . '"/>'; ?>
			<ul class="builder_data_tabs axisbuilder-tabs">
				<?php
					$builder_data_tabs = apply_filters( 'axisbuilder_shortcode_tabs', array(
						'layout'  => array(
							'label'  => __( 'Layout Elements', 'axisbuilder' ),
							'target' => 'layout_builder_data',
							'class'  => array( 'hide_if_empty' ),
						),
						'content' => array(
							'label'  => __( 'Content Elements', 'axisbuilder' ),
							'target' => 'content_builder_data',
							'class'  => array( 'hide_if_empty' ),
						),
						'media'   => array(
							'label'  => __( 'Media Elements', 'axisbuilder' ),
							'target' => 'media_builder_data',
							'class'  => array( 'hide_if_empty' ),
						),
						'plugin'  => array(
							'label'  => __( 'Plugin Elements', 'axisbuilder' ),
							'target' => 'plugin_builder_data',
							'class'  => array( 'hide_if_empty' ),
						),
					) );

					foreach ( $builder_data_tabs as $key => $tab ) {
						?><li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode( ' ' , $tab['class'] ); ?>">
							<a href="#<?php echo $tab['target']; ?>"><?php echo esc_html( $tab['label'] ); ?></a>
						</li><?php
					}

					do_action( 'axisbuilder_shortcode_write_panel_tabs' );
				?>
			</ul>

			<div id="layout_builder_data" class="panel axisbuilder_options_panel">
				<?php self::fetch_shortcode_buttons( 'layout' ); ?>
			</div>

			<div id="content_builder_data" class="panel axisbuilder_options_panel">
				<?php self::fetch_shortcode_buttons( 'content' ); ?>
			</div>

			<div id="media_builder_data" class="panel axisbuilder_options_panel">
				<?php self::fetch_shortcode_buttons( 'media' ); ?>
			</div>

			<div id="plugin_builder_data" class="panel axisbuilder_options_panel">
				<?php self::fetch_shortcode_buttons( 'plugin' ); ?>
			</div>

			<?php

				do_action( 'axisbuilder_shortcode_data_panels' );
			?>

			<div class="clear"></div>
		</div>
		<?php
	}

	/**
	 * Fetch the shortcodes buttons.
	 * @param  string $type Tabbed Name.
	 * @return string       Shortcode Button Links.
	 */
	public static function fetch_shortcode_buttons( $type = 'plugin' ) {
		echo $type;
	}

	/**
	 * Save Meta-Box data.
	 */
	public function save( $post_id, $post ) {

		// Save the builder status and canvas textarea data :)
		$builder_post_meta = array( 'axisbuilder_status', 'axisbuilder_canvas' );

		foreach ( $builder_post_meta as $post_meta ) {
			if ( isset( $_POST[$post_meta] ) ) {
				update_post_meta( $post_id, '_' . $post_meta, $_POST[$post_meta] );
			}
		}

		// Filter the redirect url in case we got a Meta-Box that is expanded.
		if ( ! empty( $_POST['axisbuilder-expanded-hidden'] ) ) {
			add_filter( 'redirect_post_location', array( __CLASS__, 'set_builder_expanded' ), 10, 2 );
		}
	}

	/**
	 * Set the correct Builder Expanded ID.
	 *
	 * @param  $location
	 * @return string
	 */
	public static function set_builder_expanded( $location ) {
		return add_query_arg( 'axisbuilder-expanded', $_POST['axisbuilder-expanded-hidden'], $location );
	}
}
