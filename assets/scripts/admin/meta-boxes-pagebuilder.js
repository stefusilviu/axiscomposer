/* global axisbuilder_admin_meta_boxes_builder */
jQuery( function( $ ) {

	/**
	 * Page Builder Items Panel
	 */
	var axisbuilder_meta_boxes_builder_items = {
		init: function() {
			this.stupidtable.init();

			$( '#axisbuilder-editor' )

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
				message += '<form>';

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

					message += '<div class="axisbuilder-layout-row-modal"><label class="axisbuilder-layout-row-modal-label">';
					message += '<input type="radio" id="add_cell_size_' + x + '" name="add_cell_size" value="' + x + '" /><span class="axisbuilder-layout-row-inner-label">' + label + '</span></label></div>';
				}

				message += '</form>';
			} else {
				dismiss = true;
				message += '<p>' + axisbuilder_admin_meta_boxes_builder.i18n_no_layout + '<br />';

				if ( cells.length === 1 ) {
					message += axisbuilder_admin_meta_boxes_builder.i18n_add_one_cell;
				} else {
					message += axisbuilder_admin_meta_boxes_builder.i18n_remove_one_cell;
				}

				message += '</p>';
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
					axisbuilder_meta_boxes_builder_items.backbone.trash_data();
				}

				if ( '#tmpl-axisbuilder-modal-edit-element' === template ) {
					axisbuilder_meta_boxes_builder_items.backbone.edit_element();
				}

				if ( '#tmpl-axisbuilder-modal-cell-size' === template ) {
					axisbuilder_meta_boxes_builder_items.backbone.cell_size( data.add_cell_size );
				}
			},

			trash_data: function() {
				$( '.canvas-area' ).empty();
			},

			edit_element: function() {},

			cell_size: function( add_cell_size ) {
				var $row                 = $( 'a.axisbuilder-cell-set' ).parents( '.axisbuilder-layout-row:eq(0)' ),
					cells                = $row.find( '.axisbuilder-layout-cell' ),
					cell_size_variations = axisbuilder_meta_boxes_builder_cells.cell_size_variations[cells.length];

				if ( add_cell_size ) {
					axisbuilder_meta_boxes_builder_cells.change_multiple_cell_size( cells, cell_size_variations[add_cell_size], true );
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

		change_multiple_cell_size: function( cells, newEl, multi ) {
			var key       = '',
				new_size  = newEl,
				cell_size = axisbuilder_meta_boxes_builder_cells.cell_size;

			cells.each( function( i ) {
				if ( multi ) {
					key = newEl[i];
					for ( var x in cell_size ) {
						if ( key === cell_size[x][0] ) {
							new_size = cell_size[x];
						}
					}
				}

				axisbuilder_meta_boxes_builder_cells.change_single_cell_size( $( this ), new_size );
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

	axisbuilder_meta_boxes_builder_items.init();
});
