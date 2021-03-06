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
		// Sanitize.
		$ac_queued_js = wp_check_invalid_utf8( $ac_queued_js );
		$ac_queued_js = preg_replace( '/&#(x)?0*(?(1)27|39);?/i', "'", $ac_queued_js );
		$ac_queued_js = str_replace( "\r", '', $ac_queued_js );

		$js = "<!-- AxisComposer JavaScript -->\n<script type=\"text/javascript\">\njQuery(function($) { $ac_queued_js });\n</script>\n";

		/**
		 * axiscomposer_queued_js filter.
		 * @param string $js JavaScript code.
		 */
		echo apply_filters( 'axiscomposer_queued_js', $js );

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

	// Fetch Public Custom Post Types.
	foreach ( $post_types as $post_type ) {
		$screen_types[ $post_type ] = $wp_post_types[ $post_type ]->labels->menu_name;
	}

	// Sort screens.
	if ( apply_filters( 'axiscomposer_sort_screens', true ) ) {
		asort( $screen_types );
	}

	return $screen_types;
}

/**
 * Get the allowed Custom Post Types Screen.
 * @return array
 */
function ac_get_allowed_screen_types() {
	$screen_types = ac_get_screen_types();

	if ( 'all' === get_option( 'axiscomposer_allowed_screens' ) ) {
		return array_keys( $screen_types );
	}

	if ( 'all_except' === get_option( 'axiscomposer_allowed_screens' ) ) {
		$except_screens = get_option( 'axiscomposer_all_except_screens', array() );

		if ( ! $except_screens ) {
			return array_keys( $screen_types );
		} else {
			$all_except_screens = $screen_types;
			foreach( $except_screens as $screen ) {
				unset( $all_except_screens[ $screen ] );
			}
			return apply_filters( 'axiscomposer_allowed_screen_types', array_keys( $all_except_screens ) );
		}
	}

	$screens     = array();
	$raw_screens = get_option( 'axiscomposer_specific_allowed_screens', array() );

	if ( $raw_screens ) {
		foreach ( $raw_screens as $key => $screen ) {
			$screens[ $key ] = $screen;
		}
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

/**
 * Outputs a "back" link so admin screens can easily jump back a page.
 *
 * @param string $label Title of the page to return to.
 * @param string $url   URL of the page to return to.
 */
function ac_back_link( $label, $url ) {
	echo '<small class="ac-admin-breadcrumb"><a href="' . esc_url( $url ) . '" title="' . esc_attr( $label ) . '">&#x21a9;</a></small>';
}

/**
 * Display a AxisComposer help tip.
 * @param  string $tip        Help tip text
 * @param  bool   $allow_html Allow sanitized HTML if true or escape
 * @return string
 */
function ac_help_tip( $tip, $allow_html = false ) {
	if ( $allow_html ) {
		$tip = ac_sanitize_tooltip( $tip );
	} else {
		$tip = esc_attr( $tip );
	}

	return '<span class="axiscomposer-help-tip" data-tip="' . $tip . '"></span>';
}
