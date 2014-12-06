/* global pagenow */

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
		this.shortcodes    = $.AxisBuilder.shortcodes || {};
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
				var parents = $( this ).parents( '.axisbuilder-shortcodes' ),
					execute = this.hash.replace( '#', '' ),
					targets = 'instant-insert', // ( this.className.indexOf( 'axisbuilder-target-insert' ) !== -1 ) ? "target_insert" : "instant_insert",
					already_active = ( this.className.indexOf( 'axisbuilder-active-insert' ) !== -1 ) ? true : false;

				obj.shortcodes.fetchShortcodeEditorElement( execute, targets, obj );

				return false;
			});

			// Trash the entire canvas elements
			this.axisBuilderHandle.on( 'click', 'a.trash-data', function() {

				sweetAlert({
					title: "Are you sure?",
					text: "You will not be able to recover this canvas elements!",
					type: "warning",
					showCancelButton: true,
					confirmButtonColor: "#DD6B55",
					confirmButtonText: "Yes, delete it!",
					closeOnConfirm: false
				}, function() {
					swal({
						title: "Deleted!",
						text: "Your canvas elements has been deleted.",
						type: "success",
						timer: 2000
					});
				});

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
		}
	};

	$( document ).ready( function () {
		$.AxisBuilderObj = new $.AxisBuilder();
	});

})(jQuery);

/**
 * AxisBuilder Shortcodes JS
 */
( function( $ ) {

	'use strict';

	$.AxisBuilder.shortcodes = $.AxisBuilder.shortcodes || {};

	$.AxisBuilder.shortcodes.fetchShortcodeEditorElement = function( shortcode, insert_target, obj ) {

	}

})(jQuery);
