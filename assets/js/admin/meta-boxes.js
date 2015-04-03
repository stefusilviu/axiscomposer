jQuery( function ( $ ) {

	// Tooltips
	var tiptip_args = {
		'attribute' : 'data-tip',
		'fadeIn' : 50,
		'fadeOut' : 50,
		'delay' : 200
	};
	$( '.tips, .help_tip' ).tipTip( tiptip_args );

	// Tabs
	$( 'ul.axisbuilder-tabs' ).show();
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

	// Date Picker
	$( 'body' ).on( 'axisbuilder-init-datepickers', function() {
		$( '.date-picker-field, .date-picker' ).datepicker({
			dateFormat: 'yy-mm-dd',
			numberOfMonths: 1,
			showButtonPanel: true
		});
	});
	$( 'body' ).trigger( 'axisbuilder-init-datepickers' );

});
