/* global axisbuilder_admin_meta_boxes_builder */
jQuery( function( $ ) {

	/**
	 * Page Builder Data Panel
	 */
	var axisbuilder_meta_boxes_builder = {
		pagebuilder: null,
		init: function() {
			this.pagebuilder = $( '#axisbuilder-editor' ).find( ':input.axisbuilder-status' );

			// Stupid table
			this.stupidtable.init();

			$( '#axisbuilder-editor' )
				.on( 'click', 'a.axisbuilder-trash', this.trash_element )
				.on( 'click', 'a.axisbuilder-cell-add', this.cell.add_cell )
				.on( 'click', 'a.axisbuilder-change-column-size:not(.axisbuilder-change-cell-size)', this.resize_layout )

				// Backbone Modal
				.on( 'click', 'a.trash-data', this.trash_data )
				.on( 'click', '.axisbuilder-edit', this.edit_element )
				.on( 'click', 'a.axisbuilder-cell-set', this.cell_size );

			$( 'body' )
				.on( 'axisbuilder_backbone_modal_loaded', this.backbone.init )
				.on( 'axisbuilder_backbone_modal_response', this.backbone.response );
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

		init_tiptip: function() {
			$( '#tiptip_holder' ).removeAttr( 'style' );
			$( '#tiptip_arrow' ).removeAttr( 'style' );
			$( '.tips' ).tipTip({
				'attribute': 'data-tip',
				'fadeIn': 50,
				'fadeOut': 50,
				'delay': 200
			});
		},

		history_snapshot: function( timeout ) {
			setTimeout( function() {
				$( '.canvas-area' ).trigger( 'axisbuilder-storage-update' );
			}, timeout ? timeout : 150 );
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
					axisbuilder_meta_boxes_builder.cell.remove_cell( $(this) );
				}

				element.remove();

				if ( parents && parents.length ) {
					axisbuilder_meta_boxes_builder.textarea.inner( parents );
				}
				axisbuilder_meta_boxes_builder.textarea.outer();

				// Bugfix for column delete that renders the canvas undropbable for unknown reason
				// if ( $( '.canvas-data' ).val() === '' ) {
				// 	axisbuilder_meta_boxes_builder.activate_dropping($( '.canvas-data' ).parents( '.postbox:eq(0)' ), 'destroy' );
				// }

				axisbuilder_meta_boxes_builder.history_snapshot();
			});

			return false;
		},

		resize_layout: function() {
			var	direction    = $( this ).is( '.axisbuilder-increase' ) ? 1 : -1,
				column       = $( this ).parents( '.axisbuilder-layout-column:eq(0)' ),
				section      = column.parents( '.axisbuilder-layout-section:eq(0)' ),
				current_size = column.data( 'width' ),
				size_string  = column.find( '.axisbuilder-column-size' ),
				data_storage = column.find( '.axisbuilder-inner-shortcode > textarea[data-name="text-shortcode"]' ),
				data_string  = data_storage.val(),
				next_size    = [],
				layout_sizes = [
					[ 'ab_one_full', '1/1' ], [ 'ab_four_fifth', '4/5' ], [ 'ab_three_fourth', '3/4' ], [ 'ab_two_third', '2/3' ], [ 'ab_three_fifth', '3/5' ], [ 'ab_one_half', '1/2' ], [ 'ab_two_fifth', '2/5' ], [ 'ab_one_third', '1/3' ], [ 'ab_one_fourth', '1/4' ], [ 'ab_one_fifth', '1/5' ]
				];

			for ( var i = 0; i < layout_sizes.length; i++ ) {
				if ( layout_sizes[i][0] === current_size ) {
					next_size = layout_sizes[ i - direction ];
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

		cell_size: function() {
			var $row                 = $( this ).parents( '.axisbuilder-layout-row:eq(0)' ),
				cells                = $row.find( '.axisbuilder-layout-cell' ),
				cell_size            = axisbuilder_meta_boxes_builder_cells.cell_size,
				cell_size_variations = axisbuilder_meta_boxes_builder_cells.cell_size_variations[cells.length],
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

				if ( '#tmpl-axisbuilder-modal-edit-element' === template ) {
					axisbuilder_meta_boxes_builder.backbone.edit_element();
				}

				if ( '#tmpl-axisbuilder-modal-cell-size' === template ) {
					axisbuilder_meta_boxes_builder.backbone.cell_size( data.add_cell_size );
				}
			},

			trash_data: function() {
				$( '.canvas-area' ).empty();
				axisbuilder_meta_boxes_builder.textarea.outer();
			},

			edit_element: function() {},

			cell_size: function( add_cell_size ) {
				var $row                 = $( 'a.axisbuilder-cell-set' ).parents( '.axisbuilder-layout-row:eq(0)' ),
					cells                = $row.find( '.axisbuilder-layout-cell' ),
					cell_size_variations = axisbuilder_meta_boxes_builder_cells.cell_size_variations[cells.length];

				if ( add_cell_size ) {
					axisbuilder_meta_boxes_builder_cells.change_multiple_cell_size( cells, cell_size_variations[add_cell_size], true );
					axisbuilder_meta_boxes_builder.textarea.inner( false, $row );
					axisbuilder_meta_boxes_builder.textarea.outer();
					axisbuilder_meta_boxes_builder.history_snapshot();
				}
			}
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

				var sizes          = { 'ab_one_full': 1.00, 'ab_four_fifth': 0.80, 'ab_three_fourth': 0.75, 'ab_two_third': 0.66, 'ab_three_fifth': 0.60, 'ab_one_half': 0.50, 'ab_two_fifth': 0.40, 'ab_one_third': 0.33, 'ab_one_fourth': 0.25, 'ab_one_fifth': 0.20 },
					size_count     = 0,
					content_value  = '',
					content_fields = scope.find( '>textarea[data-name="text-shortcode"]' ),
					current_field, current_content, current_parents, current_size;

				for ( var i = 0; i < content_fields.length; i++ ) {
					current_field   = $( content_fields[i] );
					current_content = current_field.val();
					current_parents = current_field.parents( '.axisbuilder-layout-column-no-cell:eq(0)' );

					// If we are checking a column we need to make sure to add/remove the first class :)
					if ( current_parents.length ) {
						current_size = current_parents.data( 'width' );
						size_count  += sizes[current_size];

						if ( size_count > 1 || i === 0 ) {

							if ( ! current_parents.is( '.axisbuilder-first-column' ) ) {
								current_parents.addClass( 'axisbuilder-first-column' );
								current_content = current_content.replace( new RegExp( '^\\[' + current_size ), '[' + current_size + ' first' );
								current_field.val( current_content );
							}

							size_count = sizes[current_size];
						} else if ( current_parents.is( '.axisbuilder-first-column' ) ) {
							current_parents.removeClass( 'axisbuilder-first-column' );
							current_content = current_content.replace( ' first', '' );
							current_field.val( current_content );
						}
					} else {
						size_count = 1;
					}

					content_value += current_content;
				}

				if ( typeof window.tinyMCE !== 'undefined' ) {
					setTimeout( function() {
						window.tinyMCE.get( 'content' ).setContent( window.switchEditors.wpautop( content_value ), { format: 'html' } );
					}, 500 );
				}

				$( '.canvas-data' ).val( content_value );
				$( '#content.wp-editor-area' ).val( content_value );
			}
		},

		cell: {
			add_cell: function() {
				axisbuilder_meta_boxes_builder_cells.modify_cell_count( $( this ), 0 );
			},

			recalc_cell: function() {
				axisbuilder_meta_boxes_builder_cells.modify_cell_count( $( this ), -1 );
			},

			remove_cell: function( clicked ) {
				axisbuilder_meta_boxes_builder_cells.modify_cell_count( clicked, -2 );
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

	var axisbuilder_meta_boxes_builder_cells = {

		cell_list: [
			[ 'ab_cell_one_full', '1/1' ], [ 'ab_cell_one_half', '1/2' ], [ 'ab_cell_one_third', '1/3' ], [ 'ab_cell_one_fourth', '1/4' ], [ 'ab_cell_one_fifth', '1/5' ]
		],

		cell_size: [
			[ 'ab_cell_one_full', '1/1', 1.00 ], [ 'ab_cell_four_fifth', '4/5', 0.80 ], [ 'ab_cell_three_fourth', '3/4', 0.75 ], [ 'ab_cell_two_third', '2/3', 0.66 ], [ 'ab_cell_three_fifth', '3/5', 0.60 ], [ 'ab_cell_one_half', '1/2', 0.50 ], [ 'ab_cell_two_fifth', '2/5', 0.40 ], [ 'ab_cell_one_third', '1/3', 0.33 ], [ 'ab_cell_one_fourth', '1/4', 0.25 ], [ 'ab_cell_one_fifth', '1/5', 0.20 ]
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
		},

		modify_cell_count: function( clicked, direction ) {
			var $row  = $( clicked ).parents( '.axisbuilder-layout-row:eq(0)' ),
				cells = $row.find( '.axisbuilder-layout-cell' ),
				count = ( cells.length + direction ),
				newEl = axisbuilder_meta_boxes_builder_cells.cell_list[count];

			if ( typeof newEl !== 'undefined' ) {
				if ( count !== cells.length ) {
					axisbuilder_meta_boxes_builder_cells.change_multiple_cell_size( cells, newEl );
				} else {
					axisbuilder_meta_boxes_builder_cells.change_multiple_cell_size( cells, newEl );
					axisbuilder_meta_boxes_builder_cells.append_cell( $row, newEl );
					// axisbuilder_meta_boxes_builder.dropping();
				}

				axisbuilder_meta_boxes_builder.textarea.inner( false, $row );
				axisbuilder_meta_boxes_builder.textarea.outer();
				axisbuilder_meta_boxes_builder.history_snapshot();
			}
		},

		append_cell: function( row, newEl ) {
			var data_storage    = row.find( '> .axisbuilder-inner-shortcode' ),
				shortcode_class = newEl[0].replace( 'ab_cell_', 'ab_shortcode_cells_' ).replace( '_one_full', '' ),
				template        = $( $( '#axisbuilder-tmpl-' + shortcode_class ).html() );

			data_storage.append( template );
		},

		change_multiple_cell_size: function( cells, newEl, multi ) {
			var key       = '',
				next_size = newEl,
				cell_size = axisbuilder_meta_boxes_builder_cells.cell_size;

			cells.each( function( i ) {
				if ( multi ) {
					key = newEl[i];
					for ( var x in cell_size ) {
						if ( key === cell_size[x][0] ) {
							next_size = cell_size[x];
						}
					}
				}

				axisbuilder_meta_boxes_builder_cells.change_single_cell_size( $( this ), next_size );
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
	};

	axisbuilder_meta_boxes_builder.init();
});
