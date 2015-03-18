/* global axisbuilder_admin_meta_boxes_builder, console */
function axisbuilder_log( text, type ) {
	if ( typeof console === undefined ) {
		return true;
	}

	var logger = ( typeof type !== undefined ) ? ( ( type === true ) ? text : ( '[AB_' + type + '] - ' + text ) ) : ( '[AB_Logger] - ' + text );
	console.log( type ? logger : text );
}

// Logger
if ( axisbuilder_admin_meta_boxes_builder.debug_mode === 'yes' ) {
	new axisbuilder_log( 'Page Builder Debug Mode is enabled', false );
}
