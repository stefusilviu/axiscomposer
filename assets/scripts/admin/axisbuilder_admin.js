/* global axisbuilder_admin */

/**
 * AxisBuilder Admin JS
 */
( function( $ ) {

	'use strict';

	$.AxisBuilderAdmin = ( function() {

		// Wrapper around tinyMCE Editor
		var classicEditorWrap = $( '#postdivrich_wrap' );

		// Button to switch between WordPress editor and axis builder
		var switchButton = $( '#axisbuilder-toggle-button' );

		// Initial Axis Page Builder state
		window.axisBuilderState = 'inactive';

		// Axis page builder element
		var axisPageBuilder = $( '#axis-page-builder' );

		// WordPress default editor element
		var wpDefaultEditor = $( '#postdivrich' );

		// Axis editor inside
		var axisInsider = $( '.axis_insider' );

		// Toggle handler
		var toggleHandler = $( '.axis_toggler' );

		// Check if builder was used last time
		jQuery( document ).ready( function( $ ) {

			// Allow interaction outside jQuery Dialog
			$.ui.dialog.prototype._allowInteraction = function( event ) {
				return true;
			};

			var instance = jQuery('#axis-page-builder').attr('instance');
			var data = {
				action : 'axis_editor_state',
				instance : instance
			};

			var hidden_field = jQuery('#axis-page-builder input[name=axisbuilder_status]').val();

			if ( hidden_field == 'active' ) {
				showFusionEditor( switchButton );
				setBuilderState( 'active' );
			}
		});

		switchButton.on('click', function( e ) {

			if( axisBuilderState !== 'active' ) { //if page builder currently inactive
				showFusionEditor(this);
				setBuilderState('active');

			} else {  //if page builder currently actives
				hideFusionEditor(this);
				setBuilderState('inactive');
			}
		});

		// Change builder state
		function setBuilderState( state ) {
			jQuery( '#axis-page-builder input[name=axisbuilder_status]' ).val( state );
			window.axisBuilderState = state;
		}

		//function to show fusion editor and hide wp editor
		function showFusionEditor( obj ) {

			wpDefaultEditor.parent().addClass('default-editor-hide');//hide default editor
			axisPageBuilder.removeClass('axis-page-builder-hide');
			$(obj).text($(obj).attr('data-active-button'));
			$(obj).addClass('button-secondary');
			$(obj).removeClass('button-primary');
		}

		//function to hide wp editor and show fusion editor
		function hideFusionEditor( obj ) {

			wpDefaultEditor.parent().removeClass('default-editor-hide');//show default editor
			axisPageBuilder.addClass('axis-page-builder-hide');
			$(obj).text($(obj).attr('data-inactive-button'));
			$(obj).addClass('button-primary');
			$(obj).removeClass('button-secondary');
		}
	});


	$(document).ready(function () {
		$.AxisBuilderAdminObj = new $.AxisBuilderAdmin();
	});

})(jQuery);
