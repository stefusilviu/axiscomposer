/* global axiscomposer_settings_params */
( function( $ ) {

	// Color picker
	$( '.colorpick' ).iris({
		change: function( event, ui ) {
			$( this ).parent().find( '.colorpickpreview' ).css({ backgroundColor: ui.color.toString() });
		},
		hide: true,
		border: true
	}).click( function() {
		$( '.iris-picker' ).hide();
		$( this ).closest( 'td' ).find( '.iris-picker' ).show();
	});

	$( 'body' ).click( function() {
		$( '.iris-picker' ).hide();
	});

	$( '.colorpick' ).click( function( event ) {
		event.stopPropagation();
	});

	// Edit prompt
	$( function() {
		var changed = false;

		$( 'input, textarea, select, checkbox' ).change( function() {
			changed = true;
		});

		$( '.axis-nav-tab-wrapper a' ).click( function() {
			if ( changed ) {
				window.onbeforeunload = function() {
				    return axiscomposer_settings_params.i18n_nav_warning;
				};
			} else {
				window.onbeforeunload = '';
			}
		});

		$( '.submit input' ).click( function() {
			window.onbeforeunload = '';
		});
	});

	// Select all/none
	$( '.axiscomposer' ).on( 'click', '.select_all', function() {
		$( this ).closest( 'td' ).find( 'select option' ).attr( 'selected', 'selected' );
		$( this ).closest( 'td' ).find( 'select' ).trigger( 'change' );
		return false;
	});

	$( '.axiscomposer' ).on( 'click', '.select_none', function() {
		$( this ).closest( 'td' ).find( 'select option' ).removeAttr( 'selected' );
		$( this ).closest( 'td' ).find( 'select' ).trigger( 'change' );
		return false;
	});
})( jQuery );
