/* global axiscomposer_admin_iconfont, jQuery, Backbone, _ */

var AC_Icon_Fonts = AC_Icon_Fonts || {};

/**
 * AxisComposer Backbone Iconfonts JS
 */
( function ( $, Backbone, _ ) {
	'use strict';

	// Views
	AC_Icon_Fonts.AppView = Backbone.View.extend({
		el: '#axiscomposer-iconfont',
		events: {
			'click .modal-close': 'closeButton',
			'click #btn-ok': 'addButton'
		},
		initialize: function() {
			_.bindAll( this, 'render' );
			this.render();
		},
		render: function() {
			// console.log( 'Iconfonts Manager render goes here!' );
		},
		addButton: function ( e ) {
			var $el = $( '.add-iconfont' );
			var iconfont_media_frame = '';

			e.preventDefault();

			// If the media frame already exists, reopen it.
			if ( iconfont_media_frame ) {
				iconfont_media_frame.open();
				return;
			}

			// Create the media frame.
			iconfont_media_frame = wp.media.frames.iconfont_media_frame = wp.media({
				// Set the title of the modal.
				title: $el.data( 'choose' ),
				button: {
					text: $el.data( 'update' )
				},
				library: {
					type: $el.data( 'mime' )
				},
				multiple: false
			});

			// When an ZIP file is selected, run a callback.
			iconfont_media_frame.on( 'select', function() {
				var attachment = iconfont_media_frame.state().get( 'selection' ).first().toJSON();
				$( '#' + $el.data( 'target' ) ).val( attachment.id ).trigger( 'change' );
				$( document.body ).trigger( $el.data( 'trigger' ), [ attachment, $el ] );
			});

			// Finally, open the modal
			iconfont_media_frame.open();
		},
		closeButton: function( e ) {
			var	$el = $( '.del-iconfont' );
			var message = $( '#msg' );

			e.preventDefault();

			var data = {
				term: $el.data( 'delete' ),
				action: 'axiscomposer_delete_iconfont',
				security: axiscomposer_admin_iconfont.delete_custom_iconfont_nonce
			};

			$.ajax({
				url: axiscomposer_admin_iconfont.ajax_url,
				data: data,
				type: 'POST',
				beforeSend: function() {
					$( '.spinner' ).css({
						opacity: 0,
						display: 'block',
						position: 'absolute',
						top: '21px',
						left: '300px'
					}).animate({ opacity: 1 });
				},
				error: function() {
					$( '.spinner' ).hide();

					message.html( '<div class="error"><p>Unable to remove the font because the server didn\'t respond.<br />Please reload the page, then try again</p></div>' );
					message.show();

					setTimeout( function() {
						message.slideUp();
					}, 5000 );
				},
				success: function( response ) {
					$( '.spinner' ).hide();

					if ( response.match( /axiscomposer_iconfont_removed/ ) ) {
						message.html( '<div class="updated"><p>Font icon removed successfully! Reloading the page... </p></div>' );
						message.show();

						setTimeout( function() {
							message.slideUp();
							location.reload();
						}, 5000 );
					} else {
						message.html( '<div class="error"><p>Unable to remove the font. Reloading the page... </p></div>' );
						message.show();

						setTimeout( function() {
							message.slideUp();
							location.reload();
						}, 5000 );
					}
				}
			});
		}
	});

	// Kick things off by creating the 'App'
	$( document ).ready( function() {
		new AC_Icon_Fonts.AppView();

		$( 'body' ).on( 'insert_iconfont_zip', AC_Icon_Fonts.icon_insert );
	});

	AC_Icon_Fonts.icon_insert = function( event, attachment ) {
		var message = $( '#msg' );

		if ( attachment.subtype !== 'zip' ) {
			$( '.spinner' ).hide();
			message.html( '<div class="error"><p>Please upload a valid ZIP file.<br />You can create the file on icomoon.io</p></div>' );
			message.show();

			setTimeout( function() {
				message.slideUp();
			}, 5000 );

			return false;
		}

		var	data = {
			value: attachment,
			action: 'axiscomposer_add_iconfont',
			security: axiscomposer_admin_iconfont.add_custom_iconfont_nonce
		};

		$.ajax({
			url: axiscomposer_admin_iconfont.ajax_url,
			data: data,
			type: 'POST',
			beforeSend: function() {
				$( '.spinner' ).css({
					opacity: 0,
					display: 'block',
					position: 'absolute',
					top: '21px',
					left: '300px'
				}).animate({ opacity: 1 });
			},
			success: function( response ) {
				$( '.spinner' ).hide();

				if ( response.match( /axiscomposer_iconfont_added/ ) ) {
					message.html( '<div class="updated"><p>Font icon added successfully! Reloading the page... </p></div>' );
					message.show();

					setTimeout( function() {
						message.slideUp();
						location.reload();
					}, 5000 );
				} else {
					message.html( '<div class="error"><p>Couldn\'t add the font.<br/>The script returned the following error: ' + response + '</p></div>' );
					message.show();

					setTimeout( function() {
						message.slideUp();
					}, 5000 );
				}
			}
		});
	};

}( jQuery, Backbone, _ ));
