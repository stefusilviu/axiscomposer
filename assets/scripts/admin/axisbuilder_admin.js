/**
 * AxisBuilder Admin JS
 */
( function( $ ) {

	'use strict';

	$.AxisBuilder = function() {

		// WordPress default tinyMCE editor Element
		this.wpDefaultEditor = $( '#postdivrich' );

		// Axis Page Builder {Status|Button|Editor}
		this.axisBuilderButton = $( '#axisbuilder-button' );
		this.axisBuilderEditor = $( '#axisbuilder-editor' );
		this.axisBuilderCanvas = $( '#axisbuilder-editor.postbox' );
		this.axisBuilderStatus = this.axisBuilderEditor.find( 'input[name=axisbuilder_status]' );

		// WordPress tinyMCE {Defined|Version|Content}
		this.tinyMceDefined = typeof window.tinyMCE !== 'undefined' ? true : false;
		this.tinyMceVersion = this.tinyMceDefined ? window.tinyMCE.majorVersion : false;
		this.tinyMceContent = this.tinyMceDefined ? window.tinyMCE.get( 'content' ) : false;

		// Activate the Builder
		this.builderActivate();
	};

	$.AxisBuilder.prototype = {

		// Activate the Whole Interface
		builderActivate: function() {
			this.builderTop();
			this.addBehaviour();
		},

		// Move the editor to the first position and trigger postbox saving, in case it is no already at the first pos :)
		builderTop: function() {
			var meta_box = $( '#normal-sortables' ), post_box = meta_box.find( '.postbox' );

			// if ( this.axisBuilderCanvas.length && post_box.index( this.axisBuilderCanvas ) !== 0 ) {
			// 	this.axisBuilderCanvas.prependTo( meta_box );
			// 	window.postboxes.save_order( pagenow );
			// };

		},

		// All event binding goes here
		addBehaviour: function() {
			var self = this;

			// Toggle between default editor and page builder
			this.axisBuilderButton.on( 'click', function( e ) {
				e.preventDefault();
				self.switchEditors();
			});
		},

		// Switch between the {WordPress|AxisBuilder} Editors
		switchEditors: function() {

			if ( this.axisBuilderStatus.val() !== 'active' ) {
				this.wpDefaultEditor.parent().addClass( 'axisbuilder-hidden-editor' );
				this.axisBuilderButton.removeClass( 'button-primary' ).addClass( 'button-secondary' ).text( this.axisBuilderButton.data( 'default-editor' ) );
				this.axisBuilderEditor.removeClass( 'axisbuilder-hidden');
				this.axisBuilderStatus.val( 'active' );
			} else {
				this.wpDefaultEditor.parent().removeClass( 'axisbuilder-hidden-editor' );
				this.axisBuilderButton.addClass( 'button-primary' ).removeClass( 'button-secondary' ).text( this.axisBuilderButton.data( 'page-builder' ) );
				this.axisBuilderEditor.addClass( 'axisbuilder-hidden');
				this.axisBuilderStatus.val( 'inactive' );
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
