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

	public static $font_name  = 'unknown';
	public static $svg_config = array();

	/**
	 * Hook in methods
	 */
	public static function init() {
		add_action( 'wp_head',    array( __CLASS__, 'iconfont_style' ) );
		add_action( 'admin_head', array( __CLASS__, 'iconfont_style' ) );
	}

	/**
	 * Outputs some styles in the wp <head> to load iconfonts font-face
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
	 * @param string $zipfile
	 * @param string[] $filter
	 */
	public static function zip_flatten( $zipfile, $filter ) {

		@ini_set( 'memory_limit', apply_filters( 'admin_memory_limit', WP_MAX_MEMORY_LIMIT ) );

		// Control temp directory?
		$temp_dir = AB_UPLOAD_DIR . 'axisfonts-temp';
		if ( is_dir( $temp_dir ) ) {
			self::delete_files( $temp_dir );
		} else {
			self::create_files( $temp_dir );
		}

		// Create a ZipArchive instance
		$zip = new ZipArchive();

		// Open the Zip Archive
		if ( $zip->open( $zipfile ) === true ) {

			// Iterate the archive files array
			for ( $i = 0; $i < $zip->numFiles; $i++ ) {
				$entry = $zip->getNameIndex( $i );

				if ( ! empty( $filter ) ) {
					$delete  = true;
					$matches = array();

					foreach ( $filter as $regex ) {
						preg_match( '!' . $regex. '!', $entry, $matches );

						if ( ! empty( $matches ) ) {
							$delete = false;
							break;
						}
					}
				}

				// Skip directories and non matching files
				if ( ( substr( $entry, -1 ) == '/' ) || ( ! empty( $delete ) ) ) {
					continue;
				}

				$fp  = $zip->getStream( $entry );
				$ofp = fopen( $temp_dir . '/' . basename( $entry ), 'w' );

				if ( ! $fp ) {
					exit( 'Unable to extract the file.' );
				}

				while ( ! feof( $fp ) ) {
					fwrite( $ofp, fread( $fp, 8192 ) );
				}

				fclose( $fp );
				fclose( $ofp );
			}

			$zip->close();
		} else {
			exit( 'Failed to open the Zip Archive!' );
		}

		return true;
	}

	/**
	 * Iterate over xml file and extract the glyphs for the font.
	 */
	public static function create_config() {
		$file_svg = self::get_svg_file();
		$temp_dir = trailingslashit( AB_UPLOAD_DIR . 'axisfonts-temp' );
		$temp_url = trailingslashit( AB_UPLOAD_URL . 'axisfonts-temp' );

		// If we got no SVG file, remove it?
		if ( empty( $file_svg ) ) {
			self::delete_files( $temp_dir );
			exit( 'Found no SVG file with font information in your folder. Was not able to create the necessary config files' );
		}

		// Fetch the SVG file content
		$response = file_get_contents( $temp_dir . $file_svg );

		// If we weren't able to get the content try to fetch it by using WordPress
		if ( empty( $response ) || trim( $response ) == "" || strpos( $response, '<svg' ) === false ) {
			$response = wp_remote_fopen( $temp_url . $file_svg );
		}

		// Filter the response
		$response = apply_filters( 'axisbuilder_iconfont_uploader_response', $response, $file_svg, $temp_dir );

		if ( ! is_wp_error( $response ) && ! empty( $response ) ) {
			$xml   = simplexml_load_string( $response );
			$glyph = $xml->defs->font->children();
			$attrs = $xml->defs->font->attributes();

			// Store font name ;)
			self::$font_name = (string) $attrs['id'];

			foreach ( $glyph as $key => $value ) {

				if ( $key == 'glyph' ) {
					$attr    = $value->attributes();
					$class   =  (string) $attr['class'];
					$unicode =  (string) $attr['unicode'];

					if ( $class != 'hidden' ) {
						$unicode_key = trim( json_encode( $unicode ), '\\\"' );
						if ( $key == 'glyph' && ! empty( $unicode_key ) && trim( $unicode_key ) != '' ) {
							self::$svg_config[self::$font_name][$unicode_key] = $unicode_key;
						}
					}
				}
			}

			if ( ! empty( self::$svg_config ) && self::$font_name != 'unknown' ) {
				self::charmap_file();
				self::rename_files();
				self::add_iconfonts();
			}
		}

		return false;
	}

	/**
	 * Write the charmap config file for the font
	 */
	public static function charmap_file() {
		$temp_dir = trailingslashit( AB_UPLOAD_DIR . 'axisfonts-temp' );
		$charmap  = $temp_dir . '/charmap.php';
		$handle   = @fopen( $charmap, 'w' );

		if ( $handle ) {
			fwrite( $handle, '<?php $chars = array();' );

			foreach ( self::$svg_config[self::$font_name] as $unicode ) {

				if ( ! empty( $unicode ) ) {
					$delimiter = "'";
					if ( strpos( $unicode, $delimiter ) !== false ) {
						$delimiter = '"';
					}

					fwrite( $handle, "\r\n" . '$chars[\'' . self::$font_name . '\'][' . $delimiter . $unicode . $delimiter . '] = ' . $delimiter . $unicode . $delimiter . ';' );
				}
			}

			// Necessary for EOL-End of Line ;)
			fwrite( $handle, "\r\n" );

			fclose( $handle );
		} else {
			self::delete_files( $temp_dir );
			exit( 'Unable to write a charmap config file' );
		}
	}

	/**
	 * Add Iconfonts
	 */
	public static function add_iconfonts() {
		$fonts = get_option( 'axisbuilder_custom_iconfonts' );

		if ( ! empty( $fonts ) ) {
			$fonts = array();
		}

		$fonts[ self::$font_name ] = array(
			'config'  => 'charmap.php',
			'folder'  => 'iconfonts/' . self::$font_name,
			'include' => 'iconfonts/' . self::$font_name
		);

		update_option( 'axisbuilder_custom_iconfonts', $fonts );
	}

	/**
	 * Find the file with extension we need to create the config
	 */
	private static function get_svg_file() {
		$files = scandir( AB_UPLOAD_DIR . 'axisfonts-temp' );

		foreach ( $files as $file ) {
			if ( strpos( strtolower( $file ), '.svg' ) !== false && $file[0] != '.' ) {
				return $file;
			}
		}
	}

	/**
	 * Create files/directories
	 * @param string $folder
	 */
	public static function create_files( $folder ) {
		if ( is_dir( $folder ) ) {
			return true;
		}

		$created = wp_mkdir_p( trailingslashit( $folder ) );
		@chmod( $folder, 0777 );

		return $created;
	}

	/**
	 * Rename files/directories
	 */
	public static function rename_files() {
		$font_ext = array( 'eot', 'svg', 'ttf', 'woff' );
		$font_dir = trailingslashit( AB_UPLOAD_DIR . self::$font_name );
		$temp_dir = trailingslashit( AB_UPLOAD_DIR . 'axisfonts-temp' );

		// Rename files
		foreach ( glob( $temp_dir . '*' ) as $file ) {
			$path_parts = pathinfo( $file );
			if ( strpos( $path_parts['filename'], '.dev' ) === false && in_array( $path_parts['extension'], $font_ext ) ) {
				rename( $file, trailingslashit( $path_parts['dirname'] ) . self::$font_name . '.' . $path_parts['extension'] );
			}
		}

		// Delete folder and content if they alreay exists ;)
		self::delete_files( $font_dir );

		// Rename the temp folder and all its font files ;)
		rename( $temp_dir, $font_dir );
	}

	/**
	 * Delete files/directories
	 */
	public static function delete_files( $folder ) {
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
