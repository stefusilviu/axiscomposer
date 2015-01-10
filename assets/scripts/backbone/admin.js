/* global jQuery, Backbone, _ */

var AB_Builder = AB_Builder || {};

/**
 * AxisBuilder Admin JS
 */
( function ( $, Backbone, _ ) {
	'use strict';

	// Models
	AB_Builder.Module = Backbone.Model.extend({
		defaults: {
			type: 'elements'
		}
	});

	// Collections
	AB_Builder.Modules = Backbone.Collection.extend({
		model: AB_Builder.Module
	});

	// Views
	AB_Builder.AppView = Backbone.View.extend({
		el: '#axis-pagebuilder',

		initialize: function() {
			_.bindAll( this, 'render' );
			this.render();
		},
		render: function() {
			console.log( 'Pagebuilder render goes here!' );
		}
	});

	// Kick things off by creating the 'App'
	$( document ).ready( function() {
		new AB_Builder.AppView();
	});

}( jQuery, Backbone, _ ));
