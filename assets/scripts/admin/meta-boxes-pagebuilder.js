/* global axisbuilder_admin_meta_boxes_builder */
jQuery( function( $ ) {

	/**
	 * Page Builder Items Panel
	 */
	var axisbuilder_meta_boxes_builder_items = {
		init: function() {
			$( '#axisbuilder-editor' )
				.on( 'click', 'a.trash-data', this.trash_data );

			$( 'body' )
				.on( 'axisbuilder_backbone_modal_loaded', this.backbone.init )
				.on( 'axisbuilder_backbone_modal_response', this.backbone.response );
		},

		block: function() {
			$( '#axisbuilder-editor' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		unblock: function() {
			$( '#axisbuilder-editor' ).unblock();
		},

		trash_data: function() {
			var length = $( '.canvas-area' ).children().length;

			$( this ).AxisBuilderBackboneModal({
				title: axisbuilder_admin_meta_boxes_builder.i18n_trash_all_elements_title,
				message: ( length > 0 ) ? axisbuilder_admin_meta_boxes_builder.i18n_trash_all_elements_message : axisbuilder_admin_meta_boxes_builder.i18n_trash_all_elements_atleast,
				dismiss: ( length > 0 ) ? false : true,
				template: '#tmpl-axisbuilder-modal-trash-data'
			});

			return false;
		},

		backbone: {

			init: function( e, template ) {
				if ( '#tmpl-axisbuilder-modal-edit-element' === template ) {
					$( 'body' ).trigger( 'axisbuilder-enhanced-select-init' );
				}
			},

			response: function( e, template, data ) {
				if ( '#tmpl-axisbuilder-modal-trash-data' === template ) {
					$( '.canvas-area' ).empty();
					$( '.canvas-secure-data textarea' ).val('').empty();
				}
			}
		}
	};

	axisbuilder_meta_boxes_builder_items.init();
});
