/* global axisbuilder_admin_meta_boxes_builder */
jQuery( function( $ ) {

	/** Storage Handling */
	var $supports_html5_storage;
	try {
		$supports_html5_storage = ( 'sessionStorage' in window && window.sessionStorage !== null );

		window.sessionStorage.setItem( 'ab', 'test' );
		window.sessionStorage.removeItem( 'ab' );
	} catch( err ) {
		$supports_html5_storage = false;
	}

	/**
	 * Page Builder Panel
	 */
	var axisbuilder_meta_boxes_builder = {
		pagebuilder: null,
		init: function() {
			this.pagebuilder = $( '#axisbuilder-editor' ).find( ':input.axisbuilder-status' );

			this.storage.init();
			this.stupidtable.init();
			this.shortcode_interface();

			$( 'a.axisbuilder-toggle-editor' ).click( this.toggle_editor );

			$( '#axisbuilder-editor' )
				.on( 'click', '.insert-shortcode', this.add_element )
				.on( 'click', '.axisbuilder-edit', this.edit_element )
				.on( 'click', 'a.axisbuilder-clone', this.clone_element )
				.on( 'click', 'a.axisbuilder-trash', this.trash_element )

				// History
				.on( 'click', 'a.undo-data', this.storage.undo_data )
				.on( 'click', 'a.redo-data', this.storage.redo_data )

				// Trash data
				.on( 'click', 'a.trash-data', this.trash_data )

				// Resize Layout
				.on( 'click', 'a.axisbuilder-change-column-size:not(.axisbuilder-change-cell-size)', this.resize_layout )

				// Grid row cell
				.on( 'click', 'a.axisbuilder-cell-add', this.cell.add_cell )
				.on( 'click', 'a.axisbuilder-cell-set', this.cell.set_cell_size )

				// Recalc element
				.on( 'change', 'select.axisbuilder-recalculate-shortcode', this.select_changed );

			$( document.body )
				.on( 'keydown storage', this.storage.keyboard_actions )
				.on( 'axisbuilder_storage_snapshot', this.storage.snapshot )
				.on( 'axisbuilder_dragdrop_items_loaded', this.dragdrop.init )
				.on( 'axisbuilder_backbone_modal_loaded', this.backbone.init )
				.on( 'axisbuilder_backbone_modal_response', this.backbone.response );
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
			$( '#axisbuilder-editor, .axisbuilder-backbone-modal-article' ).unblock();
		},

		tinyMCE: function( content ) {
			if ( typeof window.tinyMCE !== 'undefined' ) {
				var editor = window.tinyMCE.get( 'content' );
				if ( editor ) {
					editor.setContent( window.switchEditors.wpautop( content ), { format: 'html' } );
				}
			}

			// Fallback WP tinyMCE editor html textarea
			$( '#content.wp-editor-area' ).val( content );
		},

		toggle_editor: function( e ) {
			e.preventDefault();

			// Prevent if page builder is disabled
			var button = $( this );
			if ( button.is( '.disabled' ) ) {
				return;
			}

			if ( axisbuilder_meta_boxes_builder.pagebuilder.val() !== 'active' ) {
				$( '#content-html' ).trigger( 'click' );
				$( '#axisbuilder-editor' ).removeClass( 'axisbuilder-hidden' );
				$( '#postdivrich' ).parent().addClass( 'axisbuilder-hidden-editor' );
				button.removeClass( 'button-primary' ).addClass( 'button-secondary' ).text( $( this ).data( 'editor' ) );
				axisbuilder_meta_boxes_builder.pagebuilder.val( 'active' );

				setTimeout( function() {
					$( '#content-tmce' ).trigger( 'click' );
					axisbuilder_meta_boxes_builder.shortcode_interface();
				}, 10 );
			} else {
				$( '#axisbuilder-editor' ).find( '.canvas-area' ).empty();
				$( '#axisbuilder-editor' ).addClass( 'axisbuilder-hidden' );
				$( '#postdivrich' ).parent().removeClass( 'axisbuilder-hidden-editor' );
				button.addClass( 'button-primary' ).removeClass( 'button-secondary' ).text( $( this ).data( 'builder' ) );
				axisbuilder_meta_boxes_builder.pagebuilder.val( 'inactive' );

				// Clear default tinyMCE editor if debug mode is disabled
				if ( axisbuilder_admin_meta_boxes_builder.debug_mode !== 'yes' && ( $( '.canvas-data' ).val().indexOf( '[' ) !== -1 ) ) {
					axisbuilder_meta_boxes_builder.tinyMCE( '' );
				}
			}

			// Auto resize WordPress editor
			if ( $( '#editor-expand-toggle' ).prop( 'checked' ) ) {
				window.editorExpand.off();
				window.editorExpand.on();
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
					$( '.canvas-area' ).empty();
					$( '.canvas-area' ).append( response );
					$( document.body ).trigger( 'axisbuilder_dragdrop_items_loaded' );
					// axisbuilder_meta_boxes_builder.textarea.outer(); // Don't update textarea on load, only when elements got edited.
					axisbuilder_meta_boxes_builder.storage.history_snapshot();
					axisbuilder_meta_boxes_builder.tiptip();
					axisbuilder_meta_boxes_builder.unblock();
					axisbuilder_meta_boxes_builder.stupidtable.init();
				}
			});
		},

		add_element: function() {
			var shortcode     = this.hash.replace( '#', '' ),
				element_tmpl  = $( '#tmpl-axisbuilder-' + shortcode ),
				insert_target = 'instant-insert'; // ( this.className.indexOf( 'axisbuilder-target-insert' ) !== -1 ) ? 'target_insert' : 'instant_insert',

			if ( element_tmpl.length ) {
				if ( insert_target === 'instant-insert' ) {
					$( '.canvas-area' ).append( element_tmpl.html() );
					$( document.body ).trigger( 'axisbuilder_dragdrop_items_loaded' );
					axisbuilder_meta_boxes_builder.textarea.outer();
					axisbuilder_meta_boxes_builder.storage.history_snapshot();
				}
			}

			return false;
		},

		edit_element: function() {
			var parents = $( this ).parents( '.axisbuilder-sortable-element:eq(0)' );
			if ( ! parents.length ) {
				parents = $( this ).parents( '.axisbuilder-layout-cell:eq(0)' );
				if ( ! parents.length ) {
					parents = $( this ).parents( '.axisbuilder-layout-section:eq(0)' );
				}
			}

			window.axisbuilder_shortcode = parents;

			// AxisBuilder Backbone Modal
			$( this ).AxisBuilderBackboneModal({
				title: parents.data( 'modal-title' ),
				template: '#tmpl-axisbuilder-modal-edit-element'
			});

			return false;
		},

		clone_element: function() {
			var element = $( this ).parents( '.axisbuilder-sortable-element:eq(0)' ), recalc_cell = false;
			if ( ! element.length ) {
				element = $( this ).parents( '.axisbuilder-layout-column:eq(0)' );
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

			// Update all textarea html with actual value
			element.find( 'textarea' ).each( function() {
				this.innerHTML = this.value;
			});

			var cloned  = element.clone(),
				wrapped = element.parents( '.axisbuilder-layout-section, .axisbuilder-layout-column' );

			// Remove all previous drag-drop classes so we can apply new ones
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

			$( document.body ).trigger( 'axisbuilder_dragdrop_items_loaded' );
			axisbuilder_meta_boxes_builder.textarea.outer();
			axisbuilder_meta_boxes_builder.storage.history_snapshot();

			return false;
		},

		trash_element: function() {
			var element = $( this ).parents( '.axisbuilder-sortable-element:eq(0)' ), parents = false, remove_cell = false, hide_timer = 200;
			if ( ! element.length ) {
				element = $( this ).parents( '.axisbuilder-layout-column:eq(0)' );
				parents = $( this ).parents( '.axisbuilder-layout-section:eq(0)>.axisbuilder-inner-shortcode' );
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

				// Bugfix - column delete makes the canvas undropbable for unknown reason
				if ( $( '.canvas-data' ).val() === '' ) {
					axisbuilder_meta_boxes_builder.dragdrop.droppable( '', 'destroy' );
				}

				axisbuilder_meta_boxes_builder.storage.history_snapshot();
			});

			return false;
		},

		trash_data: function() {
			var length = $( '.canvas-area' ).children().length;

			// Clear storage
			if ( length === 0 ) {
				axisbuilder_meta_boxes_builder.storage.clear_storage();
				axisbuilder_meta_boxes_builder.storage.history_snapshot();
			}

			// AxisBuilder Backbone Modal
			$( this ).AxisBuilderBackboneModal({
				title: axisbuilder_admin_meta_boxes_builder.i18n_trash_elements_title,
				message: ( length > 0 ) ? axisbuilder_admin_meta_boxes_builder.i18n_trash_elements_notice : axisbuilder_admin_meta_boxes_builder.i18n_trash_elements_least,
				dismiss: ( length > 0 ) ? false : true,
				template: '#tmpl-axisbuilder-modal-trash-data'
			});

			return false;
		},

		resize_layout: function() {
			var direction    = $( this ).is( '.axisbuilder-increase' ) ? 1 : -1,
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
				column.attr( 'data-width', next_size[0] ).data( 'width', next_size[0] );

				// Change the column size text
				size_string.text( next_size[1] );

				// Textarea Update and History snapshot :)
				axisbuilder_meta_boxes_builder.textarea.outer();
				if ( section.length ) {
					axisbuilder_meta_boxes_builder.textarea.inner( false, section );
					axisbuilder_meta_boxes_builder.textarea.outer();
				}
				axisbuilder_meta_boxes_builder.storage.history_snapshot();
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

			// If we got a string value passed insert the string, otherwise calculate the shortcode
			if ( typeof values === 'string' ) {
				output = values;
			} else {
				var extract_html = axisbuilder_meta_boxes_builder.update_builder_html( element_container, values );

				output = extract_html.output;
				tags   = extract_html.tags;
			}

			// If we are working inside a section or cell just update the shortcode open tag else update everything
			if ( element_container.is( '.axisbuilder-layout-section' ) || element_container.is( '.axisbuilder-layout-cell' ) ) {
				save_data.val( save_data.val().replace( new RegExp( '^\\[' + shortcode + '.*?\\]' ), tags.open ) );
			} else {
				save_data.val( output );
			}

			// Update the Section and column inner textarea
			if ( section.length ) {
				axisbuilder_meta_boxes_builder.textarea.inner( false, section );
			} else if ( column.length ) {
				axisbuilder_meta_boxes_builder.textarea.inner( false, column );
			}

			axisbuilder_meta_boxes_builder.textarea.outer();
			axisbuilder_meta_boxes_builder.storage.history_snapshot();
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

				var content        = '',
					size_count     = 0,
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

					content += current_content;
				}

				$( '.canvas-data' ).val( content );

				// Slows the whole process considerably
				var timer = false;
				clearTimeout( timer );
				timer = setTimeout( function() {
					axisbuilder_meta_boxes_builder.tinyMCE( content );
				}, 500 );
			}
		},

		dragdrop: {

			init: function() {
				axisbuilder_meta_boxes_builder.dragdrop.draggable();
				axisbuilder_meta_boxes_builder.dragdrop.droppable();
			},

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

							template = $( $( '#tmpl-axisbuilder-' + shortcode ).html() );
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
							$( document.body ).trigger( 'axisbuilder_dragdrop_items_loaded' );
						}

						// History Snapshot
						axisbuilder_meta_boxes_builder.storage.history_snapshot();
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
				var $row                 = $( 'a.axisbuilder-cell-set' ).parents( '.axisbuilder-layout-row:eq(0)' ),
					cells                = $row.find( '.axisbuilder-layout-cell' ),
					cell_size            = axisbuilder_meta_boxes_builder_data.cell_size,
					cell_size_variations = axisbuilder_meta_boxes_builder_data.cell_size_variations[cells.length], notification = '';

				// Create cell size lists
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

						notification += '<div class="axisbuilder-layout-row-modal"><label class="axisbuilder-layout-row-modal-label"><input type="radio" id="add_cell_size_' + x + '" name="add_cell_size" value="' + x + '" /><span class="axisbuilder-layout-row-inner-label">' + label + '</span></label></div>';
					}
				} else {
					notification += axisbuilder_admin_meta_boxes_builder.i18n_no_layout + '<br />';
					notification += ( cells.length === 1 ) ? '<mark class="yes">' + axisbuilder_admin_meta_boxes_builder.i18n_add_one_cell + '</mark>' : '<mark class="no">' + axisbuilder_admin_meta_boxes_builder.i18n_remove_one_cell + '</mark>';
				}

				// AxisBuilder Backbone Modal
				$( this ).AxisBuilderBackboneModal({
					title: axisbuilder_admin_meta_boxes_builder.i18n_select_cell_layout,
					message: notification,
					dismiss: cell_size_variations ? false : true,
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
					axisbuilder_meta_boxes_builder.storage.history_snapshot();
				}
			},

			insert_cell: function( row, newEl ) {
				var storage   = row.find( '> .axisbuilder-inner-shortcode' ),
					shortcode = newEl[0].replace( 'ab_cell_', 'ab_shortcode_cells_' ).replace( '_one_full', '' ),
					cell_tmpl = $( $( '#tmpl-axisbuilder-' + shortcode ).html() );

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

			init: function( e, target ) {
				if ( '#tmpl-axisbuilder-modal-edit-element' === target ) {
					$( document.body ).trigger( 'axisbuilder-enhanced-select-init' );
					axisbuilder_meta_boxes_builder.backbone.init_edit_element();
				}
			},

			block: function() {
				$( '.axisbuilder-backbone-modal-article' ).block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
			},

			enable: function() {
				$( '.axisbuilder-backbone-modal-content' ).find( 'button' ).removeAttr( 'disabled' );
			},

			disable: function() {
				$( '.axisbuilder-backbone-modal-content' ).find( 'button' ).attr( 'disabled', 'disabled' );
			},

			dismiss: function() {
				$( '.axisbuilder-backbone-modal-content' ).find( 'p' ).append( axisbuilder_admin_meta_boxes_builder.i18n_backbone_loading_falied );
				$( '.axisbuilder-backbone-modal-footer .inner' ).find( 'button' ).removeAttr( 'id' ).removeClass( 'button-primary' ).addClass( 'button-secondary modal-close' ).text( axisbuilder_admin_meta_boxes_builder.i18n_backbone_dismiss_button );
			},

			init_edit_element: function() {
				var parents = window.axisbuilder_shortcode;

				axisbuilder_meta_boxes_builder.backbone.block();
				axisbuilder_meta_boxes_builder.backbone.disable();

				var data = {
					fetch: true,
					params: {
						extract: true,
						shortcode: parents.find( '> .axisbuilder-inner-shortcode > textarea[data-name="text-shortcode"]:eq(0)' ).val()
					},
					action: 'axisbuilder_' + parents.data( 'modal-action' ),
					security: axisbuilder_admin_meta_boxes_builder.modal_item_nonce
				};

				$.ajax({
					url:  axisbuilder_admin_meta_boxes_builder.ajax_url,
					data: data,
					type: 'POST',
					success: function( response ) {

						// Login(0) and session(-1) error response xD
						if ( response === '0' || response === '-1' ) {
							axisbuilder_meta_boxes_builder.backbone.dismiss();
						} else {
							$( '.axisbuilder-backbone-modal-article form' ).empty();
							$( '.axisbuilder-backbone-modal-article form' ).append( response );

							// Trigger Event
							$( document.body ).trigger( 'axisbuilder-enhanced-modal-elements-init' );
						}

						axisbuilder_meta_boxes_builder.tiptip();
						axisbuilder_meta_boxes_builder.unblock();
						axisbuilder_meta_boxes_builder.backbone.enable();
						axisbuilder_meta_boxes_builder.stupidtable.init();
					}
				});
			},

			response: function( e, target, data ) {
				if ( '#tmpl-axisbuilder-modal-trash-data' === target ) {
					axisbuilder_meta_boxes_builder.backbone.trash_data();
				}
				if ( '#tmpl-axisbuilder-modal-cell-size' === target ) {
					axisbuilder_meta_boxes_builder.backbone.cell_size( data.add_cell_size );
				}
				if ( '#tmpl-axisbuilder-modal-edit-element' === target ) {
					axisbuilder_meta_boxes_builder.backbone.edit_element( data );
				}
			},

			trash_data: function() {
				$( '.canvas-area' ).empty();
				axisbuilder_meta_boxes_builder.textarea.outer();
				axisbuilder_meta_boxes_builder.storage.clear_storage();
				axisbuilder_meta_boxes_builder.storage.history_snapshot();
			},

			cell_size: function( add_cell_size ) {
				var $row                 = $( 'a.axisbuilder-cell-set' ).parents( '.axisbuilder-layout-row:eq(0)' ),
					cells                = $row.find( '.axisbuilder-layout-cell' ),
					cell_size_variations = axisbuilder_meta_boxes_builder_data.cell_size_variations[cells.length];

				if ( add_cell_size ) {
					axisbuilder_meta_boxes_builder.cell.change_multiple_cell_size( cells, cell_size_variations[add_cell_size], true );
					axisbuilder_meta_boxes_builder.textarea.inner( false, $row );
					axisbuilder_meta_boxes_builder.textarea.outer();
					axisbuilder_meta_boxes_builder.storage.history_snapshot();
				}
			},

			edit_element: function( data ) {
				var parents = window.axisbuilder_shortcode;
				axisbuilder_meta_boxes_builder.send_to_datastorage( data, parents );
			}
		},

		storage: {

			init: function() {
				this.storage = this.get_key() || [];
				this.maximum = this.storage.length - 1;

				// Temporary storage index
				this.temporary = this.get_key( this.set_key() + '-temp' );
				if ( typeof this.temporary === 'undefined' || this.temporary === null ) {
					this.temporary = this.maximum;
				}

				// Clear storage
				this.clear_storage();
			},

			set_key: function() {
				return ( 'axisbuilder-storage-' + axisbuilder_admin_meta_boxes_builder.post_id ).toLowerCase();
			},

			get_key: function( passed_key ) {
				var history = axisbuilder_meta_boxes_builder.storage;
				return $.parseJSON( sessionStorage.getItem( passed_key || history.set_key() ) );
			},

			undo_data: function( e ) {
				e.preventDefault();
				var history = axisbuilder_meta_boxes_builder.storage;
				if ( ( history.temporary - 1 ) >= 0 ) {
					history.temporary --;
					history.canvas_update( history.storage[ history.temporary ] );
				}
			},

			redo_data: function( e ) {
				e.preventDefault();
				var history = axisbuilder_meta_boxes_builder.storage;
				if ( ( history.temporary + 1 ) <= history.maximum ) {
					history.temporary ++;
					history.canvas_update( history.storage[ history.temporary ] );
				}
			},

			canvas_update: function( values ) {
				var history = axisbuilder_meta_boxes_builder.storage;

				$( '.canvas-data' ).val( values[0] );
				$( '.canvas-area' ).html( values[1] );
				axisbuilder_meta_boxes_builder.tinyMCE( values[0] );
				sessionStorage.setItem( history.set_key() + '-temp', history.temporary );

				// Undo button
				if ( history.temporary <= 0 ) {
					$( '.undo-data' ).addClass( 'inactive-history' );
				} else {
					$( '.undo-data' ).removeClass( 'inactive-history' );
				}

				// Redo button
				if ( history.temporary + 1 > history.maximum ) {
					$( '.redo-data' ).addClass( 'inactive-history' );
				} else {
					$( '.redo-data' ).removeClass( 'inactive-history' );
				}

				$( document.body ).trigger( 'axisbuilder_dragdrop_items_loaded' );
			},

			snapshot: function() {
				// Update all textarea html with actual value
				$( '.canvas-area' ).find( 'textarea' ).each( function() {
					this.innerHTML = this.value;
				});

				var history = axisbuilder_meta_boxes_builder.storage;
				if ( typeof history.temporary === 'undefined' || history.temporary === null ) {
					history.temporary = history.storage.length - 1;
				}

				var last_storage = history.storage[ history.temporary ],
					new_snapshot = [ $( '.canvas-data' ).val(), $( '.canvas-area' ).html().replace( /modal-animation/g, '' ) ];

				// Create new snapshot
				if ( typeof last_storage === 'undefined' || ( last_storage[0] !== new_snapshot[0] ) ) {
					history.temporary ++;

					history.storage = history.storage.slice( 0, history.temporary );
					history.storage.push( new_snapshot );

					if ( history.storage.length > 40 ) {
						history.storage.shift();
					}

					try {
						sessionStorage.setItem( history.set_key(), JSON.stringify( history.storage ) );
					} catch( err ) {
						axisbuilder_meta_boxes_builder.storage.clear_storage();
						$( '.undo-data, .redo-data' ).addClass( 'inactive-history' );
					}
				}

				history.maximum = history.storage.length - 1;

				// Undo button
				if ( history.temporary === 0 || history.storage.length === 1 ) {
					$( '.undo-data' ).addClass( 'inactive-history' );
				} else {
					$( '.undo-data' ).removeClass( 'inactive-history' );
				}

				// Redo button
				if ( history.maximum === history.temporary ) {
					$( '.redo-data' ).addClass( 'inactive-history' );
				} else {
					$( '.redo-data' ).removeClass( 'inactive-history' );
				}
			},

			clear_storage: function() {
				var history = axisbuilder_meta_boxes_builder.storage;
				sessionStorage.removeItem( history.set_key() );
				sessionStorage.removeItem( history.set_key() + '-temp' );

				// Reset huh?
				history.storage   = [];
				history.temporary = null;
			},

			history_snapshot: function( timeout ) {
				setTimeout( function() {
					$( document.body ).trigger( 'axisbuilder_storage_snapshot' );
				}, timeout ? timeout : 150 );
			},

			keyboard_actions: function( e ) {
				var	button     = e.keyCode || e.which,
					controlled = e.ctrlKey || e.metaKey;

				// Ctrl+z key
				if ( 90 === button && controlled && ! e.shiftKey && ! e.altKey ) {
					axisbuilder_meta_boxes_builder.storage.undo_data( e );
				}

				// Ctrl+y key
				if ( 89 === button && controlled && ! e.shiftKey && ! e.altKey ) {
					axisbuilder_meta_boxes_builder.storage.redo_data( e );
				}
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
