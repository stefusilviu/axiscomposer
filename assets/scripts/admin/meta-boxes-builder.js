( function( $ ) {
	'use strict';

	$.AxisBuilderHelper = $.AxisBuilderHelper || {};
	$.AxisBuilderHelper.wp_media = $.AxisBuilderHelper.wp_media || [];

	$( document ).ready( function() {

		// Show/Hide the dependent elements.
		$.AxisBuilderHelper.check_depedencies();

		// Image Insert functionality
		$.AxisBuilderHelper.wp_media_advanced();
	});

	// Depedency checker for selected elements.
	$.AxisBuilderHelper.check_depedencies = function() {
		var body = $( 'body' );

		body.on( 'change', '.axisbuilder-style input[type=hidden], .axisbuilder-style input[type=text], .axisbuilder-style input[type=checkbox], .axisbuilder-style textarea, .axisbuilder-style select, .axisbuilder-style radio', function() {
			var current = $( this ),
				scope   = current.parents( '.axisbuilder-modal:eq(0)' );

			if ( ! scope.length ) {
				scope = body;
			}

			var element     = this.id.replace( /axisbuilderTB-/, '' ),
				dependent   = scope.find( '.axisbuilder-form-element-container[data-check-element="' + element + '"]' ),
				is_hidden   = current.parents( '.axisbuilder-form-element-container:eq(0)' ).is( '.axisbuilder-hidden' ),
				first_value = this.value;

			if ( current.is( 'input[type=checkbox]' ) && ! current.prop( 'checked') ) {
				first_value = '';
			}

			if ( ! dependent.length ) {
				return true;
			}

			dependent.each( function() {
				var	visible     = false,
					current     = $( this ),
					operator    = current.data( 'check-operator' ),
					final_value = current.data( 'check-value' ).toString();

				if ( ! is_hidden ) {
					switch( operator ) {
						case 'equals':
							visible = ( first_value === final_value ) ? true : false;
						break;

						case 'not':
							visible = ( first_value !== final_value ) ? true : false;
						break;

						case 'is_larger':
							visible = ( first_value > final_value ) ? true : false;
						break;

						case 'is_smaller':
							visible = ( first_value < final_value ) ? true : false;
						break;

						case 'contains':
							visible = ( first_value.indexOf( final_value ) !== -1 ) ? true : false;
						break;

						case 'doesnot_contain':
							visible = ( first_value.indexOf( final_value ) === -1 ) ? true : false;
						break;

						case 'is_empty_or':
							visible = ( ( first_value === '' ) || ( first_value === final_value ) ) ? true : false;
						break;

						case 'not_empty_and':
							visible = ( ( first_value !== '' ) || ( first_value !== final_value ) ) ? true : false;
						break;
					}
				}

				if ( visible === true && current.is( '.axisbuilder-hidden' ) ) {
					current.css({ display: 'none' }).removeClass( 'axisbuilder-hidden' ).find( 'select, radio, input[type=checkbox]' ).trigger( 'change' );
					current.slideDown();
				} else if ( visible === false && ! current.is( '.axisbuilder-hidden' ) ) {
					current.css({ display: 'block' }).addClass( 'axisbuilder-hidden' ).find( 'select, radio, input[type=checkbox]' ).trigger( 'change' );
					current.slideUp();
				}
			});
		});
	};

	// WordPress Media Uploader Advanced.
	$.AxisBuilderHelper.wp_media_advanced = function() {

		var file_frame = [], media = wp.media;

		// Click Event Upload Button
		$( 'body' ).on( 'click', '.axisbuilder-image-upload', function( e ) {
			e.preventDefault();

			var clicked = $( this ),
				options = clicked.data(),
				frame_key = _.random(0, 999999999999999999);

			// Set vars so we know that an editor is open
			$.AxisBuilderHelper.wp_media.unshift( this );

			// If the media frame alreay exist, reopen it.
			if ( file_frame[frame_key] ) {
				file_frame[frame_key].open();
				return;
			}

			// Create the media frame.
			file_frame[frame_key] = wp.media({
				frame: options.frame,
				state: options.state,
				className: options['class'],
				button: {
					text: options.button
				},
				library: {
					type: 'image'
				}
			});

			// Add the single insert state
			file_frame[frame_key].states.add([
				new wp.media.controller.Library({
					id: 'axisbuilder_insert_single',
					title: clicked.data( 'title' ),
					priority: 20,
					editable: true,
					multiple: false,
					toolbar: 'select',
					filterable: 'uploaded',
					allowLocalEdits: true,
					displaySettings: true,
					displayUserSettings: false,
					library: media.query( file_frame[frame_key].options.library )
				})
			]);

			// On modal close remove the item from the global array so that the Backbone Modal accepts keyboard events again
			file_frame[frame_key].on( 'close', function() {
				_.defer( function() {
					$.AxisBuilderHelper.wp_media.shift();
				});
			});

			// Finally open the modal
			file_frame[frame_key].open();
		});
	};

})( jQuery );
