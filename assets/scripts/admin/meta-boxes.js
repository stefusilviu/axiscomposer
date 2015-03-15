/*global axisbuilder_admin_meta_boxes */
jQuery( function ( $ ) {

	// TABS
	$( 'div.panel-wrap' ).each( function() {
		$( this ).find( 'div.panel:not(:first)' ).hide();
	});
	$( 'ul.axisbuilder-tabs a' ).click( function() {
		var panel_wrap = $( this ).closest( 'div.panel-wrap' );
		$( 'ul.axisbuilder-tabs li', panel_wrap ).removeClass( 'active' );
		$( this ).parent().addClass( 'active' );
		$( 'div.panel', panel_wrap ).hide();
		$( $( this ).attr( 'href' ) ).show();
		return false;
	});
	$( 'ul.axisbuilder-tabs li' ).eq( 0 ).find( 'a' ).click();

	// DATE PICKER
	$( 'body' ).on( 'axisbuilder-init-datepickers', function() {
		$( '.date-picker-field, .date-picker' ).datepicker({
			dateFormat: 'yy-mm-dd',
			numberOfMonths: 1,
			showButtonPanel: true,
		});
	});
	$( 'body' ).trigger( 'axisbuilder-init-datepickers' );

});
