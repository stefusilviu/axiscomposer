/* global wpWidgets, axisbuilder_admin_sidebars */
( function( $ ) {
	'use strict';

	$.AxisBuilderSidebars = function() {
		this.widgetArea = $( '#widgets-right' );
		this.widgetWrap = $( '.widget-liquid-right' );
		this.widgetTmpl = $( '#tmpl-axisbuilder-form-delete-sidebar' );

		this.createForm();
		this.deleteIcon();
		this.bindEvents();
	};

	$.AxisBuilderSidebars.prototype = {

		// Create Custom Widget Area Form
		createForm: function() {
			this.widgetArea.prepend( this.widgetTmpl.html() );
		},

		// Add Delete Icon to Custom Widget Areas
		deleteIcon: function() {
			this.widgetArea.find( '.sidebar-axisbuilder-custom-widgets-area' ).css( 'position', 'relative' ).append( '<div class="axisbuilder-delete-sidebar"><br /></div>' );
		},

		// Bind Events to delete Custom Widget Area
		bindEvents: function() {
			this.widgetWrap.on( 'click', '.axisbuilder-delete-sidebar', $.proxy( this.delete_sidebar, this ) );
		},

		// Delete the Widget Area (Sidebar) with all Widgets within, then re-calculate the other sidebar ids and re-save the order
		delete_sidebar: function( e ) {
			var obj     = this,
				widgets = $( e.currentTarget ).parents( '.widgets-holder-wrap:eq(0)' ),
				heading = widgets.find( '.sidebar-name h3' ),
				spinner = heading.find( '.spinner' ),
				sidebar	= $.trim( heading.text() ),
				data    = {
					sidebar: sidebar,
					action: 'axisbuilder_delete_custom_sidebar',
					security: axisbuilder_admin_sidebars.delete_custom_sidebar_nonce
				};

			// AxisBuilder Backbone Modal
			$( this ).AxisBuilderBackboneModal({
				template: '#tmpl-axisbuilder-modal-delete-sidebar'
			});

			$( document.body ).on( 'axisbuilder_backbone_modal_response', function( e, template ) {
				if ( '#tmpl-axisbuilder-modal-delete-sidebar' !== template ) {
					return;
				}

				$.ajax( {
					url: axisbuilder_admin_sidebars.ajax_url,
					data: data,
					type: 'POST',
					beforeSend: function() {
						spinner.css({
							'visibility': 'visible',
							'display': 'inline-block'
						});
					},
					success: function( response ) {

						if ( response === true ) {
							widgets.slideUp( 200, function() {

								// Remove all Widgets inside
								$( '.widget-control-remove', widgets ).trigger( 'click' );
								widgets.remove();

								// Re-calculate Widget Id's
								obj.widgetArea.find( '.widgets-holder-wrap .widgets-sortables' ).each( function( i ) {
									$(this).attr( 'id', 'sidebar-' + ( i + 1 ) );
								});

								// Re-save the Widgets Order
								wpWidgets.saveOrder();
							});

							// Re-load the Window location
							window.setTimeout( function() {
								location.reload( false );
							}, 100 );
						}
					}
				});
			});
		}
	};

	$( function() {
		$.AxisBuilderSidebarsObj = new $.AxisBuilderSidebars();
	});

})( jQuery );
