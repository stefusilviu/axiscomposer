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

	private static $load_shortcode;

	/**
	 * Output the Metabox
	 */
	public static function output( $post ) {
		wp_nonce_field( 'axisbuilder_save_data', 'axisbuilder_meta_nonce' );

		// Builder Post Meta
		$builder_status = get_post_meta( $post->ID, '_axisbuilder_status', true );
		$builder_canvas = get_post_meta( $post->ID, '_axisbuilder_canvas', true );

		// Builder Status
		echo '<input type="hidden" name="axisbuilder_status" value="' . esc_attr( $builder_status ? $builder_status : 'inactive' ) . '"/>';

		?>

		<div id="axis-pagebuilder" class="axisbuilder-shortcodes axisbuilder-style panel-wrap">
			<div id="axisbuilder-panels" class="panels-tab">
				<ul class="axisbuilder-tabs">
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
								'label'  => __( 'Plugin Additions', 'axisbuilder' ),
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

				<div id="layout_builder_data" class="panel axisbuilder-shortcodes-panel"><?php self::fetch_shortcode_buttons( 'layout' ); ?></div>
				<div id="content_builder_data" class="panel axisbuilder-shortcodes-panel"><?php self::fetch_shortcode_buttons( 'content' ); ?></div>
				<div id="media_builder_data" class="panel axisbuilder-shortcodes-panel"><?php self::fetch_shortcode_buttons( 'media' ); ?></div>
				<div id="plugin_builder_data" class="panel axisbuilder-shortcodes-panel"><?php self::fetch_shortcode_buttons( 'plugin' ); ?></div>

				<?php

					do_action( 'axisbuilder_shortcode_data_panels' );
				?>

				<div class="clear"></div>

			</div>
			<div id="axisbuilder-handle" class="handle-bar">
				<div class="control-bar">
					<div class="history-sections">
						<div class="history-action" data-axis-tooltip="<?php _e( 'History', 'axisbuilder' ); ?>">
							<a href="#undo" class="undo-icon undo-data" title="<?php _e( 'Undo', 'axisbuilder' ); ?>"></a>
							<a href="#redo" class="redo-icon redo-data" title="<?php _e( 'Redo', 'axisbuilder' ); ?>"></a>
						</div>
						<div class="delete-action">
							<a href="#trash" class="trash-icon trash-data" data-axis-tooltip="<?php _e( 'Permanently delete all canvas elements', 'axisbuilder' ); ?>"></a>
						</div>
					</div>
					<div class="template-sections">
						<a href="#template" class="button-secondary" data-axis-tooltip="<?php _e( 'Save or Load templates', 'axisbuilder' ); ?>">Templates</a>
					</div>
				</div>
			</div>
			<div id="axisbuilder-canvas" class="visual-editor">
				<div class="canvas-area axisbuilder-data loader layout-flex-grid axisbuilder-drop" data-dragdrop-level="0"></div>
				<div class="canvas-secure-data">
					<textarea name="axisbuilder_canvas" id="canvas-data" class="canvas-data"><?php echo esc_textarea( $builder_canvas ); ?></textarea> <!-- readonly="readonly" later -->
				</div>
			</div>
		</div>

		<script type="text/template" id="tmpl-axisbuilder-modal-trash-data">
			<div class="axisbuilder-backbone-modal">
				<div class="axisbuilder-backbone-modal-content modal-animation">
					<section class="axisbuilder-backbone-modal-main" role="main">
						<header class="axisbuilder-backbone-modal-header">
							<a class="modal-close modal-close-link" href="#"><span class="close-icon"><span class="screen-reader-text">Close media panel</span></span></a>
							<h1><%= title %></h1>
						</header>
						<article>
							<form action="" method="post">
								<p><%= message %></p>
							</form>
						</article>
						<footer>
							<div class="inner">
								<% if ( dismiss ) { %>
									<button class="button button-large modal-close"><?php _e( 'Dismiss' , 'axisbuilder' ); ?></button>
								<% } else { %>
									<button id="btn-ok" class="button button-large button-primary"><?php _e( 'Delete' , 'axisbuilder' ); ?></button>
								<% } %>
							</div>
						</footer>
					</section>
				</div>
			</div>
			<div class="axisbuilder-backbone-modal-backdrop modal-close">&nbsp;</div>
		</script>

		<script type="text/template" id="tmpl-axisbuilder-modal-cell-size">
			<div class="axisbuilder-backbone-modal">
				<div class="axisbuilder-backbone-modal-content modal-animation">
					<section class="axisbuilder-backbone-modal-main" role="main">
						<header class="axisbuilder-backbone-modal-header">
							<a class="modal-close modal-close-link" href="#"><span class="close-icon"><span class="screen-reader-text">Close media panel</span></span></a>
							<h1><%= title %></h1>
						</header>
						<article>
							<form action="" method="post">
								<p><%= message %></p>
							</form>
						</article>
						<footer>
							<div class="inner">
								<% if ( dismiss ) { %>
									<button class="button button-large modal-close"><?php _e( 'Dismiss' , 'axisbuilder' ); ?></button>
								<% } else { %>
									<button id="btn-ok" class="button button-large button-primary"><?php _e( 'Add' , 'axisbuilder' ); ?></button>
								<% } %>
							</div>
						</footer>
					</section>
				</div>
			</div>
			<div class="axisbuilder-backbone-modal-backdrop modal-close">&nbsp;</div>
		</script>
		<?php
	}

	/**
	 * Fetch Shortcode Buttons.
	 * @param  string      $type    Tabbed content type
	 * @param  boolean     $display Return or Print
	 * @return string|null          Shortcode Buttons
	 */
	protected static function fetch_shortcode_buttons( $type = 'plugin', $display = true ) {

		foreach ( AB()->shortcodes->get_shortcodes() as $load_shortcodes ) {

			if ( empty( $load_shortcodes->shortcode['invisible'] ) ) {

				if ( $load_shortcodes->shortcode['type'] === $type ) {

					// Fetch shortcode data :)
					$title     = $load_shortcodes->title;
					$tooltip   = $load_shortcodes->tooltip;
					$shortcode = $load_shortcodes->shortcode;

					// Fallback if icon is missing :)
					$shortcode_icon = ( isset( $shortcode['image'] ) && ! empty( $shortcode['image'] ) ) ? '<img src="' . $shortcode['image'] . '" alt="' . $title . '" />' : '<i class="' . $shortcode['icon'] . '"></i>';

					// Create a button Link :)
					self::$load_shortcode = '<a href="#' . strtolower( $shortcode['href-class'] ) . '" class="insert-shortcode ' . $shortcode['class'] . $shortcode['target'] . '" data-dragdrop-level="' . $shortcode['drag-level'] . '" data-axis-tooltip="' . $tooltip . '">' . $shortcode_icon . '<span>' . $title. '</span></a>';

					if ( $display ) {
						echo self::$load_shortcode;
					} else {
						return self::$load_shortcode;
					}
				}
			}
		}
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

		// Class for Debug or Test-mode
		if ( ( get_option( 'axisbuilder_debug_enabled' ) === 'yes' ) ) {
			$classes[] = 'axisbuilder-debug';
		}

		return $classes;
	}

	/**
	 * Save Meta-Box data.
	 */
	public static function save( $post_id ) {

		// Save the builder status and canvas textarea data :)
		$builder_post_meta = array( 'axisbuilder_status', 'axisbuilder_canvas' );

		foreach ( $builder_post_meta as $post_meta ) {
			if ( isset( $_POST[$post_meta] ) ) {
				update_post_meta( $post_id, '_' . $post_meta, $_POST[$post_meta] );
			}
		}
	}
}
