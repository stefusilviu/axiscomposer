/* global axiscomposer_admin_meta_boxes_pagebuilder */
jQuery( function( $ ) {

	/** Storage Handling */
	var $supports_html5_storage;
	try {
		$supports_html5_storage = ( 'sessionStorage' in window && window.sessionStorage !== null );

		window.sessionStorage.setItem( 'ac', 'test' );
		window.sessionStorage.removeItem( 'ac' );
	} catch( err ) {
		$supports_html5_storage = false;
	}

	/**
	 * Page Builder Panel
	 */
	var ac_meta_boxes_pagebuilder = {
		pagebuilder: null,
		init: function() {
			this.pagebuilder = $( '#axiscomposer-pagebuilder' ).find( ':input.pagebuilder-status' );

			this.storage.init();
			this.stupidtable.init();
			this.shortcode_interface();

			$( 'a.axiscomposer-toggle-editor' ).click( this.toggle_editor );

			$( '#axiscomposer-pagebuilder' )
				.on( 'click', '.insert-shortcode', this.add_element )
				.on( 'click', '.axiscomposer-edit', this.edit_element )
				.on( 'click', 'a.axiscomposer-clone', this.clone_element )
				.on( 'click', 'a.axiscomposer-trash', this.trash_element )

				// History
				.on( 'click', 'a.undo-data', this.storage.undo_data )
				.on( 'click', 'a.redo-data', this.storage.redo_data )

				// Trash data
				.on( 'click', 'a.trash-data', this.trash_data )

				// Resize Layout
				.on( 'click', 'a.ac-change-column-size:not(.ac-change-cell-size)', this.resize_layout )

				// Grid row cell
				.on( 'click', 'a.axiscomposer-cell-add', this.cell.add_cell )
				.on( 'click', 'a.axiscomposer-cell-set', this.cell.set_cell_size )

				// Recalc element
				.on( 'change', 'select.ac-recalc-shortcode', this.select_changed );

			$( document.body )
				.on( 'keydown storage', this.storage.keyboard_actions )
				.on( 'ac_storage_snapshot', this.storage.snapshot )
				.on( 'ac_dragdrop_items_loaded', this.dragdrop.init )
				.on( 'ac_backbone_modal_loaded', this.backbone.init )
				.on( 'ac_backbone_modal_response', this.backbone.response );
		},

		tiptip: function() {
			$( '#tiptip_holder' ).removeAttr( 'style' );
			$( '#tiptip_arrow' ).removeAttr( 'style' );
			$( '.tips, .help_tip' ).tipTip({
				'attribute': 'data-tip',
				'fadeIn': 50,
				'fadeOut': 50,
				'delay': 200
			});
		},

		block: function() {
			$( '#axiscomposer-pagebuilder' ).block({
				message: null,
				overlayCSS: {
					background: '#fff',
					opacity: 0.6
				}
			});
		},

		unblock: function() {
			$( '#axiscomposer-pagebuilder, .ac-enhanced-settings' ).unblock();
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

			if ( ac_meta_boxes_pagebuilder.pagebuilder.val() !== 'active' ) {
				$( '#content-html' ).trigger( 'click' );
				$( '#axiscomposer-pagebuilder' ).removeClass( 'ac-hidden' );
				$( '#postdivrich' ).parent().addClass( 'ac-hidden-editor' );
				button.removeClass( 'button-primary' ).addClass( 'button-secondary' ).text( $( this ).data( 'editor' ) );
				ac_meta_boxes_pagebuilder.pagebuilder.val( 'active' );

				setTimeout( function() {
					$( '#content-tmce' ).trigger( 'click' );
					ac_meta_boxes_pagebuilder.shortcode_interface();
				}, 10 );
			} else {
				$( '#axiscomposer-pagebuilder' ).find( '.canvas-area' ).empty();
				$( '#axiscomposer-pagebuilder' ).addClass( 'ac-hidden' );
				$( '#postdivrich' ).parent().removeClass( 'ac-hidden-editor' );
				button.addClass( 'button-primary' ).removeClass( 'button-secondary' ).text( $( this ).data( 'builder' ) );
				ac_meta_boxes_pagebuilder.pagebuilder.val( 'inactive' );

				// Clear default tinyMCE editor if debug mode is disabled
				if ( axiscomposer_admin_meta_boxes_pagebuilder.debug_mode !== 'yes' && ( $( '.canvas-data' ).val().indexOf( '[' ) !== -1 ) ) {
					ac_meta_boxes_pagebuilder.tinyMCE( '' );
				}
			}

			// Auto resize WordPress editor
			$( document.body ).trigger( 'ac-init-wp-editor' );
		},

		shortcode_interface: function( text ) {
			// Prevent if we don't have the pagebuilder active
			if ( ac_meta_boxes_pagebuilder.pagebuilder.val() !== 'active' ) {
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
				action: 'axiscomposer_shortcodes_to_interface'
			};

			ac_meta_boxes_pagebuilder.block();

			$.ajax({
				url: axiscomposer_admin_meta_boxes_pagebuilder.ajax_url,
				data: data,
				type: 'POST',
				success: function( response ) {
					$( '.canvas-area' ).empty();
					$( '.canvas-area' ).append( response );
					$( document.body ).trigger( 'ac_dragdrop_items_loaded' );
					// ac_meta_boxes_pagebuilder.textarea.outer(); // Don't update textarea on load, only when elements got edited.
					ac_meta_boxes_pagebuilder.storage.history_snapshot();
					ac_meta_boxes_pagebuilder.tiptip();
					ac_meta_boxes_pagebuilder.unblock();
					ac_meta_boxes_pagebuilder.stupidtable.init();
				}
			});
		},

		add_element: function() {
			var shortcode     = this.hash.replace( '#', '' ),
				element_tmpl  = $( '#tmpl-axiscomposer-' + shortcode ),
				insert_target = 'instant-insert'; // ( this.className.indexOf( 'axisbuilder-target-insert' ) !== -1 ) ? 'target_insert' : 'instant_insert',

			if ( element_tmpl.length ) {
				if ( insert_target === 'instant-insert' ) {
					$( '.canvas-area' ).append( element_tmpl.html() );
					$( document.body ).trigger( 'ac_dragdrop_items_loaded' );
					ac_meta_boxes_pagebuilder.textarea.outer();
					ac_meta_boxes_pagebuilder.storage.history_snapshot();
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

			window.ac_shortcode = parents;

			// AxisComposer Backbone Modal
			$( this ).ACBackboneModal({
				title: parents.data( 'modal-title' ),
				template: '#tmpl-axiscomposer-modal-edit-element'
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
				if ( typeof ac_meta_boxes_pagebuilder_data.new_cell_order[count] !== 'undefined' ) {
					recalc_cell = true;
				} else {
					return false;
				}
			}

			// Update all textarea html with actual value
			element.find( 'textarea' ).each( function() {
				this.innerHTML = this.value;
			});

			// Clone and insert an element
			element.clone().insertAfter( element );

			if ( recalc_cell ) {
				ac_meta_boxes_pagebuilder.cell.modify_cell_count( this, -1 );
			}

			var wrapper = element.parents( '.axisbuilder-layout-section, .axisbuilder-layout-column' );
			if ( element.is( '.axisbuilder-layout-section' ) || element.is( '.axisbuilder-layout-column' ) || wrapper.length ) {
				if ( wrapper.length ) {
					ac_meta_boxes_pagebuilder.textarea.outer();
					ac_meta_boxes_pagebuilder.textarea.inner( element );
				}
			}

			ac_meta_boxes_pagebuilder.textarea.outer();
			ac_meta_boxes_pagebuilder.storage.history_snapshot();
			$( document.body ).trigger( 'ac_dragdrop_items_loaded' );
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
					ac_meta_boxes_pagebuilder.cell.modify_cell_count( this, -2 );
				}

				element.remove();

				if ( parents && parents.length ) {
					ac_meta_boxes_pagebuilder.textarea.inner( parents );
				}

				ac_meta_boxes_pagebuilder.textarea.outer();
				ac_meta_boxes_pagebuilder.storage.history_snapshot();

				// Bugfix - column delete makes the canvas undroppable for unknown reason
				if ( $( '.canvas-data' ).val() === '' ) {
					$( '.ac-drop' ).droppable( 'destroy' );
					$( document.body ).trigger( 'ac_dragdrop_items_loaded' );
				}
			});

			return false;
		},

		trash_data: function() {
			var length = $( '.canvas-area' ).children().length;

			// Clear storage
			if ( length === 0 ) {
				ac_meta_boxes_pagebuilder.storage.clear_storage();
				ac_meta_boxes_pagebuilder.storage.history_snapshot();
			}

			// AxisComposer Backbone Modal
			$( this ).ACBackboneModal({
				title: axiscomposer_admin_meta_boxes_pagebuilder.i18n_trash_elements_title,
				message: ( length > 0 ) ? axiscomposer_admin_meta_boxes_pagebuilder.i18n_trash_elements_notice : axiscomposer_admin_meta_boxes_pagebuilder.i18n_trash_elements_least,
				dismiss: ( length > 0 ) ? false : true,
				template: '#tmpl-axiscomposer-modal-trash-data'
			});

			return false;
		},

		resize_layout: function() {
			var direction    = $( this ).is( '.ac-increase' ) ? 1 : -1,
				column       = $( this ).parents( '.axisbuilder-layout-column:eq(0)' ),
				section      = column.parents( '.axisbuilder-layout-section:eq(0)' ),
				size_string  = column.find( '.axisbuilder-column-size' ),
				data_storage = column.find( '.axisbuilder-inner-shortcode > textarea[data-name="text-shortcode"]' ),
				data_string  = data_storage.val(),
				next_size    = [],
				column_size  = ac_meta_boxes_pagebuilder_data.col_size,
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
				ac_meta_boxes_pagebuilder.textarea.outer();
				if ( section.length ) {
					ac_meta_boxes_pagebuilder.textarea.inner( false, section );
					ac_meta_boxes_pagebuilder.textarea.outer();
				}
				ac_meta_boxes_pagebuilder.storage.history_snapshot();
			}

			return false;
		},

		select_changed: function() {
			var container = $( this ).parents( '.axisbuilder-sortable-element:eq(0)' );
			ac_meta_boxes_pagebuilder.recalc_element( container );
			return false;
		},

		recalc_element: function( element_container ) {
			var values  = [],
				current = false,
				recalcs = element_container.find( 'select.ac-recalc-shortcode' );

			for ( var i = recalcs.length - 1; i >= 0; i-- ) {
				current = $( recalcs[i] );
				values[current.data( 'attr' )] = current.val();
			}

			ac_meta_boxes_pagebuilder.send_to_datastorage( values, element_container );
		},

		send_to_datastorage: function( values, element_container ) {
			var column    = element_container.parents( '.axisbuilder-layout-column:eq(0)' ),
				section   = element_container.parents( '.axisbuilder-layout-section:eq(0)' ),
				selector  = element_container.is( '.ac-modal-group-element' ) ? ( 'textarea[data-name="text-shortcode"]:eq(0)' ) : ( '> .axisbuilder-inner-shortcode >textarea[data-name="text-shortcode"]:eq(0)' ),
				save_data = element_container.find( selector ),
				shortcode = element_container.data( 'shortcode-handler' ), output = '', tags = {};

			// If we got a string value passed insert the string, otherwise calculate the shortcode
			if ( typeof values === 'string' ) {
				output = values;
			} else {
				var extract_html = ac_meta_boxes_pagebuilder.update_builder_html( element_container, values );

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
				ac_meta_boxes_pagebuilder.textarea.inner( false, section );
			} else if ( column.length ) {
				ac_meta_boxes_pagebuilder.textarea.inner( false, column );
			}

			ac_meta_boxes_pagebuilder.textarea.outer();
			ac_meta_boxes_pagebuilder.storage.history_snapshot();
			element_container.trigger( 'update' );
		},

		update_builder_html: function( element_container, values, force_content_close ) {
			var key, subkey, new_key, old_val;

			// Filter keys for the 'axiscomposerTB-' string prefix and re-modify the key that was edited.
			for ( key in values ) {
				if ( values.hasOwnProperty( key ) ) {
					new_key = key.replace( /axiscomposerTB-/g, '' );
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
			extract_html.output = ac_meta_boxes_pagebuilder.shortcode_string( values, shortcode, tags, force_content_close );

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
						ac_meta_boxes_pagebuilder.textarea.inner( false, $( columns[i] ) );
					}

					columns = container.find( '.axisbuilder-layout-cell' );
					for ( i = 0; i < columns.length; i++ ) {
						ac_meta_boxes_pagebuilder.textarea.inner( false, $( columns[i] ) );
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
				if ( ac_meta_boxes_pagebuilder.pagebuilder.val() !== 'active' ) {
					return;
				}

				if ( ! scope ) {
					$( '.canvas-area' ).find( '.axisbuilder-layout-section' ).each( function() {
						var col_in_section   = $( this ).find( '>.axisbuilder-inner-shortcode > div > .axisbuilder-inner-shortcode' ),
							col_in_grid_cell = $( this ).find( '.axisbuilder-layout-cell .axisbuilder-layout-column-no-cell > .axisbuilder-inner-shortcode' );

						if ( col_in_section.length ) {
							ac_meta_boxes_pagebuilder.textarea.outer( col_in_section );
						}

						if ( col_in_grid_cell.length ) {
							ac_meta_boxes_pagebuilder.textarea.outer( col_in_grid_cell );
						}
					});

					scope = $( '.ac-data > div > .axisbuilder-inner-shortcode' );
				}

				var content        = '',
					size_count     = 0,
					column_size    = ac_meta_boxes_pagebuilder_data.col_size,
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
					ac_meta_boxes_pagebuilder.tinyMCE( content );
				}, 500 );
			}
		},

		dragdrop: {

			init: function() {
				ac_meta_boxes_pagebuilder.dragdrop.disable();
				ac_meta_boxes_pagebuilder.dragdrop.draggable();
				ac_meta_boxes_pagebuilder.dragdrop.droppable();
			},

			disable: function() {
				$( '#axiscomposer-pagebuilder' ).find( '.ui-draggable, .ui-droppable' ).removeClass( 'ui-draggable ui-droppable' );
			},

			is_droppable: function( draggable, droppable ) {
				return draggable.data( 'dragdrop-level' ) > droppable.data( 'dragdrop-level' );
			},

			draggable: function() {
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
						$( '.ac-hover-active' ).removeClass( 'ac-hover-active' );
						$( '.canvas-area' ).addClass( 'ac-select-target-' + current.data( 'dragdrop-level' ) );
					},
					stop: function( event ) {
						$( event.target ).css({ opacity: 1 });
						$( '.ac-hover-active' ).removeClass( 'ac-hover-active' );
						$( '.canvas-area' ).removeClass( 'ac-select-target-1 ac-select-target-2 ac-select-target-3 ac-select-target-4' );
					}
				};

				// Draggable
				$( '#axiscomposer-pagebuilder' ).find( '.ac-drag' ).not( '.ui-draggable' ).draggable( data );
				$( '#axiscomposer-pagebuilder' ).find( '.insert-shortcode' ).not( '.ui-draggable' ).draggable(
					$.extend( {}, data, {
						handle: false,
						cursorAt: {
							top: 33,
							left: 33
						}
					})
				);
			},

			droppable: function() {
				var data = {
					greedy: true,
					tolerance: 'pointer',
					over: function( event, ui ) {
						var droppable = $( this );
						if ( ac_meta_boxes_pagebuilder.dragdrop.is_droppable( ui.helper, droppable ) ) {
							droppable.addClass( 'ac-hover-active' );
						}
					},
					out: function() {
						$( this ).removeClass( 'ac-hover-active' );
					},
					drop: function( event, ui ) {
						var droppable = $( this );
						if ( ! droppable.is( '.ac-hover-active' ) ) {
							return;
						}

						var elements = droppable.find( '>.ac-drag' ), template = {}, offset = {}, method = 'after', toEl = false, position_array = [], last_pos, max_height, i;

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

							template = $.parseHTML( $( '#tmpl-axiscomposer-' + shortcode ).html() );
							ui.draggable = template;
						}

						// Move the real draggable element to the new position
						toEl[ method ]( ui.draggable );

						// Before finally moving the element, save the former parent of the draggable to a var so we can check later if we need to update the parent as well
						var formerParent = $( ui.draggable ).parents( '.ac-drag:last' );

						// If the element got a former parent we need to update that as well
						if ( formerParent.length ) {
							ac_meta_boxes_pagebuilder.textarea.inner( false, formerParent );
						}

						// Get the element that the new element was inserted into. This has to be the parent of the current toEL since we usually insert the new element outside of the toEL with the 'after' method
						// If method !== 'after' the element was inserted with prepend directly to the toEL and toEL should therefore also the insertedInto element :)
						var insertedInto = ( method === 'after' ) ? toEl.parents( '.ac-drop' ) : toEl;

						if ( insertedInto.data( 'dragdrop-level' ) !== 0 ) {
							ac_meta_boxes_pagebuilder.textarea.outer(); // <-- actually only necessary because of column first class. optimize that so we can remove the costly function of updating all elements :)
							ac_meta_boxes_pagebuilder.textarea.inner( ui.draggable );
						}

						// Everything is fine, now do the re sorting and textarea updating
						ac_meta_boxes_pagebuilder.textarea.outer();

						// Apply dragging and dropping in case we got a new element
						if ( typeof template !== 'undefined' ) {
							$( document.body ).trigger( 'ac_dragdrop_items_loaded' );
						}

						// History Snapshot
						ac_meta_boxes_pagebuilder.storage.history_snapshot();
					}
				};

				// Droppable
				$( '#axiscomposer-pagebuilder' ).find( '.ac-drop' ).not( '.ui-droppable' ).droppable( data );
			}
		},

		cell: {

			add_cell: function() {
				ac_meta_boxes_pagebuilder.cell.modify_cell_count( this, 0 );
				return false;
			},

			set_cell_size: function() {
				var $row                 = $( this ).parents( '.axisbuilder-layout-row:eq(0)' ),
					cells                = $row.find( '.axisbuilder-layout-cell' ),
					cell_size            = ac_meta_boxes_pagebuilder_data.cell_size,
					cell_size_variations = ac_meta_boxes_pagebuilder_data.cell_size_variations[cells.length], notification = '';

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

							label += '<span class="ac-modal-label ' + cell_size_variations[x][y] + '">' + labeltext + '</span>';
						}

						notification += '<div class="ac-layout-row-modal"><label class="ac-layout-row-modal-label"><input type="radio" id="add_cell_size_' + x + '" name="add_cell_size" value="' + x + '" /><span class="ac-layout-row-inner-label">' + label + '</span></label></div>';
					}
				} else {
					notification += axiscomposer_admin_meta_boxes_pagebuilder.i18n_no_layout + '<br />';
					notification += ( cells.length === 1 ) ? '<mark class="yes">' + axiscomposer_admin_meta_boxes_pagebuilder.i18n_add_one_cell + '</mark>' : '<mark class="no">' + axiscomposer_admin_meta_boxes_pagebuilder.i18n_remove_one_cell + '</mark>';
				}

				// AxisComposer Backbone Modal
				$( this ).ACBackboneModal({
					title: axiscomposer_admin_meta_boxes_pagebuilder.i18n_select_cell_layout,
					message: notification,
					dismiss: cell_size_variations ? false : true,
					template: '#tmpl-axiscomposer-modal-cell-size'
				});

				return false;
			},

			modify_cell_count: function( clicked, direction ) {
				var $row  = $( clicked ).parents( '.axisbuilder-layout-row:eq(0)' ),
					cells = $row.find( '.axisbuilder-layout-cell' ),
					count = ( cells.length + direction ),
					newEl = ac_meta_boxes_pagebuilder_data.new_cell_order[count];

				if ( typeof newEl !== 'undefined' ) {
					ac_meta_boxes_pagebuilder.cell.change_multiple_cell_size( cells, newEl );

					// Check if we can append cells
					if ( cells.length === count ) {
						var cell_tmpl = $( '#tmpl-axiscomposer-' + newEl[0].replace( 'ac_cell_', 'ac_shortcode_cells_' ).replace( '_one_full', '' ) );
						if ( cell_tmpl.length ) {
							$row.find( '> .axisbuilder-inner-shortcode' ).append( cell_tmpl.html() );
							$( document.body ).trigger( 'ac_dragdrop_items_loaded' );
						}
					}

					ac_meta_boxes_pagebuilder.textarea.inner( false, $row );
					ac_meta_boxes_pagebuilder.textarea.outer();
					ac_meta_boxes_pagebuilder.storage.history_snapshot();
				}
			},

			change_multiple_cell_size: function( cells, newEl, multi ) {
				var key       = '',
					next_size = newEl,
					cell_size = ac_meta_boxes_pagebuilder_data.cell_size;

				cells.each( function( i ) {
					if ( multi ) {
						key = newEl[i];
						for ( var x in cell_size ) {
							if ( key === cell_size[x][0] ) {
								next_size = cell_size[x];
							}
						}
					}

					ac_meta_boxes_pagebuilder.cell.change_single_cell_size( $( this ), next_size );
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
				if ( '#tmpl-axiscomposer-modal-edit-element' === target ) {
					ac_meta_boxes_pagebuilder.backbone.init_edit_element();
				}
			},

			block: function() {
				$( '.ac-enhanced-settings' ).block({
					message: null,
					overlayCSS: {
						background: '#fff',
						opacity: 0.6
					}
				});
			},

			enable: function() {
				$( '.ac-backbone-modal-content' ).find( 'button' ).removeAttr( 'disabled' );
			},

			disable: function() {
				$( '.ac-backbone-modal-content' ).find( 'button' ).attr( 'disabled', 'disabled' );
			},

			dismiss: function() {
				$( '.ac-backbone-modal-content' ).find( 'p' ).append( axiscomposer_admin_meta_boxes_pagebuilder.i18n_backbone_loading_falied );
				$( '.ac-backbone-modal-footer .inner' ).find( 'button' ).removeAttr( 'id' ).removeClass( 'button-primary' ).addClass( 'button-secondary modal-close' ).text( axiscomposer_admin_meta_boxes_pagebuilder.i18n_backbone_dismiss_button );
			},

			init_edit_element: function() {
				var parents = window.ac_shortcode;

				ac_meta_boxes_pagebuilder.backbone.block();
				ac_meta_boxes_pagebuilder.backbone.disable();

				var data = {
					fetch: true,
					params: {
						extract: true,
						shortcode: parents.find( '> .axisbuilder-inner-shortcode > textarea[data-name="text-shortcode"]:eq(0)' ).val()
					},
					action: 'axiscomposer_' + parents.data( 'modal-action' ),
					security: axiscomposer_admin_meta_boxes_pagebuilder.modal_item_nonce
				};

				$.ajax({
					url:  axiscomposer_admin_meta_boxes_pagebuilder.ajax_url,
					data: data,
					type: 'POST',
					success: function( response ) {

						// Login(0) and session(-1) error response xD
						if ( response === '0' || response === '-1' ) {
							ac_meta_boxes_pagebuilder.backbone.dismiss();
						} else {
							$( '.ac-backbone-modal-article form' ).empty();
							$( '.ac-backbone-modal-article form' ).append( response );

							// Trigger Event
							$( document.body ).trigger( 'ac-enhanced-modal-elements-init' );
						}

						ac_meta_boxes_pagebuilder.tiptip();
						ac_meta_boxes_pagebuilder.unblock();
						ac_meta_boxes_pagebuilder.backbone.enable();
						ac_meta_boxes_pagebuilder.stupidtable.init();
					}
				});
			},

			response: function( e, target, data ) {
				if ( '#tmpl-axiscomposer-modal-trash-data' === target ) {
					ac_meta_boxes_pagebuilder.backbone.trash_data();
				}
				if ( '#tmpl-axiscomposer-modal-cell-size' === target ) {
					ac_meta_boxes_pagebuilder.backbone.cell_size( data.add_cell_size );
				}
				if ( '#tmpl-axiscomposer-modal-edit-element' === target ) {
					ac_meta_boxes_pagebuilder.backbone.edit_element( data );
				}
			},

			trash_data: function() {
				$( '.canvas-area' ).empty();
				ac_meta_boxes_pagebuilder.textarea.outer();
				ac_meta_boxes_pagebuilder.storage.clear_storage();
				ac_meta_boxes_pagebuilder.storage.history_snapshot();
			},

			cell_size: function( add_cell_size ) {
				var $row                 = $( 'a.axiscomposer-cell-set' ).parents( '.axisbuilder-layout-row:eq(0)' ),
					cells                = $row.find( '.axisbuilder-layout-cell' ),
					cell_size_variations = ac_meta_boxes_pagebuilder_data.cell_size_variations[cells.length];

				if ( add_cell_size ) {
					ac_meta_boxes_pagebuilder.cell.change_multiple_cell_size( cells, cell_size_variations[add_cell_size], true );
					ac_meta_boxes_pagebuilder.textarea.inner( false, $row );
					ac_meta_boxes_pagebuilder.textarea.outer();
					ac_meta_boxes_pagebuilder.storage.history_snapshot();
				}
			},

			edit_element: function( data ) {
				var parents = window.ac_shortcode;
				ac_meta_boxes_pagebuilder.send_to_datastorage( data, parents );
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
				return ( 'ac-storage-' + axiscomposer_admin_meta_boxes_pagebuilder.post_id ).toLowerCase();
			},

			get_key: function( passed_key ) {
				var history = ac_meta_boxes_pagebuilder.storage;
				return $.parseJSON( sessionStorage.getItem( passed_key || history.set_key() ) );
			},

			undo_data: function( e ) {
				e.preventDefault();
				var history = ac_meta_boxes_pagebuilder.storage;
				if ( ( history.temporary - 1 ) >= 0 ) {
					history.temporary --;
					history.canvas_update( history.storage[ history.temporary ] );
				}
			},

			redo_data: function( e ) {
				e.preventDefault();
				var history = ac_meta_boxes_pagebuilder.storage;
				if ( ( history.temporary + 1 ) <= history.maximum ) {
					history.temporary ++;
					history.canvas_update( history.storage[ history.temporary ] );
				}
			},

			canvas_update: function( values ) {
				var history = ac_meta_boxes_pagebuilder.storage;

				$( '.canvas-data' ).val( values[0] );
				$( '.canvas-area' ).html( values[1] );
				ac_meta_boxes_pagebuilder.tinyMCE( values[0] );
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

				$( document.body ).trigger( 'ac_dragdrop_items_loaded' );
			},

			snapshot: function() {
				// Update all textarea html with actual value
				$( '.canvas-area' ).find( 'textarea' ).each( function() {
					this.innerHTML = this.value;
				});

				var history = ac_meta_boxes_pagebuilder.storage;
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
						ac_meta_boxes_pagebuilder.storage.clear_storage();
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
				var history = ac_meta_boxes_pagebuilder.storage;
				sessionStorage.removeItem( history.set_key() );
				sessionStorage.removeItem( history.set_key() + '-temp' );

				// Reset huh?
				history.storage   = [];
				history.temporary = null;
			},

			history_snapshot: function( timeout ) {
				setTimeout( function() {
					$( document.body ).trigger( 'ac_storage_snapshot' );
				}, timeout ? timeout : 150 );
			},

			keyboard_actions: function( e ) {
				var	button     = e.keyCode || e.which,
					controlled = e.ctrlKey || e.metaKey;

				// Ctrl+z key
				if ( 90 === button && controlled && ! e.shiftKey && ! e.altKey ) {
					ac_meta_boxes_pagebuilder.storage.undo_data( e );
				}

				// Ctrl+y key
				if ( 89 === button && controlled && ! e.shiftKey && ! e.altKey ) {
					ac_meta_boxes_pagebuilder.storage.redo_data( e );
				}
			}
		},

		stupidtable: {

			init: function() {
				$( '.axiscomposer_pagebuilder' ).stupidtable().on( 'aftertablesort', this.add_arrows );
			},

			add_arrows: function( event, data ) {
				var th    = $( this ).find( 'th' );
				var arrow = data.direction === 'asc' ? '&uarr;' : '&darr;';
				var index = data.column;

				if ( 1 < index ) {
					index = index - 1;
				}

				th.find( '.ac-arrow' ).remove();
				th.eq( index ).append( '<span class="ac-arrow">' + arrow + '</span>' );
			}
		}
	};

	/**
	 * Page Builder Data
	 */
	var ac_meta_boxes_pagebuilder_data = {

		col_size: [
			[ 'ac_one_full', '1/1', 1.00 ], [ 'ac_four_fifth', '4/5', 0.80 ], [ 'ac_three_fourth', '3/4', 0.75 ], [ 'ac_two_third', '2/3', 0.66 ], [ 'ac_three_fifth', '3/5', 0.60 ], [ 'ac_one_half', '1/2', 0.50 ], [ 'ac_two_fifth', '2/5', 0.40 ], [ 'ac_one_third', '1/3', 0.33 ], [ 'ac_one_fourth', '1/4', 0.25 ], [ 'ac_one_fifth', '1/5', 0.20 ]
		],

		cell_size: [
			[ 'ac_cell_one_full', '1/1', 1.00 ], [ 'ac_cell_four_fifth', '4/5', 0.80 ], [ 'ac_cell_three_fourth', '3/4', 0.75 ], [ 'ac_cell_two_third', '2/3', 0.66 ], [ 'ac_cell_three_fifth', '3/5', 0.60 ], [ 'ac_cell_one_half', '1/2', 0.50 ], [ 'ac_cell_two_fifth', '2/5', 0.40 ], [ 'ac_cell_one_third', '1/3', 0.33 ], [ 'ac_cell_one_fourth', '1/4', 0.25 ], [ 'ac_cell_one_fifth', '1/5', 0.20 ]
		],

		new_cell_order: [
			[ 'ac_cell_one_full', '1/1' ], [ 'ac_cell_one_half', '1/2' ], [ 'ac_cell_one_third', '1/3' ], [ 'ac_cell_one_fourth', '1/4' ], [ 'ac_cell_one_fifth', '1/5' ]
		],

		cell_size_variations: {
			4 : {
				1 : [ 'ac_cell_one_fourth', 'ac_cell_one_fourth', 'ac_cell_one_fourth', 'ac_cell_one_fourth' ],
				2 : [ 'ac_cell_one_fifth',  'ac_cell_one_fifth',  'ac_cell_one_fifth',  'ac_cell_two_fifth'  ],
				3 : [ 'ac_cell_one_fifth',  'ac_cell_one_fifth',  'ac_cell_two_fifth',  'ac_cell_one_fifth'  ],
				4 : [ 'ac_cell_one_fifth',  'ac_cell_two_fifth',  'ac_cell_one_fifth',  'ac_cell_one_fifth'  ],
				5 : [ 'ac_cell_two_fifth',  'ac_cell_one_fifth',  'ac_cell_one_fifth',  'ac_cell_one_fifth'  ]
			},
			3 : {
				1 : [ 'ac_cell_one_third',    'ac_cell_one_third',    'ac_cell_one_third'   ],
				2 : [ 'ac_cell_one_fourth',   'ac_cell_one_fourth',   'ac_cell_one_half'    ],
				3 : [ 'ac_cell_one_fourth',   'ac_cell_one_half',     'ac_cell_one_fourth'  ],
				4 : [ 'ac_cell_one_half',     'ac_cell_one_fourth',   'ac_cell_one_fourth'  ],
				5 : [ 'ac_cell_one_fifth',    'ac_cell_one_fifth',    'ac_cell_three_fifth' ],
				6 : [ 'ac_cell_one_fifth',    'ac_cell_three_fifth',  'ac_cell_one_fifth'   ],
				7 : [ 'ac_cell_three_fifth',  'ac_cell_one_fifth',    'ac_cell_one_fifth'   ],
				8 : [ 'ac_cell_one_fifth',    'ac_cell_two_fifth',    'ac_cell_two_fifth'   ],
				9 : [ 'ac_cell_two_fifth',    'ac_cell_one_fifth',    'ac_cell_two_fifth'   ],
				10: [ 'ac_cell_two_fifth',    'ac_cell_two_fifth',    'ac_cell_one_fifth'   ]
			},
			2 : {
				1 : [ 'ac_cell_one_half',     'ac_cell_one_half'     ],
				2 : [ 'ac_cell_two_third',    'ac_cell_one_third'    ],
				3 : [ 'ac_cell_one_third',    'ac_cell_two_third'    ],
				4 : [ 'ac_cell_one_fourth',   'ac_cell_three_fourth' ],
				5 : [ 'ac_cell_three_fourth', 'ac_cell_one_fourth'   ],
				6 : [ 'ac_cell_one_fifth',    'ac_cell_four_fifth'   ],
				7 : [ 'ac_cell_four_fifth',   'ac_cell_one_fifth'    ],
				8 : [ 'ac_cell_two_fifth',    'ac_cell_three_fifth'  ],
				9 : [ 'ac_cell_three_fifth',  'ac_cell_two_fifth'    ]
			}
		}
	};

	ac_meta_boxes_pagebuilder.init();
});
