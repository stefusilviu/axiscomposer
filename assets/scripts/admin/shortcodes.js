/**
 * AxisBuilder Shortcodes JS
 */
( function( $ ) {

	'use strict';

	$.AxisBuilderShortcodes = $.AxisBuilderShortcodes || {};

	$.AxisBuilderShortcodes.fetchShortcodeEditorElement = function( shortcode, insert_target, obj ) {
		var template = $( '#axisbuilder-template-' + shortcode );

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

})(jQuery);
