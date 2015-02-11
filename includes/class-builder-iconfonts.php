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

	public static $font_name = 'unknown';

	/**
	 * Hook in methods
	 */
	public static function init() {
		add_action( 'wp_head',    array( __CLASS__, 'iconfont_style' ) );
		add_action( 'admin_head', array( __CLASS__, 'iconfont_style' ) );
	}

	/**
	 * Outputs some styles in the wp <head> to show iconsfonts font-face
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

	/**
	 * Check for capability
	 */
	public static function check_capability() {
		if ( ! current_user_can( 'manage_axisbuilder' ) ) {
			exit( __( 'Using this feature is reserved for Super Admins. You unfortunately don\'t have the necessary permissions.', 'axisbuilder' ) );
		}
	}

	/**
	 * Extract the zip file to get flat folder and remove the files that are not needed.
	 */
	public static function zip_flatten( $zipfile, $filter ) {

		@ini_set( 'memory_limit', apply_filters( 'admin_memory_limit', WP_MAX_MEMORY_LIMIT ) );

	}

	/**
	 * Iterate over xml file and extract the glyphs for the font.
	 */
	public static function crete_config() {

	}

	/**
	 * Create a folder
	 */
	public static function create_folder( $folder, $addindex = true ) {
		if ( is_dir( $folder ) && $addindex == false ) {
			return true;
		}

		$created = wp_mkdir_p( trailingslashit( $folder ) );
		@chmod( $folder, 0777 );

		if ( $addindex == false ) {
			return $created;
		}

		$index_file = trailingslashit( $folder ) . 'index.php';
		if ( file_exists( $index_file, 'w' ) ) {
			return $created;
		}

		$handle = @fopen( $index_file, 'w' );
		if ( $handle ) {
			fwrite( $handle, "<?php\r\necho 'Sorry, browsing the directory is not allowed!';\r\n?>" );
			fclose( $handle );
		}

		return $created;
	}

	/**
	 * Delete a folder and content if they already exists
	 */
	public static function delete_folder( $folder ) {
		if ( is_dir( $folder ) ) {
			$scan = scandir( $folder );

			foreach ( $scan as $object ) {
				if ( $object != '.' && $object != '..' ) {
					unlink( $folder . '/' . $object );
				}
			}

			reset( $scan );
			rmdir( $folder );
		}
	}
}

AB_Iconfonts::init();
