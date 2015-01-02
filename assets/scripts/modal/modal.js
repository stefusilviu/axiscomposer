/* global jQuery, Backbone, _ */

/**
 * AxisBuilder Backbone Modal JS
 */
( function ( $, Backbone, _ ) {
	'use strict';

	/**
	 * AxisBuilder Backbone Modal Plugin
	 *
	 * @param {object} options
	 */
	$.fn.AxisBuilderBackboneModal = function( options ) {
		return this.each( function () {
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
				action: settings.action,
				screen: settings.screen,
				message: settings.message,
				template: settings.template
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
		action: '',
		screen: '',
		message: '',
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
		_action: undefined,
		_screen: undefined,
		_message: undefined,
		_template: undefined,
		events: {
			'keydown': 'keyboardActions',
			'click #button-cancel': 'cancelButton',
			'click #button-action': 'actionButton'
		},
		initialize: function( data ) {
			this._title = data.title;
			this._action = data.action;
			this._screen = data.screen;
			this._message = data.message;
			this._template = data.template;
			_.bindAll( this, 'render' );
			this.render();
		},
		render: function() {
			var variables = {
				title: this._title,
				action: this._action,
				screen: this._screen,
				message: this._message
			};

			this.$el.attr( 'tabindex', '0' ).append( _.template( $( this._template ).html(), variables ) );

			$( 'body' ).css({
				'overflow': 'hidden'
			}).append( this.$el );

			var $content  = $( '.axisbuilder-backbone-modal-content' ).find( 'article' );
			var content_h = $content.height();
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

			$( 'body' ).trigger( 'axisbuilder_backbone_modal_loaded', this._template );
		},
		keyboardActions: function( e ) {
			var button = e.keyCode || e.which;

			// Enter key
			if ( 13 === button && ! ( e.target.tagName && e.target.tagName.toLowerCase() === 'textarea' ) ) {
				this.actionButton( e );
			}

			// ESC key
			if ( 27 === button ) {
				this.cancelButton( e );
			}
		},
		cancelButton: function( e ) {
			e.preventDefault();
			this.undelegateEvents();
			$( document ).off( 'focusin' );
			$( 'body' ).css({
				'overflow': 'auto'
			});
			this.remove();
			$( 'body' ).trigger( 'axisbuilder_backbone_modal_removed', this._template );
		},
		actionButton: function( e ) {
			$( 'body' ).trigger( 'axisbuilder_backbone_modal_response', this._template, this.getFormData() );
			this.cancelButton( e );
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
		}
	});

}( jQuery, Backbone, _ ));
