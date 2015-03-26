/* global axisbuilder_admin_meta_boxes_builder */
jQuery( function( $ ) {

	/**
	 * Page Builder Items Panel
	 */
	var axisbuilder_meta_boxes_builder_items = {
		init: function() {
			this.stupidtable.init();

			$( '#axisbuilder-editor' )

				// Backbone Modal
				.on( 'click', 'a.trash-data', this.trash_data )
				.on( 'click', 'a.axisbuilder-edit', this.edit_element )
				.on( 'click', 'a.axisbuilder-cell-set', this.cell_size );

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

		edit_element: function() {
			var	parents = $( this ).parents( '.axisbuilder-sortable-element:eq(0)' );

			if ( ! parents.length ) {
				parents = $( this ).parents( '.axisbuilder-layout-cell:eq(0)' );

				if ( ! parents.length ) {
					parents = $( this ).parents( '.axisbuilder-layout-section:eq(0)' );
				}
			}

			$( this ).AxisBuilderBackboneModal({
				title: parents.data( 'modal-title' ),
				screen: parents.data( 'modal-class' ),
				message: 'Fetch options field with validation using AJAX...',
				template: '#tmpl-axisbuilder-modal-edit-element'
			});

			return false;
		},

		cell_size: function() {

		},

		backbone: {

			init: function( e, template ) {
				if ( '#tmpl-axisbuilder-modal-edit-element' === template ) {
					$( 'body' ).trigger( 'axisbuilder-enhanced-select-init' );
				}
			},

			response: function( e, template, data ) {
				if ( '#tmpl-axisbuilder-modal-trash-data' === template ) {
					axisbuilder_meta_boxes_builder_items.backbone.trash_data();
				}

				if ( '#tmpl-axisbuilder-modal-edit-element' === template ) {
					axisbuilder_meta_boxes_builder_items.backbone.edit_element();
				}

				if ( '#tmpl-axisbuilder-modal-cell-size' === template ) {
					axisbuilder_meta_boxes_builder_items.backbone.cell_size();
				}
			},

			trash_data: function() {
				$( '.canvas-area' ).empty();
			},

			edit_element: function() {

			},

			cell_size: function() {

			}
		},

		stupidtable: {
			init: function() {
				$( '.axisbuilder_editor' ).stupidtable().on( 'aftertablesort', this.add_arrows );
			},

			add_arrows: function( event, data ) {
				var th    = $( this ).find( 'th' );
				var arrow = data.direction === 'asc' ? '&uarr;' : '&darr;';
				var index = data.column;

				if ( 1 < index ) {
					index = index - 1;
				}

				th.find( '.axisbuilder-arrow' ).remove();
				th.eq( index ).append( '<span class="axisbuilder-arrow">' + arrow + '</span>' );
			}
		}
	};

	axisbuilder_meta_boxes_builder_items.init();
});
