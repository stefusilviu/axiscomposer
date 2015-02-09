/* global axisbuilder_shortcodes */

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
				message += '<p>' + axisbuilder_shortcodes.i18n_no_layout + '<br />';

				if ( rowCount === 1 ) {
					message += axisbuilder_shortcodes.i18n_add_one_cell;
				} else {
					message += axisbuilder_shortcodes.i18n_remove_one_cell;
				}

				message += '</p>';
			}

			// Load Backbone Modal
			$( this ).AxisBuilderBackboneModal({
				title: axisbuilder_shortcodes.i18n_select_cell_layout,
				message: message,
				dismiss: dismiss,
				template: '#tmpl-axisbuilder-modal-cell-size'
			});
		}
	};

})( jQuery );
