/**
 * AxisBuilder Admin JS
 */
jQuery( function ( $ ) {

	// Tooltips
	var tiptip_args = {
		'attribute' : 'data-tip',
		'fadeIn' : 50,
		'fadeOut' : 50,
		'delay' : 200
	};
	$( '.tips, .help_tip' ).tipTip( tiptip_args );

	// Add tiptip to parent element for widefat tables
	$( '.parent-tips' ).each(function(){
		$(this).closest( 'a, th' ).attr( 'data-tip', $(this).data( 'tip' ) ).tipTip( tiptip_args ).css( 'cursor', 'help' );
	});

	// Sidebars
	jQuery( 'select.show_if_sidebar' ).change( function() {
		if ( jQuery( this ).val() === 'fullsize' ) {
			jQuery( this ).parent().next( 'p.form-field' ).slideUp(300);
		} else {
			jQuery( this ).parent().next( 'p.form-field' ).slideDown(300);
		}
	}).change();
});
