/* global console, axisbuilder_admin, pagenow */

// global AxisBuilder Logger Helper
function AB_Logger( text, type ) {

	if ( typeof console === 'undefined' ) {
		return true;
	}

	if ( typeof type === 'undefined' ) {
		type = 'logger';
	}

	if ( type === false ) {
		console.log( text );
	} else {
		type = 'AB_' + type.toUpperCase();
		console.log( '[' + type + '] - ' + text );
	}
}

/**
 * AxisBuilder Admin JS
 */
( function( $ ) {

	'use strict';

	$.AxisBuilder = function() {

		// WordPress default tinyMCE Editor {Wrap|Area}
		this.wpDefaultEditorWrap = $( '#postdivrich' );
		this.wpDefaultEditorArea = $('#content.wp-editor-area');

		// AxisBuilder Debug or Test Mode
		this.axisBuilderDebug = axisbuilder_admin.debug_mode || {};

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
		this.builderActivate();
	};

	$.AxisBuilder.prototype = {

		// Activate the Whole Interface
		builderActivate: function() {
			this.builderPosition();
			this.shortcodesToInterface();
			this.builderBehaviour();
		},

		// Always make Builder available at the first position
		builderPosition: function() {
			var meta_box = $( '#normal-sortables' ),
				post_box = meta_box.find( '.postbox' );

			if ( this.axisBuilderParent.length && ( post_box.index( this.axisBuilderParent ) !== 0 ) ) {
				this.axisBuilderParent.prependTo( meta_box );

				// Re-save the postbox Order
				window.postboxes.save_order( pagenow );
			}
		},

		// All event binding goes here
		builderBehaviour: function() {
			var obj  = this,
				body = $( 'body' );

			// Toggle between default editor and page builder
			this.axisBuilderButton.on( 'click', function( e ) {
				e.preventDefault();
				obj.switchEditors();
			});

			// Add a new element to the Builder Canvas
			this.shortcodesWrap.on( 'click', '.insert-shortcode', function() {
				// var parents = $( this ).parents( '.axisbuilder-shortcodes' ),
					// already_active = ( this.className.indexOf( 'axisbuilder-active-insert' ) !== -1 ) ? true : false;

				var	execute = this.hash.replace( '#', '' ),
					targets = 'instant-insert'; // ( this.className.indexOf( 'axisbuilder-target-insert' ) !== -1 ) ? "target_insert" : "instant_insert",

				obj.shortcodes.fetchShortcodeEditorElement( execute, targets, obj );

				return false;
			});

			// Trash all element(s) from the Builder Canvas
			this.axisBuilderHandle.on( 'click', 'a.trash-data', function( e ) {
				var length = obj.axisBuilderCanvas.children().length;

				if ( length > 0 ) {
					var answer = window.confirm( axisbuilder_admin.i18n_delete_all_canvas_elements );

					if ( answer ) {
						answer = window.confirm( axisbuilder_admin.i18n_last_warning );

						if ( answer ) {
							// Empty the canvas & Update textarea with empty value :)
							obj.axisBuilderCanvas.empty();
							obj.updateTextarea();
						}
					}
				}

				e.preventDefault();
				return false;
			});

			// Clone element from the Builder Canvas
			this.axisBuilderCanvas.on( 'click', 'a.axisbuilder-clone', function() {
				obj.shortcodes.cloneElement( this, obj );
				return false;
			});

			// Remove element from the Builder Canvas
			this.axisBuilderCanvas.on( 'click', 'a.axisbuilder-trash', function() {
				obj.shortcodes.trashElement( this, obj );
				return false;
			});

			// Resize the layout element of the Builder Canvas
			this.axisBuilderCanvas.on( 'click', 'a.axisbuilder-change-column-size:not(.axisbuilder-change-cell-size)', function() {
				obj.shortcodes.resizeLayout( this, obj );
				return false;
			});

			// Set cell size for layout/grid row element of the Builder Canvas
			this.axisBuilderCanvas.on( 'click', 'a.axisbuilder-cell-set', function() {
				obj.shortcodes.setCellSize( this, obj );
				return false;
			});

			// Add new cell for layout/grid row element of the Builder Canvas
			this.axisBuilderCanvas.on( 'click', 'a.axisbuilder-cell-add', function() {
				obj.shortcodes.addNewCell( this, obj );
				return false;
			});

			// Re-calculate shortcode when select elements change occured on the Builder Canvas
			this.axisBuilderCanvas.on( 'change', 'select.axisbuilder-recalculate-shortcode', function() {
				var	container = $( this ).parents( '.axisbuilder-sortable-element:eq(0)' );
				obj.recalculateShortcode( container );
				return false;
			});

			// Reactivate sorting and dropping after Undo-Redo changes
			this.axisBuilderCanvas.on( 'axisbuilder-history-update', function() {
				obj.activateDragging( this.axisBuilderParent, '' );
				obj.activateDropping( this.axisBuilderParent, '' );
			});

			// Edit item via Modal Window
			body.on( 'click', '.axisbuilder-edit', function() {
				var	parents = $( this ).parents( '.axisbuilder-sortable-element:eq(0)' );

				if ( ! parents.length ) {
					parents = $( this ).parents( '.axisbuilder-layout-cell:eq(0)' );

					if ( ! parents.length ) {
						parents = $( this ).parents( '.axisbuilder-layout-section:eq(0)' );
					}
				}

				var params  = parents.data(), modal;

				params.scope        = obj;
				params.modal_title  = parents.data( 'modal-title' );
				params.modal_action = parents.data( 'modal-action' );

				params.on_load     = parents.data( 'modal_on_load' );
				params.before_save = parents.data( 'before_save' );
				params.on_save     = obj.updateShortcode;
				params.save_param  = parents;
				params.ajax_param  = {
					extract: true,
					shortcode: parents.find( '> .axisbuilder-inner-shortcode > ' + obj.shortcodesData + ':eq(0)' ).val(),
					allowed: params.allowedShortcodes
				};

				modal = new $.AxisBuilderModal( params );
				return false;
			});
		},

		// Switch between the {WordPress|AxisBuilder} Editors
		switchEditors: function() {
			var self = this;

			if ( this.axisBuilderStatus.val() !== 'active' ) {
				this.wpDefaultEditorWrap.parent().addClass( 'axisbuilder-hidden-editor' );
				this.axisBuilderButton.removeClass( 'button-primary' ).addClass( 'button-secondary' ).text( this.axisBuilderButton.data( 'default-editor' ) );
				this.axisBuilderParent.removeClass( 'axisbuilder-hidden');
				this.axisBuilderStatus.val( 'active' );

				// Load Shortcodes to Interface :)
				setTimeout( function() {
					self.shortcodesToInterface();
				}, 10 );
			} else {
				this.wpDefaultEditorWrap.parent().removeClass( 'axisbuilder-hidden-editor' );
				this.axisBuilderButton.addClass( 'button-primary' ).removeClass( 'button-secondary' ).text( this.axisBuilderButton.data( 'page-builder' ) );
				this.axisBuilderParent.addClass( 'axisbuilder-hidden');
				this.axisBuilderStatus.val( 'inactive' );

				// Add Loader and remove duplication of elements on canvas :)
				this.axisBuilderCanvas.addClass( 'loader' ).find( '>*:not( .control-bar, .axisbuilder-insert-area )' ).remove();

				// Reset WordPress editorExpand ;)
				if( typeof window.editorExpand === 'object' ) {
					window.editorExpand.off();
					window.editorExpand.on();
				}

				// Debug Logger
				if ( this.axisBuilderDebug !== 'disabled' && ( this.axisBuilderValues.val().indexOf( '[' ) !== -1 ) ) {
					new AB_Logger( 'Switching to Classic Editor. Page Builder is in Debug Mode and will empty the textarea so user can\'t edit shortcode directly', 'Editor' );
					if ( this.tinyMceContent ) {
						this.tinyMceContent.setContent( '', { format: 'html' } );
						this.wpDefaultEditorArea.val( '' );
					}
				}
			}

			return false;
		},

		/**
		 * Converts shortcodes to an editable element on Builder Canvas.
		 * Only executed at page load or when editor is switched from default to Page Builder.
		 */
		shortcodesToInterface: function( text ) {

			// Return if builder is not in active state
			if ( this.axisBuilderStatus.val() !== 'active' ) {
				return true;
			}

			// If text is undefined. Also Test-Drive val() to html()
			if ( typeof text === 'undefined' ) {
				text = this.axisBuilderValues.val();
				if ( text.indexOf( '[' ) === -1 ) {
					text = this.wpDefaultEditorArea.val();

					if ( this.tinyMceDefined ) {
						text = window.switchEditors._wp_Nop( text );
					}

					this.axisBuilderValues.val( text );
				}
			}

			var obj  = this,
				data = {
					text: text,
					action: 'axisbuilder_shortcodes_to_interface'
				};

			$.ajax({
				url: axisbuilder_admin.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					obj.sendToBuilderCanvas( response );
					// obj.updateTextarea(); // Don't update textarea on load, only when elements got edited.
					obj.axisBuilderCanvas.removeClass( 'loader' );
					obj.historySnapshot();
				}
			});
		},

		/**
		 * Send element(s) to AxisBuilder Canvas
		 * Gets executed on page load to display all elements and when a single item is fetchec via AJAX or HTML5 Storage.
		 */
		sendToBuilderCanvas: function( text ) {
			var add_text = $( text );
			this.axisBuilderCanvas.append( add_text );

			// Activate Element Drag-Drop
			this.activateDragging();
			this.activateDropping();
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
		 * Updates the Shortcode when changes occured via Modal window.
		 */
		updateShortcode: function( values, element_container ) {
			var column    = element_container.parents( '.axisbuilder-layout-column:eq(0)' ),
				section   = element_container.parents( '.axisbuilder-layout-section:eq(0)' ),
				selector  = element_container.is( '.axisbuilder-modal-group-element' ) ? ( this.shortcodesData + ':eq(0)' ) : ( '> .axisbuilder-inner-shortcode >' + this.shortcodesData + ':eq(0)' ),
				save_data = element_container.find( selector ),
				shortcode = element_container.data( 'shortcode-handler' ), output = '', tags = {};

			// Debug Logger
			if ( this.axisBuilderDebug !== 'disabled' ) {
				new AB_Logger( values, false );
			}

			// If we got a string value passed insert the string, otherwise calculate the shortcode ;)
			if ( typeof values === 'string' ) {
				output = values;
			} else {
				var extract_html = this.updateHTML( element_container, values );

				output = extract_html.output;
				tags   = extract_html.tags;
			}

			// If we are working inside a section or cell just update the shortcode open tag else update everything ;)
			if ( element_container.is( '.axisbuilder-layout-section' ) || element_container.is( '.axisbuilder-layout-cell' ) ) {
				save_data.val( save_data.val().replace( new RegExp( '^\\[' + shortcode + '.*?\\]' ), tags.open ) );
			} else {
				save_data.val( output );
			}

			// Update the Section and column Inner-Textarea
			if ( section.length ) {
				this.updateInnerTextarea( false, section );
			} else if ( column.length ) {
				this.updateInnerTextarea( false, column );
			}

			this.updateTextarea();
			this.historySnapshot();
			element_container.trigger( 'update' );
		},

		/**
		 * Updates Builder Canvas element(s) to reflect changes instantly.
		 */
		updateHTML: function( element_container, values, force_content_close ) {
			var key, subkey, new_key, old_val;

			// Filter keys for the 'axisbuilderTB-' string prefix and re-modify the key that was edited.
			for ( key in values ) {
				if ( values.hasOwnProperty( key ) ) {
					new_key = key.replace( /axisbuilderTB-/g, '' );
					if ( key !== new_key ) {
						old_val = ( typeof values[new_key] !== 'undefined' ) ? ( values[new_key] + ',' ) : '';
						values[new_key] = old_val ? old_val + values[key] : values[key];
						delete values[key];
					}
				}
			}

			// Replace all single quotes with real single quotes so we don't break the shortcode. Not necessary in the content.
			for ( key in values ) {
				if ( values.hasOwnProperty( key ) ) {
					if ( key !== 'content' ) {
						if ( typeof values[key] === 'string' ) {
							values[key] = values[key].replace( /'(.+?)'/g, '‘$1’' ).replace( /'/g, '’' );
						} else if ( typeof values[key] === 'object' ) {
							for ( subkey in values[key] ) {
								values[key][subkey] = values[key][subkey].replace( /'(.+?)'/g, '‘$1’' ).replace( /'/g, '’' );
							}
						}
					}
				}
			}

			var shortcode = element_container.data( 'shortcode-handler' ),
				visual_updates = element_container.find( '[data-update_with]' ),
				class_updates  = element_container.find( '[data-update_class_with]' ),
				visual_el = '', visual_key = '', visual_template = '', class_el = '', class_key = '';

			// var update_html = '', replace_val = ''; Need later for visual_updates ;)

			if ( ! element_container.is( '.axisbuilder-no-visual-updates') ) {
				// Reset class name's
				class_updates.attr( 'class', '' );

				// Update elements on the Builder Canvas like text elements to reflect those changes instantly.
				visual_updates.each( function() {
					visual_el = $( this );
					visual_key = visual_el.data( 'update_with' );
					visual_template = visual_el.data( 'update_template' );

					// Will do later when we need actually ;)
				});

				// Update element's classname on Builder Canvas to reflect visual chanages instantly.
				class_updates.each( function() {
					class_el = $( this );
					class_key = class_el.data( 'update_class_with' ).split( ',' );

					for ( var i = 0; i < class_key.length; i++ ) {
						if ( typeof values[class_key[i]] === 'string' ) {
							class_el.get(0).className += ' axisbuilder-' + class_key[i] + '-' + values[class_key[i]];
						}
					}
				});
			}

			// Create the shortcode string out of the arguments and save it to the data storage textarea.
			var tags = {}, extract_html = {};
			extract_html.tags = tags;
			extract_html.output = this.createShortcode( values, shortcode, tags, force_content_close );

			return extract_html;
		},

		/**
		 * Create the actual shortcode string out of the arguments and content.
		 */
		createShortcode: function( values, shortcode, tag, force_content_close ) {
			var i, key, output = '', attributes = '', content = '', seperator = ',', linebreak = '\n';
			if ( ! tag ) {
				tag = {};
			}

			// Create shortcode content var
			if ( typeof values.content !== 'undefined' ) {

				// Check if the content var is an array of items
				if ( typeof values.content === 'object' ) {

					// If its an array, Check if its an array of sub-shortcodes i.e contact form fields, if so switch the seperator to linebreak ;)
					if ( values.content[0].indexOf( '[' ) !== -1 ) {
						seperator = linebreak;
					}

					// Trim spaces and line breaks from an array :)
					for ( i = 0; i < values.content.length; i++ ) {
						values.content[i] = $.trim( values.content[i] );
					}

					// --> Can we move to this type of condititon.

					// Trim spaces and line breaks from an array :)
					// for ( i = values.content.length - 1; i >= 0; i--) {
					// 	values.content[i] = $.trim( values.content[i] );
					// }

					// Join the array into a single string xD
					content = values.content.join( seperator );
				} else {
					content = values.content;
				}

				content = linebreak + content + linebreak;
				delete values.content;
			}

			// Create shortcode attributes string
			for ( key in values ) {
				if ( values.hasOwnProperty( key ) ) {

					//  If the key is an integer like zero we probably need to deal with the 'first' value from columns or cells. In that case don't add the key, only the values
					if ( isNaN( key ) ) {
						if ( typeof values[key] === 'object' ) {
							values[key] = values[key].join( ',' );
						}

						attributes += key + '=\'' + values[key] + '\' ';
					} else {
						attributes += values[key] + ' ';
					}
				}
			}

			// Real Implementation is here ;)
			tag.open = '[' + shortcode + ' ' + $.trim( attributes ) + ']';
			output = tag.open;

			if ( content || typeof force_content_close !== 'undefined' && force_content_close === true ) {
				if ( $.trim( content ) === '' ) {
					content = '';
				}

				tag.close = '[/' + shortcode + ']';
				output += content + tag.close;
			}

			output += linebreak + linebreak;

			return output;
		},

		/**
		 * Executed if an element has no popup modal but is managed
		 * via directly attached form (eg: sidebar with dropdown).
		 */
		recalculateShortcode: function( element_container ) {
			var values  = [],
				current = false,
				recalcs = element_container.find( 'select.axisbuilder-recalculate-shortcode' );

			for ( var i = recalcs.length - 1; i >= 0; i-- ) {
				current = $( recalcs[i] );
				values[current.data( 'attr' )] = current.val();
			}

			this.updateShortcode( values, element_container );
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
				new AB_Logger( 'Drag and drop Positioning fix active' );
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

						// new AB_Logger( 'dragging:' + ui.draggable.find( 'h2' ).text() + ' to position: ' + ui.offset.top + '/' + ui.offset.left );

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

								// new AB_Logger( current.find( 'h2' ).text() + ' element offset: ' + offset.top + '/' + offset.left );
							} else {
								break;
							}
						}

						// If we got multiple matches that all got the same top position we also need to check for the left position.
						if ( last_pos && position_array[ 'top_' + last_pos.top ].length > 1 && ( max_height - 40 ) > ui.offset.top ) {
							var real_element = false;

							// new AB_Logger( 'Checking right Positions' );

							for ( i = 0; i < position_array[ 'top_' + last_pos.top ].length; i++ ) {

								// console.log( position_array[ 'top_' + last_pos.top ][i] );

								if ( position_array[ 'top_' + last_pos.top ][i].left < ui.offset.left ) {
									real_element = position_array[ 'top_' + last_pos.top ][i].index;
								} else {
									break;
								}
							}

							if ( real_element === false ) {
								// new AB_Logger( 'No right Position Element found, using first element' );
								real_element = position_array[ 'top_' + last_pos.top ][0].index;
								method = 'before';
							}

							toEl = elements.eq( real_element );
						}

						// If we got an index get that element from the list, else delete the toEL var because we need to append the draggable to the start and the next check will do that for us ;)
						if ( toEl === false ) {
							// new AB_Logger( 'No Element Found' );
							toEl = droppable;
							method = 'prepend';
						}

						// new AB_Logger( ui.draggable.find( 'h2' ).text() + ' dragable top: ' + ui.offset.top + '/' + ui.offset.left );

						// If the draggable and the new el are the same do nothing
						if ( toEl[0] === ui.draggable[0] ) {
							// new AB_Logger( 'Same Element Selected: stoping script' );
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

						// new AB_Logger( 'Appended to: ' + toEl.find( 'h2' ).text() );

						// If the element got a former parent we need to update that as well
						if ( formerParent.length ) {
							obj.updateInnerTextarea( false, formerParent );
						}

						// Get the element that the new element was inserted into. This has to be the parent of the current toEL since we usually insert the new element outside of the toEL with the 'after' method
						// If method !== 'after' the element was inserted with prepend directly to the toEL and toEL should therefore also the insertedInto element :)
						var insertedInto = method === 'after' ? toEl.parents( '.axisbuilder-drop' ) : toEl;

						if ( insertedInto.data( 'dragdrop-level' ) !== 0 ) {
							// new AB_Logger( 'Inner update necessary. Level: ' + insertedInto.data( 'dragdrop-level' ) );
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
						// new AB_Logger( '_______________' );
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

	$( document ).ready( function () {
		$.AxisBuilderObj = new $.AxisBuilder();
	});

})( jQuery );
