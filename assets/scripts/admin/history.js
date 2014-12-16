/* global AB_Logger, axisbuilder_history, axisbuilder_admin */

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

			// Create a unique array key for this post
			this.key     = this.create_array_key();
			this.storage = this.get() || [];
			this.maximum = this.storage.length - 1;
			this.index   = this.get( this.key + 'index' );

			if ( typeof this.index === 'undefined' || this.index === null ) {
				this.index = this.maximum;
			}

			// Clear storage for testing purpose
			this.clear();

			// Bind Events
			this.bindEvents();
		},

		// Creates the array key for this post history
		create_array_key: function() {
			var key = 'axisbuilder' + axisbuilder_history.theme_name + axisbuilder_history.theme_version + axisbuilder_admin.post_id + axisbuilder_history.plugin_version;
			key = key.replace( /[^a-zA-Z0-9]/g, '' ).toLowerCase();
		},

		bindEvents: function() {
			// var obj = this;
		},

		get: function( passed_key ) {
			var key = passed_key || this.key;
			return JSON.parse( sessionStorage.getItem( key ) );
		},

		set: function( passed_key, passed_value ) {
			var key   = passed_key || this.key,
				value = passed_value || JSON.stringify( this.storage );

			try {
				sessionStorage.setItem( key, value );
			}

			catch( e ) {
				new AB_Logger( 'Storage Limit reached. Your Browser does not offer enough session storage to save more steps for the undo/redo history.', 'Storage' );
				new AB_Logger( e, 'Storage' );
				this.clear();
			}
		},

		clear: function() {
			sessionStorage.removeItem( this.key );
			sessionStorage.removeItem( this.index );
			this.index   = null;
			this.storage = [];
		}
	};

})( jQuery );
