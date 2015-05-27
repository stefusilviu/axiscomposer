/* global axisbuilder_settings_params */
jQuery( window ).load( function() {

	// Color picker
	jQuery( '.colorpick' ).iris({
		change: function( event, ui ) {
			jQuery( this ).css({ backgroundColor: ui.color.toString() });
		},
		hide: true,
		border: true
	}).each( function() {
		jQuery( this ).css({ backgroundColor: jQuery( this ).val() });
	}).click( function() {
		jQuery( '.iris-picker' ).hide();
		jQuery( this ).closest( '.color_box, td' ).find( '.iris-picker' ).show();
	});

	jQuery( 'body' ).click( function() {
		jQuery( '.iris-picker' ).hide();
	});

	jQuery( '.color_box, .colorpick' ).click( function( event ) {
		event.stopPropagation();
	});

	// Edit prompt
	jQuery( function() {
		var changed = false;

		jQuery( 'input, textarea, select, checkbox' ).change( function() {
			changed = true;
		});

		jQuery( '.axis-nav-tab-wrapper a' ).click( function() {
			if ( changed ) {
				window.onbeforeunload = function() {
				    return axisbuilder_settings_params.i18n_nav_warning;
				};
			} else {
				window.onbeforeunload = '';
			}
		});

		jQuery( '.submit input' ).click( function() {
			window.onbeforeunload = '';
		});
	});
});
