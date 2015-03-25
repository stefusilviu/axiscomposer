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
		this.builderActivate();
	};

	$.AxisBuilder.prototype = {

		// Activate the Whole Interface
		builderActivate: function() {
			this.shortcodesToInterface();
			this.builderBehaviour();
		},

		// All event binding goes here
		builderBehaviour: function() {
			var obj  = this,
				body = $( 'body' );

			// Toggle between default editor and page builder
			this.axisBuilderButton.click( function() {
				obj.switchEditors();
				return false;
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
			this.axisBuilderHandle.on( 'click', 'a.trash-data', function() {
				var length = obj.axisBuilderCanvas.children().length;

				$( this ).AxisBuilderBackboneModal({
					title: axisbuilder_admin_meta_boxes_builder.i18n_trash_all_elements_title,
					message: ( length > 0 ) ? axisbuilder_admin_meta_boxes_builder.i18n_trash_all_elements_message : axisbuilder_admin_meta_boxes_builder.i18n_trash_all_elements_atleast,
					dismiss: ( length > 0 ) ? false : true,
					template: '#tmpl-axisbuilder-modal-trash-data'
				});

				return false;
			});

			// Builder Canvas
			this.axisBuilderCanvas.on( 'click', '.axisbuilder-edit', function() {
				var	parents = $( this ).parents( '.axisbuilder-sortable-element:eq(0)' );

				if ( ! parents.length ) {
					parents = $( this ).parents( '.axisbuilder-layout-cell:eq(0)' );

					if ( ! parents.length ) {
						parents = $( this ).parents( '.axisbuilder-layout-section:eq(0)' );
					}
				}

				var params = parents.data(), modal;

				params.scope        = obj;
				params.modal_title  = parents.data( 'modal-title' );
				params.modal_class  = parents.data( 'modal-class' );
				params.modal_action = parents.data( 'modal-action' );
				params.on_load      = parents.data( 'modal-on-load' );

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
			})
			.on( 'click', 'a.axisbuilder-clone', function() {
				obj.shortcodes.cloneElement( this, obj );
				return false;
			})
			.on( 'click', 'a.axisbuilder-trash', function() {
				obj.shortcodes.trashElement( this, obj );
				return false;
			})
			.on( 'click', 'a.axisbuilder-change-column-size:not(.axisbuilder-change-cell-size)', function() {
				obj.shortcodes.resizeLayout( this, obj );
				return false;
			})
			.on( 'click', 'a.axisbuilder-cell-set', function() {
				obj.shortcodes.setCellSize( this );
				return false;
			})
			.on( 'click', 'a.axisbuilder-cell-add', function() {
				obj.shortcodes.addNewCell( this, obj );
				return false;
			})
			.on( 'change', 'select.axisbuilder-recalculate-shortcode', function() {
				var	container = $( this ).parents( '.axisbuilder-sortable-element:eq(0)' );
				obj.recalculateShortcode( container );
				return false;
			})
			.on( 'axisbuilder-history-update', function() {
				obj.activateDragging( this.axisBuilderParent, '' );
				obj.activateDropping( this.axisBuilderParent, '' );
			});

			// Empty the Builder Canvas & Load empty Textarea
			body.on( 'axisbuilder_backbone_modal_response', function( e, template ) {
				if ( '#tmpl-axisbuilder-modal-trash-data' !== template ) {
					return;
				}

				obj.axisBuilderCanvas.empty();
				obj.updateTextarea();
			});

			// Add cell size on builder canvas
			// @todo: Refactor this procedure ;)
			body.on( 'axisbuilder_backbone_modal_response', function( e, template ) {
				if ( '#tmpl-axisbuilder-modal-cell-size' !== template ) {
					return;
				}

				// Need Refactor ;)
				var row        = $( 'a.axisbuilder-cell-set' ).parents( '.axisbuilder-layout-row:eq(0)' ),
					cells      = row.find( '.axisbuilder-layout-cell' ),
					rowCount   = cells.length,
					variations = $.AxisBuilderLayoutRow.cellSizeVariations[rowCount];

				var add_cell_size = $( 'input[name=add_cell_size]:checked' ).val();

				if ( ! add_cell_size ) {
					return true;
				}

				$.AxisBuilderLayoutRow.changeMultipleCellSize( cells, variations[add_cell_size], obj, true );
				obj.updateInnerTextarea( false, row );
				obj.updateTextarea();
				obj.historySnapshot(0);
			});
		},

		// Switch between the {WordPress|AxisBuilder} Editors
		switchEditors: function() {
			if ( this.axisBuilderButton.is( '.disabled' ) ) {
				return;
			}

			var self = this;

			if ( this.axisBuilderStatus.val() !== 'active' ) {
				this.wpDefaultEditorWrap.parent().addClass( 'axisbuilder-hidden-editor' );
				this.axisBuilderButton.removeClass( 'button-primary' ).addClass( 'button-secondary' ).text( this.axisBuilderButton.data( 'editor' ) );
				this.axisBuilderParent.removeClass( 'axisbuilder-hidden');
				this.axisBuilderStatus.val( 'active' );

				// Turn off WordPress DFW for builder ;)
				if( typeof window.wp.editor.dfw === 'object' ) {
					window.wp.editor.dfw.off();
				}

				// Load Shortcodes to Interface :)
				setTimeout( function() {
					self.shortcodesToInterface();
				}, 10 );
			} else {
				this.wpDefaultEditorWrap.parent().removeClass( 'axisbuilder-hidden-editor' );
				this.axisBuilderButton.addClass( 'button-primary' ).removeClass( 'button-secondary' ).text( this.axisBuilderButton.data( 'builder' ) );
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
				if ( this.axisBuilderDebug === 'yes' && ( this.axisBuilderValues.val().indexOf( '[' ) !== -1 ) ) {
					new axisbuilder_log( 'Switching to Classic Editor. Page Builder is in Debug Mode and will empty the textarea so user can\'t edit shortcode directly', 'Editor' );
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
				url: axisbuilder_admin_meta_boxes_builder.ajax_url,
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
			if ( this.axisBuilderDebug === 'yes' ) {
				new axisbuilder_log( values, false );
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
				new axisbuilder_log( 'Drag and drop Positioning fix active' );
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
 * AxisBuilder Shortcodes JS
 */
( function( $ ) {
	'use strict';

	$.AxisBuilderShortcodes = $.AxisBuilderShortcodes || {};

	$.AxisBuilderShortcodes.fetchShortcodeEditorElement = function( shortcode, insert_target, obj ) {
		var template = $( '#axisbuilder-tmpl-' + shortcode );

		if ( template.length ) {
			if ( insert_target === 'instant-insert' ) {
				obj.sendToBuilderCanvas( template.html() );
				obj.updateTextarea();
				obj.historySnapshot(0);
			}

			return;
		}
	};

	$.AxisBuilderShortcodes.cloneElement = function( clicked, obj ) {
		var trigger    = $( clicked ),
			element    = trigger.parents( '.axisbuilder-sortable-element:eq(0)' ),
			layoutCell = false;

		// Check if it is a column
		if ( ! element.length ) {
			element = trigger.parents( '.axisbuilder-layout-column:eq(0)' );

			// Check if it is a section
			if ( ! element.length ) {
				element = trigger.parents( '.axisbuilder-layout-section:eq(0)' );
			}
		}

		// Check if its a layout cell and if we can add one to the row :)
		if ( element.is( '.axisbuilder-layout-cell' ) ) {
			var counter = element.parents( '.axisbuilder-layout-row:eq(0)' ).find( '.axisbuilder-layout-cell' ).length;
			if ( typeof $.AxisBuilderLayoutRow.newCellOrder[counter] !== 'undefined' ) {
				layoutCell = true;
			} else {
				return false;
			}
		}

		// Make sure the elements actual html code matches the value so cloning works properly.
		element.find( 'textarea' ).each( function() {
			this.innerHTML = this.value;
		});

		var cloned  = element.clone(),
			wrapped = element.parents( '.axisbuilder-layout-section, .axisbuilder-layout-column' );

		// Remove all previous drag-drop classes so we can apply new ones.
		cloned.removeClass( 'ui-draggable ui-droppable' ).find( '.ui-draggable, .ui-droppable' ).removeClass( 'ui-draggable ui-droppable' );
		cloned.insertAfter( element );

		if ( layoutCell ) {
			$.AxisBuilderShortcodes.recalcCell( clicked, obj );
		}

		if ( element.is( '.axisbuilder-layout-section' ) || element.is( '.axisbuilder-layout-column' ) || wrapped.length ) {
			if ( wrapped.length ) {
				obj.updateTextarea();
				obj.updateInnerTextarea( element );
			}
		}

		// Activate Element Drag and Drop
		obj.activateDragging();
		obj.activateDropping();

		// Update Text-Area and Snapshot history
		obj.updateTextarea();
		obj.historySnapshot();
	};

	$.AxisBuilderShortcodes.trashElement = function( clicked, obj ) {
		var trigger = $( clicked ),
			element = trigger.parents( '.axisbuilder-sortable-element:eq(0)' ),
			parents = false, removeCell = false, element_hide = 200;

		// Check if it is a column
		if ( ! element.length ) {
			element = trigger.parents( '.axisbuilder-layout-column:eq(0)' );
			parents = trigger.parents( '.axisbuilder-layout-section:eq(0)>.axisbuilder-inner-shortcode' );

			// Check if it is a section
			if ( ! element.length ) {
				element = trigger.parents( '.axisbuilder-layout-section:eq(0)' );
				parents = false;
			}
		} else {
			parents = trigger.parents( '.axisbuilder-inner-shortcode:eq(0)' );
		}

		// Check if it a cell
		if ( element.length && element.is( '.axisbuilder-layout-cell' ) ) {
			if ( parents.find( '.axisbuilder-layout-cell' ).length > 1 ) {
				removeCell   = true;
				element_hide = 0;
			} else {
				return false;
			}
		}

		// obj.targetInsertInActive();

		element.hide( element_hide, function() {
			if ( removeCell ) {
				$.AxisBuilderShortcodes.removeCell( clicked, obj );
			}

			element.remove();

			if ( parents && parents.length ) {
				obj.updateInnerTextarea( parents );
			}

			obj.updateTextarea();

			// Bugfix for column delete that renders the canvas undropbable for unknown reason
			if ( obj.axisBuilderValues.val() === '' ) {
				obj.activateDropping( obj.axisBuilderParent, 'destroy' );
			}

			obj.historySnapshot();
		});
	};

	$.AxisBuilderShortcodes.resizeLayout = function( clicked, obj ) {
		var element     = $( clicked ),
			container   = element.parents( '.axisbuilder-layout-column:eq(0)' ),
			section     = container.parents( '.axisbuilder-layout-section:eq(0)' ),
			currentSize = container.data( 'width' ),
			nextSize    = [],
			direction   = element.is( '.axisbuilder-increase' ) ? 1 : -1,
			sizeString  = container.find( '.axisbuilder-column-size' ),
			dataStorage = container.find( '.axisbuilder-inner-shortcode > ' + obj.shortcodesData ),
			dataString  = dataStorage.val(),
			sizes       = [
				['ab_one_full',     '1/1'],
				['ab_four_fifth',   '4/5'],
				['ab_three_fourth', '3/4'],
				['ab_two_third',    '2/3'],
				['ab_three_fifth',  '3/5'],
				['ab_one_half',     '1/2'],
				['ab_two_fifth',    '2/5'],
				['ab_one_third',    '1/3'],
				['ab_one_fourth',   '1/4'],
				['ab_one_fifth',    '1/5']
			];

		for ( var i = 0; i < sizes.length; i++ ) {
			if ( sizes[i][0] === currentSize ) {
				nextSize = sizes[ i - direction ];
			}
		}

		if ( typeof nextSize !== 'undefined' ) {

			// Regular Expression
			dataString = dataString.replace( new RegExp( '^\\[' + currentSize, 'g' ), '[' + nextSize[0] );
			dataString = dataString.replace( new RegExp( currentSize + '\\]', 'g' ), nextSize[0] + ']' );

			// Data Storage
			dataStorage.val( dataString );

			// Remove and Add Layout flex-grid class for column
			container.removeClass( currentSize ).addClass( nextSize[0] );

			// Make sure to also set the data attr so html() functions fetch the correct value
			container.attr( 'data-width', nextSize[0] ).data( 'width', nextSize[0] ); // Ensure to set data attr so html() functions fetch the correct value :)

			// Change the column size text
			sizeString.text( nextSize[1] );

			// Textarea Update and History snapshot :)
			obj.updateTextarea();

			if ( section.length ) {
				obj.updateInnerTextarea( false, section );
				obj.updateTextarea();
			}

			obj.historySnapshot(0);
		}
	};

	// --------------------------------------------
	// Functions necessary for Row/Cell Management
	// --------------------------------------------
	$.AxisBuilderShortcodes.addNewCell = function( clicked, obj ) {
		$.AxisBuilderLayoutRow.modifyCellCount( clicked, obj, 0 );
	};

	$.AxisBuilderShortcodes.recalcCell = function( clicked, obj ) {
		$.AxisBuilderLayoutRow.modifyCellCount( clicked, obj, -1 );
	};

	$.AxisBuilderShortcodes.removeCell = function( clicked, obj ) {
		$.AxisBuilderLayoutRow.modifyCellCount( clicked, obj, -2 );
	};

	$.AxisBuilderShortcodes.setCellSize = function( clicked, obj ) {
		$.AxisBuilderLayoutRow.setCellSize( clicked, obj );
	};

	// Main Row/Cell control
	$.AxisBuilderLayoutRow = {

		cellSize: [
			[ 'ab_cell_one_full'     , '1/1', 1.00 ],
			[ 'ab_cell_four_fifth'   , '4/5', 0.80 ],
			[ 'ab_cell_three_fourth' , '3/4', 0.75 ],
			[ 'ab_cell_two_third'    , '2/3', 0.66 ],
			[ 'ab_cell_three_fifth'  , '3/5', 0.60 ],
			[ 'ab_cell_one_half'     , '1/2', 0.50 ],
			[ 'ab_cell_two_fifth'    , '2/5', 0.40 ],
			[ 'ab_cell_one_third'    , '1/3', 0.33 ],
			[ 'ab_cell_one_fourth'   , '1/4', 0.25 ],
			[ 'ab_cell_one_fifth'    , '1/5', 0.20 ]
		],

		newCellOrder: [
			[ 'ab_cell_one_full'     , '1/1' ],
			[ 'ab_cell_one_half'     , '1/2' ],
			[ 'ab_cell_one_third'    , '1/3' ],
			[ 'ab_cell_one_fourth'   , '1/4' ],
			[ 'ab_cell_one_fifth'    , '1/5' ]
		],

		cellSizeVariations: {

			4 : {
				1 : [ 'ab_cell_one_fourth' , 'ab_cell_one_fourth' , 'ab_cell_one_fourth' , 'ab_cell_one_fourth' ],
				2 : [ 'ab_cell_one_fifth'  , 'ab_cell_one_fifth'  , 'ab_cell_one_fifth'  , 'ab_cell_two_fifth'  ],
				3 : [ 'ab_cell_one_fifth'  , 'ab_cell_one_fifth'  , 'ab_cell_two_fifth'  , 'ab_cell_one_fifth'  ],
				4 : [ 'ab_cell_one_fifth'  , 'ab_cell_two_fifth'  , 'ab_cell_one_fifth'  , 'ab_cell_one_fifth'  ],
				5 : [ 'ab_cell_two_fifth'  , 'ab_cell_one_fifth'  , 'ab_cell_one_fifth'  , 'ab_cell_one_fifth'  ]
			},

			3 : {
				1 : [ 'ab_cell_one_third'   , 'ab_cell_one_third'   , 'ab_cell_one_third'   ],
				2 : [ 'ab_cell_one_fourth'  , 'ab_cell_one_fourth'  , 'ab_cell_one_half'    ],
				3 : [ 'ab_cell_one_fourth'  , 'ab_cell_one_half'    , 'ab_cell_one_fourth'  ],
				4 : [ 'ab_cell_one_half'    , 'ab_cell_one_fourth'  , 'ab_cell_one_fourth'  ],
				5 : [ 'ab_cell_one_fifth'   , 'ab_cell_one_fifth'   , 'ab_cell_three_fifth' ],
				6 : [ 'ab_cell_one_fifth'   , 'ab_cell_three_fifth' , 'ab_cell_one_fifth'   ],
				7 : [ 'ab_cell_three_fifth' , 'ab_cell_one_fifth'   , 'ab_cell_one_fifth'   ],
				8 : [ 'ab_cell_one_fifth'   , 'ab_cell_two_fifth'   , 'ab_cell_two_fifth'   ],
				9 : [ 'ab_cell_two_fifth'   , 'ab_cell_one_fifth'   , 'ab_cell_two_fifth'   ],
				10: [ 'ab_cell_two_fifth'   , 'ab_cell_two_fifth'   , 'ab_cell_one_fifth'   ]
			},

			2 : {
				1 : [ 'ab_cell_one_half'     , 'ab_cell_one_half'     ],
				2 : [ 'ab_cell_two_third'    , 'ab_cell_one_third'    ],
				3 : [ 'ab_cell_one_third'    , 'ab_cell_two_third'    ],
				4 : [ 'ab_cell_one_fourth'   , 'ab_cell_three_fourth' ],
				5 : [ 'ab_cell_three_fourth' , 'ab_cell_one_fourth'   ],
				6 : [ 'ab_cell_one_fifth'    , 'ab_cell_four_fifth'   ],
				7 : [ 'ab_cell_four_fifth'   , 'ab_cell_one_fifth'    ],
				8 : [ 'ab_cell_two_fifth'    , 'ab_cell_three_fifth'  ],
				9 : [ 'ab_cell_three_fifth'  , 'ab_cell_two_fifth'    ]
			}
		},

		modifyCellCount: function( clicked, obj, direction ) {
			var item    = $( clicked ),
				row     = item.parents( '.axisbuilder-layout-row:eq(0)' ),
				cells   = row.find( '.axisbuilder-layout-cell' ),
				counter = ( cells.length + direction ),
				newEl   = $.AxisBuilderLayoutRow.newCellOrder[counter];

			if ( typeof newEl !== 'undefined' ) {
				if ( counter !== cells.length ) {
					$.AxisBuilderLayoutRow.changeMultipleCellSize( cells, newEl, obj );
				} else {
					$.AxisBuilderLayoutRow.changeMultipleCellSize( cells, newEl, obj );
					$.AxisBuilderLayoutRow.appendCell( row, newEl );
					obj.activateDropping();
				}

				obj.updateInnerTextarea( false, row );
				obj.updateTextarea();
				obj.historySnapshot(0);
			}
		},

		appendCell: function ( row, newEl ) {
			var dataStorage    = row.find( '> .axisbuilder-inner-shortcode' ),
				shortcodeClass = newEl[0].replace( 'ab_cell_', 'ab_shortcode_cells_' ).replace( '_one_full', '' ),
				template       = $( $( '#axisbuilder-tmpl-' + shortcodeClass ).html() );

			dataStorage.append( template );
		},

		changeMultipleCellSize: function( cells, newEl, obj, multi ) {
			var key      = '',
				new_size = newEl;

			cells.each( function( i ) {
				if ( multi ) {
					key = newEl[i];
					for ( var x in $.AxisBuilderLayoutRow.cellSize ) {
						if ( key === $.AxisBuilderLayoutRow.cellSize[x][0] ) {
							new_size = $.AxisBuilderLayoutRow.cellSize[x];
						}
					}
				}

				$.AxisBuilderLayoutRow.changeSingleCellSize( $( this ), new_size, obj );
			});
		},

		changeSingleCellSize: function( cell, nextSize, obj ) {
			var currentSize = cell.data( 'width' ),
				sizeString  = cell.find( '> .axisbuilder-sorthandle > .axisbuilder-column-size' ),
				dataStorage = cell.find( '> .axisbuilder-inner-shortcode > ' + obj.shortcodesData ),
				dataString  = dataStorage.val();

			// Regular Expression
			dataString = dataString.replace( new RegExp( '^\\[' + currentSize, 'g' ), '[' + nextSize[0] );
			dataString = dataString.replace( new RegExp( currentSize + '\\]', 'g' ), nextSize[0] + ']' );

			// Data Storage
			dataStorage.val( dataString );

			// Remove and Add layout flex-grid class for cell
			cell.removeClass( currentSize ).addClass( nextSize[0] );

			// Make sure to also set the data attr so html() functions fetch the correct value
			cell.attr( 'data-width', nextSize[0] ).data( 'width', nextSize[0] );
			cell.attr( 'data-shortcode-handler', nextSize[0] ).data( 'shortcode-handler', nextSize[0] );
			cell.attr( 'data-shortcode-allowed', nextSize[0] ).data( 'shortcode-allowed', nextSize[0] );

			// Change the cell size text
			sizeString.text( nextSize[1] );
		},

		setCellSize: function( clicked ) {
			var item       = $( clicked ),
				row        = item.parents( '.axisbuilder-layout-row:eq(0)' ),
				cells      = row.find( '.axisbuilder-layout-cell' ),
				rowCount   = cells.length,
				variations = this.cellSizeVariations[rowCount],
				dismiss, message = '';

			if ( variations ) {
				message += '<form>';

				for ( var x in variations ) {
					var label = '',	labeltext = '';

					for ( var y in variations[x] ) {
						for ( var z in this.cellSize ) {
							if ( this.cellSize[z][0] === variations[x][y] ) {
								labeltext = this.cellSize[z][1];
							}
						}

						label += '<span class="axisbuilder-modal-label ' + variations[x][y] + '">' + labeltext + '</span>';
					}

					message += '<div class="axisbuilder-layout-row-modal"><label class="axisbuilder-layout-row-modal-label">';
					message += '<input type="radio" id="add_cell_size_' + x + '" name="add_cell_size" value="' + x + '" /><span class="axisbuilder-layout-row-inner-label">' + label + '</span></label></div>';
				}

				message += '</form>';

			} else {
				dismiss = true;
				message += '<p>' + axisbuilder_admin_meta_boxes_builder.i18n_no_layout + '<br />';

				if ( rowCount === 1 ) {
					message += axisbuilder_admin_meta_boxes_builder.i18n_add_one_cell;
				} else {
					message += axisbuilder_admin_meta_boxes_builder.i18n_remove_one_cell;
				}

				message += '</p>';
			}

			// Load Backbone Modal
			$( this ).AxisBuilderBackboneModal({
				title: axisbuilder_admin_meta_boxes_builder.i18n_select_cell_layout,
				message: message,
				dismiss: dismiss,
				template: '#tmpl-axisbuilder-modal-cell-size'
			});
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
