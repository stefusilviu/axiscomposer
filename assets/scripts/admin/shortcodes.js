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
			} else {

			}

			return;
		}
	};

	$.AxisBuilderShortcodes.cloneElement = function( clicked, obj ) {
		var trigger = $( clicked ),
			element = trigger.parents( '.axisbuilder-sortable-element:eq(0)' );

		// var	layoutCell = false;

		// Check if it is a column
		if ( ! element.length ) {
			element = trigger.parents( '.axisbuilder-layout-column:eq(0)' );

			// Check if it is a section
			if ( ! element.length ) {
				element = trigger.parents( '.axisbuilder-layout-section:eq(0)' );
			}
		}

		// Check if its a layout cell and if we can add one to the row :)
		// if ( element.length && element.is( '.axisbuilder-layout-cell' ) ) {
			// Let's add condition when cell is available :)
		// }

		// Make sure the elements actual html code matches the value so cloning works properly.
		element.find( 'textarea' ).each( function() {
			this.innerHTML = this.value;
		});

		var cloned = element.clone();

		// Remove all previous drag/drop classes so we can apply new ones.
		cloned.find( '.ui-draggable, .ui-droppable' ).removeClass( '.ui-draggable, .ui-droppable' );
		cloned.insertAfter( element );

		var wrap = element.parents( '.axisbuilder-layout-section, .axisbuilder-layout-column' );

		if ( element.is( '.axisbuilder-layout-section' ) || element.is( '.axisbuilder-layout-column' ) || wrap.length ) {
			if ( wrap.length ) {
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
				// $.AxisBuilderShortcodes.removeCell( clicked, obj );
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
			dataString = dataString.replace( new RegExp( '^\\[' + currentSize, 'g' ), '[' + nextSize[0] );
			dataString = dataString.replace( new RegExp( currentSize + '\\]', 'g' ), nextSize[0] + ']' );

			dataStorage.val( dataString );
			container.removeClass( currentSize ).addClass( nextSize[0] );
			container.attr( 'data-width', nextSize[0] ).data( 'width', nextSize[0] ); // Ensure to set data attr so html() functions fetch the correct value :)
			sizeString.text( nextSize[1] );

			obj.updateTextarea();

			if ( section.length ) {
				obj.updateInnerTextarea( false, section );
				obj.updateTextarea();
			}

			obj.historySnapshot(0);
		}
	};

})(jQuery);
