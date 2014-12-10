/* global axisbuilder_admin */

/**
 * AxisBuilder Elements Behaviour JS
 */
( function( $ ) {

	'use strict';

	$.AxisBuilderHelper = $.AxisBuilderHelper || {};

	$( document ).ready( function() {

		// Can be removed once all browser support css only tabs (:target support needed)
		$.AxisBuilderHelper.tabs( '.panels-tab' );

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
				postid     = 'axisbuilder_post_' + i + '_' + axisbuilder_admin.post_id;

			if ( typeof( storage ) !== 'undefined' ) {
				storage = true,
				active_tab = sessionStorage[postid] || 0;
			}

			var	current = $( this ),
				items   = current.find( '.axisbuilder-tabs li' ),
				tabs    = current.find( '.axisbuilder-shortcodes-panel' ),
				current_item;

			items.unbind( 'click' ).bind( 'click', function() {
				items.removeClass( 'active' );
				current_item = $( this ).addClass( 'active' );

				var index = items.index( current_item );

				tabs.css({ display: 'none' }).filter( ':eq(' + index + ')' ).css({ display:'block' });
				if ( storage ) {
					sessionStorage[postid] = index;
				}

				// mirror_container should be defined when the tab element is cloned for the fullscreen view
				if ( typeof mirror_container !== 'undefined' ) {
					mirror_container.find( '.axisbuilder-tabs a' ).eq( index ).trigger( 'click' );
				}

				return false;
			});

			if ( ! items.filter( '.active-tab' ).length ) {
				items.filter( ':eq(' + active_tab + ')' ).addClass( 'active' ).trigger( 'click' );
			}
		});
	};

	// Functionality to Expand Builder Meta-Box to fullscreen proportions.
	$.AxisBuilderHelper.fullscreen = function() {

		var body             = $( 'body' ),
			publish          = $( 'input#publish' ),
			preview          = $( 'a#post-preview' ),
			fullscreen       = $( '<div class="axisbuilder-expand-fullscreen"></div>' ).appendTo( body ),
			already_expanded = $( '.axisbuilder-expanded' ).find( '.axisbuilder-attach-expand' ),
			clicked, parents, container, clone_tab, button_container;

		if ( already_expanded.length ) {
			clicked = already_expanded;
			parents = clicked.parents( '.postbox:eq(0)' );
			fullscreen_open();
		}

		body.on( 'click', '.axisbuilder-attach-expand', function( e ) {
			e.preventDefault();
			clicked = $( this );
			parents = clicked.parents( '.postbox:eq(0)' );

			if ( parents.is( '.axisbuilder-expanded' ) ) {
				fullscreen.css({ display: 'block', opacity: 0 }).animate({ opacity: 1 }, function() {

					// Close Fullscreen
					fullscreen_close();

					fullscreen.animate({ opacity: 0 }, function() {
						fullscreen.css({ display: 'none' });
					});
				});
			} else {
				fullscreen.css({ display: 'block', opacity: 0 }).animate({ opacity: 1 }, function() {

					// Open Fullscreen
					fullscreen_open();

					fullscreen.animate({ opacity: 0 }, function() {
						fullscreen.css({ display: 'none' });
					});
				});
			}

		});

		function fullscreen_open() {
			parents.addClass( 'axisbuilder-expanded' );
			body.addClass( 'axisbuilder-noscroll-box' );
			clone_tab = parents.find( '.panels-tab' ).clone( true );

			if ( clone_tab.length ) {

				// Create the cloned tab controls with buttons
				button_container = $( '<div class="axisbuilder-fullscreen-buttons"></div>' ).appendTo( clone_tab );

				preview.clone( true ).appendTo( button_container ).bind( 'click', function() {

					// Hackathon for switching to WordPress Preview Window :)
					setTimeout( function() {
						var wp_prev = window.open('', 'wp-preview', '');
						wp_prev.focus();
					}, 10 ); // This hack has "Uncaught TypeError: Cannot read property 'focus' of undefined"
				});

				publish.clone( true ).appendTo( button_container );
				clicked.clone( true ).addClass( 'button button-large button-secondary' ).appendTo( button_container );

				//create hidden input that tells wordpress which element to expand in case the save button was pressed
				$('<input type="hidden" name="axisbuilder-expanded-hidden" value="' + parents.attr('id') +'" />').appendTo(button_container);

				// Append the cloned tabs controls to the container
				container = $( '<div class="axisbuilder-fixed-controls"></div>' ).appendTo( parents );
				clone_tab.appendTo( container );

				//activate behavior
				$.AxisBuilderHelper.tabs( clone_tab, $( '.panels-tab:not(.axisbuilder-fixed-controls .panels-tab):first' ) );
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
