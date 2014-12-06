/* global console, pagenow */

// global AxisBuilder Logger Helper
function AB_Logger( text, type ) {

	if ( typeof console === 'undefined' ) {
		return true;
	}

	if ( typeof type === 'undefined' ) {
		type = 'logger';
	}

	if ( type === false ) {
		console.log( text );
	} else {
		type = 'AB_' + type.toUpperCase();
		console.log( '[' + type + '] - ' + text );
	}
}

/**
 * AxisBuilder Admin JS
 */
( function( $ ) {

	'use strict';

	$.AxisBuilder = function() {

		// WordPress default tinyMCE editor Element
		this.wpDefaultEditor = $( '#postdivrich' );

		// Axis Page Builder {Button|Handle|Canvas|Parent|Status}
		this.axisBuilderButton = $( '#axisbuilder-button' );
		this.axisBuilderHandle = $( '#axisbuilder-handle' ).find( '.control-bar' );
		this.axisBuilderCanvas = $( '#axisbuilder-canvas' ).find( '.canvas-area' );
		this.axisBuilderParent = this.axisBuilderCanvas.parents( '.postbox:eq(0)' );
		this.axisBuilderStatus = this.axisBuilderParent.find( 'input[name=axisbuilder_status]' );

		// WordPress tinyMCE {Defined|Version|Content}
		this.tinyMceDefined = typeof window.tinyMCE !== 'undefined' ? true : false;
		this.tinyMceVersion = this.tinyMceDefined ? window.tinyMCE.majorVersion : false;
		this.tinyMceContent = this.tinyMceDefined ? window.tinyMCE.get( 'content' ) : false;

		// Shortcode Buttons {Object|Wrap}
		this.shortcodes    = $.AxisBuilderShortcodes || {};
		this.shortcodeWrap = $( '.axisbuilder-shortcodes' );

		// Activate the Builder
		this.builderActivate();
	};

	$.AxisBuilder.prototype = {

		// Activate the Whole Interface
		builderActivate: function() {
			this.builderPosition();
			this.builderBehaviour();
		},

		// Always make Builder available at the first position
		builderPosition: function() {
			var meta_box = $( '#normal-sortables' ),
				post_box = meta_box.find( '.postbox' );

			if ( this.axisBuilderParent.length && ( post_box.index( this.axisBuilderParent ) !== 0 ) ) {
				this.axisBuilderParent.prependTo( meta_box );

				// Re-save the postbox Order
				window.postboxes.save_order( pagenow );
			}
		},

		// All event binding goes here
		builderBehaviour: function() {
			var obj = this;

			// Toggle between default editor and page builder
			this.axisBuilderButton.on( 'click', function( e ) {
				e.preventDefault();
				obj.switchEditors();
			});

			// Add a new element to the Builder Canvas
			this.shortcodeWrap.on( 'click', '.insert-shortcode', function() {
				// var parents = $( this ).parents( '.axisbuilder-shortcodes' ),
					// already_active = ( this.className.indexOf( 'axisbuilder-active-insert' ) !== -1 ) ? true : false;

				var	execute = this.hash.replace( '#', '' ),
					targets = 'instant-insert'; // ( this.className.indexOf( 'axisbuilder-target-insert' ) !== -1 ) ? "target_insert" : "instant_insert",

				obj.shortcodes.fetchShortcodeEditorElement( execute, targets, obj );

				return false;
			});

			// Trash the entire canvas elements
			this.axisBuilderHandle.on( 'click', 'a.trash-data', function() {

				// sweetAlert({
				// 	title: "Are you sure?",
				// 	text: "You will not be able to recover this canvas elements!",
				// 	type: "warning",
				// 	showCancelButton: true,
				// 	confirmButtonColor: "#DD6B55",
				// 	confirmButtonText: "Yes, delete it!",
				// 	closeOnConfirm: false
				// }, function() {
				// 	swal({
				// 		title: "Deleted!",
				// 		text: "Your canvas elements has been deleted.",
				// 		type: "success",
				// 		timer: 2000
				// 	});
				// });

				return false;
			});
		},

		// Switch between the {WordPress|AxisBuilder} Editors
		switchEditors: function() {

			if ( this.axisBuilderStatus.val() !== 'active' ) {
				this.wpDefaultEditor.parent().addClass( 'axisbuilder-hidden-editor' );
				this.axisBuilderButton.removeClass( 'button-primary' ).addClass( 'button-secondary' ).text( this.axisBuilderButton.data( 'default-editor' ) );
				this.axisBuilderParent.removeClass( 'axisbuilder-hidden');
				this.axisBuilderStatus.val( 'active' );
			} else {
				this.wpDefaultEditor.parent().removeClass( 'axisbuilder-hidden-editor' );
				this.axisBuilderButton.addClass( 'button-primary' ).removeClass( 'button-secondary' ).text( this.axisBuilderButton.data( 'page-builder' ) );
				this.axisBuilderParent.addClass( 'axisbuilder-hidden');
				this.axisBuilderStatus.val( 'inactive' );

				// Add Loader
				this.axisBuilderCanvas.addClass( 'loader' );

				// Turn WordPress editor resizing off :)
				if( typeof window.editorExpand === 'object' ) {
					window.editorExpand.off();
				}
			}

			return false;
		},

		/**
		 * Send element(s) to AxisBuilder Canvas
		 * Gets executed on page load to display all elements and when a single item is fetchec via AJAX or HTML5 Storage.
		 */
		sendToBuilderCanvas: function( text ) {
			var add_text = $( text );
			this.axisBuilderCanvas.append( add_text );

			// Activate Element Drag and Drop
			this.activate_element_dragging();
			this.activate_element_dropping();
		},

		// --------------------------------------------
		// Main Interface drag and drop Implementation
		// --------------------------------------------

		// Version Compare helper function for drag and drop fix below
		compareVersion: function( a, b ) {
			var i, compare, length, regex = /(\.0)+[^\.]*$/;

			a      = ( a + '' ).replace( regex, '' ).split( '.' );
			b      = ( b + '' ).replace( regex, '' ).split( '.' );
			length = Math.min( a.length, b.length );

			for( i = 0; i < length; i++ ) {
				compare = parseInt( a[i], 10 ) - parseInt( b[i], 10 );
				if( compare !== 0 ) {
					return compare;
				}
			}

			return ( a.length - b.length );
		},

		// Activate Element Dragging
		activate_element_dragging: function( passed_scope, exclude ) {

			var windows    = $( window ),
				fix_active = ( this.compareVersion( $.ui.draggable.version, '1.10.9' ) <= 0 ) ? true : false;

			if ( ( navigator.userAgent.indexOf( 'Safari' ) !== -1 ) || ( navigator.userAgent.indexOf( 'Chrome' ) !== -1 ) ) {
				fix_active = false;
			}

			if ( fix_active ) {
				new AB_Logger( 'Drag and drop Positioning fix active' );
			}

			// Drag
			var obj    = this,
				scope  = passed_scope || this.axisBuilderParent,
				params = {};

			// If exclude is undefined
			if ( typeof exclude === 'undefined') {
				exclude = ':not(.ui-draggable)';
			}

			// Let's Bail Draggeble UI
			scope.find( '.axisbuilder-drag' + exclude ).draggable( params );

			params.cursorAt = { left: 33, top:33 };
			params.handle   = false;
			scope.find( '.insert-shortcode' ).not( '.ui-draggable' ).draggable( params );
		},

		// Activate Element Dropping
		activate_element_dropping: function( passed_scope, exclude ) {

			// Drop
			var obj    = this,
				scope  = passed_scope || this.axisBuilderParent,
				params = {};

			// If exclude is undefined
			if ( typeof exclude === 'undefined') {
				exclude = ':not(.ui-droppable)';
			}

			// If exclude is set to destroy remove all droppables and then re-apply
			if ( exclude === 'destroy' ) {
				scope.find( '.axisbuilder-drop' ).droppable( 'destroy' );
				exclude = '';
			}

			// Let's Bail Droppable UI
			scope.find( '.axisbuilder-drop' + exclude ).droppable( params );
		},

		// Compares the drop level of the 2 elements. If the dragable has a higher drop level it may be dropped upon the droppable.
		dropping_allowed: function( dragable, droppable ) {
			if ( dragable.data( 'dragdrop-level' ) > droppable.data( 'dragdrop-level' ) ) {
				return true;
			}

			return false;
		}
	};

	$( document ).ready( function () {
		$.AxisBuilderObj = new $.AxisBuilder();
	});

})(jQuery);
