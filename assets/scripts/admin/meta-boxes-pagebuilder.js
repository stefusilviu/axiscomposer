/* global axisbuilder_admin_meta_boxes_builder, console */
jQuery( function( $ ) {

	/**
	 * Page Builder Panel
	 */
	var axisbuilder_meta_boxes_builder = {
		pagebuilder: null,
		init: function() {
			this.pagebuilder = $( '#axisbuilder-editor' ).find( ':input.axisbuilder-status' );

			this.undo_redo.init();
			this.stupidtable.init();
			this.shortcode_interface();

			$( 'a.axisbuilder-toggle-editor' ).click( this.toggle_editor );

			$( '#axisbuilder-editor' )
				.on( 'click', '.insert-shortcode', this.add_element )
				.on( 'click', '.axisbuilder-edit', this.edit_element )
				.on( 'click', 'a.axisbuilder-clone', this.clone_element )
				.on( 'click', 'a.axisbuilder-trash', this.trash_element )

				// History
				.on( 'click', 'a.undo-data', this.undo_redo.undo_data )
				.on( 'click', 'a.redo-data', this.undo_redo.redo_data )

				// Trash data
				.on( 'click', 'a.trash-data', this.trash_data )

				// Resize Layout
				.on( 'click', 'a.axisbuilder-change-column-size:not(.axisbuilder-change-cell-size)', this.resize_layout )

				// Grid row cell
				.on( 'click', 'a.axisbuilder-cell-add', this.cell.add_cell )
				.on( 'click', 'a.axisbuilder-cell-set', this.cell.set_cell_size )

				// Recalc element
				.on( 'change', 'select.axisbuilder-recalculate-shortcode', this.select_changed );

			$( 'body' )
				.on( 'axisbuilder_backbone_modal_loaded', this.backbone.init )
				.on( 'axisbuilder_backbone_modal_response', this.backbone.response );

			$( '.canvas-area' )
				.on( 'axisbuilder_history_update', this.undo_redo.history_update )
				.on( 'axisbuilder_storage_update', this.undo_redo.storage_update );

			$( document )
				.bind( 'keyup.axisbuilder_history', this.undo_redo.keyboard_actions );
		},

		tiptip: function() {
			$( '#tiptip_holder' ).removeAttr( 'style' );
			$( '#tiptip_arrow' ).removeAttr( 'style' );
			$( '.tips' ).tipTip({
				'attribute': 'data-tip',
				'fadeIn': 50,
				'fadeOut': 50,
				'delay': 200
			});
		},

		block: function() {
			$( '#axisbuilder-editor' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		unblock: function() {
			$( '#axisbuilder-editor' ).unblock();
		},

		toggle_editor: function( e ) {
			e.preventDefault();

			// Prevent if page builder is disabled
			var button = $( this );
			if ( button.is( '.disabled' ) ) {
				return;
			}

			if ( axisbuilder_meta_boxes_builder.pagebuilder.val() !== 'active' ) {
				$( '#axisbuilder-editor' ).removeClass( 'axisbuilder-hidden' );
				$( '#postdivrich' ).parent().addClass( 'axisbuilder-hidden-editor' );
				button.removeClass( 'button-primary' ).addClass( 'button-secondary' ).text( $( this ).data( 'editor' ) );
				axisbuilder_meta_boxes_builder.pagebuilder.val( 'active' );

				if( typeof window.wp.editor.dfw === 'object' ) {
					window.wp.editor.dfw.off();
				}

				setTimeout( function() {
					axisbuilder_meta_boxes_builder.shortcode_interface();
				}, 10 );
			} else {
				$( '#axisbuilder-editor' ).addClass( 'axisbuilder-hidden' );
				$( '#postdivrich' ).parent().removeClass( 'axisbuilder-hidden-editor' );
				button.addClass( 'button-primary' ).removeClass( 'button-secondary' ).text( $( this ).data( 'builder' ) );
				axisbuilder_meta_boxes_builder.pagebuilder.val( 'inactive' );

				// Remove duplication of canvas elements
				$( '.canvas-area' ).find( '>*:not( .control-bar, .axisbuilder-insert-area )' ).remove();

				if( typeof window.editorExpand === 'object' ) {
					window.editorExpand.off();
					window.editorExpand.on();
				}

				// Debug Logger
				if ( axisbuilder_admin_meta_boxes_builder.debug_mode === 'yes' && ( $( '.canvas-data' ).val().indexOf( '[' ) !== -1 ) ) {
					console.log( 'Switching to Classic Editor. Page Builder is in Debug Mode and will empty the textarea so user can\'t edit shortcode directly.' );
					if ( typeof window.tinyMCE !== 'undefined' ) {
						window.tinyMCE.get( 'content' ).setContent( '', { format: 'html' } );
						$( '#content.wp-editor-area' ).val('');
					}
				}
			}
		},

		shortcode_interface: function( text ) {
			// Prevent if we don't have the pagebuilder active
			if ( axisbuilder_meta_boxes_builder.pagebuilder.val() !== 'active' ) {
				return;
			}

			// Also test-drive val() to html()
			if ( typeof text === 'undefined' ) {
				text = $( '.canvas-data' ).val();
				if ( text.indexOf( '[' ) === -1 ) {
					text = $( '#content.wp-editor-area' ).val();

					if ( typeof window.tinyMCE !== 'undefined' ) {
						text = window.switchEditors._wp_Nop( text );
					}

					$( '.canvas-data' ).val( text );
				}
			}

			var data = {
				text: text,
				action: 'axisbuilder_shortcodes_to_interface'
			};

			axisbuilder_meta_boxes_builder.block();

			$.ajax({
				url: axisbuilder_admin_meta_boxes_builder.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					axisbuilder_meta_boxes_builder.send_to_canvas( response );
					// axisbuilder_meta_boxes_builder.textarea.outer(); // Don't update textarea on load, only when elements got edited.
					axisbuilder_meta_boxes_builder.history_snapshot();
					axisbuilder_meta_boxes_builder.tiptip();
					axisbuilder_meta_boxes_builder.unblock();
					axisbuilder_meta_boxes_builder.stupidtable.init();
				}
			});
		},

		send_to_canvas: function( text ) {
			var add_text = $( text );
			$( '.canvas-area' ).append( add_text );

			// Activate Draggable-Droppable
			axisbuilder_meta_boxes_builder.dragdrop.draggable();
			axisbuilder_meta_boxes_builder.dragdrop.droppable();
		},

		add_element: function() {
			var shortcode     = this.hash.replace( '#', '' ),
				element_tmpl  = $( '#axisbuilder-tmpl-' + shortcode ),
				insert_target = 'instant-insert'; // ( this.className.indexOf( 'axisbuilder-target-insert' ) !== -1 ) ? 'target_insert' : 'instant_insert',

			if ( element_tmpl.length ) {
				if ( insert_target === 'instant-insert' ) {
					axisbuilder_meta_boxes_builder.send_to_canvas( element_tmpl.html() );
					axisbuilder_meta_boxes_builder.textarea.outer();
					axisbuilder_meta_boxes_builder.history_snapshot();
				}
			}

			return false;
		},

		edit_element: function() {
			var	parents = $( this ).parents( '.axisbuilder-sortable-element:eq(0)' );

			if ( ! parents.length ) {
				parents = $( this ).parents( '.axisbuilder-layout-cell:eq(0)' );

				if ( ! parents.length ) {
					parents = $( this ).parents( '.axisbuilder-layout-section:eq(0)' );
				}
			}

			$( this ).AxisBuilderBackboneModal({
				title: parents.data( 'modal-title' ),
				screen: parents.data( 'modal-class' ),
				message: 'Fetch options field with validation using AJAX...',
				template: '#tmpl-axisbuilder-modal-edit-element'
			});

			return false;
		},

		clone_element: function() {
			var element  = $( this ).parents( '.axisbuilder-sortable-element:eq(0)' ),
				recalc_cell = false;

			// Check if column
			if ( ! element.length ) {
				element = $( this ).parents( '.axisbuilder-layout-column:eq(0)' );

				// Check if section
				if ( ! element.length ) {
					element = $( this ).parents( '.axisbuilder-layout-section:eq(0)' );
				}
			}

			// Check if cell
			if ( element.is( '.axisbuilder-layout-cell' ) ) {
				var count  = element.parents( '.axisbuilder-layout-row:eq(0)' ).find( '.axisbuilder-layout-cell' ).length;
				if ( typeof axisbuilder_meta_boxes_builder_data.new_cell_order[count] !== 'undefined' ) {
					recalc_cell = true;
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

			if ( recalc_cell ) {
				axisbuilder_meta_boxes_builder.cell.modify_cell_count( this, -1 );
			}

			if ( element.is( '.axisbuilder-layout-section' ) || element.is( '.axisbuilder-layout-column' ) || wrapped.length ) {
				if ( wrapped.length ) {
					axisbuilder_meta_boxes_builder.textarea.outer();
					axisbuilder_meta_boxes_builder.textarea.inner( element );
				}
			}

			// Activate Draggable-Droppable
			axisbuilder_meta_boxes_builder.dragdrop.draggable();
			axisbuilder_meta_boxes_builder.dragdrop.droppable();

			// Textarea Update and History snapshot
			axisbuilder_meta_boxes_builder.textarea.outer();
			axisbuilder_meta_boxes_builder.history_snapshot();

			return false;
		},

		trash_element: function() {
			var	element = $( this ).parents( '.axisbuilder-sortable-element:eq(0)' ),
				parents = false, remove_cell = false, hide_timer = 200;

			// Check if column
			if ( ! element.length ) {
				element = $( this ).parents( '.axisbuilder-layout-column:eq(0)' );
				parents = $( this ).parents( '.axisbuilder-layout-section:eq(0)>.axisbuilder-inner-shortcode' );

				// Check if section
				if ( ! element.length ) {
					element = $( this ).parents( '.axisbuilder-layout-section:eq(0)' );
					parents = false;
				}
			} else {
				parents = $( this ).parents( '.axisbuilder-inner-shortcode:eq(0)' );
			}

			// Check if cell
			if ( element.length && element.is( '.axisbuilder-layout-cell' ) ) {
				if ( parents.find( '.axisbuilder-layout-cell' ).length > 1 ) {
					hide_timer  = 0;
					remove_cell = true;
				} else {
					return false;
				}
			}

			element.hide( hide_timer, function() {
				if ( remove_cell ) {
					axisbuilder_meta_boxes_builder.cell.modify_cell_count( this, -2 );
				}

				element.remove();

				if ( parents && parents.length ) {
					axisbuilder_meta_boxes_builder.textarea.inner( parents );
				}
				axisbuilder_meta_boxes_builder.textarea.outer();

				// Bugfix for column delete that renders the canvas undropbable for unknown reason
				if ( $( '.canvas-data' ).val() === '' ) {
					axisbuilder_meta_boxes_builder.dragdrop.droppable( '', 'destroy' );
				}

				axisbuilder_meta_boxes_builder.history_snapshot();
			});

			return false;
		},

		trash_data: function() {
			var length = $( '.canvas-area' ).children().length;

			$( this ).AxisBuilderBackboneModal({
				title: axisbuilder_admin_meta_boxes_builder.i18n_trash_all_elements_title,
				message: ( length > 0 ) ? axisbuilder_admin_meta_boxes_builder.i18n_trash_all_elements_message : axisbuilder_admin_meta_boxes_builder.i18n_trash_all_elements_atleast,
				dismiss: ( length > 0 ) ? false : true,
				template: '#tmpl-axisbuilder-modal-trash-data'
			});

			return false;
		},

		resize_layout: function() {
			var	direction    = $( this ).is( '.axisbuilder-increase' ) ? 1 : -1,
				column       = $( this ).parents( '.axisbuilder-layout-column:eq(0)' ),
				section      = column.parents( '.axisbuilder-layout-section:eq(0)' ),
				size_string  = column.find( '.axisbuilder-column-size' ),
				data_storage = column.find( '.axisbuilder-inner-shortcode > textarea[data-name="text-shortcode"]' ),
				data_string  = data_storage.val(),
				next_size    = [],
				column_size  = axisbuilder_meta_boxes_builder_data.col_size,
				current_size = column.data( 'width' );

			// Next size?
			for ( var i = 0; i < column_size.length; i++ ) {
				if ( column_size[i][0] === current_size ) {
					next_size = column_size[ i - direction ];
				}
			}

			if ( typeof next_size !== 'undefined' ) {
				// Regular Expression
				data_string = data_string.replace( new RegExp( '^\\[' + current_size, 'g' ), '[' + next_size[0] );
				data_string = data_string.replace( new RegExp( current_size + '\\]', 'g' ), next_size[0] + ']' );

				// Data Storage
				data_storage.val( data_string );

				// Remove and Add Layout flex-grid class for column
				column.removeClass( current_size ).addClass( next_size[0] );

				// Make sure to also set the data attr so html() functions fetch the correct value
				column.attr( 'data-width', next_size[0] ).data( 'width', next_size[0] ); // Ensure to set data attr so html() functions fetch the correct value :)

				// Change the column size text
				size_string.text( next_size[1] );

				// Textarea Update and History snapshot :)
				axisbuilder_meta_boxes_builder.textarea.outer();
				if ( section.length ) {
					axisbuilder_meta_boxes_builder.textarea.inner( false, section );
					axisbuilder_meta_boxes_builder.textarea.outer();
				}
				axisbuilder_meta_boxes_builder.history_snapshot();
			}

			return false;
		},

		select_changed: function() {
			var container = $( this ).parents( '.axisbuilder-sortable-element:eq(0)' );
			axisbuilder_meta_boxes_builder.recalc_element( container );
			return false;
		},

		recalc_element: function( element_container ) {
			var values  = [],
				current = false,
				recalcs = element_container.find( 'select.axisbuilder-recalculate-shortcode' );

			for ( var i = recalcs.length - 1; i >= 0; i-- ) {
				current = $( recalcs[i] );
				values[current.data( 'attr' )] = current.val();
			}

			axisbuilder_meta_boxes_builder.send_to_datastorage( values, element_container );
		},

		send_to_datastorage: function( values, element_container ) {
			var column    = element_container.parents( '.axisbuilder-layout-column:eq(0)' ),
				section   = element_container.parents( '.axisbuilder-layout-section:eq(0)' ),
				selector  = element_container.is( '.axisbuilder-modal-group-element' ) ? ( 'textarea[data-name="text-shortcode"]:eq(0)' ) : ( '> .axisbuilder-inner-shortcode >textarea[data-name="text-shortcode"]:eq(0)' ),
				save_data = element_container.find( selector ),
				shortcode = element_container.data( 'shortcode-handler' ), output = '', tags = {};

			// Debug Logger
			if ( axisbuilder_admin_meta_boxes_builder.debug_mode === 'yes' ) {
				console.log( values );
			}

			// If we got a string value passed insert the string, otherwise calculate the shortcode ;)
			if ( typeof values === 'string' ) {
				output = values;
			} else {
				var extract_html = axisbuilder_meta_boxes_builder.update_builder_html( element_container, values );

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
				axisbuilder_meta_boxes_builder.textarea.inner( false, section );
			} else if ( column.length ) {
				axisbuilder_meta_boxes_builder.textarea.inner( false, column );
			}

			axisbuilder_meta_boxes_builder.textarea.outer();
			axisbuilder_meta_boxes_builder.history_snapshot();
			element_container.trigger( 'update' );
		},

		update_builder_html: function( element_container, values, force_content_close ) {
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

			var shortcode      = element_container.data( 'shortcode-handler' ),
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

					// @todo Will do later when we need actually ;)
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
			extract_html.output = axisbuilder_meta_boxes_builder.shortcode_string( values, shortcode, tags, force_content_close );

			return extract_html;
		},

		shortcode_string: function( values, shortcode, tag, force_content_close ) {
			var i, key, output = '', content = '', attr = '', seperator = ',', linebreak = '\n';
			if ( ! tag ) {
				tag = {};
			}

			// Create shortcode content var
			if ( typeof values.content !== 'undefined' ) {
				if ( typeof values.content === 'object' ) {
					if ( values.content[0].indexOf( '[' ) !== -1 ) {
						seperator = linebreak;
					}

					for ( i = 0; i < values.content.length; i++ ) {
						values.content[i] = $.trim( values.content[i] );
					}

					content = values.content.join( seperator );
				} else {
					content = values.content;
				}

				content = linebreak + content + linebreak;
				delete values.content;
			}

			// Create shortcode attr string
			for ( key in values ) {
				if ( values.hasOwnProperty( key ) ) {
					if ( isNaN( key ) ) {
						if ( typeof values[key] === 'object' ) {
							values[key] = values[key].join( ',' );
						}

						attr += key + '=\'' + values[key] + '\' ';
					} else {
						attr += values[key] + ' ';
					}
				}
			}

			tag.open = '[' + shortcode + ' ' + $.trim( attr ) + ']';
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

		history_snapshot: function( timeout ) {
			setTimeout( function() {
				$( '.canvas-area' ).trigger( 'axisbuilder_storage_update' );
			}, timeout ? timeout : 150 );
		},

		textarea: {

			inner: function( element, container ) {
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
						axisbuilder_meta_boxes_builder.textarea.inner( false, $( columns[i] ) );
					}

					columns = container.find( '.axisbuilder-layout-cell' );
					for ( i = 0; i < columns.length; i++ ) {
						axisbuilder_meta_boxes_builder.textarea.inner( false, $( columns[i] ) );
					}

					content        = '';
					currentName    = container.data( 'shortcode-handler' );
					main_storage   = container.find( '>.axisbuilder-inner-shortcode >textarea[data-name="text-shortcode"]' );
					content_fields = container.find( '>.axisbuilder-inner-shortcode > div textarea[data-name="text-shortcode"]:not( .axisbuilder-layout-column .axisbuilder-sortable-element textarea[data-name="text-shortcode"], .axisbuilder-layout-cell .axisbuilder-layout-column textarea[data-name="text-shortcode"] )' );
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
					main_storage   = container.find( '>.axisbuilder-inner-shortcode >textarea[data-name="text-shortcode"]' );
					content_fields = container.find( '>.axisbuilder-inner-shortcode > div textarea[data-name="text-shortcode"]:not( .axisbuilder-layout-column-no-cell .axisbuilder-sortable-element textarea[data-name="text-shortcode"] )' );
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
					content_fields = container.find( '.axisbuilder-sortable-element textarea[data-name="text-shortcode"]' );
					main_storage   = container.find( '>.axisbuilder-inner-shortcode >textarea[data-name="text-shortcode"]' );

					for ( i = 0; i < content_fields.length; i++ ) {
						content += $( content_fields[i] ).val();
					}

					content = '[' + currentSize + currentFirst + ']\n\n' + content + '[/' + currentSize + ']';
					main_storage.val( content );
				}
			},

			outer: function( scope ) {
				// Prevent if we don't have the pagebuilder active
				if ( axisbuilder_meta_boxes_builder.pagebuilder.val() !== 'active' ) {
					return;
				}

				if ( ! scope ) {
					$( '.canvas-area' ).find( '.axisbuilder-layout-section' ).each( function() {
						var col_in_section   = $( this ).find( '>.axisbuilder-inner-shortcode > div > .axisbuilder-inner-shortcode' ),
							col_in_grid_cell = $( this ).find( '.axisbuilder-layout-cell .axisbuilder-layout-column-no-cell > .axisbuilder-inner-shortcode' );

						if ( col_in_section.length ) {
							axisbuilder_meta_boxes_builder.textarea.outer( col_in_section );
						}

						if ( col_in_grid_cell.length ) {
							axisbuilder_meta_boxes_builder.textarea.outer( col_in_grid_cell );
						}
					});

					scope = $( '.axisbuilder-data > div > .axisbuilder-inner-shortcode' );
				}

				var size_count     = 0,
					content_val    = '',
					column_size    = axisbuilder_meta_boxes_builder_data.col_size,
					content_fields = scope.find( '>textarea[data-name="text-shortcode"]' ),
					current_field, current_content, current_parents, current_size, next_size;

				for ( var i = 0; i < content_fields.length; i++ ) {
					current_field   = $( content_fields[i] );
					current_content = current_field.val();
					current_parents = current_field.parents( '.axisbuilder-layout-column-no-cell:eq(0)' );

					// Check for column to add/remove first class
					if ( current_parents.length ) {
						current_size = current_parents.data( 'width' );

						for( var x in column_size ) {
							if ( current_size === column_size[x][0] ) {
								next_size = column_size[x];
							}
						}

						size_count += next_size[2];

						if ( size_count > 1 || i === 0 ) {

							if ( ! current_parents.is( '.axisbuilder-first-column' ) ) {
								current_parents.addClass( 'axisbuilder-first-column' );
								current_content = current_content.replace( new RegExp( '^\\[' + current_size ), '[' + current_size + ' first' );
								current_field.val( current_content );
							}

							size_count = next_size[2];
						} else if ( current_parents.is( '.axisbuilder-first-column' ) ) {
							current_parents.removeClass( 'axisbuilder-first-column' );
							current_content = current_content.replace( ' first', '' );
							current_field.val( current_content );
						}
					} else {
						size_count = 1;
					}

					content_val += current_content;
				}

				if ( typeof window.tinyMCE !== 'undefined' ) {
					setTimeout( function() {
						window.tinyMCE.get( 'content' ).setContent( window.switchEditors.wpautop( content_val ), { format: 'html' } );
					}, 500 );
				}

				$( '.canvas-data' ).val( content_val );
				$( '#content.wp-editor-area' ).val( content_val ).trigger( 'axisbuilder_update' );
			}
		},

		dragdrop: {

			is_scope: function( passed_scope ) {
				return passed_scope || $( '.canvas-area' ).parents( '.postbox:eq(0)' );
			},

			is_droppable: function( draggable, droppable ) {
				return draggable.data( 'dragdrop-level' ) > droppable.data( 'dragdrop-level' );
			},

			draggable: function( scope, exclude ) {
				scope = axisbuilder_meta_boxes_builder.dragdrop.is_scope( scope );
				if ( typeof exclude === 'undefined' ) {
					exclude = ':not(.ui-draggable)';
				}

				var data = {
					handle: '>.menu-item-handle',
					helper: 'clone',
					scroll: true,
					zIndex: 20000,
					appendTo: 'body',
					cursorAt: {
						left: 20
					},
					start: function( event ) {
						var current = $( event.target );
						current.css({ opacity: 0.4 });
						$( '.axisbuilder-hover-active' ).removeClass( 'axisbuilder-hover-active' );
						$( '.canvas-area' ).addClass( 'axisbuilder-select-target-' + current.data( 'dragdrop-level' ) );
					},
					stop: function( event ) {
						$( event.target ).css({ opacity: 1 });
						$( '.axisbuilder-hover-active' ).removeClass( 'axisbuilder-hover-active' );
						$( '.canvas-area' ).removeClass( 'axisbuilder-select-target-1 axisbuilder-select-target-2 axisbuilder-select-target-3 axisbuilder-select-target-4' );
					}
				};

				// Draggable
				scope.find( '.axisbuilder-drag' + exclude ).draggable( data );
				scope.find( '.insert-shortcode' ).not( '.ui-draggable' ).draggable(
					$.extend( {}, data, {
						handle: false,
						cursorAt: {
							top: 33,
							left: 33
						}
					})
				);
			},

			droppable: function( scope, exclude ) {
				scope = axisbuilder_meta_boxes_builder.dragdrop.is_scope( scope );
				if ( typeof exclude === 'undefined' ) {
					exclude = ':not(.ui-droppable)';
				}

				var data = {
					greedy: true,
					tolerance: 'pointer',
					over: function( event, ui ) {
						var droppable = $( this );
						if ( axisbuilder_meta_boxes_builder.dragdrop.is_droppable( ui.helper, droppable ) ) {
							droppable.addClass( 'axisbuilder-hover-active' );
						}
					},
					out: function() {
						$( this ).removeClass( 'axisbuilder-hover-active' );
					},
					drop: function( event, ui ) {
						var droppable = $( this );
						if ( ! droppable.is( '.axisbuilder-hover-active' ) ) {
							return;
						}

						var elements = droppable.find( '>.axisbuilder-drag' ), template = {}, offset = {}, method = 'after', toEl = false, position_array = [], last_pos, max_height, i;

						// Iterate over all elements and check their positions
						for ( i = 0; i < elements.length; i++ ) {
							var current = elements.eq(i);
							offset = current.offset();

							if ( offset.top < ui.offset.top ) {
								toEl = current;
								last_pos = offset;

								// Save all items before the draggable to a position array so we can check if the right positioning is important
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
							} else {
								break;
							}
						}

						// If we got multiple matches that all got the same top position we also need to check for the left position
						if ( last_pos && position_array[ 'top_' + last_pos.top ].length > 1 && ( max_height - 40 ) > ui.offset.top ) {
							var real_element = false;

							for ( i = 0; i < position_array[ 'top_' + last_pos.top ].length; i++ ) {
								if ( position_array[ 'top_' + last_pos.top ][i].left < ui.offset.left ) {
									real_element = position_array[ 'top_' + last_pos.top ][i].index;
								} else {
									break;
								}
							}

							// If we got an index get that element from the list, else delete the toEL var because we need to append the draggable to the start and the next check will do that for us
							if ( real_element === false ) {
								method = 'before';
								real_element = position_array[ 'top_' + last_pos.top ][0].index;
							}

							toEl = elements.eq( real_element );
						}

						// If no element with higher offset were found there either are no at all or the new position is at the top so we change the params accordingly
						if ( toEl === false ) {
							toEl = droppable;
							method = 'prepend';
						}

						// If the draggable and the new el are the same do nothing
						if ( toEl[0] === ui.draggable[0] ) {
							return true;
						}

						// If we got a hash on the draggable we are not dragging element but a new one via shortcode button so we need to fetch an empty shortcode template
						if ( ui.draggable[0].hash ) {
							var shortcode = ui.draggable.get(0).hash.replace( '#', '' );

							template = $( $( '#axisbuilder-tmpl-' + shortcode ).html() );
							ui.draggable = template;
						}

						// Before finally moving the element, save the former parent of the draggable to a var so we can check later if we need to update the parent as well
						var formerParent = ui.draggable.parents( '.axisbuilder-drag:last' );

						// Move the real draggable element to the new position
						toEl[ method ]( ui.draggable );

						// If the element got a former parent we need to update that as well
						if ( formerParent.length ) {
							axisbuilder_meta_boxes_builder.textarea.inner( false, formerParent );
						}

						// Get the element that the new element was inserted into. This has to be the parent of the current toEL since we usually insert the new element outside of the toEL with the 'after' method
						// If method !== 'after' the element was inserted with prepend directly to the toEL and toEL should therefore also the insertedInto element :)
						var insertedInto = ( method === 'after' ) ? toEl.parents( '.axisbuilder-drop' ) : toEl;

						if ( insertedInto.data( 'dragdrop-level' ) !== 0 ) {
							axisbuilder_meta_boxes_builder.textarea.outer(); // <-- actually only necessary because of column first class. optimize that so we can remove the costly function of updating all elements :)
							axisbuilder_meta_boxes_builder.textarea.inner( ui.draggable );
						}

						// Everything is fine, now do the re sorting and textarea updating
						axisbuilder_meta_boxes_builder.textarea.outer();

						// Apply dragging and dropping in case we got a new element
						if ( typeof template !== 'undefined' ) {
							$( '.canvas-area' ).removeClass( 'ui-droppable' ).droppable( 'destroy' );
							axisbuilder_meta_boxes_builder.dragdrop.draggable();
							axisbuilder_meta_boxes_builder.dragdrop.droppable();
						}

						// History Snapshot
						axisbuilder_meta_boxes_builder.history_snapshot();
					}
				};

				// Destroy droppable
				if ( exclude === 'destroy' ) {
					scope.find( '.axisbuilder-drop' ).droppable( 'destroy' );
					exclude = '';
				}

				// Droppable
				scope.find( '.axisbuilder-drop' + exclude ).droppable( data );
			}
		},

		cell: {

			add_cell: function() {
				axisbuilder_meta_boxes_builder.cell.modify_cell_count( this, 0 );
				return false;
			},

			set_cell_size: function() {
				var $row                 = $( this ).parents( '.axisbuilder-layout-row:eq(0)' ),
					cells                = $row.find( '.axisbuilder-layout-cell' ),
					cell_size            = axisbuilder_meta_boxes_builder_data.cell_size,
					cell_size_variations = axisbuilder_meta_boxes_builder_data.cell_size_variations[cells.length],
					dismiss, message = '';

				if ( cell_size_variations ) {
					for ( var x in cell_size_variations ) {
						var label = '',	labeltext = '';

						for ( var y in cell_size_variations[x] ) {
							for ( var z in cell_size ) {
								if ( cell_size[z][0] === cell_size_variations[x][y] ) {
									labeltext = cell_size[z][1];
								}
							}

							label += '<span class="axisbuilder-modal-label ' + cell_size_variations[x][y] + '">' + labeltext + '</span>';
						}

						message += '<div class="axisbuilder-layout-row-modal"><label class="axisbuilder-layout-row-modal-label"><input type="radio" id="add_cell_size_' + x + '" name="add_cell_size" value="' + x + '" /><span class="axisbuilder-layout-row-inner-label">' + label + '</span></label></div>';
					}
				} else {
					dismiss = true;
					message += axisbuilder_admin_meta_boxes_builder.i18n_no_layout + '<br />';
					message += ( cells.length === 1 ) ? axisbuilder_admin_meta_boxes_builder.i18n_add_one_cell : axisbuilder_admin_meta_boxes_builder.i18n_remove_one_cell;
				}

				$( this ).AxisBuilderBackboneModal({
					title: axisbuilder_admin_meta_boxes_builder.i18n_select_cell_layout,
					message: message,
					dismiss: dismiss,
					template: '#tmpl-axisbuilder-modal-cell-size'
				});

				return false;
			},

			modify_cell_count: function( clicked, direction ) {
				var $row  = $( clicked ).parents( '.axisbuilder-layout-row:eq(0)' ),
					cells = $row.find( '.axisbuilder-layout-cell' ),
					count = ( cells.length + direction ),
					newEl = axisbuilder_meta_boxes_builder_data.new_cell_order[count];

				if ( typeof newEl !== 'undefined' ) {
					if ( count !== cells.length ) {
						axisbuilder_meta_boxes_builder.cell.change_multiple_cell_size( cells, newEl );
					} else {
						axisbuilder_meta_boxes_builder.cell.change_multiple_cell_size( cells, newEl );
						axisbuilder_meta_boxes_builder.cell.insert_cell( $row, newEl );
						axisbuilder_meta_boxes_builder.dragdrop.droppable();
					}

					axisbuilder_meta_boxes_builder.textarea.inner( false, $row );
					axisbuilder_meta_boxes_builder.textarea.outer();
					axisbuilder_meta_boxes_builder.history_snapshot();
				}
			},

			insert_cell: function( row, newEl ) {
				var storage   = row.find( '> .axisbuilder-inner-shortcode' ),
					shortcode = newEl[0].replace( 'ab_cell_', 'ab_shortcode_cells_' ).replace( '_one_full', '' ),
					cell_tmpl = $( $( '#axisbuilder-tmpl-' + shortcode ).html() );

				storage.append( cell_tmpl );
			},

			change_multiple_cell_size: function( cells, newEl, multi ) {
				var key       = '',
					next_size = newEl,
					cell_size = axisbuilder_meta_boxes_builder_data.cell_size;

				cells.each( function( i ) {
					if ( multi ) {
						key = newEl[i];
						for ( var x in cell_size ) {
							if ( key === cell_size[x][0] ) {
								next_size = cell_size[x];
							}
						}
					}

					axisbuilder_meta_boxes_builder.cell.change_single_cell_size( $( this ), next_size );
				});
			},

			change_single_cell_size: function( cell, next_size ) {
				var current_size = cell.data( 'width' ),
					size_string  = cell.find( '> .axisbuilder-sorthandle > .axisbuilder-column-size' ),
					data_storage = cell.find( '> .axisbuilder-inner-shortcode > textarea[data-name="text-shortcode"]' ),
					data_string  = data_storage.val();

				// Regular Expression
				data_string = data_string.replace( new RegExp( '^\\[' + current_size, 'g' ), '[' + next_size[0] );
				data_string = data_string.replace( new RegExp( current_size + '\\]', 'g' ), next_size[0] + ']' );

				// Data storage
				data_storage.val( data_string );

				// Remove and Add Layout flex-grid class for cell
				cell.removeClass( current_size ).addClass( next_size[0] );

				// Make sure to also set the data attr so html() functions fetch the correct value
				cell.attr( 'data-width', next_size[0] ).data( 'width', next_size[0] );
				cell.attr( 'data-shortcode-handler', next_size[0] ).data( 'shortcode-handler', next_size[0] );
				cell.attr( 'data-shortcode-allowed', next_size[0] ).data( 'shortcode-allowed', next_size[0] );

				// Change the cell size text
				size_string.text( next_size[1] );
			}
		},

		backbone: {

			init: function( e, template ) {
				if ( '#tmpl-axisbuilder-modal-edit-element' === template ) {
					$( 'body' ).trigger( 'axisbuilder-enhanced-select-init' );
				}
			},

			response: function( e, template, data ) {
				if ( '#tmpl-axisbuilder-modal-trash-data' === template ) {
					axisbuilder_meta_boxes_builder.backbone.trash_data();
				}

				if ( '#tmpl-axisbuilder-modal-cell-size' === template ) {
					axisbuilder_meta_boxes_builder.backbone.cell_size( data.add_cell_size );
				}

				if ( '#tmpl-axisbuilder-modal-edit-element' === template ) {
					axisbuilder_meta_boxes_builder.backbone.edit_element();
				}
			},

			trash_data: function() {
				$( '.canvas-area' ).empty();
				axisbuilder_meta_boxes_builder.textarea.outer();
			},

			cell_size: function( add_cell_size ) {
				var $row                 = $( 'a.axisbuilder-cell-set' ).parents( '.axisbuilder-layout-row:eq(0)' ),
					cells                = $row.find( '.axisbuilder-layout-cell' ),
					cell_size_variations = axisbuilder_meta_boxes_builder_data.cell_size_variations[cells.length];

				if ( add_cell_size ) {
					axisbuilder_meta_boxes_builder.cell.change_multiple_cell_size( cells, cell_size_variations[add_cell_size], true );
					axisbuilder_meta_boxes_builder.textarea.inner( false, $row );
					axisbuilder_meta_boxes_builder.textarea.outer();
					axisbuilder_meta_boxes_builder.history_snapshot();
				}
			},

			edit_element: function() {}
		},

		undo_redo: {

			init: function() {
				// Check for Storage before using it
				if ( typeof Storage === 'undefined' ) {
					return;
				}

				// Create unique post array key
				this.key     = this.add_key();
				this.storage = this.get_key() || [];
				this.maximum = this.storage.length - 1;

				// Temporary steps storage
				this.temporary = this.get_key( this.key + 'temp' );
				if ( typeof this.temporary === 'undefined' || this.temporary === null ) {
					this.temporary = this.maximum;
				}

				// Clear storage
				this.clear_storage();
			},

			add_key: function() {
				var key = 'axisbuilder' + axisbuilder_admin_meta_boxes_builder.theme_name + axisbuilder_admin_meta_boxes_builder.theme_version + axisbuilder_admin_meta_boxes_builder.post_id + axisbuilder_admin_meta_boxes_builder.plugin_version;
				return key.replace( /[^a-zA-Z0-9]/g, '' ).toLowerCase();
			},

			get_key: function( passed_key ) {
				var key = passed_key || axisbuilder_meta_boxes_builder.undo_redo.key;
				return JSON.parse( sessionStorage.getItem( key ) );
			},

			set_key: function( passed_key, passed_value ) {
				var key   = passed_key || axisbuilder_meta_boxes_builder.undo_redo.key,
					value = passed_value || JSON.stringify( axisbuilder_meta_boxes_builder.undo_redo.storage );

				try {
					sessionStorage.setItem( key, value );
				}

				catch( e ) {
					axisbuilder_meta_boxes_builder.undo_redo.clear_storage();
					$( '.undo-data, .redo-data' ).addClass( 'inactive-history' );
					console.log( 'Storage Limit reached. Your Browser does not offer enough session storage to save more steps for the undo/redo history.', e );
				}
			},

			clear_storage: function() {
				sessionStorage.removeItem( axisbuilder_meta_boxes_builder.undo_redo.key );
				sessionStorage.removeItem( axisbuilder_meta_boxes_builder.undo_redo.key + 'temp' );

				// Reset storage and temporary
				axisbuilder_meta_boxes_builder.undo_redo.storage   = [];
				axisbuilder_meta_boxes_builder.undo_redo.temporary = null;
			},

			undo_data: function() {
				var history = axisbuilder_meta_boxes_builder.undo_redo;
				if ( ( history.temporary - 1 ) >= 0 ) {
					history.temporary --;
					history.canvas_update( history.storage[ history.temporary ] );
				}

				return false;
			},

			redo_data: function() {
				var history = axisbuilder_meta_boxes_builder.undo_redo;
				if ( ( history.temporary + 1 ) <= history.maximum ) {
					history.temporary ++;
					history.canvas_update( history.storage[ history.temporary ] );
				}

				return false;
			},

			keyboard_actions: function( e ) {
				var	button     = e.keyCode || e.which,
					controlled = e.ctrlKey || e.metaKey,
					history    = axisbuilder_meta_boxes_builder.undo_redo;

				// Undo Event
				if ( 90 === button && ( controlled || ( controlled && e.shiftKey ) ) ) {
					setTimeout( function() {
						history.undo_data();
					}, 100 );

					e.stopImmediatePropagation();
				}

				// Redo Event
				if ( 89 === button && ( controlled || ( controlled && e.shiftKey ) ) ) {
					setTimeout( function() {
						history.redo_data();
					}, 100 );

					e.stopImmediatePropagation();
				}
			},

			canvas_update: function( values ) {
				var history = axisbuilder_meta_boxes_builder.undo_redo;
				if ( typeof window.tinyMCE !== 'undefined' ) {
					window.tinyMCE.get( 'content' ).setContent( window.switchEditors.wpautop( values[0] ), { format: 'html' } );
				}

				$( '.canvas-data' ).val( values[0] );
				$( '.canvas-area' ).html( values[1] );
				sessionStorage.setItem( history.key + 'temp', history.temporary );

				// History buttons
				if ( history.temporary <= 0 ) {
					$( '.undo-data' ).addClass( 'inactive-history' );
				} else {
					$( '.undo-data' ).removeClass( 'inactive-history' );
				}

				if ( history.temporary + 1 > history.maximum ) {
					$( '.redo-data' ).addClass( 'inactive-history' );
				} else {
					$( '.redo-data' ).removeClass( 'inactive-history' );
				}

				// Trigger history update
				$( '.canvas-area' ).trigger( 'axisbuilder_history_update' );
			},

			storage_update: function() {
				var history = axisbuilder_meta_boxes_builder.undo_redo;
				$( '.canvas-area' ).find( 'textarea' ).each( function() {
					this.innerHTML = this.value;
				});

				history.storage   = history.storage || history.get_key() || [];
				history.temporary = history.temporary || history.get_key( history.key + 'temp' );
				if ( typeof history.temporary === 'undefined' || history.temporary === null ) {
					history.temporary = history.storage.length - 1;
				}

				var snapshot     = [ $( '.canvas-data' ).val(), $( '.canvas-area' ).html().replace( /modal-animation/g, '' ) ],
					last_storage = history.storage[ history.temporary ];

				// Create new snapshot
				if ( typeof last_storage === 'undefined' || ( last_storage[0] !== snapshot[0] ) ) {
					history.temporary ++;

					// Storage update
					history.storage = history.storage.slice( 0, history.temporary );
					history.storage.push( snapshot );
					if ( history.storage.length > 40 ) {
						history.storage.shift();
					}

					// Set the browser storage object
					axisbuilder_meta_boxes_builder.undo_redo.set_key();
				}

				history.maximum = history.storage.length - 1;

				// History buttons
				if ( history.storage.length === 1 || history.temporary === 0 ) {
					$( '.undo-data' ).addClass( 'inactive-history' );
				} else {
					$( '.undo-data' ).removeClass( 'inactive-history' );
				}

				if ( history.storage.length - 1 === history.temporary ) {
					$( '.redo-data' ).addClass( 'inactive-history' );
				} else {
					$( '.redo-data' ).removeClass( 'inactive-history' );
				}
			},

			history_update: function() {
				axisbuilder_meta_boxes_builder.dragdrop.draggable( '', '' );
				axisbuilder_meta_boxes_builder.dragdrop.droppable( '', '' );
			}
		},

		stupidtable: {

			init: function() {
				$( '.axisbuilder_editor' ).stupidtable().on( 'aftertablesort', this.add_arrows );
			},

			add_arrows: function( event, data ) {
				var th    = $( this ).find( 'th' );
				var arrow = data.direction === 'asc' ? '&uarr;' : '&darr;';
				var index = data.column;

				if ( 1 < index ) {
					index = index - 1;
				}

				th.find( '.axisbuilder-arrow' ).remove();
				th.eq( index ).append( '<span class="axisbuilder-arrow">' + arrow + '</span>' );
			}
		}
	};

	/**
	 * Page Builder Data
	 */
	var axisbuilder_meta_boxes_builder_data = {

		col_size: [
			[ 'ab_one_full', '1/1', 1.00 ], [ 'ab_four_fifth', '4/5', 0.80 ], [ 'ab_three_fourth', '3/4', 0.75 ], [ 'ab_two_third', '2/3', 0.66 ], [ 'ab_three_fifth', '3/5', 0.60 ], [ 'ab_one_half', '1/2', 0.50 ], [ 'ab_two_fifth', '2/5', 0.40 ], [ 'ab_one_third', '1/3', 0.33 ], [ 'ab_one_fourth', '1/4', 0.25 ], [ 'ab_one_fifth', '1/5', 0.20 ]
		],

		cell_size: [
			[ 'ab_cell_one_full', '1/1', 1.00 ], [ 'ab_cell_four_fifth', '4/5', 0.80 ], [ 'ab_cell_three_fourth', '3/4', 0.75 ], [ 'ab_cell_two_third', '2/3', 0.66 ], [ 'ab_cell_three_fifth', '3/5', 0.60 ], [ 'ab_cell_one_half', '1/2', 0.50 ], [ 'ab_cell_two_fifth', '2/5', 0.40 ], [ 'ab_cell_one_third', '1/3', 0.33 ], [ 'ab_cell_one_fourth', '1/4', 0.25 ], [ 'ab_cell_one_fifth', '1/5', 0.20 ]
		],

		new_cell_order: [
			[ 'ab_cell_one_full', '1/1' ], [ 'ab_cell_one_half', '1/2' ], [ 'ab_cell_one_third', '1/3' ], [ 'ab_cell_one_fourth', '1/4' ], [ 'ab_cell_one_fifth', '1/5' ]
		],

		cell_size_variations: {
			4 : {
				1 : [ 'ab_cell_one_fourth', 'ab_cell_one_fourth', 'ab_cell_one_fourth', 'ab_cell_one_fourth' ],
				2 : [ 'ab_cell_one_fifth',  'ab_cell_one_fifth',  'ab_cell_one_fifth',  'ab_cell_two_fifth'  ],
				3 : [ 'ab_cell_one_fifth',  'ab_cell_one_fifth',  'ab_cell_two_fifth',  'ab_cell_one_fifth'  ],
				4 : [ 'ab_cell_one_fifth',  'ab_cell_two_fifth',  'ab_cell_one_fifth',  'ab_cell_one_fifth'  ],
				5 : [ 'ab_cell_two_fifth',  'ab_cell_one_fifth',  'ab_cell_one_fifth',  'ab_cell_one_fifth'  ]
			},
			3 : {
				1 : [ 'ab_cell_one_third',    'ab_cell_one_third',    'ab_cell_one_third'   ],
				2 : [ 'ab_cell_one_fourth',   'ab_cell_one_fourth',   'ab_cell_one_half'    ],
				3 : [ 'ab_cell_one_fourth',   'ab_cell_one_half',     'ab_cell_one_fourth'  ],
				4 : [ 'ab_cell_one_half',     'ab_cell_one_fourth',   'ab_cell_one_fourth'  ],
				5 : [ 'ab_cell_one_fifth',    'ab_cell_one_fifth',    'ab_cell_three_fifth' ],
				6 : [ 'ab_cell_one_fifth',    'ab_cell_three_fifth',  'ab_cell_one_fifth'   ],
				7 : [ 'ab_cell_three_fifth',  'ab_cell_one_fifth',    'ab_cell_one_fifth'   ],
				8 : [ 'ab_cell_one_fifth',    'ab_cell_two_fifth',    'ab_cell_two_fifth'   ],
				9 : [ 'ab_cell_two_fifth',    'ab_cell_one_fifth',    'ab_cell_two_fifth'   ],
				10: [ 'ab_cell_two_fifth',    'ab_cell_two_fifth',    'ab_cell_one_fifth'   ]
			},
			2 : {
				1 : [ 'ab_cell_one_half',     'ab_cell_one_half'     ],
				2 : [ 'ab_cell_two_third',    'ab_cell_one_third'    ],
				3 : [ 'ab_cell_one_third',    'ab_cell_two_third'    ],
				4 : [ 'ab_cell_one_fourth',   'ab_cell_three_fourth' ],
				5 : [ 'ab_cell_three_fourth', 'ab_cell_one_fourth'   ],
				6 : [ 'ab_cell_one_fifth',    'ab_cell_four_fifth'   ],
				7 : [ 'ab_cell_four_fifth',   'ab_cell_one_fifth'    ],
				8 : [ 'ab_cell_two_fifth',    'ab_cell_three_fifth'  ],
				9 : [ 'ab_cell_three_fifth',  'ab_cell_two_fifth'    ]
			}
		}
	};

	axisbuilder_meta_boxes_builder.init();
});
