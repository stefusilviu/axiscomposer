/* global axisbuilder_admin_meta_boxes_builder, console */
function axisbuilder_log( string, type ) {
	if ( typeof console === undefined ) {
		return true;
	}

	var logger = ( typeof type !== undefined ) ? ( ( type === true ) ? string : ( '[AB_' + type.charAt(0).toUpperCase() + type.slice(1).toLowerCase() + '] - ' + string ) ) : ( '[AB_Logger] - ' + string );
	console.log( type ? logger : string );
}

// Debug Logger
if ( axisbuilder_admin_meta_boxes_builder.debug_mode === 'yes' ) {
	new axisbuilder_log( 'AxisBuilder Debug Mode is enabled', 'debug' );
}
