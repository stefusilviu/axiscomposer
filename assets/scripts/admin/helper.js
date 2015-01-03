/* global axisbuilder_admin */

/**
 * AxisBuilder Elements Behaviour JS
 */
( function( $ ) {

	'use strict';

	$.AxisBuilderHelper = $.AxisBuilderHelper || {};
	$.AxisBuilderHelper.wp_media = $.AxisBuilderHelper.wp_media || [];

	$( document ).ready( function() {

		// Can be removed once all browser support css only tabs (:target support needed)
		$.AxisBuilderHelper.tabs( '.panels-tab' );

		// Allows to expand Builder Meta-Box to fullscreen proportions.
		$.AxisBuilderHelper.fullscreen();

		// Show/Hide the dependent elements.
		$.AxisBuilderHelper.check_depedencies();

		// Image Insert functionality
		$.AxisBuilderHelper.wp_media_advanced();

		// Default tooltips for various elements like shortcodes.
		new $.AxisTooltip({
			scope: '#axisbuilder-editor',
			attach: 'body'
		});

		// Control the History Undo-Redo button.
		new $.AxisBuilderHistory({
			button: '.history-action',
			canvas: '.canvas-area',
			editor: '.canvas-data'
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

			if ( ! items.filter( '.active' ).length ) {
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

	// Depedency checker for selected elements.
	$.AxisBuilderHelper.check_depedencies = function() {
		var body = $( 'body' );

		body.on( 'change', '.axisbuilder-style input[type=hidden], .axisbuilder-style input[type=text], .axisbuilder-style input[type=checkbox], .axisbuilder-style textarea, .axisbuilder-style select, .axisbuilder-style radio', function() {
			var current = $( this ),
				scope   = current.parents( '.axisbuilder-modal:eq(0)' );

			if ( ! scope.length ) {
				scope = body;
			}

			var element     = this.id.replace( /axisbuilderTB-/, '' ),
				dependent   = scope.find( '.axisbuilder-form-element-container[data-check-element="' + element + '"]' ),
				is_hidden   = current.parents( '.axisbuilder-form-element-container:eq(0)' ).is( '.axisbuilder-hidden' ),
				first_value = this.value;

			if ( current.is( 'input[type=checkbox]' ) && ! current.prop( 'checked') ) {
				first_value = '';
			}

			if ( ! dependent.length ) {
				return true;
			}

			dependent.each( function() {
				var	visible     = false,
					current     = $( this ),
					operator    = current.data( 'check-operator'),
					final_value = current.data( 'check-value');

				if ( ! is_hidden ) {
					switch( operator ) {
						case 'equals':
							visible = ( first_value === final_value ) ? true : false;
						break;

						case 'not':
							visible = ( first_value !== final_value ) ? true : false;
						break;

						case 'is_larger':
							visible = ( first_value > final_value ) ? true : false;
						break;

						case 'is_smaller':
							visible = ( first_value < final_value ) ? true : false;
						break;

						case 'contains':
							visible = ( first_value.indexOf( final_value ) !== -1 ) ? true : false;
						break;

						case 'doesnot_contain':
							visible = ( first_value.indexOf( final_value ) === -1 ) ? true : false;
						break;

						case 'is_empty_or':
							visible = ( ( first_value === '' ) || ( first_value === final_value ) ) ? true : false;
						break;

						case 'not_empty_and':
							visible = ( ( first_value !== '' ) || ( first_value !== final_value ) ) ? true : false;
						break;
					}
				}

				if ( visible === true && current.is( '.axisbuilder-hidden' ) ) {
					current.css({ display: 'none' }).removeClass( 'axisbuilder-hidden' ).find( 'select, radio, input[type=checkbox]' ).trigger( 'change' );
					current.slideDown();
				} else if ( visible === false && ! current.is( '.axisbuilder-hidden' ) ) {
					current.css({ display: 'block' }).addClass( 'axisbuilder-hidden' ).find( 'select, radio, input[type=checkbox]' ).trigger( 'change' );
					current.slideUp();
				}
			});
		});
	};

	// WordPress Media Uploader Advanced.
	$.AxisBuilderHelper.wp_media_advanced = function() {

		var file_frame = [], media = wp.media;

		// Click Event Upload Button
		$( 'body' ).on( 'click', '.axisbuilder-image-upload', function( e ) {
			e.preventDefault();

			var clicked = $( this ),
				options = clicked.data(),
				parents = clicked.parents( '.axisbuilder-form-element-container:last' ),
				targets = parents.find( '#' + options.target ),
				frame_key = _.random(0, 999999999999999999);

			// Set vars so we know that an editor is open
			$.AxisBuilderHelper.wp_media.unshift( this );

			// If the media frame alreay exist, reopen it.
			if ( file_frame[frame_key] ) {
				file_frame[frame_key].open();
				return;
			}

			// Create the media frame.
			file_frame[frame_key] = wp.media({
				frame: options.frame,
				state: options.state,
				className: options.class,
				button: {
					text: options.button
				},
				library: {
					type: 'image'
				}
			});

			// Add the single insert state
			file_frame[frame_key].states.add([
				new wp.media.controller.Library({
					id: 'axisbuilder_insert_single',
					title: clicked.data( 'title' ),
					priority: 20,
					editable: true,
					multiple: false,
					toolbar: 'select',
					filterable: 'uploaded',
					allowLocalEdits: true,
					displaySettings: true,
					displayUserSettings: false,
					library: media.query( file_frame[frame_key].options.library ),
				})
			]);

			// On modal close remove the item from the global array so that the Backbone Modal accepts keyboard events again
			file_frame[frame_key].on( 'close', function() {
				_.defer( function() {
					$.AxisBuilderHelper.wp_media.shift();
				});
			});

			// Finally open the modal
			file_frame[frame_key].open();
		});
	};

})( jQuery );
