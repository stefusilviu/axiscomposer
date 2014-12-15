/**
 * AxisBuilder History JS
 */
( function( $ ) {

	'use strict';

	$.AxisBuilderHistory = $.AxisBuilderHistory || {};

	$.AxisBuilderHistory = function( options ) {
		var defaults = {
			steps: 40,
			editor: '',
			monitor: '',
			buttons: '',
			event: 'axisbuilder-storage-update'
		};

		// No web storage? stop here :)
		if ( typeof Storage === 'undefined' ) {
			return false;
		}

		this.options = $.extend( {}, defaults, options );

		// Setup
		this.setups();
	};

	$.AxisBuilderHistory.prototype = {

		setups: function() {
			this.editor  = $( this.options.editor );
			this.canvas  = $( this.options.monitor );
			this.wrapper = this.canvas.parent();
			this.buttons = $( this.options.buttons );
		}
	};

})( jQuery );
