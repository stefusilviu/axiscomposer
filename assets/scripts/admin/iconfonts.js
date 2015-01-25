/* global jQuery, Backbone, _ */

var AB_Icon_Fonts = AB_Icon_Fonts || {};

/**
 * AxisBuilder Backbone Iconfonts JS
 */
( function ( $, Backbone, _ ) {
	'use strict';

	// Views
	AB_Icon_Fonts.AppView = Backbone.View.extend({
		el: '#axisbuilder-iconfonts',
		events: {
			'click .add-iconfont': 'addButton'
		},
		initialize: function() {
			_.bindAll( this, 'render' );
			this.render();
		},
		render: function() {
			console.log( 'Pagebuilder render goes here!' );
		},
		addButton: function ( e ) {
			var clicked = $( '.add-iconfont' );

			e.preventDefault();

			var axisbuilder_file_frame = wp.media.frames.axisbuilder_file_frame = wp.media({
				title: clicked.data( 'title' ),
				library: {
					type: clicked.data( 'type' )
				},
				button: {
					text: clicked.data( 'button' )
				},
				multiple: false
			});

			// Open the Media Frame
			axisbuilder_file_frame.open();
		}
	});

	// Kick things off by creating the 'App'
	$( document ).ready( function() {
		new AB_Icon_Fonts.AppView();
	});

}( jQuery, Backbone, _ ));
