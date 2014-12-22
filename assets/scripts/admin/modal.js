/* global axisbuilder_modal */

/**
 * AxisBuilder Modal JS
 */
( function( $ ) {

	'use strict';

	$.AxisBuilderModal = function ( options ) {
		var defaults = {
			modal_title: '',        // @string   modal window title
			modal_class: '',        // @string   modal window class
			modal_content: false,   // @string   modal window content. If not specified ajax function will execute
			modal_ajax_hook: '',    // @string   name of php ajax hook that will execute the content fetching function
			button: 'save',         // @string   parameter that tells the modal window which button to generate
			ajax_param: '',         // @string   parameters that are passed to the ajax content fetching function
			save_param: {},         // @obj      parameters that are passed to the callback function in addition to the form values
			scope: this,            // @obj      pass the 'this' var of the invoking function to apply the correct callback later
			on_save: function() {}, // @function modal window callback function when the save button is hit
			on_load: function() {}, // @function modal window callback function when the modal is open and finished loading
			before_save: ''         // @function modal window callback function when the save button is hit and the data is collected but before the final save is executed
		}

		$.AxisBuilderModal.openInstance.unshift( this );

		this.doc        = $( document );
		this.wrap       = $( '#wpwrap' );
		this.body       = $( 'body' ).addClass( 'axisbuilder-noscroll' );
		this.options    = $.extend( {}, defaults, options );
		this.modal      = $( '<div class="axisbuilder-modal axisbuilder-style"></div>' );
		this.backdrop   = $( '<div class="axisbuilder-modal-backdrop"></div>' );
		this.instanceNr = $.AxisBuilderModal.openInstance.length;
		this.namespace  = '.AxisBuilderModal' + this.instanceNr;

		// Activate the Modal
		this.modalActivate();
	};

	$.AxisBuilderModal.openInstance = [];
	$.AxisBuilderModal.prototype = {

		modalActivate: function() {
			this.appendHTML();
			this.modalBehaviour();

		},

		appendHTML: function() {

		},

		modalBehaviour: function() {

		}
	};

})( jQuery );
