jQuery( function ( $ ) {

	// Tooltips
	var tiptip_args = {
		'attribute': 'data-tip',
		'fadeIn': 50,
		'fadeOut': 50,
		'delay': 200
	};
	$( '.tips, .help_tip, .axiscomposer-help-tip' ).tipTip( tiptip_args );

	// Add tiptip to parent element for widefat tables
	$( '.parent-tips' ).each( function() {
		$( this ).closest( 'a, th' ).attr( 'data-tip', $( this ).data( 'tip' ) ).tipTip( tiptip_args ).css( 'cursor', 'help' );
	});

	// Select availability
	$( 'select.availability' ).change( function() {
		if ( $( this ).val() === 'all' ) {
			$( this ).closest( 'tr' ).next( 'tr' ).hide();
		} else {
			$( this ).closest( 'tr' ).next( 'tr' ).show();
		}
	}).change();

	// Hidden options
	$( '.hide_options_if_checked' ).each( function() {
		$( this ).find( 'input:eq(0)' ).change( function() {
			if ( $( this ).is( ':checked' ) ) {
				$( this ).closest( 'fieldset, tr' ).nextUntil( '.hide_options_if_checked, .show_options_if_checked', '.hidden_option' ).hide();
			} else {
				$( this ).closest( 'fieldset, tr' ).nextUntil( '.hide_options_if_checked, .show_options_if_checked', '.hidden_option' ).show();
			}
		}).change();
	});

	$( '.show_options_if_checked' ).each( function() {
		$( this ).find( 'input:eq(0)' ).change( function() {
			if ( $( this ).is( ':checked' ) ) {
				$( this ).closest( 'fieldset, tr' ).nextUntil( '.hide_options_if_checked, .show_options_if_checked', '.hidden_option' ).show();
			} else {
				$( this ).closest( 'fieldset, tr' ).nextUntil( '.hide_options_if_checked, .show_options_if_checked', '.hidden_option' ).hide();
			}
		}).change();
	});
});
