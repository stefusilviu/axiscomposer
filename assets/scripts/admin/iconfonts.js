/* global axisbuilder_admin_iconfonts, jQuery, Backbone, _ */

var AB_Icon_Fonts = AB_Icon_Fonts || {};

/**
 * AxisBuilder Backbone Iconfonts JS
 */
( function ( $, Backbone, _ ) {
	'use strict';

	// Views
	AB_Icon_Fonts.AppView = Backbone.View.extend({
		el: '#axisbuilder-iconfonts',
		events: {
			'click .add-iconfont': 'addButton',
			'click .del-iconfont': 'delButton'
		},
		initialize: function() {
			_.bindAll( this, 'render' );
			this.render();
		},
		render: function() {
			console.log( 'Pagebuilder render goes here!' );
		},
		addButton: function ( e ) {
			var clicked = $( '.add-iconfont' );

			e.preventDefault();

			// Create the media frame.
			var axisbuilder_file_frame = wp.media.frames.axisbuilder_file_frame = wp.media({
				title: clicked.data( 'title' ),
				library: {
					type: clicked.data( 'type' )
				},
				button: {
					text: clicked.data( 'button' )
				},
				multiple: false
			});

			// When an ZIP file is selected, run a callback.
			axisbuilder_file_frame.on( 'select', function() {
				var attachment = axisbuilder_file_frame.state().get( 'selection' ).first().toJSON();
				$( '#' + clicked.data( 'target' ) ).val( attachment.id ).trigger( 'change' );
				$( 'body' ).trigger( clicked.data( 'trigger' ), [ attachment, clicked ] );
			});

			// Finally, open the modal
			axisbuilder_file_frame.open();
		},
		delButton: function( e ) {
			e.preventDefault();
			this.undelegateEvents();
		}
	});

	// Kick things off by creating the 'App'
	$( document ).ready( function() {
		new AB_Icon_Fonts.AppView();

		$( 'body' ).on( 'insert_iconfont_zip', AB_Icon_Fonts.icon_insert );
	});

	AB_Icon_Fonts.icon_insert = function( event, attachment, clicked ) {
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
			action: 'axisbuilder_add_iconfont',
			security: axisbuilder_admin_iconfonts.add_custom_iconfont_nonce
		};

		$.ajax({
			url: axisbuilder_admin_iconfonts.ajax_url,
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

				if ( response.match( /axisbuilder_iconfont_added/ ) ) {
					message.html( '<div class="updated"><p>Font icon added successfully! Reloading the page... </p></div>' );
					message.show();

					// setTimeout( function() {
					// 	message.slideUp();
					// 	location.reload();
					// }, 5000 );
				} else {
					message.html( '<div class="error"><p>Couldn\'t add the font.<br/>The script returned the following error: ' + response + '</p></div>' );
					message.show();

					// setTimeout( function() {
					// 	message.slideUp();
					// }, 5000 );
				}
			}
		});
	};

}( jQuery, Backbone, _ ));
