<?php
/**
 * AxisBuilder Conditional Functions
 *
 * Functions for determining the current query/page.
 *
 * @package     AxisBuilder/Functions
 * @category    Core
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'is_ajax' ) ) :

/**
 * is_ajax - Returns true when the page is loaded via ajax.
 *
 * @return bool
 */
function is_ajax() {
	return defined( 'DOING_AJAX' );
}

endif;

if ( ! function_exists( 'is_user_logged_out' ) ) :

/**
 * is_user_logged_out - Returns true when the User is Logged Out.
 *
 * @return bool
 */
function is_user_logged_out() {
	return is_user_logged_in() ? false : true;
}

endif;

if ( ! function_exists( 'is_current_user_admin' ) ) :

/**
 * is_current_user_admin - Returns true when the User is Admin.
 *
 * @return bool
 */
function is_current_user_admin() {
	global $current_user;
	// $current_user = wp_get_current_user();

	if ( is_user_logged_in() ) {
		return in_array( 'administrator', $current_user->roles );
	}

	return false;
}

endif;

if ( ! function_exists( 'is_current_user_editor' ) ) :

/**
 * is_current_user_editor - Returns true when the User is Editor.
 *
 * @return boolean|null
 */
function is_current_user_editor() {
	global $current_user;
	$user_roles = array( 'administrator', 'editor' );

	if ( is_user_logged_in() ) {
		foreach ( $user_roles as $role ) {
			if ( in_array( $role, $current_user->roles ) ) {
				return true;
			}
		}
		return false;
	}
}

endif;

if ( ! function_exists( 'is_current_user_author' ) ) :

/**
 * is_current_user_author - Returns true when the User is Author.
 *
 * @return boolean|null
 */
function is_current_user_author() {
	global $current_user;
	$user_roles = array( 'administrator', 'editor', 'author' );

	if ( is_user_logged_in() ) {
		foreach ( $user_roles as $role ) {
			if ( in_array( $role, $current_user->roles ) ) {
				return true;
			}
		}
		return false;
	}
}

endif;

if ( ! function_exists( 'is_current_user_contributor' ) ) :

/**
 * is_current_user_contributor - Returns true when the User is Contributor.
 *
 * @return boolean|null
 */
function is_current_user_contributor() {
	global $current_user;
	$user_roles = array( 'administrator', 'editor', 'author', 'contributor' );

	if ( is_user_logged_in() ) {
		foreach ( $user_roles as $role ) {
			if ( in_array( $role, $current_user->roles ) ) {
				return true;
			}
		}
		return false;
	}
}

endif;

if ( ! function_exists( 'is_current_user_subscriber' ) ) :

/**
 * is_current_user_subscriber - Returns true when the User is Subscriber.
 *
 * @return boolean|null
 */
function is_current_user_subscriber() {
	global $current_user;
	$user_roles = array( 'administrator', 'editor', 'author', 'contributor', 'subscriber' );

	if ( is_user_logged_in() ) {
		foreach ( $user_roles as $role ) {
			if ( in_array( $role, $current_user->roles ) ) {
				return true;
			}
		}
		return false;
	}
}

endif;
