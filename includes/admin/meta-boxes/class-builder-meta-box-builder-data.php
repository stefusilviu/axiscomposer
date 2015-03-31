<?php
/**
 * Page Builder Data
 *
 * Displays the page builder data meta box, tabbed, with several drag and drop canvas elements.
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
	 * Output the meta box
	 */
	public static function output( $post ) {
		wp_nonce_field( 'axisbuilder_save_data', 'axisbuilder_meta_nonce' );

		?>
		<input type="hidden" class="axisbuilder-status" name="axisbuilder_status" value="<?php echo esc_attr( is_pagebuilder_active( $post->ID ) ? 'active' : 'inactive' ); ?>" />
		<div id="axis-pagebuilder" class="axisbuilder-shortcodes axisbuilder-style">
			<div id="axisbuilder-panels" class="panel-wrap">
				<ul class="axisbuilder-tabs" style="display:none">
					<?php
						$shortcode_data_tabs = apply_filters( 'axisbuilder_shortcode_data_tabs', array(
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

						foreach ( $shortcode_data_tabs as $key => $tab ) {
							?><li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode( ' ', $tab['class'] ); ?>">
								<a href="#<?php echo $tab['target']; ?>"><?php echo esc_html( $tab['label'] ); ?></a>
							</li><?php
						}

						do_action( 'axisbuilder_shortcode_write_panel_tabs' );
					?>
				</ul>

				<div id="layout_builder_data" class="panel axisbuilder-options-panel"><?php self::fetch_shortcode_buttons( 'layout' ); ?></div>
				<div id="content_builder_data" class="panel axisbuilder-options-panel"><?php self::fetch_shortcode_buttons( 'content' ); ?></div>
				<div id="media_builder_data" class="panel axisbuilder-options-panel"><?php self::fetch_shortcode_buttons( 'media' ); ?></div>
				<div id="plugin_builder_data" class="panel axisbuilder-options-panel"><?php self::fetch_shortcode_buttons( 'plugin' ); ?></div>

				<?php do_action( 'axisbuilder_shortcode_data_panels' ); ?>

				<div class="clear"></div>
			</div>
			<div id="axisbuilder-handle" class="handle-bar">
				<div class="control-bar">
					<div class="history-sections">
						<div class="history-action help_tip" data-tip="<?php _e( 'History', 'axisbuilder' ); ?>">
							<a href="#undo" class="undo-icon undo-data" title="<?php _e( 'Undo', 'axisbuilder' ); ?>"></a>
							<a href="#redo" class="redo-icon redo-data" title="<?php _e( 'Redo', 'axisbuilder' ); ?>"></a>
						</div>
						<div class="delete-action help_tip" data-tip="<?php _e( 'Permanently delete all canvas elements', 'axisbuilder' ); ?>">
							<a href="#trash" class="trash-icon trash-data"></a>
						</div>
					</div>
					<div class="template-sections help_tip" data-tip="<?php _e( 'Save or Load the Page Builder Templates', 'axisbuilder' ); ?>">
						<a href="#template" class="button-secondary"><?php _e( 'Templates', 'axisbuilder' ); ?></a>
					</div>
				</div>
			</div>
			<div id="axisbuilder-canvas" class="visual-editor">
				<div class="canvas-area axisbuilder-data layout-flex-grid axisbuilder-drop" data-dragdrop-level="0"></div>
				<div class="canvas-secure-data">
					<textarea name="axisbuilder_canvas" id="canvas-data" class="canvas-data"><?php echo esc_textarea( get_post_meta( $post->ID, '_axisbuilder_canvas', true ) ); ?></textarea> <!-- readonly="readonly" later -->
				</div>
			</div>
		</div><?php

		// Output Backbone Templates
		self::output_backbone_tmpl();
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
					self::$load_shortcode = '<a href="#' . strtolower( $shortcode['href-class'] ) . '" class="insert-shortcode help_tip ' . $shortcode['class'] . $shortcode['target'] . '" data-dragdrop-level="' . $shortcode['drag-level'] . '" data-tip="' . esc_attr( axisbuilder_sanitize_tooltip( $tooltip ) ) . '">' . $shortcode_icon . '<span>' . $title. '</span></a>';

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
	 * Show Backbone Modal Templates.
	 */
	protected static function output_backbone_tmpl() {
		$shortcode_modal_tmpl = apply_filters( 'axisbuilder_shortcode_backbone_modal_tmpl', array(
			'trash' => array(
				'title'  => __( 'Permanently Delete all Canvas Elements', 'axisbuilder' ),
				'button' => __( 'Delete', 'axisbuilder' ),
				'target' => 'trash-data',
				'class'  => array( 'modal-animation' )
			),
			'cell' => array(
				'title'  => __( 'Select a cell layout', 'axisbuilder' ),
				'button' => __( 'Add', 'axisbuilder' ),
				'target' => 'cell-size',
				'class'  => array( 'modal-animation' )
			),
			'edit' => array(
				'title'  => __( 'Edit Element', 'axisbuilder' ),
				'button' => __( 'Save', 'axisbuilder' ),
				'target' => 'edit-element',
				'class'  => array( 'modal-animation' )
			)
		) );

		foreach ( $shortcode_modal_tmpl as $key => $template ) {
			?>
			<script type="text/template" id="tmpl-axisbuilder-modal-<?php echo esc_attr( $template['target'] ); ?>">
				<div class="axisbuilder-backbone-modal">
					<div class="axisbuilder-backbone-modal-content <?php echo implode( ' ', $template['class'] ); ?>">
						<section class="axisbuilder-backbone-modal-main" role="main">
							<header class="axisbuilder-backbone-modal-header">
								<a class="modal-close modal-close-link" href="#"><span class="close-icon"><span class="screen-reader-text">Close media panel</span></span></a>
								<h1><?php echo esc_html( $template['title'] ); ?></h1>
							</header>
							<article class="axisbuilder-backbone-modal-article">
								<form action="" method="post"></form><p></p>
							</article>
							<footer>
								<div class="inner">
									<% if ( dismiss ) { %>
										<button class="button button-large modal-close"><?php _e( 'Dismiss' , 'axisbuilder' ); ?></button>
									<% } else { %>
										<button id="btn-ok" class="button button-large button-primary"><?php echo esc_html( $template['button'] ); ?></button>
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
	}

	/**
	 * Filter the postbox classes for a specific screen and screen ID combo.
	 * @param  array $classes An array of postbox classes.
	 * @return array
	 */
	public static function postbox_classes( $classes ) {
		$status_options = get_option( 'axisbuilder_status_options', array() );

		// Class for Debug Mode
		if ( ! empty( $status_options['builder_debug_mode'] ) ) {
			$classes[] = 'axisbuilder-debug';
		}

		// Class for hidden items
		if ( empty( $_GET['post'] ) || ( isset( $_GET['post'] ) && is_pagebuilder_active( $_GET['post'] ) === false ) ) {
			$classes[] = 'axisbuilder-hidden';
		}

		return $classes;
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id ) {

		// Save the builder status and canvas textarea data :)
		$builder_post_meta = array( 'axisbuilder_status', 'axisbuilder_canvas' );

		foreach ( $builder_post_meta as $post_meta ) {
			if ( isset( $_POST[ $post_meta ] ) ) {
				update_post_meta( $post_id, '_' . $post_meta, $_POST[ $post_meta ] );
			}
		}
	}
}
