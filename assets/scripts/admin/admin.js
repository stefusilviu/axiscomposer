/* global console, axisbuilder_admin, pagenow */

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

		// WordPress default tinyMCE Editor {Wrap|Area}
		this.wpDefaultEditorWrap = $( '#postdivrich' );
		this.wpDefaultEditorArea = $('#content.wp-editor-area');

		// AxisBuilder Debug or Test Mode
		this.axisBuilderDebug = axisbuilder_admin.debug || {};

		// Axis Page Builder {Button|Handle|Canvas|Values|Parent|Status}
		this.axisBuilderButton = $( '#axisbuilder-button' );
		this.axisBuilderHandle = $( '#axisbuilder-handle' ).find( '.control-bar' );
		this.axisBuilderCanvas = $( '#axisbuilder-canvas' ).find( '.canvas-area' );
		this.axisBuilderValues = $( '#axisbuilder-canvas' ).find( '.canvas-data' );
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
			var self = this;

			if ( this.axisBuilderStatus.val() !== 'active' ) {
				this.wpDefaultEditorWrap.parent().addClass( 'axisbuilder-hidden-editor' );
				this.axisBuilderButton.removeClass( 'button-primary' ).addClass( 'button-secondary' ).text( this.axisBuilderButton.data( 'default-editor' ) );
				this.axisBuilderParent.removeClass( 'axisbuilder-hidden');
				this.axisBuilderStatus.val( 'active' );

				// Load Shortcodes to Interface :)
				setTimeout( function() {
					self.shortcodesToInterface();
				}, 10 );
			} else {
				this.wpDefaultEditorWrap.parent().removeClass( 'axisbuilder-hidden-editor' );
				this.axisBuilderButton.addClass( 'button-primary' ).removeClass( 'button-secondary' ).text( this.axisBuilderButton.data( 'page-builder' ) );
				this.axisBuilderParent.addClass( 'axisbuilder-hidden');
				this.axisBuilderStatus.val( 'inactive' );

				// Add Loader
				this.axisBuilderCanvas.addClass( 'loader' );

				// Turn WordPress editor resizing off :)
				if( typeof window.editorExpand === 'object' ) {
					window.editorExpand.off();
				}

				// Debug Logger
				if ( this.axisBuilderDebug !== 'disable' && ( this.axisBuilderValues.val().indexOf( '[' ) !== -1 ) ) {
					new AB_Logger( 'Switching to Classic Editor. Page Builder is in Debug Mode and will empty the textarea so user can\'t edit shortcode directly', 'Editor' );
					if ( this.tinyMceContent ) {
						this.tinyMceContent.setContent( '', { format: 'html' } );
						this.wpDefaultEditorArea.val( '' );
					}
				}
			}

			return false;
		},

		/**
		 * Converts shortcodes to an editable element on Builder Canvas.
		 * Only executed at page load or when editor is switched from default to Page Builder.
		 */
		shortcodesToInterface: function( text ) {
			var obj = this;

			// Return if builder is not in active state
			if ( this.axisBuilderStatus.val() !== 'active' ) {
				return true;
			}

			// If text is undefined. Also Test-Drive val() to html()
			if ( typeof text === 'undefined' ) {
				text = this.axisBuilderValues.val();
				if ( text.indexOf( '[' ) === -1 ) {
					text = this.wpDefaultEditorArea.val();

					if ( this.tinyMceDefined ) {
						text = window.switchEditors._wp_Nop( text );
					}

					this.axisBuilderValues.val( text );
				}
			}

			// Do snap test for drag and drop :)
			obj.sendToBuilderCanvas();
			obj.axisBuilderCanvas.removeClass( 'loader' );
		},

		/**
		 * Send element(s) to AxisBuilder Canvas
		 * Gets executed on page load to display all elements and when a single item is fetchec via AJAX or HTML5 Storage.
		 */
		sendToBuilderCanvas: function( text ) {
			var add_text = $( text );
			this.axisBuilderCanvas.append( add_text );

			// Activate Element Drag and Drop
			this.activateDragging();
			this.activateDropping();
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

		// Activate dragging for the given DOM element.
		activateDragging: function( passed_scope, exclude ) {

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
				params = {
					appendTo : 'body',
					handle   : '>.menu-item-handle',
					helper   : 'clone',
					zIndex   : 20000,
					scroll   : true,
					revert	 : false,
					cursorAt : {
						left : 20
					},

					start: function( event ) {
						var current = $( event.target );

						// Reduce elements opacity so user got a visual feedback on what (s)he is editing
						current.css({ opacity: 0.4 });

						// Remove all previous hover elements
						$( '.axisbuilder-hover-active' ).removeClass( 'axisbuilder-hover-active' );

						// Add a class to the container element that highlights all possible drop targets
						obj.axisBuilderCanvas.addClass( 'axisbuilder-select-target-' + current.data( 'dragdrop-level' ) );
					},

					drag: function( event, ui ) {
						if ( fix_active ) {
							ui.position.top -= parseInt( windows.scrollTop(), 10 );
						}
					},

					stop: function( event ) {

						// Return opacity of element to normal
						$( event.target ).css({ opacity: 1 });

						// Remove hover class from all elements
						$( '.axisbuilder-hover-active' ).removeClass( 'axisbuilder-hover-active' );

						/**
						 * Reset highlight on container class
						 *
						 * Currently have setting for 4 nested level of element.
						 * If you have more levels, just add styles like the other 'axisbuilder-select-target'
						 */
						obj.axisBuilderCanvas.removeClass( 'axisbuilder-select-target-1 axisbuilder-select-target-2 axisbuilder-select-target-3 axisbuilder-select-target-4' );
					}
				};

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

		// Activate dropping for the given DOM element.
		activateDropping: function( passed_scope, exclude ) {

			// Drop
			var obj    = this,
				scope  = passed_scope || this.axisBuilderParent,
				params = {
					greedy: true,
					tolerance: 'pointer',

					// If there's a draggable element and it's over the current element, this function will be executed.
					over: function( event, ui ) {
						var droppable = $( this );

						// Check if the current element can accept the droppable element
						if ( obj.isDropingAllowed( ui.helper, droppable ) ) {
							 // Add active class that will highlight the element with gree, i.e drop is allowed.
							droppable.addClass( 'axisbuilder-hover-active' );
						}
					},

					// If there's a draggable element and it was over the current element, when it moves out this function will be executed.
					out: function() {
						$( this ).removeClass( 'axisbuilder-hover-active' );
					}
				};

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

		/**
		 * Check if the droppable element can accept the draggable element based on attribute "dragdrop-level"
		 * @returns {Boolean}
		 */
		isDropingAllowed: function( draggable, droppable ) {
			if ( draggable.data( 'dragdrop-level' ) > droppable.data( 'dragdrop-level' ) ) {
				return true;
			}

			return false;
		}
	};

	$( document ).ready( function () {
		$.AxisBuilderObj = new $.AxisBuilder();
	});

})(jQuery);
