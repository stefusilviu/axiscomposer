<?php
/**
 * Portfolio Short Description
 *
 * Replaces the standard excerpt box.
 *
 * @class    AC_Meta_Box_Portfolio_Short_Description
 * @package  AxisComposer/Admin/Meta Boxes
 * @category Admin
 * @author   AxisThemes
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Meta_Box_Portfolio_Short_Description Class
 */
class AC_Meta_Box_Portfolio_Short_Description {

	/**
	 * Output the meta box
	 */
	public static function output( $post ) {

		$settings = array(
			'textarea_name' => 'excerpt',
			'quicktags'     => array( 'buttons' => 'em,strong,link' ),
			'tinymce'       => array(
				'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
				'theme_advanced_buttons2' => '',
			),
			'editor_css'    => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>'
		);

		wp_editor( htmlspecialchars_decode( $post->post_excerpt ), 'excerpt', apply_filters( 'axiscomposer_portfolio_short_description_editor_settings', $settings ) );
	}

}
