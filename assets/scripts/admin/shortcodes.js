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
				// obj.updateTextarea();
				// obj.historySnapshot()
			} else {

			}

			return;
		}
	};

	$.AxisBuilderShortcodes.trashItem = function( clicked, obj ) {
		var trigger = $( clicked ),
			element = trigger.parents( '.axisbuilder-sortable-element:eq(0)' ),
			parents = false, removeCell = false, element_hide = 200;

		// Check if it is a column
		if ( ! element.length ) {
			element = trigger.parents( '.axisbuilder-layout-column:eq(0)' );
			parents = trigger.parents( '.axisbuilder-layout-column:eq(0)>.axisbuilder-inner-shortcode' );

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
			element.remove();
		});
	};

})(jQuery);
