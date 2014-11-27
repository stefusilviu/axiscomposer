/**
 * AxisBuilder Admin JS
 */
( function( $ ) {

	'use strict';

	$.AxisBuilder = function() {

		// WordPress default tinyMCE editor Element
		this.wpDefaultEditor = $( '#postdivrich' );

		// Axis Page Builder {Status|Button|Editor}
		this.axisBuilderStatus = $( '#axisbuilder-editor' ); // should be changed
		this.axisBuilderEditor = $( '#axisbuilder-editor' );
		this.axisBuilderButton = $( '#axisbuilder-button' );

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
			this.behaviour();
		},

		// All event binding goes here
		behaviour: function() {
			var self = this;

			// Toggle between default editor and page builder
			this.axisBuilderButton.on( 'click', function( e ) {
				e.preventDefault();
				self.switchEditor();
			});
		},

		// Switch between the {WordPress|AxisBuilder} Editors
		switchEditor: function() {

			if ( this.axisBuilderStatus.val() !== 'active' ) {
				$( '#content-tmce' ).trigger( 'click' );
				this.wpDefaultEditor.parent().addClass( 'axisbuilder-hidden-editor' );
				this.axisBuilderStatus.val( 'active' );
				this.axisBuilderEditor.removeClass( 'axisbuilder-hidden');
				this.axisBuilderButton.addClass( 'button-secondary' ).removeClass( 'button-primary' ).text( this.axisBuilderButton.data( 'default-editor' ) );

				setTimeout( function() {
					$( '#content-tmce' ).trigger( 'click' );
				}, 10 );
			} else {
				this.wpDefaultEditor.parent().removeClass( 'axisbuilder-hidden-editor' );
				this.axisBuilderStatus.val( 'inactive' );
				this.axisBuilderEditor.addClass( 'axisbuilder-hidden');
				this.axisBuilderButton.addClass( 'button-primary' ).removeClass( 'button-secondary' ).text( this.axisBuilderButton.data( 'page-builder' ) );

				$( window ).trigger( 'scroll' );
			}

			return false;
		}
	};

	$( document ).ready( function () {
		$.AxisBuilderObj = new $.AxisBuilder();
	});

})(jQuery);
