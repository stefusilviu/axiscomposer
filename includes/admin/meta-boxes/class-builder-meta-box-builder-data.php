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

		// Builder Post Meta
		$builder_status = get_post_meta( get_the_ID(), '_axisbuilder_status', true );
		$builder_canvas = get_post_meta( get_the_ID(), '_axisbuilder_canvas', true );

		// Builder Status
		echo '<input type="hidden" name="axisbuilder_status" value="' . esc_attr( $builder_status ? $builder_status : 'inactive' ) . '"/>';

		?>
		<div class="panel-wrap builder_data">
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
				<?php echo AB()->shortcodes->load_shortcode_buttons( 'layout' ); ?>
			</div>

			<div id="content_builder_data" class="panel axisbuilder_options_panel">
				<?php echo AB()->shortcodes->load_shortcode_buttons( 'content' ); ?>
			</div>

			<div id="media_builder_data" class="panel axisbuilder_options_panel">
				<?php echo AB()->shortcodes->load_shortcode_buttons( 'media' ); ?>
			</div>

			<div id="plugin_builder_data" class="panel axisbuilder_options_panel">
				<?php echo AB()->shortcodes->load_shortcode_buttons( 'plugin' ); ?>
			</div>

			<?php

				do_action( 'axisbuilder_shortcode_data_panels' );
			?>

			<div class="clear"></div>
		</div>
		<?php

		$html = '<div id="axisbuilder-wrapper" class="wrap-pagebuilder">';

			// Builder Handle
			$html .= '<div id="axisbuilder-handle" class="handle-bar">';

				$html .= '<div class="control-bar">';

					// History Sections
					$html .= '<div class="history-sections">';
						$html .= '<div class="history-action" data-axis-tooltip="History">';
							$html .= '<a href="#" class="undo-icon undo-data" title="Undo"></i></a>';
							$html .= '<a href="#" class="redo-icon redo-data" title="Redo"></i></a>';
						$html .= '</div>';
						$html .= '<div class="delete-action">';
							$html .= '<a href="#" class="trash-icon trash-data" data-axis-tooltip="Permanently delete all canvas elements"></a>';
						$html .= '</div>';
					$html .= '</div>';

					// Content Sections
					$html .= '<div class="content-sections">';
						$html .= '<div class="template-action">';
							$html .= '<a href="#" class="button button-secondary" data-axis-tooltip="Save or Load templates">Templates</a>';
						$html .= '</div>';
						$html .= '<div class="fullscreen-action">';
							$html .= '<a href="#" class="expand-icon axisbuilder-attach-expand">Close</a>';
						$html .= '</div>';
					$html .= '</div>';

				$html .= '</div>';

			$html .= '</div>';

			// Builder Canvas
			$html .= '<div id="axisbuilder-canvas" class="visual-editor">';
				$html .= '<div class="canvas-area loader drag-element" data-dragdrop-level="0"></div>';
				$html .= '<div class="canvas-secure-data">';
					$html .= '<textarea name="axisbuilder_canvas" id="canvas-data" class="canvas-data">' . esc_textarea( $builder_canvas ) . '</textarea>'; // readonly="readonly" later on
				$html .= '</div>';
			$html .= '</div>';
		$html .= '</div>';

		echo $html;
	}

	/**
	 * Filter the postbox classes for a specific screen and screen ID combo.
	 * @param  array $classes An array of postbox classes.
	 * @return array
	 */
	public static function postbox_classes( $classes ) {

		// Class for hidden items
		if ( empty( $_GET['post'] ) || ( isset( $_GET['post'] ) && get_post_meta( $_GET['post'], '_axisbuilder_status', true ) != 'active' ) ) {
			$classes[] = 'axisbuilder-hidden';
		}

		// Class for expanded items
		if ( ! empty( $_GET['axisbuilder-expanded'] ) && ( 'axisbuilder-editor' === $_GET['axisbuilder-expanded'] ) ) {
			$classes[] = 'axisbuilder-expanded';
		}

		// Class for Debug or Test-mode
		if ( defined( 'AB_DEBUG' ) && AB_DEBUG ) {
			$classes[] = 'axisbuilder-debug';
		}

		return $classes;
	}

	/**
	 * Save Meta-Box data.
	 */
	public static function save( $post_id, $post ) {

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
