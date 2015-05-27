/* global jQuery, Backbone, _ */
( function( $, Backbone, _ ) {
	'use strict';

	/**
	 * AxisComposer Backbone Modal Plugin
	 *
	 * @param {object} options
	 */
	$.fn.ACBackboneModal = function( options ) {
		return this.each( function() {
			( new $.ACBackboneModal( $( this ), options ) );
		});
	};

	/**
	 * Initialize the Backbone Modal
	 *
	 * @param {object} element [description]
	 * @param {object} options [description]
	 */
	$.ACBackboneModal = function( element, options ) {
		// Set Settings
		var settings = $.extend( {}, $.ACBackboneModal.defaultOptions, options );

		if ( settings.template ) {
			new $.ACBackboneModal.View({
				title: settings.title,
				message: settings.message,
				dismiss: settings.dismiss,
				target: settings.template
			});
		}
	};

	/**
	 * Set default options
	 *
	 * @type {object}
	 */
	$.ACBackboneModal.defaultOptions = {
		title: '',
		message: '',
		dismiss: '',
		template: ''
	};

	/**
	 * Create the Backbone Modal
	 *
	 * @return {null}
	 */
	$.ACBackboneModal.View = Backbone.View.extend({
		tagName: 'div',
		id: 'ac-backbone-modal-dialog',
		_title:   undefined,
		_message: undefined,
		_dismiss: undefined,
		_target:  undefined,
		events: {
			'click .modal-close': 'closeButton',
			'click #btn-ok':      'addButton',
			'keydown':            'keyboardActions'
		},
		initialize: function( data ) {
			this._title   = data.title;
			this._message = data.message;
			this._dismiss = data.dismiss;
			this._target  = data.target;
			_.bindAll( this, 'render' );
			this.render();
		},
		render: function() {
			var variables = {
				title:   this._title,
				message: this._message,
				dismiss: this._dismiss
			};

			this.$el.attr( 'tabindex', '0' ).append( _.template( $( this._target ).html(), variables ) );

			$( document.body ).css({
				'overflow': 'hidden'
			}).append( this.$el );

			var $content  = $( '.ac-backbone-modal-content' ).find( 'article' );
			var content_h = ( $content.height() < 90 ) ? 90 : $content.height();
			var max_h     = $( window ).height() - 200;

			if ( max_h > 400 ) {
				max_h = 400;
			}

			if ( content_h > max_h ) {
				$content.css({
					'overflow': 'auto',
					height: max_h + 'px'
				});
			} else {
				$content.css({
					'overflow': 'visible',
					height: ( content_h > 90 ) ? 'auto' : content_h + 'px'
				});
			}

			$( '.ac-backbone-modal-content' ).css({
				'margin-top': '-' + ( $( '.ac-backbone-modal-content' ).height() / 2 ) + 'px',
				'margin-left': '-' + ( $( '.ac-backbone-modal-content' ).width() / 2 ) + 'px'
			});

			$( document.body ).trigger( 'ac_backbone_modal_loaded', this._target );
		},
		closeButton: function( e ) {
			e.preventDefault();
			$( document.body ).trigger( 'ac_backbone_modal_before_remove', this._target );
			this.undelegateEvents();
			$( document ).off( 'focusin' );
			$( document.body ).css({
				'overflow': 'auto'
			});
			this.remove();
			$( document.body ).trigger( 'ac_backbone_modal_removed', this._target );
		},
		addButton: function( e ) {
			$( document.body ).trigger( 'ac_backbone_modal_response', [ this._target, this.getFormData() ] );
			this.closeButton( e );
		},
		getFormData: function() {
			var data = {};

			$( document.body ).trigger( 'ac_backbone_modal_before_update', this._target );

			$.each( $( 'form', this.$el ).serializeArray(), function( index, item ) {
				if ( data.hasOwnProperty( item.name ) ) {
					data[ item.name ] = $.makeArray( data[ item.name ] );
					data[ item.name ].push( item.value );
				} else {
					data[ item.name ] = item.value;
				}
			});

			return data;
		},
		keyboardActions: function( e ) {
			var button = e.keyCode || e.which;

			// Enter key
			if ( 13 === button && ! ( e.target.tagName && ( e.target.tagName.toLowerCase() === 'input' || e.target.tagName.toLowerCase() === 'textarea' ) ) ) {
				this.addButton( e );
			}

			// ESC key
			if ( 27 === button ) {
				this.closeButton( e );
			}
		}
	});

}( jQuery, Backbone, _ ));
