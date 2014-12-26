/* global wpWidgets, axisbuilder_admin_sidebars */

/**
 * AxisBuilder Sidebars JS
 */
( function( $ ) {

	'use strict';

	$.AxisBuilderSidebars = function() {

		this.widgetArea = $( '#widgets-right' );
		this.widgetWrap = $( '.widget-liquid-right' );
		this.widgetTmpl = $( '#axisbuilder-sidebar-tmpl' );

		this.createForm();
		this.deleteIcon();
		this.bindEvents();
	};

	$.AxisBuilderSidebars.prototype = {

		// Create Widget Area Form
		createForm : function() {
			this.widgetArea.prepend( this.widgetTmpl.html() );
		},

		// Add Delete Icon to Widget Areas
		deleteIcon : function() {
			this.widgetArea.find( '.sidebar-axisbuilder-custom' ).css( 'position', 'relative' ).append( '<div class="axisbuilder-delete-sidebar"><br /></div>' );
		},

		// Widget Area Delete or Bind Events
		bindEvents : function() {
			this.widgetWrap.on( 'click', '.axisbuilder-delete-sidebar', $.proxy( this.delete_sidebar, this ) );
		},

		// Delete the Widget Area (Sidebar) with all Widgets within, then re-calculate the other sidebar ids and re-save the order
		delete_sidebar : function( e ) {
			var answer		= window.confirm( axisbuilder_admin_sidebars.i18n_delete_custom_sidebar ),
				widget		= $( e.currentTarget ).parents( '.widgets-holder-wrap:eq(0)' ),
				title		= widget.find( '.sidebar-name h3' ),
				spinner		= title.find( '.spinner' ),
				widget_name	= $.trim( title.text() );

			if( answer ) {

				answer = window.confirm( axisbuilder_admin_sidebars.i18n_last_warning );

				if ( answer ) {

					var obj = this,
						data = {
							sidebar: widget_name,
							action: 'axisbuilder_delete_custom_sidebar',
							security: axisbuilder_admin_sidebars.delete_custom_sidebar_nonce
						};

					$.ajax( {
						url: axisbuilder_admin_sidebars.ajax_url,
						data: data,
						type: 'POST',
						beforeSend: function() {
							spinner.css( 'display', 'inline-block' );
						},
						success: function( response ) {

							if ( response === true ) {
								widget.slideUp( 200, function() {

									// Remove all Widgets inside
									$( '.widget-control-remove', widget ).trigger( 'click' );
									widget.remove();

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
				}
			}
		}
	};

	$( function() {
		$.AxisBuilderSidebarsObj = new $.AxisBuilderSidebars();
	});

})( jQuery );
