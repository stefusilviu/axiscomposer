/* global axiscomposer_settings_params */
jQuery( window ).load( function() {

	// Color picker
	jQuery( '.colorpick' ).iris({
		change: function( event, ui ) {
			jQuery( this ).parent().find( '.colorpickpreview' ).css({ backgroundColor: ui.color.toString() });
		},
		hide: true,
		border: true
	}).click( function() {
		jQuery( '.iris-picker' ).hide();
		jQuery( this ).closest( 'td' ).find( '.iris-picker' ).show();
	});

	jQuery( 'body' ).click( function() {
		jQuery( '.iris-picker' ).hide();
	});

	jQuery( '.colorpick' ).click( function( event ) {
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
				    return axiscomposer_settings_params.i18n_nav_warning;
				};
			} else {
				window.onbeforeunload = '';
			}
		});

		jQuery( '.submit input' ).click( function() {
			window.onbeforeunload = '';
		});
	});

	// Sorting
	jQuery( 'table.ac_iconfonts tbody' ).sortable({
		items: 'tr',
		cursor: 'move',
		axis: 'y',
		handle: 'td.sort',
		scrollSensitivity: 40,
		helper: function( event, ui ) {
			ui.children().each( function() {
				jQuery( this ).width( jQuery( this ).width() );
			});
			ui.css( 'left', '0' );
			return ui;
		},
		start: function( event, ui ) {
			ui.item.css( 'background-color', '#f6f6f6' );
		},
		stop: function( event, ui ) {
			ui.item.removeAttr( 'style' );
		}
	});

	// Select all/none
	jQuery( '.axiscomposer' ).on( 'click', '.select_all', function() {
		jQuery( this ).closest( 'td' ).find( 'select option' ).attr( 'selected', 'selected' );
		jQuery( this ).closest( 'td' ).find( 'select' ).trigger( 'change' );
		return false;
	});

	jQuery( '.axiscomposer' ).on( 'click', '.select_none', function() {
		jQuery( this ).closest( 'td' ).find( 'select option' ).removeAttr( 'selected' );
		jQuery( this ).closest( 'td' ).find( 'select' ).trigger( 'change' );
		return false;
	});
});
