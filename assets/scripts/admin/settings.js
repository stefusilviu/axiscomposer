/* global axisbuilder_settings_params */

/**
 * AxisBuilder Settings JS
 */
jQuery( window ).load(function(){

	// Screens
	jQuery( 'select#axisbuilder_allowed_screens' ).change( function() {
		if ( jQuery( this ).val() === 'specific' ) {
			jQuery( this ).parent().parent().next( 'tr' ).show();
		} else {
			jQuery( this ).parent().parent().next( 'tr' ).hide();
		}
	}).change();

	// Edit prompt
	jQuery(function(){
		var changed = false;

		jQuery( 'input, textarea, select, checkbox' ).change(function(){
			changed = true;
		});

		jQuery( '.axisbuilder-nav-tab-wrapper a' ).click(function(){
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

	// Select all/none
	jQuery( '.axisbuilder' ).on( 'click', '.select_all', function() {
		jQuery( this ).closest( 'td' ).find( 'select option' ).attr( 'selected', 'selected' );
		jQuery( this ).closest( 'td' ).find( 'select' ).trigger( 'change' );
		return false;
	});

	jQuery( '.axisbuilder' ).on( 'click', '.select_none', function() {
		jQuery( this ).closest( 'td' ).find( 'select option' ).removeAttr( 'selected' );
		jQuery( this ).closest( 'td' ).find( 'select' ).trigger( 'change' );
		return false;
	});
});
