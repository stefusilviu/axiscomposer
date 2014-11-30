/*global tinymce */

( function () {

	/**
	 * Check is empty.
	 *
	 * @param  {string} value
	 * @return {bool}
	 */
	function axisShortcodesIsEmpty( value ) {
		value = value.toString();

		if ( 0 !== value.length ) {
			return false;
		}

		return true;
	}

	/**
	 * Add the shortcodes downdown.
	 */
	tinymce.PluginManager.add( 'axisbuilder_shortcodes', function ( editor ) {
		var ed = tinymce.activeEditor;
		editor.addButton( 'axisbuilder_shortcodes', {
			title : ed.getLang( 'axisbuilder_shortcodes.shortcode_title' ),
			text: ed.getLang( 'axisbuilder_shortcodes.shortcode_text' ),
			icon: 'axisbuilder-shortcodes',
			type: 'menubutton',
			menu: [

			]
		});
	});
})();
