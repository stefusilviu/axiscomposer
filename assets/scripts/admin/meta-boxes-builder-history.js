/* global axisbuilder_admin_meta_boxes_builder, axisbuilder_log */
( function( $ ) {
	'use strict';

	$.AxisBuilderHistory = $.AxisBuilderHistory || {};

	$.AxisBuilderHistory = function( options ) {
		var defaults = {
			steps: 40,
			button: '',
			canvas: '',
			editor: '',
			event: 'axisbuilder-storage-update'
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
				new axisbuilder_log( 'Storage Limit reached. Your Browser does not offer enough session storage to save more steps for the undo/redo history.', 'Storage' );
				new axisbuilder_log( e, 'Storage' );
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

})( jQuery );
