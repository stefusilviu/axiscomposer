/*global tinymce */

( function ( $ ) {

	'use strict';

	/**
	 * Create the Plugin.
	 */
	tinymce.create( 'tinymce.plugins.axisbuilder_shortcodes', {

        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the on Init event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         */
		init: function( editor ) {
			var ed = tinymce.activeEditor, self = this;

			editor.addButton( 'axisbuilder_shortcodes', {
				title : ed.getLang( 'axisbuilder_shortcodes.shortcode_title' ),
				text: ed.getLang( 'axisbuilder_shortcodes.shortcode_text' ),
				icon: 'axisbuilder-shortcodes',
				type: 'menubutton',
				menu: self.createMenu( editor )
			});

			editor.addCommand( 'Open_AxisBuilderModal', function( ui, params ) {
				var modal = new $.AxisBuilderModal( params );
				return false;
			});
		},

		/**
		 * Structure the Menu.
		 */
		createMenu: function( editor ) {
			var self            = this,
				counter         = 0,
				submenu         = [],
				final_options   = [],
				open_modal      = $.AxisBuilderModal.openInstance || [];
				// shortcode_array = axisbuilder_globals.shortcode['axisbuilder_shortcodes'].config;

		}
	});

	/**
	 * Register the Plugin.
	 */
	tinymce.PluginManager.add( 'axisbuilder_shortcodes', tinymce.plugins.axisbuilder_shortcodes );

})( jQuery );
