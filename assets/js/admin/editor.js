/*global tinymce */

( function () { // ($) Should be here in function

	'use strict';

	/**
	 * Create the Plugin.
	 */
	tinymce.create( 'tinymce.plugins.AC_Shortcodes', {

		/**
		 * Initialize the Plugin.
		 * @param  {tinymce.Editor} editor Editor instance that the plugin is initialized in.
		 * @return {null}
		 */
		init: function( editor ) {
			var ed = tinymce.activeEditor, self = this;

			editor.addButton( 'axiscomposer_shortcodes', {
				title : ed.getLang( 'axiscomposer_shortcodes.shortcode_title' ),
				icon: 'axiscomposer-shortcodes',
				type: 'menubutton',
				menu: self.createMenu()
			});

			// editor.addCommand( 'Open_ACBackboneModal', function( ui, params ) {
				// var modal = new $.Open_ACBackboneModal( params );
				// return false;
			// });
		},

		/**
		 * Structure the Menu.
		 */
		createMenu: function() {
			var ed         = tinymce.activeEditor,
				shortcodes = ed.getLang( 'axiscomposer_shortcodes.shortcodes' ),
				title, dropdown, self = this, tabs = [], submenu = [], loop = 0;

			// Get all tabs
			for ( var i in shortcodes ) {
				tabs[shortcodes[i].type] = [];
			}

			// Create the sub-menus
			for ( title in tabs ) {
				if ( title !== 'undefined' ) {
					loop++;
					submenu.push({
						text: ed.getLang( 'axiscomposer_shortcodes.' + title + '_label' ),
						menu: []
					});
				}
			}

			// Add items to sub-menus.
			for ( dropdown in shortcodes ) {

				// Set default
				shortcodes[dropdown].tinyMCE = shortcodes[dropdown].tinyMCE || {};

				// Only render subset of elements if backbone modal is open
				// if ( modal.length == 0 && ! shortcodes[dropdown].type || typeof shortcodes[dropdown].tinyMCE.tiny_always !== 'undefined' ) {

					var current = submenu, paramText = '', paramOnClick = '';

					for ( title in submenu ) {
						if ( title !== 'undefined' ) {
							var text = ed.getLang( 'axiscomposer_shortcodes.' + shortcodes[dropdown].type + '_label' );

							if ( submenu[title].text === text ) {
								current = submenu[title].menu;
							}
						}
					}

					paramText    = shortcodes[dropdown].tinyMCE.name || shortcodes[dropdown].title;
					paramOnClick = ( typeof shortcodes[dropdown].tinyMCE.instantInsert !== 'undefined' ) ? self.instantInsert : self.modalInsert;

					current.push({
						text: paramText,
						onClick: paramOnClick,
						axiscomposer_shortcode: shortcodes[dropdown]
					});

				// }
			}

			// Remove empty sub-menus
			for ( title in submenu ) {
				if ( ( title !== 'undefined' ) && ( typeof submenu[title].menu !== 'undefined' ) ) {
					if ( submenu[title].menu.length === 0 ) {
						delete( submenu[title] );
					}
				}
			}

			return submenu;
		},

		instantInsert: function() {
			var ed        = tinymce.activeEditor,
				shortcode = this.settings.axiscomposer_shortcode;

			// Execute command ;)
			ed.execCommand( 'mceInsertContent', false, window.switchEditors.wpautop( shortcode.tinyMCE.instantInsert ) );
		},

		modalInsert: function() {
			// Load Backbone Modal soon ;)
		}
	});

	/**
	 * Register the Plugin.
	 */
	tinymce.PluginManager.add( 'axiscomposer_shortcodes', tinymce.plugins.AC_Shortcodes );

})( jQuery );
