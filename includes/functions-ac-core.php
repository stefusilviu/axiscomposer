<?php
/**
 * AxisComposer Core Functions
 *
 * General core functions available on both the front-end and admin.
 *
 * @author   AxisThemes
 * @category Core
 * @package  AxisComposer/Functions
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Include core functions (available in both admin and frontend).
include( 'functions-ac-conditional.php' );
include( 'functions-ac-deprecated.php' );
include( 'functions-ac-formatting.php' );
include( 'functions-ac-portfolio.php' );
include( 'functions-ac-shortcode.php' );
include( 'functions-ac-iconfont.php' );
include( 'functions-ac-helper.php' );

/**
 * Filters on data used in admin and frontend.
 */
add_filter( 'widget_text', 'do_shortcode' );
add_filter( 'the_content', 'ac_fix_shortcodes' );

/**
 * Short Description (excerpt).
 */
add_filter( 'axiscomposer_short_description', 'wptexturize' );
add_filter( 'axiscomposer_short_description', 'convert_smilies' );
add_filter( 'axiscomposer_short_description', 'convert_chars' );
add_filter( 'axiscomposer_short_description', 'wpautop' );
add_filter( 'axiscomposer_short_description', 'shortcode_unautop' );
add_filter( 'axiscomposer_short_description', 'prepend_attachment' );
add_filter( 'axiscomposer_short_description', 'do_shortcode', 11 ); // AFTER wpautop()

/**
 * Format content to fix shortcodes.
 * @param  string $content
 * @return string
 */
function ac_fix_shortcodes( $content ) {
	global $post;

	if ( is_singular() && is_pagebuilder_active( $post->ID ) ) {
		$content = ac_format_shortcode( $content );
	}

	return $content;
}

/**
 * Get an image size.
 *
 * Variable is filtered by axiscomposer_get_image_size_{image_size}.
 *
 * @param  mixed $image_size
 * @return array
 */
function ac_get_image_size( $image_size ) {
	if ( is_array( $image_size ) ) {
		$width  = isset( $image_size[0] ) ? $image_size[0] : '300';
		$height = isset( $image_size[1] ) ? $image_size[1] : '300';
		$crop   = isset( $image_size[2] ) ? $image_size[2] : 1;

		$size = array(
			'width'  => $width,
			'height' => $height,
			'crop'   => $crop
		);

		$image_size = $width . '_' . $height;

	} elseif ( in_array( $image_size, array( 'portfolio_thumbnail', 'portfolio_single' ) ) ) {
		$size           = get_option( $image_size . '_image_size', array() );
		$size['width']  = isset( $size['width'] ) ? $size['width'] : '300';
		$size['height'] = isset( $size['height'] ) ? $size['height'] : '300';
		$size['crop']   = isset( $size['crop'] ) ? $size['crop'] : 0;

	} else {
		$size = array(
			'width'  => '300',
			'height' => '300',
			'crop'   => 1
		);
	}

	return apply_filters( 'axiscomposer_get_image_size_' . $image_size, $size );
}

/**
 * Queue some JavaScript code to be output in the footer.
 * @param string $code
 */
function ac_enqueue_js( $code ) {
	global $ac_queued_js;

	if ( empty( $ac_queued_js ) ) {
		$ac_queued_js = '';
	}

	$ac_queued_js .= "\n" . $code . "\n";
}

/**
 * Output any queued javascript code in the footer.
 */
function ac_print_js() {
	global $ac_queued_js;

	if ( ! empty( $ac_queued_js ) ) {

		echo "<!-- AxisComposer JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) {";

		// Sanitize
		$ac_queued_js = wp_check_invalid_utf8( $ac_queued_js );
		$ac_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $ac_queued_js );
		$ac_queued_js = str_replace( "\r", '', $ac_queued_js );

		echo $ac_queued_js . "});\n</script>\n";

		unset( $ac_queued_js );
	}
}

/**
 * Get a log file path.
 * @param  string $handle name.
 * @return string the log file path.
 */
function ac_get_log_file_path( $handle ) {
	return trailingslashit( AC_LOG_DIR ) . $handle . '-' . sanitize_file_name( wp_hash( $handle ) ) . '.log';
}

/**
 * Get all available sidebars.
 * @param  array $sidebars
 * @return array
 */
function ac_get_sidebars( $sidebars = array() ) {
	global $wp_registered_sidebars;

	foreach ( $wp_registered_sidebars as $sidebar ) {
		if ( ! in_array( $sidebar['name'], apply_filters( 'axiscomposer_sidebars_exclude', array( 'Display Everywhere' ) ) ) ) {
			$sidebars[ $sidebar['id'] ] = $sidebar['name'];
		}
	}

	return $sidebars;
}

/**
 * Get all Custom Post Types Screen.
 * @return array
 */
function ac_get_screen_types() {
	global $wp_post_types;
	$post_types   = get_post_types( array( 'public' => true, 'show_in_menu' => true, '_builtin' => false ), 'names' );
	$screen_types = apply_filters( 'axiscomposer_screens_types', array(
		'post' => __( 'Post', 'axiscomposer' ),
		'page' => __( 'Page', 'axiscomposer' )
	) );

	// Fetch Public Custom Post Types
	foreach ( $post_types as $post_type ) {
		$screen_types[ $post_type ] = $wp_post_types[ $post_type ]->labels->menu_name;
	}

	// Sort screens
	if ( apply_filters( 'axiscomposer_sort_screens', true ) ) {
		asort( $screen_types );
	}

	return $screen_types;
}

/**
 * Get allowed specific Custom Post Types Screen.
 * @return array
 */
function ac_get_allowed_screen_types() {
	if ( get_option( 'axiscomposer_allowed_screens' ) !== 'specific' ) {
		return array_keys( ac_get_screen_types() );
	}

	$screens    = array();
	$post_types = get_option( 'axiscomposer_specific_allowed_screens' );

	foreach ( $post_types as $key => $post_type ) {
		$screens[ $key ] = $post_type;
	}

	return apply_filters( 'axiscomposer_allowed_screen_types', $screens );
}

/**
 * AxisComposer Core Supported Themes.
 * @return string[]
 */
function ac_get_core_supported_themes() {
	return array( 'twentysixteen', 'twentyfifteen', 'twentyfourteen', 'twentythirteen', 'twentytwelve','twentyeleven', 'twentyten' );
}

/**
 * AxisComposer Core Supported Iconfonts.
 * @return array
 */
function ac_get_core_supported_iconfonts() {
	$core_iconfonts['entypo-fontello'] = array(
		'default'  => true,
		'version'  => '?v=3',
		'charmap'  => 'charmap.php',
		'font_url' => AC()->plugin_url() . '/assets/fonts/entypo-fontello',
		'font_dir' => AC()->plugin_path() . '/assets/fonts/entypo-fontello'
	);

	return apply_filters( 'axiscomposer_core_supported_iconfonts', $core_iconfonts );
}

/**
 * AxisComposer Layout Supported Screens or Post types.
 * @return array
 */
function ac_get_layout_supported_screens() {
	return (array) apply_filters( 'axiscomposer_layout_supported_screens', array( 'post', 'page', 'portfolio', 'jetpack-portfolio' ) );
}
