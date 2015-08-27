/* global axiscomposer_admin_meta_boxes_pagebuilder, quicktags, QTags */
jQuery( function ( $ ) {

	// Run tipTip
	function runTipTip() {
		// Remove any lingering tooltips
		$( '#tiptip_holder' ).removeAttr( 'style' );
		$( '#tiptip_arrow' ).removeAttr( 'style' );
		$( '.tips' ).tipTip({
			'attribute': 'data-tip',
			'fadeIn': 50,
			'fadeOut': 50,
			'delay': 200
		});
	}

	runTipTip();

	// Field validation error tips
	$( document.body )
		.on( 'ac_add_error_tip', function( e, element, error_type ) {
			var offset = element.position();

			if ( element.parent().find( '.ac_error_tip' ).size() === 0 ) {
				element.after( '<div class="ac_error_tip ' + error_type + '">' + axiscomposer_admin_meta_boxes_pagebuilder[error_type] + '</div>' );
				element.parent().find( '.ac_error_tip' )
					.css( 'left', offset.left + element.width() - ( element.width() / 2 ) - ( $( '.ac_error_tip' ).width() / 2 ) )
					.css( 'top', offset.top + element.height() )
					.fadeIn( '100' );
			}
		})
		.on( 'ac_remove_error_tip', function( e, element, error_type ) {
			element.parent().find( '.ac_error_tip.' + error_type ).remove();
		})
		.on( 'click', function() {
			$( '.ac_error_tip' ).fadeOut( '100', function() { $( this ).remove(); } );
		})
		.on( 'blur', '.ac_input_css[type=text], ac_input_gist[type=text]', function() {
			$( '.ac_error_tip' ).fadeOut( '100', function() { $( this ).remove(); } );
		})
		.on( 'keyup change', '.ac_input_css[type=text]', function() {
			var value    = $( this ).val();
			var regex    = new RegExp( '[^A-Za-z0-9_-]+', 'gi' );
			var newvalue = value.replace( regex, '' );

			if ( value !== newvalue ) {
				$( this ).val( newvalue );
				$( document.body ).triggerHandler( 'ac_add_error_tip', [ $( this ), 'i18n_css_error' ] );
			} else {
				$( document.body ).triggerHandler( 'ac_remove_error_tip', [ $( this ), 'i18n_css_error' ] );
			}
		})
		.on( 'keyup change', '.ac_input_gist[type=text]', function() {
			var value    = $( this ).val();
			var regex    = new RegExp( '[^A-Za-z0-9]+', 'gi' );
			var newvalue = value.replace( regex, '' );

			if ( value !== newvalue ) {
				$( this ).val( newvalue );
				$( document.body ).triggerHandler( 'ac_add_error_tip', [ $( this ), 'i18n_gist_error' ] );
			} else {
				$( document.body ).triggerHandler( 'ac_remove_error_tip', [ $( this ), 'i18n_gist_error' ] );
			}
		});

	// Tabbed Panels
	$( document.body ).on( 'ac-init-tabbed-panels', function() {
		$( 'ul.ac-tabs' ).show();
		$( 'ul.ac-tabs a' ).click( function( e ) {
			e.preventDefault();
			var panel_wrap = $( this ).closest( 'div.panel-wrap' );
			$( 'ul.ac-tabs li', panel_wrap ).removeClass( 'active' );
			$( this ).parent().addClass( 'active' );
			$( 'div.panel', panel_wrap ).hide();
			$( $( this ).attr( 'href' ) ).show();
		});
		$( 'div.panel-wrap' ).filter( ':not(.pagebuilder_data)' ).each( function() {
			$( this ).find( 'ul.ac-tabs li' ).eq( 0 ).find( 'a' ).click();
		});
	}).trigger( 'ac-init-tabbed-panels' );

	// Date Picker
	$( document.body ).on( 'ac-init-datepickers', function() {
		$( '.date-picker-field, .date-picker' ).datepicker({
			dateFormat: 'yy-mm-dd',
			numberOfMonths: 1,
			showButtonPanel: true
		});
	}).trigger( 'ac-init-datepickers' );

	// Icon Picker
	$( document.body ).on( 'ac-init-iconpicker', function() {
		$( '.icon-picker-field, .icon-picker' ).click( function() {
			$( ':input.ac_iconfont_input' ).val( $( this ).parent().data( 'iconfont' ) + ',' + $( this ).data( 'charcode' ) );
			$( this ).closest( 'div.ac-iconfont-container' ).find( 'span.icon-picker' ).removeClass( 'active' );
			$( this ).addClass( 'active' );
			return false;
		});
		$( 'div.ac-iconfont-container' ).each( function() {
			$( this ).find( 'span.icon-picker' ).removeClass( 'inactive' );
		});
	}).trigger( 'ac-init-iconpicker' );

	// Auto resize WordPress editor
	$( document.body ).on( 'ac-init-wp-editor', function() {
		var $supports_editor_expand = ( 'editorExpand' in window && window.editorExpand !== null );
		if ( $supports_editor_expand && $( '#editor-expand-toggle' ).prop( 'checked' ) ) {
			window.editorExpand.off();
			window.editorExpand.on();
		}
	});

	// Enhanced Modal Elements
	$( document.body )

		.on( 'ac-enhanced-modal-elements-init', function() {

			// Select availability
			$( 'select.availability' ).change( function() {
				if ( $( this ).val() === 'specific' ) {
					$( this ).closest( 'tr' ).next( 'tr' ).show();
				} else {
					$( this ).closest( 'tr' ).next( 'tr' ).hide();
				}
			}).change();

			// Input availability
			$( 'input.availability' ).on( 'keyup change', function() {
				if ( $( this ).val() !== '' ) {
					$( this ).closest( 'tr' ).next( 'tr' ).show();
				} else {
					$( this ).closest( 'tr' ).next( 'tr' ).hide();
				}
			}).change();

			// TinyMCE Editor
			$( 'textarea.axiscomposer-tinymce' ).each( function() {
				var $el      = this.id,
					$this    = $( this ),
					parents  = $this.parents( '.wp-editor-wrap:eq(0)' ),
					textarea = parents.find( 'textarea.axiscomposer-tinymce' ),
					switcher = parents.find( '.wp-switch-editor' ).removeAttr( 'onclick' ),
					settings = {
						id: this.id,
						buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,spell,close'
					};

				// Fix Quick tags
				quicktags(settings);
				QTags._buttonsInit();

				// Modify behaviour for html editor
				switcher.bind( 'click', function() {
					var button = $( this );
					if ( button.is( '.switch-tmce' ) ) {
						parents.removeClass( 'html-active' ).addClass( 'tmce-active' );
						window.tinyMCE.execCommand( 'mceAddEditor', true, $el );
						window.tinyMCE.get( $el ).setContent( window.switchEditors.wpautop( textarea.val() ), { format: 'raw' } );
					} else {
						var value = textarea.val();
						if ( window.tinyMCE.get( $el ) ) {
							value = window.tinyMCE.get( $el ).getContent();
						}

						parents.removeClass( 'tmce-active' ).addClass( 'html-active' );
						window.tinyMCE.execCommand( 'mceRemoveEditor', true, $el );
						textarea.val( window.switchEditors._wp_Nop( value ) );
					}
				});

				// Activate the visual editor
				switcher.filter( '.switch-tmce' ).trigger( 'click' );

				// Trigger events
				$( document.body ).on( 'ac-enhanced-form-tinymce-update', function() {
					switcher.filter( '.switch-html' ).trigger( 'click' );
				}).on( 'ac-enhanced-form-tinymce-remove', function() {
					$( document.body ).trigger( 'ac-init-wp-editor' );
					window.tinyMCE.execCommand( 'mceRemoveEditor', true, $el );
				});
			});

			// Regular color pickers
			$( ':input.color-picker-field, :input.color-picker' ).filter( ':not(.enhanced)' ).each( function() {
				var colorpicker_args = {
					palettes: [ '#000000', '#ffffff', '#B02B2C', '#edae44', '#eeee22', '#83a846', '#7bb0e7', '#745f7e', '#5f8789', '#d65799', '#4ecac2' ]
				};

				$( this ).wpColorPicker( colorpicker_args ).addClass( 'enhanced' );
			});

			// Regular icon pickers
			$( document.body ).trigger( 'ac-init-iconpicker' );

			// Regular select boxes
			$( document.body ).trigger( 'ac-enhanced-select-init' );
		})

		// AxisComposer Backbone modal
		.on( 'ac_backbone_modal_before_update', function() {
			$( document.body ).trigger( 'ac-enhanced-form-tinymce-update' );
		})

		.on( 'ac_backbone_modal_before_remove', function() {
			$( document.body ).trigger( 'ac-enhanced-form-tinymce-remove' );
			$( ':input.color-picker-field, :input.color-picker' ).wpColorPicker( 'close' );
		})

		.trigger( 'ac-enhanced-modal-elements-init' );

});
