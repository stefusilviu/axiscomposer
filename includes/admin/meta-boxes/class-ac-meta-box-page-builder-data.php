<?php
/**
 * Page Builder Data
 *
 * Displays the page builder data meta box, tabbed, with several drag and drop canvas elements.
 *
 * @class    AC_Meta_Box_Page_Builder_Data
 * @version  1.0.0
 * @package  AxisComposer/Admin/Meta Boxes
 * @category Admin
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Meta_Box_Page_Builder_Data Class
 */
class AC_Meta_Box_Page_Builder_Data {

	/**
	 * Output the meta box
	 */
	public static function output( $post ) {
		wp_nonce_field( 'axiscomposer_save_data', 'axiscomposer_meta_nonce' );

		?>
		<input type="hidden" class="pagebuilder-status" name="pagebuilder_status" value="<?php echo esc_attr( is_pagebuilder_active( $post->ID ) ? 'active' : 'inactive' ); ?>" />
		<div id="wrap-pagebuilder" class="axiscomposer-shortcodes">
			<div id="axiscomposer-panels" class="panel-wrap pagebuilder_data">
				<ul class="pagebuilder_data_tabs ac-tabs horizontal" style="display:none">
					<?php
						$shortcode_data_tabs = apply_filters( 'axiscomposer_shortcode_data_tabs', array(
							'layout'  => array(
								'label'  => __( 'Layout Elements', 'axiscomposer' ),
								'target' => 'layout_builder_data',
								'class'  => array( 'hide_if_empty' ),
							),
							'content' => array(
								'label'  => __( 'Content Elements', 'axiscomposer' ),
								'target' => 'content_builder_data',
								'class'  => array( 'hide_if_empty' ),
							),
							'media'   => array(
								'label'  => __( 'Media Elements', 'axiscomposer' ),
								'target' => 'media_builder_data',
								'class'  => array( 'hide_if_empty' ),
							),
							'plugin'  => array(
								'label'  => __( 'Plugin Additions', 'axiscomposer' ),
								'target' => 'plugin_builder_data',
								'class'  => array( 'hide_if_empty' ),
							),
						) );

						foreach ( $shortcode_data_tabs as $key => $tab ) {
							?><li class="<?php echo $key; ?>_options <?php echo $key; ?>_tab <?php echo implode( ' ', $tab['class'] ); ?>">
								<a href="#<?php echo $tab['target']; ?>"><?php echo esc_html( $tab['label'] ); ?></a>
							</li><?php
						}

						do_action( 'axiscomposer_shortcode_write_panel_tabs' );
					?>
				</ul>

				<div id="layout_builder_data" class="panel axiscomposer-options-panel"><?php self::display_panel_shortcodes( 'layout' ); ?></div>
				<div id="content_builder_data" class="panel axiscomposer-options-panel"><?php self::display_panel_shortcodes( 'content' ); ?></div>
				<div id="media_builder_data" class="panel axiscomposer-options-panel"><?php self::display_panel_shortcodes( 'media' ); ?></div>
				<div id="plugin_builder_data" class="panel axiscomposer-options-panel"><?php self::display_panel_shortcodes( 'plugin' ); ?></div>

				<?php do_action( 'axiscomposer_shortcode_data_panels' ); ?>

				<div class="clear"></div>
			</div>
			<div id="axiscomposer-handle" class="handle-bar">
				<div class="control-bar">
					<div class="history-sections">
						<div class="history-action help_tip" data-tip="<?php esc_attr_e( 'History', 'axiscomposer' ); ?>">
							<a href="#undo" class="undo-icon undo-data" title="<?php esc_attr_e( 'Undo', 'axiscomposer' ); ?>"></a>
							<a href="#redo" class="redo-icon redo-data" title="<?php esc_attr_e( 'Redo', 'axiscomposer' ); ?>"></a>
						</div>
						<div class="delete-action help_tip" data-tip="<?php esc_attr_e( 'Permanently delete all canvas elements', 'axiscomposer' ); ?>">
							<a href="#trash" class="trash-icon trash-data"></a>
						</div>
					</div>
					<div class="template-sections help_tip" data-tip="<?php esc_attr_e( 'Save or Load the Page Builder Templates', 'axiscomposer' ); ?>">
						<a href="#template" class="button-secondary"><?php _e( 'Templates', 'axiscomposer' ); ?></a>
					</div>
				</div>
			</div>
			<div id="axiscomposer-canvas" class="visual-editor">
				<div class="canvas-area ac-data layout-flex-grid ac-drop" data-dragdrop-level="0"></div>
				<div class="canvas-secure-data">
					<textarea name="pagebuilder_canvas" id="canvas-data" class="canvas-data"><?php echo esc_textarea( get_post_meta( $post->ID, '_pagebuilder_canvas', true ) ); ?></textarea> <!-- readonly="readonly" later -->
				</div>
			</div>
		</div><?php

		// Output Backbone Templates
		self::output_backbone_tmpl();
	}

	/**
	 * Display shortcode links for an option panel.
	 * @param string $type Options panel data type.
	 */
	protected static function display_panel_shortcodes( $type = 'plugin' ) {

		foreach ( AC()->shortcodes->get_shortcodes() as $load_shortcodes ) {

			if ( empty( $load_shortcodes->shortcode['invisible'] ) ) {

				if ( $type === $load_shortcodes->shortcode['type'] ) {
					$screen = get_current_screen();

					// Fetch shortcode data
					$title     = $load_shortcodes->method_title;
					$tooltip   = $load_shortcodes->method_description;
					$shortcode = $load_shortcodes->shortcode;

					// Specific screen shortcode
					if ( isset( $screen->id ) && isset( $shortcode['specific']['screen'] ) && ! in_array( $screen->id, $shortcode['specific']['screen'] ) ) {
						$shortcode['class'] .= empty( $shortcode['class'] ) ? 'disabled-shortcode' : ' disabled-shortcode';
						$tooltip = isset( $shortcode['specific']['notice'] ) ? $shortcode['specific']['notice'] : __( 'Current screen doesn\'t support this shortcode element =/', 'axiscomposer' );
					}

					// Fallback if icon is missing =/
					$shortcode_icon = ( isset( $shortcode['image'] ) && ! empty( $shortcode['image'] ) ) ? '<img src="' . $shortcode['image'] . '" alt="' . $title . '" />' : '<i class="' . $shortcode['icon'] . '"></i>';

					// Create a button Link
					echo '<a href="#' . strtolower( $shortcode['href-class'] ) . '" class="insert-shortcode help_tip ' . esc_attr( $shortcode['class'] ) . ' ' . esc_attr( $shortcode['target'] ) . '" data-dragdrop-level="' . esc_attr( $shortcode['drag-level'] ) . '" data-tip="' . ac_sanitize_tooltip( $tooltip ) . '">' . $shortcode_icon . '<span>' . wp_kses_post( $title ) . '</span></a>';
				}
			}
		}
	}

	/**
	 * Output Backbone Modal Templates.
	 */
	protected static function output_backbone_tmpl() {
		$shortcode_modal_tmpl = apply_filters( 'axiscomposer_shortcode_backbone_modal_tmpl', array(
			'trash' => array(
				'tmpl'   => 'trash-data',
				'button' => __( 'Delete', 'axiscomposer' ),
				'class'  => array( 'modal-animation' )
			),
			'cell' => array(
				'tmpl'   => 'cell-size',
				'button' => __( 'Add', 'axiscomposer' ),
				'class'  => array( 'modal-animation' )
			),
			'edit' => array(
				'tmpl' => 'edit-element',
				'button' => __( 'Save', 'axiscomposer' ),
				'class'  => array( 'axiscomposer', 'modal-animation', 'normal-screen' )
			)
		) );

		foreach ( $shortcode_modal_tmpl as $key => $template ) {
			?>
			<script type="text/template" id="tmpl-axiscomposer-modal-<?php esc_attr_e( $template['tmpl'] ); ?>">
				<div class="ac-backbone-modal">
					<div class="ac-backbone-modal-content <?php echo implode( ' ', $template['class'] ); ?>">
						<section class="ac-backbone-modal-main" role="main">
							<header class="ac-backbone-modal-header">
								<h1><%= title %></h1>
								<button class="modal-close modal-close-link dashicons dashicons-no-alt">
									<span class="screen-reader-text">Close modal panel</span>
								</button>
							</header>
							<article class="ac-backbone-modal-article">
								<form action="" method="post" class="ac-enhanced-form">
									<% if ( message ) { %>
										<div class="message">
											<%= message %>
										</div>
									<% } else { %>
										<div class="ac-enhanced-settings ajax-connect">&nbsp;</div>
									<% } %>
								</form>
							</article>
							<footer class="ac-backbone-modal-footer">
								<div class="inner">
									<% if ( dismiss ) { %>
										<button class="button button-large button-secondary modal-close"><?php _e( 'Dismiss' , 'axiscomposer' ); ?></button>
									<% } else { %>
										<button id="btn-ok" class="button button-large button-primary"><?php esc_html_e( $template['button'] ); ?></button>
									<% } %>
								</div>
							</footer>
						</section>
					</div>
				</div>
				<div class="ac-backbone-modal-backdrop modal-close"></div>
			</script>
			<?php
		}
	}

	/**
	 * Filter the postbox classes for a specific screen and screen ID combo.
	 * @param  array $classes An array of postbox classes.
	 * @return array $classes
	 */
	public static function postbox_classes( $classes ) {
		$status_options = get_option( 'axiscomposer_status_options', array() );

		// Class for Debug Mode
		if ( ! empty( $status_options['builder_debug_mode'] ) ) {
			$classes[] = 'ac-debug';
		}

		// Class for hidden items
		if ( empty( $_GET['post'] ) || ( isset( $_GET['post'] ) && is_pagebuilder_active( $_GET['post'] ) === false ) ) {
			$classes[] = 'ac-hidden';
		}

		return $classes;
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id ) {

		// Save the page builder status and canvas textarea data :)
		$pagebuilder_post_meta = array( 'pagebuilder_status', 'pagebuilder_canvas' );

		foreach ( $pagebuilder_post_meta as $post_meta ) {
			if ( isset( $_POST[ $post_meta ] ) ) {
				update_post_meta( $post_id, '_' . $post_meta, $_POST[ $post_meta ] );
			}
		}
	}
}
