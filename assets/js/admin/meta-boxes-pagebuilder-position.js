/* global pagenow */
jQuery( window ).load( function() {

	/**
	 * Page Builder at the first position.
	 */
	jQuery( function() {
		var builder = jQuery( '#axiscomposer-pagebuilder' ),
			columns = jQuery( '#post-body' ).hasClass( 'columns-1' ),
			maximum = Math.max( window.innerWidth, document.documentElement.clientWidth ),
			metabox = jQuery( '#' + ( maximum && maximum <= 850 || columns ? 'side' : 'normal' ) + '-sortables' );

		// Adjusts the pagebuilder to the fist position and triggers postbox saving.
		if ( builder.length && metabox.find( '.postbox' ).index( builder ) !== 0 ) {
			builder.prependTo( metabox );
			window.postboxes.save_order( pagenow );
		}
	});
});
