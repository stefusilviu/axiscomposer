/* global axiscomposer_admin_sidebars */
jQuery( function( $ ) {

	var ac_enhanced_sidebars = {
		init: function() {
			this.create_form();
			this.add_trash_icon();

			$( '.widget-liquid-right' ).on( 'click', '.axiscomposer-delete-sidebar', this.remove_sidebar );
		},
		create_form: function() {
			$( '#widgets-right' ).prepend( $( '#tmpl-axiscomposer-form-create-sidebar' ).html() );
		},
		add_trash_icon: function() {
			$( '#widgets-right' ).find( '.sidebar-axiscomposer-custom-widgets-area' ).css( 'position', 'relative' ).append( '<div class="axiscomposer-delete-sidebar">&nbsp;</div>' );
		},
		remove_sidebar: function( e ) {
			var	widgets = $( e.currentTarget ).parents( '.widgets-holder-wrap:eq(0)' ),
				heading = widgets.find( '.sidebar-name h3' ),
				spinner = heading.find( '.spinner' ),
				sidebar	= $.trim( heading.text() );

			// AxisComposer Backbone Modal
			$( this ).ACBackboneModal({
				template: '#tmpl-axiscomposer-modal-delete-sidebar'
			});

			$( document.body ).on( 'ac_backbone_modal_response', function( e, template ) {
				if ( '#tmpl-axiscomposer-modal-delete-sidebar' !== template ) {
					return;
				}

				var	data = {
					sidebar: sidebar,
					action: 'axiscomposer_delete_custom_sidebar',
					security: axiscomposer_admin_sidebars.delete_custom_sidebar_nonce
				};

				$.ajax( {
					url: axiscomposer_admin_sidebars.ajax_url,
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
							$( '.widget-control-remove', widgets ).trigger( 'click' );

							// Remove Widgets
							widgets.slideUp( 250, function() {
								widgets.remove();
							});

							// Reload page
							window.setTimeout( function() {
								window.location.reload();
							}, 100 );
						}
					}
				});
			});
		}
	};

	ac_enhanced_sidebars.init();
});
