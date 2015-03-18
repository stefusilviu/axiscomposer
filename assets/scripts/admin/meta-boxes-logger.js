/* global axisbuilder_admin_meta_boxes_builder, console */
function axisbuilder_log( text, type ) {
	if ( typeof console === 'undefined' ) {
		return true;
	}

	if ( typeof type === 'undefined' ) {
		type = 'logger';
	}

	if ( type === false ) {
		console.log( text );
	} else {
		type = 'AB_' + type.toUpperCase();
		console.log( '[' + type + '] - ' + text );
	}
}

// Logger
if ( axisbuilder_admin_meta_boxes_builder.debug_mode === 'yes' ) {
	new axisbuilder_log( 'Page Builder Debug Mode is enabled', 'Debug' );
}
