<?php
/**
 * AxisComposer Admin.
 *
 * @class    AC_Admin
 * @version  1.0.0
 * @package  AxisComposer/Admin
 * @category Admin
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Admin Class
 */
class AC_Admin {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'current_screen', array( $this, 'conditional_includes' ) );
		add_action( 'admin_footer', 'ac_print_js', 25 );
		add_filter( 'admin_footer_text', array( $this, 'admin_footer_text' ), 1 );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		include_once( 'functions-ac-admin.php' );
		include_once( 'functions-ac-meta-box.php' );
		include_once( 'class-ac-admin-post-types.php' );
		include_once( 'class-ac-admin-menus.php' );
		include_once( 'class-ac-admin-notices.php' );
		include_once( 'class-ac-admin-assets.php' );
		include_once( 'class-ac-admin-pointers.php' );

		// Help Tabs
		if ( apply_filters( 'axiscomposer_enable_admin_help_tab', true ) ) {
			include_once( 'class-ac-admin-help.php' );
		}

		// TinyMCE
		if ( 'yes' === get_option( 'axiscomposer_tinymce_enabled', 'yes' ) ) {
			include_once( 'class-ac-admin-tinymce.php' );
		}
	}

	/**
	 * Include admin files conditionally.
	 */
	public function conditional_includes() {
		if ( ! $screen = get_current_screen() ) {
			return;
		}

		switch ( $screen->id ) {
			case 'options-permalink' :
				include( 'class-ac-admin-permalink-settings.php' );
		}
	}

	/**
	 * Change the admin footer text on AxisComposer admin pages.
	 * @param  string $footer_text
	 * @return string
	 */
	public function admin_footer_text( $footer_text ) {
		if ( ! current_user_can( 'manage_axiscomposer' ) ) {
			return;
		}
		$current_screen = get_current_screen();
		$ac_pages       = ac_get_screen_ids();

		// Check to make sure we're on a AxisComposer admin page
		if ( isset( $current_screen->id ) && apply_filters( 'axiscomposer_display_admin_footer_text', in_array( $current_screen->id, $ac_pages ) ) ) {
			// Change the footer text
			if ( ! get_option( 'axiscomposer_admin_footer_text_rated' ) ) {
				$footer_text = sprintf( __( 'If you like <strong>AxisComposer</strong> please leave us a %s&#9733;&#9733;&#9733;&#9733;&#9733;%s rating. A huge thank you from AxisThemes in advance!', 'axiscomposer' ), '<a href="https://wordpress.org/support/view/plugin-reviews/axiscomposer?filter=5#postform" target="_blank" class="ac-rating-link" data-rated="' . esc_attr__( 'Thanks :)', 'axiscomposer' ) . '">', '</a>' );
				ac_enqueue_js( "
					jQuery( 'a.ac-rating-link' ).click( function() {
						jQuery.post( '" . AC()->ajax_url() . "', { action: 'axiscomposer_rated' } );
						jQuery( this ).parent().text( jQuery( this ).data( 'rated' ) );
					});
				" );
			} else {
				$footer_text = __( 'Thank you for creating with AxisComposer.', 'axiscomposer' );
			}
		}

		return $footer_text;
	}
}

new AC_Admin();
