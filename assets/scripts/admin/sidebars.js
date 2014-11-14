/*!
 * jQuery AxisThemes Sidebar Core v1.0
 * Core Functionalities for Axis Sidebars
 *
 * Copyright 2013, AxisThemes
 * Freely distributable under the MIT license.
 */

/* global wpWidgets, axisbuilder_admin_sidebars */

( function( $ ) {
	var AT_Sidebars = function() {

		this.widget_wrap = $('.widget-liquid-right');
		this.widget_area = $('#widgets-right');
		this.widget_add  = $('#axis-add-widget-template');

		this.create_form();
		this.delete_icon();
		this.bind_events();
	};

	AT_Sidebars.prototype = {

		// Create Widget Area Form
		create_form : function() {
			this.widget_area.prepend( this.widget_add.html() );
			this.nonce = this.widget_wrap.find( 'input[name="_axis_nonce_custom_sidebar"]' ).val();
		},

		// Add Delete Icon to Widget Areas
		delete_icon : function() {
			this.widget_area.find( '.sidebar-axis-custom' ).css( 'position', 'relative' ).append( '<div class="axis-delete-sidebar"><br /></div>' );
		},

		// Widget Area Delete or Bind Events
		bind_events : function() {
			this.widget_wrap.on( 'click', '.axis-delete-sidebar', $.proxy( this.delete_sidebar, this ) );
		},

		// Delete the Widget Area (Sidebar) with all Widgets within, then re-calculate the other sidebar ids and re-save the order
		delete_sidebar : function( e ) {
			var answer		= window.confirm( axisbuilder_admin_sidebars.remove_sidebar_notice ),
				widget		= $( e.currentTarget ).parents( '.widgets-holder-wrap:eq(0)' ),
				title		= widget.find( '.sidebar-name h3' ),
				spinner		= title.find( '.spinner' ),
				widget_name	= $.trim( title.text() ),
				obj			= this;

			if( answer ) {
				$.ajax( {
					type: 'POST',
					url: window.ajaxurl,
					data: {
						action: axisbuilder_admin_sidebars.delete_sidebar_nonce,
						name: widget_name,
						_wpnonce: obj.nonce
					},

					beforeSend : function() {
						spinner.css( 'display', 'inline-block' );
					},

					success : function( response ) {

						if ( response === 'axisbuilder-sidebar-deleted' ) {
							widget.slideUp( 200, function() {

								// Remove all Widgets inside
								$( '.widget-control-remove', widget ).trigger( 'click' );
								widget.remove();

								// Re-calculate Widget Ids
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
				} );
			}
		}
	};

	$( function() {
		new AT_Sidebars();
	} );
} )( jQuery );
