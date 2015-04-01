/* global jQuery, Backbone, _ */
( function( $, Backbone, _ ) {
	'use strict';

	/**
	 * AxisBuilder Backbone Modal Plugin
	 *
	 * @param {object} options
	 */
	$.fn.AxisBuilderBackboneModal = function( options ) {
		return this.each( function() {
			( new $.AxisBuilderBackboneModal( $( this ), options ) );
		});
	};

	/**
	 * Initialize the Backbone Modal
	 *
	 * @param {object} element [description]
	 * @param {object} options [description]
	 */
	$.AxisBuilderBackboneModal = function( element, options ) {
		// Set Settings
		var settings = $.extend( {}, $.AxisBuilderBackboneModal.defaultOptions, options );

		if ( settings.template ) {
			new $.AxisBuilderBackboneModal.View({
				title: settings.title,
				screen: settings.screen,
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
	$.AxisBuilderBackboneModal.defaultOptions = {
		title: '',
		screen: '',
		message: '',
		dismiss: '',
		template: ''
	};

	/**
	 * Create the Backbone Modal
	 *
	 * @return {null}
	 */
	$.AxisBuilderBackboneModal.View = Backbone.View.extend({
		tagName: 'div',
		id: 'axisbuilder-backbone-modal-dialog',
		_title: undefined,
		_screen: undefined,
		_message: undefined,
		_dismiss: undefined,
		_target: undefined,
		events: {
			'click .modal-close': 'closeButton',
			'click #btn-ok':      'addButton',
			'keydown':            'keyboardActions'
		},
		initialize: function( data ) {
			this._title   = data.title;
			this._screen  = data.screen;
			this._message = data.message;
			this._dismiss = data.dismiss;
			this._target  = data.target;
			_.bindAll( this, 'render' );
			this.render();
		},
		render: function() {
			var variables = {
				title: this._title,
				message: this._message,
				dismiss: this._dismiss
			};

			this.$el.attr( 'tabindex', '0' ).append( _.template( $( this._target ).html(), variables ) );

			$( 'body' ).css({
				'overflow': 'hidden'
			}).append( this.$el ).trigger( 'axisbuilder_backbone_modal_init', this._target );

			var $content  = $( '.axisbuilder-backbone-modal-content' ).find( 'article' );
			var content_h = ( 0 === $content.height() ) ? 90 : $content.height();
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
					height: content_h
				});
			}

			$( '.axisbuilder-backbone-modal-content' ).css({
				'margin-top': '-' + ( $( '.axisbuilder-backbone-modal-content' ).height() / 2 ) + 'px'
			}).addClass( this._screen );

			$( 'body' ).trigger( 'axisbuilder_backbone_modal_loaded', this._target );
		},
		closeButton: function( e ) {
			e.preventDefault();
			$( 'body' ).trigger( 'axisbuilder_backbone_modal_before_remove', this._target );
			this.undelegateEvents();
			$( document ).off( 'focusin' );
			$( 'body' ).css({
				'overflow': 'auto'
			});
			this.remove();
			$( 'body' ).trigger( 'axisbuilder_backbone_modal_removed', this._target );
		},
		addButton: function( e ) {
			$( 'body' ).trigger( 'axisbuilder_backbone_modal_response', [ this._target, this.getFormData() ] );
			this.closeButton( e );
		},
		getFormData: function() {
			var data = {};

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
