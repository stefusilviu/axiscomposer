/* global axisbuilder_admin_meta_boxes_builder, console */
( function( $ ) {
	'use strict';

	$.AxisBuilderHistory = $.AxisBuilderHistory || {};

	$.AxisBuilderHistory = function( options ) {
		var defaults = {
			steps: 40,
			button: '',
			canvas: '',
			editor: ''
		};

		// No web storage? stop here :)
		if ( typeof Storage === 'undefined' ) {
			return false;
		}

		this.doc     = $( document );
		this.options = $.extend( {}, defaults, options );

		// Setup
		this.setups();
	};

	$.AxisBuilderHistory.prototype = {

		setups: function() {
			this.button = $( this.options.button );
			this.canvas = $( this.options.canvas );
			this.editor = $( this.options.editor );

			// Create a unique array key for this post
			this.key     = this.create_array_key();
			this.storage = this.get() || [];
			this.maximum = this.storage.length - 1;
			this.index   = this.get( this.key + 'index' );

			if ( typeof this.index === 'undefined' || this.index === null ) {
				this.index = this.maximum;
			}

			// Undo-Redo Buttons
			this.undoButton = this.button.find( '.undo-data' );
			this.redoButton = this.button.find( '.redo-data' );

			// Clear storage for testing purpose
			this.clear();

			// Bind Events
			this.bindEvents();
		},

		// Creates the array key for this post history
		create_array_key: function() {
			var key = 'axisbuilder' + axisbuilder_admin_meta_boxes_builder.theme_name + axisbuilder_admin_meta_boxes_builder.theme_version + axisbuilder_admin_meta_boxes_builder.post_id + axisbuilder_admin_meta_boxes_builder.plugin_version;
			return key.replace( /[^a-zA-Z0-9]/g, '' ).toLowerCase();
		},

		bindEvents: function() {
			var obj = this;

			this.canvas.on( 'axisbuilder-storage-update', function() {
				obj.snapshot();
			});

			this.button.on( 'click', 'a.undo-data', function() {
				obj.undo();
				return false;
			});

			this.button.on( 'click', 'a.redo-data', function() {
				obj.redo();
				return false;
			});

			// Undo-Redo events on CTRL+{Z/Y} or CTRL+SHIFT+{Z/Y} keypress.
			this.doc.bind( 'keydown.AxisBuilderHistory', function( e ) {

				// Ensure event is not null
				e = e || window.event;

				// Undo Event
				if ( ( e.which === 90 ) && ( e.ctrlKey || ( e.ctrlKey && e.shiftKey ) ) ) {
					setTimeout( function() {
						obj.undo();
					}, 100 );

					e.stopImmediatePropagation();
				}

				// Redo Event
				if ( ( e.which === 89 ) && ( e.ctrlKey || ( e.ctrlKey && e.shiftKey ) ) ) {
					setTimeout( function() {
						obj.redo();
					}, 100 );

					e.stopImmediatePropagation();
				}
			});
		},

		get: function( passed_key ) {
			var key = passed_key || this.key;
			return JSON.parse( sessionStorage.getItem( key ) );
		},

		set: function( passed_key, passed_value ) {
			var key   = passed_key || this.key,
				value = passed_value || JSON.stringify( this.storage );

			try {
				sessionStorage.setItem( key, value );
			}

			catch( e ) {
				console.log( 'Storage Limit reached. Your Browser does not offer enough session storage to save more steps for the undo/redo history.', 'Storage' );
				console.log( e, 'Storage' );
				this.clear();
				this.redoButton.addClass( 'inactive-history' );
				this.undoButton.addClass( 'inactive-history' );
			}
		},

		clear: function() {
			sessionStorage.removeItem( this.key );
			sessionStorage.removeItem( this.key + 'index' );
			this.storage = [];
			this.index   = null;
		},

		undo: function() {
			if ( ( this.index - 1 ) >= 0 ) {
				this.index --;
				this.canvasUpdate( this.storage[ this.index ] );
			}

			return false;
		},

		redo: function() {
			if ( ( this.index + 1 ) <= this.maximum ) {
				this.index ++;
				this.canvasUpdate( this.storage[ this.index ] );
			}

			return false;
		},

		canvasUpdate: function( values ) {

			if ( typeof this.tinyMCE === 'undefined' ) {
				this.tinyMCE = typeof window.tinyMCE === 'undefined' ? false : window.tinyMCE.get( this.options.editor.replace( '#', '' ) );
			}

			if ( this.tinyMCE ) {
				this.tinyMCE.setContent( window.switchEditors.wpautop( values[0] ), { format: 'html' } );
			}

			this.editor.val( values[0] );
			this.canvas.html( values[1] );
			sessionStorage.setItem( this.key + 'index', this.index );

			// Control Undo inactive class
			if ( this.index <= 0 ) {
				this.undoButton.addClass( 'inactive-history' );
			} else {
				this.undoButton.removeClass( 'inactive-history' );
			}

			// Control Redo inactive class
			if ( this.index + 1 > this.maximum ) {
				this.redoButton.addClass( 'inactive-history' );
			} else {
				this.redoButton.removeClass( 'inactive-history' );
			}

			// Trigger storage event
			this.canvas.trigger( 'axisbuilder-history-update' );
		},

		snapshot: function() {

			// Update all textarea html with actual value, otherwise jquerys html() fetches the values that were present on page load
			this.canvas.find( 'textarea' ).each( function() {
				this.innerHTML = this.value;
			});

			// Set Storage, index
			this.storage = this.storage || this.get() || [];
			this.index   = this.index || this.get( this.key + 'index' );
			if ( typeof this.index === 'undefined' || this.index === null ) {
				this.index = this.storage.length - 1;
			}

			var snapshot    = [ this.editor.val(), this.canvas.html().replace( /modal-animation/g, '' ) ],
				lastStorage = this.storage[ this.index ];

			// Create a new snapshot if none exists or if the last stored snapshot doesnt match the current state
			if ( typeof lastStorage === 'undefined' || ( lastStorage[0] !== snapshot[0] ) ) {
				this.index ++;

				// Remove all steps after the current one
				this.storage = this.storage.slice( 0, this.index );

				// Add the latest step to the array
				this.storage.push( snapshot );

				// If we got more steps than defined in our options, remove the first step
				if ( this.options.steps < this.storage.length ) {
					this.storage.shift();
				}

				// Set the browser storage object
				this.set();
			}

			this.maximum = this.storage.length - 1;

			// Set Undo and Redo button if storage is on the last index
			if ( this.storage.length === 1 || this.index === 0 ) {
				this.undoButton.addClass( 'inactive-history' );
			} else {
				this.undoButton.removeClass( 'inactive-history' );
			}

			if ( this.storage.length - 1 === this.index ) {
				this.redoButton.addClass( 'inactive-history' );
			} else {
				this.redoButton.removeClass( 'inactive-history' );
			}
		}
	};

	// $( document ).ready( function () {
	// 	new $.AxisBuilderHistory({
	// 		button: '.history-action',
	// 		canvas: '.canvas-area',
	// 		editor: '.canvas-data'
	// 	});
	// });

})( jQuery );

/**
 * AxisBuilder Helper JS
 */
( function( $ ) {
	'use strict';

	$.AxisBuilderHelper = $.AxisBuilderHelper || {};
	$.AxisBuilderHelper.wp_media = $.AxisBuilderHelper.wp_media || [];

	$( document ).ready( function() {

		// Show/Hide the dependent elements.
		$.AxisBuilderHelper.check_depedencies();

		// Image Insert functionality
		$.AxisBuilderHelper.wp_media_advanced();
	});

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
					operator    = current.data( 'check-operator' ),
					final_value = current.data( 'check-value' ).toString();

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
				className: options['class'],
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
					library: media.query( file_frame[frame_key].options.library )
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
