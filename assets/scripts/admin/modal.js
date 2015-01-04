/* global AB_Logger, axisbuilder_modal, quicktags, QTags */

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
			modal_action: '',       // @string   name of php ajax hook that will execute the content fetching function
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
						output += '<a href="#save" class="axisbuilder-save-modal button button-primary button-large">' + axisbuilder_modal.i18n_save_button + '</a>';
					} else if ( this.options.button === 'close' ) {
						output += '<a href="#close" class="axisbuilder-attach-close-event button button-primary button-large">' + axisbuilder_modal.i18n_close_button + '</a>';
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

			// Load Modal Content
			if ( ! this.options.modal_content ) {
				this.fetchContent();
			} else {
				this.loadCallback();
			}
		},

		fetchContent: function() {
			var obj   = this,
				inner = obj.modal.find( '.axisbuilder-modal-inner-content' ),
				data  = {
					fetch: true,
					params: this.options.ajax_param,
					action: 'axisbuilder_' + this.options.modal_action,
					security: axisbuilder_modal.get_modal_elements_nonce,
					instance: this.instanceNr
				};

			$.ajax({
				url: axisbuilder_modal.ajax_url,
				data: data,
				type: 'POST',
				error: function() {
					$.AxisBuilderModal.openInstance[0].close();
					$.AxisBuilderModalNotification({
						mode: 'error',
						message: axisbuilder_modal.i18n_ajax_error
					});
				},
				success: function( response ) {
					if ( response === '0' ) {
						$.AxisBuilderModal.openInstance[0].close();
						$.AxisBuilderModalNotification({
							mode: 'error',
							message: axisbuilder_modal.i18n_login_error
						});
					} else if ( response === '-1' ) {
						$.AxisBuilderModal.openInstance[0].close();
						$.AxisBuilderModalNotification({
							mode: 'error',
							message: axisbuilder_modal.i18n_session_error
						});
					} else {
						inner.html( response );
						obj.loadCallback();
					}
				},
				complete: function() {
					inner.removeClass( 'loader' );
				}
			});
		},

		loadCallback: function() {
			var callbacks = this.options.on_load, index = 0, execute;

			if ( typeof callbacks === 'string' ) {
				execute = callbacks.split( ', ' );

				for ( index in execute ) {
					if ( $.AxisBuilderModal.registerCallback[ execute[index] ] !== 'undefined' ) {
						$.AxisBuilderModal.registerCallback[ execute[index] ].call( this );
					} else {
						new AB_Logger( 'Not defined modal_on_load function: $.AxisBuilderModal.register_callback.' + execute[index], 'error' );
						new AB_Logger( 'Ensure that the modal_on_load function is defined in your Shortcodes Configurations.', 'help' );
					}
				}
			} else if ( typeof callbacks === 'function' ) {
				callbacks.call();
			}

			this.setFocus();
			this.propogateModalContent();
		},

		setFocus: function() {
			var field = this.modal.find( 'input[type=text], input[type=checkpox], textarea, select, radio' ).filter( ':eq(0)' );

			if ( ! field.is( '.axisbuilder-autoselect-off' ) ) {
				field.focus();
			}
		},

		modalBehaviour: function() {
			var obj = this;

			// Save Modal event (execute callback)
			this.modal.on( 'click', '.axisbuilder-save-modal', function() {
				obj.executeCallback();
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

					// Ensure event is not null
					e = e || window.event;

					// Save Event
					if ( e.which === 13 && ! ( e.target.tagName && ( e.target.tagName.toLowerCase() === 'input' || e.target.tagName.toLowerCase() === 'textarea' ) ) ) {
						setTimeout( function() {
							obj.executeCallback();
						}, 100 );

						e.stopImmediatePropagation();
					}

					// Close Event
					if ( e.which === 27 ) {
						setTimeout( function() {
							obj.close();
						}, 100 );

						e.stopImmediatePropagation();
					}
				}
			});
		},

		executeCallback: function() {
			var values = this.modal.find( 'input, textarea, select, radio' ).serializeArray(),
				value_array = this.convertValues( values );

			// Filter function for the value array in case we got a special shortcode like tables :)
			if ( typeof $.AxisBuilderModal.registerCallback[ this.options.before_save ] !== 'undefined' ) {
				value_array = $.AxisBuilderModal.registerCallback[ this.options.before_save ].call( this.options.scope, value_array, this.options.save_param );
			}

			var close_allowed = this.options.on_save.call( this.options.scope, value_array, this.options.save_param );

			if ( close_allowed !== false ) {
				this.close();
			}
		},

		convertValues: function( data ) {
			var output = {};

			$.each( data, function() {
				if ( typeof output[this.name] !== 'undefined' ) {
					if ( ! output[this.name].push ) {
						output[this.name] = [output[this.name]];
					}
					output[this.name].push( this.value || '' );
				} else {
					output[this.name] = this.value || '';
				}
			});

			return output;
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
			this.body.trigger( 'axisbuilder_modal_finished', this );
		}
	};

	// Wrapper for Modal notifications
	$.AxisBuilderModalNotification = function( options ) {
		var defaults = {
			button: 'close',
			modal_class: 'flexscreen',
			modal_title: '<span class="axisbuilder-message-' + options.mode + '">' + axisbuilder_modal[options.mode] + '</span>',
			modal_content: '<div class="axisbuilder-form-element-container">' + options.message + '</div>'
		};

		this.options = $.extend( {}, defaults, options );
		return new $.AxisBuilderModal( this.options );
	};

	// Allowed callbacks once the modal opens.
	$.AxisBuilderModal.registerCallback = $.AxisBuilderModal.registerCallback || {};

	$.AxisBuilderModal.registerCallback.modal_load_tinymce = function( textareas ) {
		textareas = textareas || this.modal.find( '.axisbuilder-modal-inner-content .axisbuilder-tinymce' );

		var self     = this,
			modal    = textareas.parents( '.axisbuilder-modal:eq(0)' ),
			save_btn = modal.find( '.axisbuilder-save-modal' );

		textareas.each( function() {
			var el_id       = this.id,
				current     = $( this ),
				parents     = current.parents( '.wp-editor-wrap:eq(0)' ),
				textarea    = parents.find( 'textarea.axisbuilder-tinymce' ),
				switch_btn  = parents.find( '.wp-switch-editor' ).removeAttr( 'onclick' ),
				tinyVersion = window.tinyMCE.majorVersion,
				executeAdd  = 'mceAddControl',
				executeRem  = 'mceRemoveControl',
				settings    = {
					id: this.id,
					buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,spell,close'
				};

			if ( tinyVersion >= 4 ) {
				executeAdd = 'mceAddEditor';
				executeRem = 'mceRemoveEditor';
			}

			// Add quicktags for text editor
			quicktags( settings );
			QTags._buttonsInit(); // Workaround since DOM ready was triggered already and there would be no initialization ;)

			// Modify behavior for html editor
			switch_btn.bind( 'click', function() {
				var button = $( this );

				if ( button.is( '.switch-tmce' ) ) {
					parents.removeClass( 'html-active' ).addClass( 'tmce-active' );
					window.tinyMCE.execCommand( executeAdd, true, el_id );
					window.tinyMCE.get( el_id ).setContent( window.switchEditors.wpautop( textarea.val() ), { format:'raw' });
				} else {
					var value = textarea.val();
					if ( window.tinyMCE.get( el_id ) ) {
						// Fixes the problem with galleries and more tag that got an image representation of the shortcode ;)
						value = window.tinyMCE.get( el_id ).getContent();
					}

					parents.removeClass( 'tmce-active' ).addClass( 'html-active' );
					window.tinyMCE.execCommand( executeRem, true, el_id );
					if ( tinyVersion >= 4 ) {
						textarea.val( window.switchEditors._wp_Nop( value ) );
					}
				}
			}).trigger( 'click' );

			// Ensure when save button is clicked, the textarea gets updated and sent to the editor
			save_btn.bind( 'click', function() {
				switch_btn.filter( '.switch-html' ).trigger( 'click' );
			});

			// Ensure that the instance is removed if the modal was clicked in anyway ;)
			if ( tinyVersion >= 4 ) {
				$( document ).bind( 'axisbuilder_modal_before_close' + self.namespace + 'tiny_close', function( e, modal ) {
					if ( self.namespace = modal.namespace ) {
						window.tinyMCE.execCommand( executeRem, true, el_id );
						$( document ).bind( 'axisbuilder_modal_before_close' + self.namespace + 'tiny_close' );
					}
				});
			}
		});
	};

	$.AxisBuilderModal.registerCallback.modal_load_colorpicker = function() {
		this.modal.find( '.colorpicker' ).wpColorPicker();

		// var scope        = this.modal,
		// 	color_picker = scope.find( '.colorpicker' ),
		// 	color_result = scope.find( '.wp-color-result' );

		// color_picker.wpColorPicker().click( function() {
		// 	var parent = $( this ).parents( '.wp-picker-container:eq(0)' ),
		// 		picker = parent.find( '.wp-picker-holder .iris-picker' ),
		// 		button = parent.find( '.wp-color-result' );

		// 	if ( picker.css( 'display' ) !== 'block' ) {
		// 		picker.css({ display: 'block' });
		// 	}

		// 	if ( ! button.hasClass( 'wp-picker-open' ) ) {
		// 		button.addClass( 'wp-picker-open' );
		// 	}

		// 	scope.find( '.wp-picker-open' ).not( button ).trigger( 'click' );

		// 	$( 'body' ).on( 'click', function() {
		// 		if ( picker.css( 'display' ) === 'block' ) {
		// 			picker.css({ display: 'none' });
		// 		}

		// 		if ( button.hasClass( 'wp-picker-open' ) ) {
		// 			button.removeClass( 'wp-picker-open' );
		// 		}
		// 	});
		// });

		// color_result.click( function() {
		// 	if ( typeof e.originalEvent !== 'undefined' ) {
		// 		var open = scope.find( '.wp-picker-open' ).not( this ).trigger( 'click' );
		// 	}
		// });
	};

})( jQuery );
