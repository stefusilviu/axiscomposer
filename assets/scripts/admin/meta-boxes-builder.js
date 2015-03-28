/* global axisbuilder_admin_meta_boxes_builder, axisbuilder_log, console */
function axisbuilder_log( string, type ) {
	if ( typeof console === undefined ) {
		return true;
	}

	var logger = ( typeof type !== undefined ) ? ( ( type === true ) ? string : ( '[AB_' + type.charAt(0).toUpperCase() + type.slice(1).toLowerCase() + '] - ' + string ) ) : ( '[AB_Logger] - ' + string );
	console.log( type ? logger : string );
}

// Debug Logger
if ( axisbuilder_admin_meta_boxes_builder.debug_mode === 'yes' ) {
	new axisbuilder_log( 'AxisBuilder Debug Mode is enabled', 'debug' );
}

/**
 * AxisBuilder Admin JS
 */
( function( $ ) {
	'use strict';

	$.AxisBuilder = function() {

		// WordPress default tinyMCE Editor {Wrap|Area}
		this.wpDefaultEditorWrap = $( '#postdivrich' );
		this.wpDefaultEditorArea = $( '#content.wp-editor-area' );

		// AxisBuilder Debug or Test Mode
		this.axisBuilderDebug = axisbuilder_admin_meta_boxes_builder.debug_mode || {};

		// Axis Page Builder {Button|Handle|Canvas|Values|Parent|Status}
		this.axisBuilderButton = $( '#axisbuilder-button' );
		this.axisBuilderHandle = $( '#axisbuilder-handle' ).find( '.control-bar' );
		this.axisBuilderCanvas = $( '#axisbuilder-canvas' ).find( '.canvas-area' );
		this.axisBuilderValues = $( '#axisbuilder-canvas' ).find( '.canvas-data' );
		this.axisBuilderParent = this.axisBuilderCanvas.parents( '.postbox:eq(0)' );
		this.axisBuilderStatus = this.axisBuilderParent.find( 'input[name=axisbuilder_status]' );

		// WordPress tinyMCE {Defined|Version|Content}
		this.tinyMceDefined = typeof window.tinyMCE !== 'undefined' ? true : false;
		this.tinyMceVersion = this.tinyMceDefined ? window.tinyMCE.majorVersion : false;
		this.tinyMceContent = this.tinyMceDefined ? window.tinyMCE.get( 'content' ) : false;

		// Shortcode Buttons {Object|Wrap|Data}
		this.shortcodes     = $.AxisBuilderShortcodes || {};
		this.shortcodesWrap = $( '.axisbuilder-shortcodes' );
		this.shortcodesData = 'textarea[data-name="text-shortcode"]';

		// Boolean Data {targetInsert|singleInsert|updateTimeout}
		this.targetInsert  = false;
		this.singleInsert  = false;
		this.updateTimeout = false;

		// Activate the Builder
		this.builderBehaviour();
	};

	$.AxisBuilder.prototype = {

		// All event binding goes here
		builderBehaviour: function() {
			var obj = this;

			this.axisBuilderCanvas.on( 'axisbuilder-history-update', function() {
				obj.activateDragging( this.axisBuilderParent, '' );
				obj.activateDropping( this.axisBuilderParent, '' );
			});
		},

		/**
		 * Updates the Textarea that holds the shortcode + values when located in a nested environment like columns.
		 */
		updateInnerTextarea: function( element, container ) {

			// If we don't have a container passed but an element try to detch the outer most possible container that wraps that element: A Section
			if ( typeof container === 'undefined' ) {
				container = $( element ).parents( '.axisbuilder-layout-section:eq(0)' );
			}

			// If we got no section and no container yet check if the container is a column
			if ( ! container.length ) {
				container = $( element ).parents( '.axisbuilder-layout-column:eq(0)' );
			}

			// Still no container? No need for an inner update
			if ( ! container.length ) {
				return true;
			}

			// variable declarations are hoisted to the top of the scope :)
			var i, content, main_storage, content_fields, open_tags, currentName, currentSize;

			// If we are in section iterate over all columns inside and set the value before setting the section value
			if ( container.is( '.axisbuilder-layout-section' ) ) {
				var columns = container.find( '.axisbuilder-layout-column-no-cell' );
				for ( i = 0; i < columns.length; i++ ) {
					this.updateInnerTextarea( false, $( columns[i] ) );
				}

				columns = container.find( '.axisbuilder-layout-cell' );
				for ( i = 0; i < columns.length; i++ ) {
					this.updateInnerTextarea( false, $( columns[i] ) );
				}

				content        = '';
				currentName    = container.data( 'shortcode-handler' );
				main_storage   = container.find( '>.axisbuilder-inner-shortcode >' + this.shortcodesData );
				content_fields = container.find( '>.axisbuilder-inner-shortcode > div ' + this.shortcodesData + ':not(.axisbuilder-layout-column .axisbuilder-sortable-element ' + this.shortcodesData + ', .axisbuilder-layout-cell .axisbuilder-layout-column ' + this.shortcodesData + ')' );
				open_tags      = main_storage.val().match( new RegExp( '\\[' + currentName + '.*?\\]' ) );

				for ( i = 0; i < content_fields.length; i++ ) {
					content += $( content_fields[i] ).val();
				}

				content = open_tags[0] + '\n\n' + content + '[/' + currentName + ']';
				main_storage.val( content );
			}

			if ( container.is( '.axisbuilder-layout-cell' ) ) {
				content        = '';
				currentSize    = container.data( 'width' );
				main_storage   = container.find( '>.axisbuilder-inner-shortcode >' + this.shortcodesData );
				content_fields = container.find( '>.axisbuilder-inner-shortcode > div ' + this.shortcodesData + ':not(.axisbuilder-layout-column-no-cell .axisbuilder-sortable-element ' + this.shortcodesData + ')' );
				open_tags      = main_storage.val().match( new RegExp( '\\[' + currentSize + '.*?\\]' ) );

				for ( i = 0; i < content_fields.length; i++ ) {
					content += $( content_fields[i] ).val();
				}

				content = open_tags[0] + '\n\n' + content + '[/' + currentSize + ']';
				main_storage.val( content );
			}

			if ( container.is( '.axisbuilder-layout-column:not(.axisbuilder-layout-cell)' ) ) {
				var	currentFirst   = container.is( '.axisbuilder-first-column' ) ? ' first' : '';

				content        = '';
				currentSize    = container.data( 'width' );
				content_fields = container.find( '.axisbuilder-sortable-element ' + this.shortcodesData );
				main_storage   = container.find( '>.axisbuilder-inner-shortcode >' + this.shortcodesData );

				for ( i = 0; i < content_fields.length; i++ ) {
					content += $( content_fields[i] ).val();
				}

				content = '[' + currentSize + currentFirst + ']\n\n' + content + '[/' + currentSize + ']';
				main_storage.val( content );
			}
		},

		/**
		 * Updates the Textarea that holds the shortcode + values when element is on the first level and not nested.
		 */
		updateTextarea: function( scope ) {

			// Return if builder is not in active state
			if ( this.axisBuilderStatus.val() !== 'active' ) {
				return true;
			}

			if ( ! scope ) {
				var obj = this;

				// If this was called without predefined scope iterate over all sections and calculate the columns widths in there, afterwards calculate the column outside :)
				this.axisBuilderCanvas.find( '.axisbuilder-layout-section' ).each( function() {
					var col_in_section = $( this ).find( '>.axisbuilder-inner-shortcode > div > .axisbuilder-inner-shortcode' ),
						col_in_cell    = $( this ).find( '.axisbuilder-layout-cell .axisbuilder-layout-column-no-cell > .axisbuilder-inner-shortcode' );

					if ( col_in_section.length ) {
						obj.updateTextarea( col_in_section );
					}

					if ( col_in_cell.length ) {
						obj.updateTextarea( col_in_cell );
					}
				});

				scope = $( '.axisbuilder-data > div > .axisbuilder-inner-shortcode' );
			}

			// @todo sizes is similar to axisbuilder_meta_boxes_builder_cells.cell_size xD
			var content        = '',
				sizeCount      = 0,
				content_fields = scope.find( '>' + this.shortcodesData ),
				currentField, currentContent, currentParents, currentSize,
				sizes          = {
					'ab_one_full'     : 1.00,
					'ab_four_fifth'   : 0.80,
					'ab_three_fourth' : 0.75,
					'ab_two_third'    : 0.66,
					'ab_three_fifth'  : 0.60,
					'ab_one_half'     : 0.50,
					'ab_two_fifth'    : 0.40,
					'ab_one_third'    : 0.33,
					'ab_one_fourth'   : 0.25,
					'ab_one_fifth'    : 0.20
				};

			for ( var i = 0; i < content_fields.length; i++ ) {
				currentField   = $( content_fields[i] );
				currentContent = currentField.val();
				currentParents = currentField.parents( '.axisbuilder-layout-column-no-cell:eq(0)' );

				// If we are checking a column we need to make sure to add/remove the first class :)
				if ( currentParents.length ) {
					currentSize = currentParents.data( 'width' );
					sizeCount  += sizes[currentSize];

					if ( sizeCount > 1 || i === 0 ) {

						if ( ! currentParents.is( '.axisbuilder-first-column' ) ) {
							currentParents.addClass( 'axisbuilder-first-column' );
							currentContent = currentContent.replace( new RegExp( '^\\[' + currentSize ), '[' + currentSize + ' first' );
							currentField.val( currentContent );
						}

						sizeCount = sizes[currentSize];
					} else if ( currentParents.is( '.axisbuilder-first-column' ) ) {
						currentParents.removeClass( 'axisbuilder-first-column' );
						currentContent = currentContent.replace( ' first', '' );
						currentField.val( currentContent );
					}
				} else {
					sizeCount = 1;
				}

				content += currentContent;
			}

			var tinyMceEditor = this.tinyMceDefined ? window.tinyMCE.get( 'content' ) : undefined;

			if ( tinyMceEditor !== 'undefined' ) {
				clearTimeout( this.updateTimeout );

				this.updateTimeout = setTimeout( function() {
					// Slow the whole process considerably :)
					tinyMceEditor.setContent( window.switchEditors.wpautop( content ), { format: 'html' } );
				}, 500 );
			}

			this.wpDefaultEditorArea.val( content );
			this.axisBuilderValues.val( content );
		},

		/**
		 * Create a snapshot for the Undo-Redo function.
		 * Timeout is added so javascript has enough time to remove animation classes and hover states.
		 */
		historySnapshot: function( timeout ) {
			var self = this;

			if ( ! timeout ) {
				timeout = 150;
			}

			setTimeout( function() {
				self.axisBuilderCanvas.trigger( 'axisbuilder-storage-update' );
			}, timeout );
		},

		// --------------------------------------------
		// Main Interface drag and drop Implementation
		// --------------------------------------------

		// Version Compare helper function for drag and drop fix below
		compareVersion: function( a, b ) {
			var i, compare, length, regex = /(\.0)+[^\.]*$/;

			a      = ( a + '' ).replace( regex, '' ).split( '.' );
			b      = ( b + '' ).replace( regex, '' ).split( '.' );
			length = Math.min( a.length, b.length );

			for( i = 0; i < length; i++ ) {
				compare = parseInt( a[i], 10 ) - parseInt( b[i], 10 );

				if( compare !== 0 ) {
					return compare;
				}
			}

			return ( a.length - b.length );
		},

		// Activate dragging for the given DOM element.
		activateDragging: function( passed_scope, exclude ) {

			var windows    = $( window ),
				fix_active = ( this.compareVersion( $.ui.draggable.version, '1.10.9' ) <= 0 ) ? true : false;

			if ( ( navigator.userAgent.indexOf( 'Safari' ) !== -1 ) || ( navigator.userAgent.indexOf( 'Chrome' ) !== -1 ) ) {
				fix_active = false;
			}

			if ( fix_active ) {
				console.log( 'Drag and drop Positioning fix active' );
			}

			// Drag
			var obj    = this,
				scope  = passed_scope || this.axisBuilderParent,
				params = {
					appendTo : 'body',
					handle   : '>.menu-item-handle',
					helper   : 'clone',
					zIndex   : 20000,
					scroll   : true,
					revert	 : false,
					cursorAt : {
						left : 20
					},

					start: function( event ) {
						var current = $( event.target );

						// Reduce elements opacity so user got a visual feedback on what (s)he is editing
						current.css({ opacity: 0.4 });

						// Remove all previous hover elements
						$( '.axisbuilder-hover-active' ).removeClass( 'axisbuilder-hover-active' );

						// Add a class to the container element that highlights all possible drop targets
						obj.axisBuilderCanvas.addClass( 'axisbuilder-select-target-' + current.data( 'dragdrop-level' ) );
					},

					drag: function( event, ui ) {
						if ( fix_active ) {
							ui.position.top -= parseInt( windows.scrollTop(), 10 );
						}
					},

					stop: function( event ) {

						// Return opacity of element to normal
						$( event.target ).css({ opacity: 1 });

						// Remove hover class from all elements
						$( '.axisbuilder-hover-active' ).removeClass( 'axisbuilder-hover-active' );

						/**
						 * Reset highlight on container class
						 *
						 * Currently have setting for 4 nested level of element.
						 * If you have more levels, just add styles like the other 'axisbuilder-select-target'
						 */
						obj.axisBuilderCanvas.removeClass( 'axisbuilder-select-target-1 axisbuilder-select-target-2 axisbuilder-select-target-3 axisbuilder-select-target-4' );
					}
				};

			// If exclude is undefined
			if ( typeof exclude === 'undefined') {
				exclude = ':not(.ui-draggable)';
			}

			// Let's Bail Draggeble UI
			scope.find( '.axisbuilder-drag' + exclude ).draggable( params );

			params.cursorAt = { left: 33, top: 33 };
			params.handle   = false;
			scope.find( '.insert-shortcode' ).not( '.ui-draggable' ).draggable( params );
		},

		// Activate dropping for the given DOM element.
		activateDropping: function( passed_scope, exclude ) {

			// Drop
			var obj    = this,
				scope  = passed_scope || this.axisBuilderParent,
				params = {
					greedy: true,
					tolerance: 'pointer',

					// If there's a draggable element and it's over the current element, this function will be executed.
					over: function( event, ui ) {
						var droppable = $( this );

						// Check if the current element can accept the droppable element
						if ( obj.isDropingAllowed( ui.helper, droppable ) ) {
							 // Add active class that will highlight the element with gree, i.e drop is allowed.
							droppable.addClass( 'axisbuilder-hover-active' );
						}
					},

					// If there's a draggable element and it was over the current element, when it moves out this function will be executed.
					out: function() {
						$( this ).removeClass( 'axisbuilder-hover-active' );
					},

					drop: function( event, ui ) {
						var droppable = $( this );

						if ( ! droppable.is( '.axisbuilder-hover-active' ) ) {
							return false;
						}

						var elements = droppable.find( '>.axisbuilder-drag' ), template = {}, offset = {}, method = 'after', toEl = false, position_array = [], last_pos, max_height, i;

						// new axisbuilder_log( 'dragging:' + ui.draggable.find( 'h2' ).text() + ' to position: ' + ui.offset.top + '/' + ui.offset.left );

						// Iterate over all elements and check their positions
						for ( i = 0; i < elements.length; i++ ) {
							var current = elements.eq(i);
							offset  = current.offset();

							if ( offset.top < ui.offset.top ) {
								toEl = current;
								last_pos = offset;

								// Save all items before the draggable to a position array so we can check if the right positioning is important :)
								if ( ! position_array[ 'top_' + offset.top ] ) {
									max_height = 0;
									position_array[ 'top_' + offset.top ] = [];
								}

								var height = ( current.outerHeight() + offset.top );
								max_height = max_height > height ? max_height : height;

								position_array[ 'top_' + offset.top ].push({
									index: i,
									top: offset.top,
									left: offset.left,
									height: current.outerHeight(),
									maxheight: current.outerHeight() + offset.top
								});

								// new axisbuilder_log( current.find( 'h2' ).text() + ' element offset: ' + offset.top + '/' + offset.left );
							} else {
								break;
							}
						}

						// If we got multiple matches that all got the same top position we also need to check for the left position.
						if ( last_pos && position_array[ 'top_' + last_pos.top ].length > 1 && ( max_height - 40 ) > ui.offset.top ) {
							var real_element = false;

							// new axisbuilder_log( 'Checking right Positions' );

							for ( i = 0; i < position_array[ 'top_' + last_pos.top ].length; i++ ) {

								// console.log( position_array[ 'top_' + last_pos.top ][i] );

								if ( position_array[ 'top_' + last_pos.top ][i].left < ui.offset.left ) {
									real_element = position_array[ 'top_' + last_pos.top ][i].index;
								} else {
									break;
								}
							}

							if ( real_element === false ) {
								// new axisbuilder_log( 'No right Position Element found, using first element' );
								real_element = position_array[ 'top_' + last_pos.top ][0].index;
								method = 'before';
							}

							toEl = elements.eq( real_element );
						}

						// If we got an index get that element from the list, else delete the toEL var because we need to append the draggable to the start and the next check will do that for us ;)
						if ( toEl === false ) {
							// new axisbuilder_log( 'No Element Found' );
							toEl = droppable;
							method = 'prepend';
						}

						// new axisbuilder_log( ui.draggable.find( 'h2' ).text() + ' dragable top: ' + ui.offset.top + '/' + ui.offset.left );

						// If the draggable and the new el are the same do nothing
						if ( toEl[0] === ui.draggable[0] ) {
							// new axisbuilder_log( 'Same Element Selected: stoping script' );
							return true;
						}

						// If we got a hash on the draggable we are not dragging element but a new one via shortcode button so we need to fetch an empty shortcode template ;)
						if ( ui.draggable[0].hash ) {
							var shortcode = ui.draggable.get(0).hash.replace( '#', '' );
							template  = $( $( '#axisbuilder-tmpl-' + shortcode ).html() );

							ui.draggable = template;
						}

						// Before finally moving the element, save the former parent of the draggable to a var so we can check later if we need to update the parent as well
						var formerParent = ui.draggable.parents( '.axisbuilder-drag:last' );

						// Move the real draggable element to the new position
						toEl[ method ]( ui.draggable );

						// new axisbuilder_log( 'Appended to: ' + toEl.find( 'h2' ).text() );

						// If the element got a former parent we need to update that as well
						if ( formerParent.length ) {
							obj.updateInnerTextarea( false, formerParent );
						}

						// Get the element that the new element was inserted into. This has to be the parent of the current toEL since we usually insert the new element outside of the toEL with the 'after' method
						// If method !== 'after' the element was inserted with prepend directly to the toEL and toEL should therefore also the insertedInto element :)
						var insertedInto = method === 'after' ? toEl.parents( '.axisbuilder-drop' ) : toEl;

						if ( insertedInto.data( 'dragdrop-level' ) !== 0 ) {
							// new axisbuilder_log( 'Inner update necessary. Level: ' + insertedInto.data( 'dragdrop-level' ) );
							obj.updateTextarea(); // <-- actually only necessary because of column first class. optimize that so we can remove the costly function of updating all elements :)
							obj.updateInnerTextarea( ui.draggable );
						}

						// Everything is fine, now do the re sorting and textarea updating
						obj.updateTextarea();

						// Apply dragging and dropping in case we got a new element
						if ( typeof template !== 'undefined' ) {
							obj.axisBuilderCanvas.removeClass( 'ui-droppable' ).droppable( 'destroy' );
							obj.activateDragging();
							obj.activateDropping();
						}

						obj.historySnapshot();
						// new axisbuilder_log( '_______________' );
					}
				};

			// If exclude is undefined
			if ( typeof exclude === 'undefined') {
				exclude = ':not(.ui-droppable)';
			}

			// If exclude is set to destroy remove all droppables and then re-apply
			if ( exclude === 'destroy' ) {
				scope.find( '.axisbuilder-drop' ).droppable( 'destroy' );
				exclude = '';
			}

			// Let's Bail Droppable UI
			scope.find( '.axisbuilder-drop' + exclude ).droppable( params );
		},

		/**
		 * Check if the droppable element can accept the draggable element based on attribute "dragdrop-level"
		 * @returns {Boolean}
		 */
		isDropingAllowed: function( draggable, droppable ) {
			if ( draggable.data( 'dragdrop-level' ) > droppable.data( 'dragdrop-level' ) ) {
				return true;
			}

			return false;
		}
	};

})( jQuery );

/**
 * AxisBuilder History JS
 */
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

	$( document ).ready( function () {
		$.AxisBuilderObj = new $.AxisBuilder();

		// Control the History Undo-Redo button.
		new $.AxisBuilderHistory({
			button: '.history-action',
			canvas: '.canvas-area',
			editor: '.canvas-data'
		});
	});

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
