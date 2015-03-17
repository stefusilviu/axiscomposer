/* global pagenow */
jQuery( window ).load( function() {

	// Page Builder at the first position
	jQuery( function() {
		var metabox = jQuery( '#normal-sortables' ),
			postbox = metabox.find( '.postbox' ),
			parents = jQuery( '#axisbuilder-canvas' ).find( '.canvas-area' ).parents( '.postbox:eq(0)' );

		if ( parents.length && ( postbox.index( parents ) !== 0 ) ) {
			parents.prependTo( metabox );

			// Re-save the postbox Order
			window.postboxes.save_order( pagenow );
		}
	});
});
