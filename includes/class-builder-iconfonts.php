<?php
/**
 * AxisBuilder Iconfonts
 *
 * Handles the Iconfonts Upload easily.
 *
 * @class       AB_Iconfonts
 * @package     AxisBuilder/Classes
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Iconfonts Class
 */
class AB_Iconfonts {

	/**
	 * Hook in methods
	 */
	public static function init() {
		add_action( 'wp_head', array( __CLASS__, 'iconfont_style' ) );
	}

	/**
	 * Outputs some styles in the wp <head> to show iconsfonts font-face.
	 * @return string $output
	 */
	public function iconfont_style() {

		if ( ! current_user_can( 'manage_axisbuilder' ) ) return;
		?>
		<style type="text/css">
			/* This is sample only */
			.iconfonts {
				font-weight: normal;
			}
		</style>
		<?php
	}
}

AB_Iconfonts::init();
