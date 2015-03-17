/* global pagenow */
jQuery( window ).load( function() {

	// Page Builder at the first position
	jQuery( function() {
		var builder = jQuery( '#axisbuilder-editor' ),
			metabox = jQuery( '#normal-sortables' ),
			postbox = metabox.find( '.postbox' );

		if ( builder.length && ( postbox.index( builder ) !== 0 ) ) {
			builder.prependTo( metabox );

			// Re-save the postbox Order
			window.postboxes.save_order( pagenow );
		}
	});
});
