/* global axisbuilder_admin */

/**
 * AxisBuilder Admin JS
 */
( function( $ ) {

	'use strict';

	$.AxisBuilder = function() {

		// Canvas used to display the interface
		this.canvas             = $( '#axisLayoutBuilder' );

		// Box wrapping the canvas
		this.canvasParent       = this.canvas.parents( '.postbox:eq(0)' );

		// Whether the Layout Builder is currently active or the WordPress default editor is
		this.activeStatus       = this.canvasParent.find('#axisLayoutBuilder_active');

		// List of available shortcode buttons
		this.shortcodes         = $.AxisBuilder.shortcodes || {};

		// Whether tinyMCE is available
		this.tiny_active        = typeof window.tinyMCE == 'undefined' ? false : true;

		// tinyMCE version
		this.tiny_version       = this.tiny_active ? window.tinyMCE.majorVersion : false;

		// WordPress default editor element
		this.wpDefaultEditor    = $( '#postdivrich' );

		// Wrapper around tinyMCE Editor
		this.classicEditorWrap  = $( '#postdivrich_wrap' );

		// Button to switch between WordPress editor and axis builder
		this.switchButton       = $( 'body' ).find( '#axisbuilder-button' );

		// Activate the Plugin
		this.activate();
	};

	$.AxisBuilder.prototype = {

		// Activate the Whole Interface
		activate: function() {
			this.behaviour();
		},

		// All event binding goes here
		behaviour: function() {
			var obj = this, $body = $( 'body' );

			// Switch between default editor and page builder
			this.switchButton.on( 'click', function(e) {
				e.preventDefault();
				obj.switch_editor();
			});
		},

		// Switch default and AxisBuilder Editors
		switch_editor: function() {
			var editor = this.tiny_active ? window.tinyMCE.get( 'content' ) : false;

			if ( this.activeStatus.val() !== 'active' ) {
				$( '#content-html' ).trigger( 'click' );
				this.classicEditorWrap.addClass( 'axisbuilder-hidden-editor' );
				this.switchButton.text( this.switchButton.data( 'default-editor' ) );
				this.activeStatus.val( 'active' );
				this.canvasParent.removeClass( 'axisbuilder-hidden');

				setTimeout( function() {
					$( '#content-tmce' ).trigger( 'click' );
				}, 10 );

			} else {
				this.classicEditorWrap.removeClass( 'axisbuilder-hidden-editor' );
				this.switchButton.text( this.switchButton.data( 'page-builder' ) );
				this.activeStatus.val( '' );
				this.canvasParent.addClass( 'axisbuilder-hidden');

				$(window).trigger('scroll');
			}

			return false;
		}
	}


	$( document ).ready( function () {
		$.AxisBuilderObj = new $.AxisBuilder();
	});

})(jQuery);
