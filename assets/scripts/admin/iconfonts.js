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

			// Create the media frame.
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

			// When an ZIP file is selected, run a callback.
			axisbuilder_file_frame.on( 'select', function() {
				var attachment = axisbuilder_file_frame.state().get( 'selection' ).first().toJSON();

				// Do something with attachment.id and/or attachment.url here
				console.log( attachment.id );
				console.log( attachment.url );
			});

			// Finally, open the modal
			axisbuilder_file_frame.open();
		}
	});

	// Kick things off by creating the 'App'
	$( document ).ready( function() {
		new AB_Icon_Fonts.AppView();
	});

}( jQuery, Backbone, _ ));
