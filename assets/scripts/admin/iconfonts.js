/* global jQuery, Backbone, _ */

var AB_Icon_Fonts = AB_Icon_Fonts || {};

/**
 * AxisBuilder Backbone Iconfonts JS
 */
( function ( $, Backbone, _ ) {

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
			e.preventDefault();

			console.log( 'add-iconfont triggered' );
		}
	});

	// Kick things off by creating the 'App'
	$( document ).ready( function() {
		new AB_Icon_Fonts.AppView();
	});

}( jQuery, Backbone, _ ));
