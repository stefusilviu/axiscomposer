/* global axiscomposer_admin_sidebars */
jQuery( function( $ ) {

	/**
	 * Custom Sidebars Actions.
	 */
	var ac_custom_sidebars_actions = {
		init: function() {
			this.initial_load();
			$( '.widget-liquid-right' ).on( 'click', '.axiscomposer-delete-sidebar', this.delete_sidebar );
		},
		initial_load: function() {
			$( '#widgets-right' ).prepend( $( '#tmpl-axiscomposer-form-create-sidebar' ).html() );

			// Add trash icon for custom sidebars.
			$( '#widgets-right .sidebar-axiscomposer-custom-widgets-area' ).css({
				'position': 'relative'
			}).append( '<div class="axiscomposer-delete-sidebar">&nbsp;</div>' );
		},
		delete_sidebar: function( e ) {
			var	widgets = $( e.currentTarget ).parents( '.widgets-holder-wrap:eq(0)' ), title = widgets.find( '.sidebar-name h3' );

			// AxisComposer Backbone Modal
			$( this ).ACBackboneModal({
				template: '#tmpl-axiscomposer-modal-delete-sidebar'
			});

			$( document.body ).on( 'ac_backbone_modal_response', function( e, template ) {
				if ( '#tmpl-axiscomposer-modal-delete-sidebar' !== template ) {
					return;
				}

				var	data = {
					sidebar: $.trim( title.text() ),
					action: 'axiscomposer_delete_custom_sidebar',
					security: axiscomposer_admin_sidebars.delete_custom_sidebar_nonce
				};

				$.ajax( {
					url: axiscomposer_admin_sidebars.ajax_url,
					data: data,
					type: 'POST',
					beforeSend: function() {
						title.find( '.spinner' ).addClass( 'is-active' );
					},
					success: function( response ) {
						if ( response.success === true ) {
							$( '.widget-control-remove', widgets ).trigger( 'click' );
							widgets.slideUp( 250, function() {
								widgets.remove();
							});

							// Reload Page
							window.setTimeout( function() {
								window.location.reload();
							}, 100 );
						}
					}
				});
			});
		}
	};

	ac_custom_sidebars_actions.init();
});
