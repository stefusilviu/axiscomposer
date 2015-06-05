<?php
/**
 * Comments Shortcode
 *
 * @extends     AC_Shortcode
 * @package     AxisComposer/Shortcodes
 * @category    Shortcodes
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Shortcode_Comments Class
 */
class AC_Shortcode_Comments extends AC_Shortcode {

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Initialise shortcode.
	 */
	public function init_shortcode() {
		$this->id        = 'comments';
		$this->title     = __( 'Comments', 'axiscomposer' );
		$this->method_description = __( 'Add a comment form and comments list to the template', 'axiscomposer' );
		$this->shortcode = array(
			'sort'    => 340,
			'type'    => 'content',
			'name'    => 'ac_comments',
			'icon'    => 'icon-comments',
			'image'   => AC()->plugin_url() . '/assets/images/content/comments.png', // Fallback if icon is missing :)
			'target'  => 'ac-target-insert',
			'tinyMCE' => array( 'disable' => true ),
		);
	}

	/**
	 * Frontend Shortcode Handle.
	 * @param  array  $atts      Array of attributes.
	 * @param  string $content   Text within enclosing form of shortcode element.
	 * @param  string $shortcode The shortcode found, when == callback name.
	 * @param  string $meta      Meta data.
	 * @return string            Returns the modified html string.
	 */
	public function shortcode_handle( $atts, $content = '', $shortcode = '', $meta = '' ) {
		ob_start();
		comments_template();
		return ob_get_clean();
	}
}
