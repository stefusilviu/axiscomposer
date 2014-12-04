/* global axisbuilder_admin */

/**
 * AxisBuilder Elements Behaviour JS
 */
( function( $ ) {

	'use strict';

	$.AxisBuilderHelper = $.AxisBuilderHelper || {};

	$( document ).ready( function() {

		// Can be removed once all browser support css only tabs (:target support needed)
		$.AxisBuilderHelper.tabs( '.axisbuilder-tab-container' );

		// Allows to expand Builder Meta-Box to fullscreen proportions.
		$.AxisBuilderHelper.fullscreen();

		// Default tooltips for various elements like shortcodes.
		new $.AxisTooltip({
			scope: '#axisbuilder-editor',
			attach: 'body'
		});
	});

	// Since css only tabs are not fully working by now this script adds tab behavior to a tab container of choice
	$.AxisBuilderHelper.tabs = function( tab_container, mirror_container ) {

		$( tab_container ).each( function( i ) {
			var active_tab = 0,
				storage    = false,
				postid     = 'axisbuilder_post_' + i + '_' + axisbuilder_admin.postid;

			if ( typeof( storage ) !== 'undefined' ) {
				storage = true,
				active_tab = sessionStorage[postid] || 0;
			}

			var	current = $( this ),
				links   = current.find( '.axisbuilder-tab-title-container a' ),
				tabs    = current.find( '.axisbuilder-tab' ),
				currentLink;

			links.unbind( 'click' ).bind( 'click', function() {
				links.removeClass( 'active-tab' );
				currentLink = $( this ).addClass( 'active-tab' );

				var index = links.index( currentLink );

				tabs.css({ display: 'none' }).filter( ':eq(' + index + ')' ).css({ display:'block' });
				if ( storage ) {
					sessionStorage[postid] = index;
				}

				// mirror_container should be defined when the tab element is cloned for the fullscreen view
				if ( typeof mirror_container !== 'undefined' ) {
					mirror_container.find( '.axisbuilder-tab-title-container a' ).eq( index ).trigger( 'click' );
				}

				return false;
			});

			if ( ! links.filter( '.active-tab' ).length ) {
				links.filter( ':eq(' + active_tab + ')' ).addClass( 'active-tab' ).trigger( 'click' );
			}
		});
	};

	// Functionality to Expand Builder Meta-Box to fullscreen proportions.
	$.AxisBuilderHelper.fullscreen = function() {

		var body       = $( 'body' ),
			expand     = $( '.axisbuilder-expanded' ).find( '.axisbuilder-attach-expand' ),
			publish    = $( 'input#publish' ),
			preview    = $( 'a#post-preview' ),
			fullscreen = $( '<div class="axisbuilder-expand-fullscreen"></div>' ).appendTo( body ),
			clicked, parents, container, clone_tab, button_container;

		if ( expand.length ) {
			clicked = expand;
			parents = clicked.parents( '.postbox:eq(0)' );
			fullscreen_open();
		}

		body.on( 'click', '.axisbuilder-attach-expand', function() {

		});

		function fullscreen_open() {
			parents.addClass( 'axisbuilder-expanded' );
			body.addClass( 'axisbuilder-noscroll-box' );
			clone_tab = parents.find( '.axisbuilder-tab-container' ).clone( true );

			if ( clone_tab.length ) {

			}
		}

		function fullscreen_close() {
			parents.removeClass( 'axisbuilder-expanded' );
			body.removeClass( 'axisbuilder-noscroll-box' );

			if ( container.length ) {
				container.remove();
			}
		}

	};

})(jQuery);
