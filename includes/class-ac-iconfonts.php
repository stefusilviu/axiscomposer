<?php
/**
 * AxisComposer Iconfonts
 *
 * Handles the Iconfonts Upload easily.
 *
 * @class       AC_Iconfonts
 * @package     AxisComposer/Classes
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Iconfonts Class
 */
class AC_Iconfonts {

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'wp_enqueue_scripts',    array( __CLASS__, 'inline_styles' ), 11 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'inline_styles' ), 11 );
	}

	/**
	 * Get all available iconfonts.
	 * @return array
	 */
	public static function get_all_iconfonts() {
		$iconfonts = get_option( 'axiscomposer_custom_iconfonts', array() );
		return array_merge( ac_get_core_supported_iconfonts(), $iconfonts );
	}

	/**
	 * Adds iconfont inline styles.
	 */
	public static function inline_styles() {
		$font_face = '';
		$iconfonts = self::get_all_iconfonts();

		foreach ( $iconfonts as $font_family => $config ) {
			$font_url = trailingslashit( $config['font_url'] ) . $font_family;
			$font_ver = isset( $config['version'] ) ? strstr( $config['version'], '?' ) : '';

			// Check for charmap before creating font-face inline styles.
			$charmap = path_join( $config['font_dir'], $config['charmap'] );
			if ( $charmap && is_readable( $charmap ) ) {
				$font_face .= self::create_font_face( $font_family, $font_url, $font_ver );
			}
		}

		if ( current_user_can( 'manage_axiscomposer' ) ) {
			wp_add_inline_style( is_admin() ? 'axiscomposer-admin' : 'axiscomposer-general', $font_face );
		}
	}

	/**
	 * Create iconfont font-face styles.
	 * @param  string $font_family
	 * @param  string $font_url
	 * @param  string $font_ver
	 * @return string
	 */
	private static function create_font_face( $font_family, $font_url, $font_ver ) {
		$ampersand = empty( $font_ver ) ? '' : str_replace( '?', '&', $font_ver );
		$font_face = "
		@font-face {
		    font-family: '{$font_family}';
		    src:url('{$font_url}.eot{$font_ver}');
		    src:url('{$font_url}.eot#iefix{$ampersand}') format('embedded-opentype'),
		        url('{$font_url}.woff{$font_ver}') format('woff'),
		        url('{$font_url}.ttf{$font_ver}') format('truetype'),
		        url('{$font_url}.svg{$font_ver}#{$font_family}') format('svg');
		    font-weight: normal;
		    font-style: normal;
		}
		body .axiscomposer-font-{$font_family},
		body .axiscomposer-font-{$font_family} span,
		body [data-iconfont='{$font_family}']:before {
		    font-family: '{$font_family}';
		}
		";

		return $font_face;
	}
}

AC_Iconfonts::init();
