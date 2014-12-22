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
		};

		$.AxisBuilderModal.openInstance.unshift( this );

		this.doc        = $( document );
		this.wrap       = $( '#wpwrap' );
		this.body       = $( 'body' ).addClass( 'axisbuilder-noscroll' );
		this.options    = $.extend( {}, defaults, options );
		this.modal      = $( '<div class="axisbuilder-modal axisbuilder-style popup-animation"></div>' );
		this.backdrop   = $( '<div class="axisbuilder-modal-backdrop"></div>' );
		this.instanceNr = $.AxisBuilderModal.openInstance.length;
		this.namespace  = '.AxisBuilderModal' + this.instanceNr;

		// Setup the Modal
		this.modalSetup();
	};

	$.AxisBuilderModal.openInstance = [];
	$.AxisBuilderModal.prototype = {

		modalSetup: function() {
			this.appendHTML();
			this.modalBehaviour();
			this.modifyBindingOrder();
			this.propogateModalopen();
		},

		appendHTML: function() {
			var output,
				content = this.options.modal_content ? this.options.modal_content : '',
				loading = this.options.modal_content ? '' : 'loader ',
				heading = '<h3 class="axisbuilder-modal-title">' + this.options.modal_title + '</h3>';

			output  = '<div class="axisbuilder-modal-inner">';
				output += '<div class="axisbuilder-modal-inner-header">' + heading + '<a href="#close" class="axisbuilder-modal-close trash-modal-icon axisbuilder-attach-close-event"></a></div>';
				output += '<div class="axisbuilder-modal-inner-content ' + loading + '">' + content + '</div>';
				output += '<div class="axisbuilder-modal-inner-footer">';
					if ( this.options.button === 'save' ) {
						output += '<a href="#save" class="axisbuilder-save-modal button button-primary button-large">' + axisbuilder_modal.save + '</a>';
					} else if ( this.options.button === 'close' ) {
						output += '<a href="#close" class="axisbuilder-attach-close-event button button-primary button-large">' + axisbuilder_modal.close + '</a>';
					} else {
						output += this.options.button;
					}
				output += '</div>';
			output += '</div>';

			// Set specified modal class
			if ( this.options.modal_class ) {
				this.modal.addClass( this.options.modal_class );
			}

			// Append the Modal on this.wrap instead of this.body to prevent bug with link editor popup ;)
			this.wrap.append( this.modal ).append( this.backdrop );
			this.modal.html( output );

			// Set modal margin and z-index for nested modals
			var multiplier = ( this.instanceNr - 1 ),
				z_previous = parseInt( this.modal.css( 'zIndex' ), 10 );

			this.modal.css({ margin: ( 30 * multiplier ), zIndex: ( z_previous + multiplier + 1 )});
			this.backdrop.css({ zIndex: ( z_previous + multiplier )});

			// Modal Content
			// if ( ! this.options.modal_content ) {
			// 	this.fetchContent();
			// } else {
			// 	this.loadCallback();
			// }
		},

		modalBehaviour: function() {
			var obj = this;

			// Save Modal event (execute callback)
			this.modal.on( 'click', '.axisbuilder-save-modal', function() {
				// obj.executeCallback();
				return false;
			});

			// Close Modal event
			this.backdrop.add( '.axisbuilder-attach-close-event', this.modal ).on( 'click', function() {
				obj.close();
				return false;
			});

			// Save and Close Modal events on Enter/Escape keypress.
			this.doc.bind( 'keydown' + this.namespace, function( e ) {
				if ( obj.linkOverlayClosed && obj.mediaOverlayClosed ) {

					// Save Event
					if ( e.keyCode === 13 && ! ( e.target.tagName && e.target.tagName.toLowerCase() === 'textarea' ) ) {
						setTimeout( function() {
							// obj.executeCallback();
						}, 100 );

						e.stopImmediatePropagation();
					}

					// Close Event
					if ( e.keyCode === 27 ) {
						setTimeout( function() {
							obj.close();
						}, 100 );

						e.stopImmediatePropagation();
					}
				}
			});
		},

		modifyBindingOrder: function() {
			var data = jQuery.hasData( document ) && jQuery._data( document ),
				lastItem = data.events.keydown.pop();

			data.events.keydown.unshift( lastItem );
		},

		close: function() {
			// Remove the first entry from the openInstance array.
			$.AxisBuilderModal.openInstance.shift();

			this.doc.trigger( 'axisbuilder_modal_before_close', [ this ] );
			this.modal.remove();
			this.backdrop.remove();
			this.doc.trigger( 'axisbuilder_modal_close', [ this ] ).unbind( 'keydown' + this.namespace );

			if ( $.AxisBuilderModal.openInstance.length === 0 ) {
				this.body.removeClass( 'axisbuilder-noscroll' );
			}
		},

		linkOverlayClosed: function() {
			var _linkOverlay = $( '#wp-link-wrap:visible' );
			return _linkOverlay.length ? false : true;
		},

		mediaOverlayClosed: function() {
			return true; // Will perform at later point ;)
		},

		propogateModalopen: function() {
			this.body.trigger( 'axisbuilder_modal_open', this );
		},

		propogateModalContent: function() {
			this.body.trigger( 'axisbuilder_modal_open', this );
		}
	};

})( jQuery );
