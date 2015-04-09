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
	$( document.body ).on( 'axisbuilder-init-datepickers', function() {
		$( '.date-picker-field, .date-picker' ).datepicker({
			dateFormat: 'yy-mm-dd',
			numberOfMonths: 1,
			showButtonPanel: true
		});
	}).trigger( 'axisbuilder-init-datepickers' );

	// Enhanced Modal Elements
	$( document.body )

		.on( 'axisbuilder-enhanced-modal-elements-init', function() {

			// Regular color pickers
			$( ':input.color-picker-field, :input.color-picker' ).filter( ':not(.enhanced)' ).each( function() {
				var colorpicker_args = {
					palettes: [ '#000000', '#ffffff', '#B02B2C', '#edae44', '#eeee22', '#83a846', '#7bb0e7', '#745f7e', '#5f8789', '#d65799', '#4ecac2' ]
				};

				$( this ).wpColorPicker( colorpicker_args ).addClass( 'enhanced' );
			});

		})

		// AxisBuilder Backbone modal
		.on( 'axisbuilder_backbone_modal_before_remove', function() {
			$( ':input.color-picker-field, :input.color-picker' ).wpColorPicker( 'close' );
		})

		.trigger( 'axisbuilder-enhanced-modal-elements-init' );

});
