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
				message: settings.message,
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
		_target: undefined,
		_message: undefined,
		events: {
			'click #btn-cancel': 'closeButton',
			'click #btn-delete': 'deleteButton',
			'click #btn-save': 'saveButton'
		},
		initialize: function ( data ) {
			this._title = data.title;
			this._target = data.target;
			this._message = data.message;
			_.bindAll( this, 'render' );
			this.render();
		},
		render: function () {
			var variables = { title: this._title, message: this._message };

			this.$el.attr( 'tabindex', '0' ).append( _.template( $( this._target ).html(), variables ) );

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
			});

			$( 'body' ).trigger( 'axisbuilder_backbone_modal_loaded', this._target );
		},
		closeButton: function ( e ) {
			e.preventDefault();
			this.undelegateEvents();
			$( document ).off( 'focusin' );
			$( 'body' ).css({
				'overflow': 'auto'
			});
			this.remove();
			$( 'body' ).trigger( 'axisbuilder_backbone_modal_removed', this._target );
		},
		deleteButton: function ( e ) {
			$( 'body' ).trigger( 'axisbuilder_backbone_modal_delete', this._target, this.getFormData() );
			this.closeButton( e );
		},
		saveButton: function ( e ) {
			$( 'body' ).trigger( 'axisbuilder_backbone_modal_response', this._target, this.getFormData() );
			this.closeButton( e );
		},
		getFormData: function () {
			var data = {};

			$.each( $( 'form', this.$el ).serializeArray(), function( index, item ) {
				if ( data.hasOwnProperty( item.name ) ) {
					data[ item.name ] = $.makeArray( data[ item.name ] );
					data[ item.name ].push( item.value );
				}
				else {
					data[ item.name ] = item.value;
				}
			});

			return data;
		}
	});

}( jQuery, Backbone, _ ));
