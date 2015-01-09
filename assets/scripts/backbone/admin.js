/* global jQuery, Backbone, _ */

var AB_Builder = AB_Builder || {};

/**
 * AxisBuilder Admin JS
 */
( function ( $, Backbone, _ ) {
	'use strict';

	$( document ).ready( function() {

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
	});

}( jQuery, Backbone, _ ));
