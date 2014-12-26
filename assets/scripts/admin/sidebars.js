/* global wpWidgets, axisbuilder_admin_sidebars */

/**
 * AxisBuilder Sidebars JS
 */
( function( $ ) {

	'use strict';

	$.AxisBuilderSidebars = function() {

		this.widget_area = $( '#widgets-right' );
		this.widget_wrap = $( '.widget-liquid-right' );
		this.widget_tmpl = $( '#axisbuilder-add-widget-tmpl' );

		this.createForm();
		this.deleteIcon();
		this.bindEvents();
	};

	$.AxisBuilderSidebars.prototype = {

		// Create Widget Area Form
		createForm : function() {
			this.widget_area.prepend( this.widget_tmpl.html() );
			this.nonce = this.widget_wrap.find( 'input[name="_axisbuilder_custom_sidebar_nonce"]' ).val();
		},

		// Add Delete Icon to Widget Areas
		deleteIcon : function() {
			this.widget_area.find( '.sidebar-axisbuilder-custom' ).css( 'position', 'relative' ).append( '<div class="axisbuilder-delete-sidebar"><br /></div>' );
		},

		// Widget Area Delete or Bind Events
		bindEvents : function() {
			this.widget_wrap.on( 'click', '.axisbuilder-delete-sidebar', $.proxy( this.delete_sidebar, this ) );
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
							action: axisbuilder_admin_sidebars.delete_custom_sidebar_nonce,
							name: widget_name,
							_wpnonce: obj.nonce
						};

					$.ajax( {
						url: axisbuilder_admin_sidebars.ajax_url,
						data: data,
						type: 'POST',

						beforeSend : function() {
							spinner.css( 'display', 'inline-block' );
						},

						success : function( response ) {

							if ( response === 'axisbuilder-sidebar-deleted' ) {
								widget.slideUp( 200, function() {

									// Remove all Widgets inside
									$( '.widget-control-remove', widget ).trigger( 'click' );
									widget.remove();

									// Re-calculate Widget Id's
									obj.widget_area.find( '.widgets-holder-wrap .widgets-sortables' ).each( function( i ) {
										$(this).attr( 'id', 'sidebar-' + ( i + 1 ) );
									} );

									// Re-save the Widgets Order
									wpWidgets.saveOrder();
								} );

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
