/* global pagenow */
jQuery( function( $ ) {

	/**
	 * Page Builder Position
	 */
	var ac_meta_box_pagebuilder_position = {

		init: function() {
			var self = this;

			$( window ).load( function() {
				self.position();
				window.postboxes.save_order( pagenow );
			});
		},

		position: function() {
			var builder = $( '#axiscomposer-pagebuilder' ),
				columns = $( '#post-body' ).hasClass( 'columns-1' ),
				maximum = Math.max( window.innerWidth, document.documentElement.clientWidth ),
				metabox = $( '#' + ( maximum && maximum <= 850 || columns ? 'side' : 'normal' ) + '-sortables' );

			// Prepend pagebuilder meta-box to first position
			if ( builder.length && metabox.find( '.postbox' ).index( builder ) !== 0 ) {
				builder.prependTo( metabox );
			}
		}
	};

	ac_meta_box_pagebuilder_position.init();
});
