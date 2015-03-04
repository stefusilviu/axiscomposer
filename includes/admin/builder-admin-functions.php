<?php
/**
 * AxisBuilder Admin Functions
 *
 * @package     AxisBuilder/Admin/Functions
 * @category    Core
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all AxisBuilder screen ids.
 * @return array
 */
function axisbuilder_get_screen_ids() {

	$ab_screen_id = sanitize_title( __( 'Axis Builder', 'axisbuilder' ) );
	$screen_ids   = array(
		'toplevel_page_' . $ab_screen_id,
		$ab_screen_id . '_page_axisbuilder-iconfonts',
		$ab_screen_id . '_page_axisbuilder-settings',
		$ab_screen_id . '_page_axisbuilder-status',
		$ab_screen_id . '_page_axisbuilder-addons',
		'portfolio',
		'edit-portfolio',
		'edit-portfolio_cat',
		'edit-portfolio_tag'
	);

	return apply_filters( 'axisbuilder_screen_ids', $screen_ids );
}

/**
 * Get all Custom Post Types Screen.
 * @return array
 */
function axisbuilder_get_screen_types() {
	global $wp_post_types;
	$post_types   = get_post_types( array( 'public' => true, 'show_in_menu' => true, '_builtin' => false ), 'names' );
	$screen_types = apply_filters( 'axisbuilder_screens_types', array(
		'post' => __( 'Post', 'axisbuilder' ),
		'page' => __( 'Page', 'axisbuilder' )
	) );

	// Fetch Public Custom Post Types
	foreach ( $post_types as $post_type ) {
		$screen_types[ $post_type ] = $wp_post_types[ $post_type ]->labels->menu_name;
	}

	return $screen_types;
}

/**
 * Get allowed specific Custom Post Types Screen.
 * @return array
 */
function axisbuilder_get_allowed_screen_types() {
	if ( get_option( 'axisbuilder_allowed_screens' ) !== 'specific' ) {
		return array_keys( axisbuilder_get_screen_types() );
	}

	$screens    = array();
	$post_types = get_option( 'axisbuilder_specific_allowed_screens' );

	foreach ( $post_types as $key => $post_type ) {
		$screens[ $key ] = $post_type;
	}

	return apply_filters( 'axisbuilder_allowed_screen_types', $screens );
}
