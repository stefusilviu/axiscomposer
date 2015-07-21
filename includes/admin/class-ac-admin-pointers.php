<?php
/**
 * Adds and controls pointers for contextual help/tutorials.
 *
 * @class    AC_Admin_Pointers
 * @version  1.0.0
 * @package  AxisComposer/Admin
 * @category Admin
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Admin_Pointers Class
 */
class AC_Admin_Pointers {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'setup_pointers_for_screen' ) );
	}

	/**
	 * Setup pointers for screen.
	 */
	public function setup_pointers_for_screen() {
		$screen = get_current_screen();

		if ( in_array( $screen->id, ac_get_allowed_screen_types() ) ) {
			$this->create_pagebuilder_pointers();
		}
	}

	/**
	 * Dismissible Pointers for Page Builder.
	 */
	public function create_pagebuilder_pointers() {
		if ( isset( $_GET['tutorial'] ) || ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$pointers = array(
			'pointers' => array(
				'ac_toggle_editor' => array(
					'target'       => '#_toggle_editor',
					'options'      => array(
						'content'  =>	'<h3>' . esc_html__( 'Page Builder', 'axiscomposer' ) . '</h3>' .
										'<p>' . esc_html__( 'Page Builder helps you to create unique layout you can imagine with the help of drag and drop interface. This requires zero coding knowledge.', 'axiscomposer' ) . '</p>',
						'position' => array(
							'edge'  => 'left',
							'align' => 'middle'
						)
					)
				)
			)
		);

		// Check for valid and available pointers.
		$pointers = $this->check_for_pointers( $pointers );
		if ( ! empty( $pointers ) ) {
			$this->enqueue_pointers( $pointers );
		}
	}

	/**
	 * Check pointers and remove dismissed ones.
	 * @param  array $pointers
	 * @return array $pointers
	 */
	public function check_for_pointers( $pointers ) {
		if ( ! is_array( $pointers ) ) {
			return;
		}

		// Get dismissed pointers
		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

		$valid_pointers = array();
		foreach ( $pointers['pointers'] as $pointer_id => $pointer ) {
			if ( in_array( $pointer_id, $dismissed ) ) {
				continue;
			}

			$valid_pointers['pointers'][ $pointer_id ] = $pointer;
		}

		return $valid_pointers;
	}

	/**
	 * Enqueue pointers and add script to page.
	 * @param array $pointers
	 */
	public function enqueue_pointers( $pointers ) {
		$pointers = wp_json_encode( $pointers );
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );
		ac_enqueue_js( "
			jQuery( function( $ ) {
				var ac_pointers = {$pointers};

				setTimeout( init_ac_pointers, 800 );

				function init_ac_pointers() {
					$.each( ac_pointers.pointers, function( i ) {
						show_ac_pointer( i );
						return false;
					});
				}

				function show_ac_pointer( id ) {
					var pointer = ac_pointers.pointers[ id ];
					var options = $.extend( pointer.options, {
						close: function() {
							$.post( ajaxurl, {
								pointer: id,
								action: 'dismiss-wp-pointer'
							});
						}
					} );
					var this_pointer = $( pointer.target ).pointer( options );
					this_pointer.pointer( 'open' );
				}
			});
		" );
	}
}

new AC_Admin_Pointers();
