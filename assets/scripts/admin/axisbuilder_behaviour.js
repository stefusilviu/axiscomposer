/**
 * AxisBuilder Elements Behaviour JS
 */
( function( $ ) {

	'use strict';

	$.AxisBuilderBehavior = $.AxisBuilderBehavior || {};

	$( document ).ready( function() {

		$.AxisBuilderBehavior.tabs( '.axisbuilder-tab-container' );

	});

	$.AxisBuilderBehavior.tabs = function( tab_container, mirror_container ) {

		$( tab_container ).each( function( i ) {
			var active_tab = 0,
				current = $( this ),
				links   = current.find( '.axisbuilder-tab-title-container a' ),
				tabs    = current.find( '.axisbuilder-tab' ),
				currentLink;

			links.unbind( 'click' ).bind( 'click', function() {
				links.removeClass('active-tab');
				currentLink = $(this).addClass('active-tab');

				var index = links.index( currentLink );
				tabs.css({display:'none'}).filter(':eq(' + index + ')').css({display:'block'});

				// mirror_container should be defined when the tab element is cloned for the fullscreen view
				if ( typeof mirror_container !== 'undefined' ) {
					mirror_container.find( '.axisbuilder-tab-title-container-container a' ).eq( index ).trigger( 'click' );
				}

				return false;
			});

			if ( ! links.filter( '.active-tab' ).length ) {
				links.filter( ':eq(' + active_tab + ')' ).addClass( 'active-tab' ).trigger( 'click' );
			}
		});
	}

})(jQuery);
