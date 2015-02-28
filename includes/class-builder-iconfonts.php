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

	public static $default_iconfont = '';
	public static $font_name        = 'unknown';
	public static $svg_config       = array();
	public static $iconlist         = array();
	public static $charlist         = array();

	/**
	 * Hook in methods
	 */
	public static function init() {
		self::$default_iconfont = apply_filters( 'axisbuilder_default_iconfont', array(
			'entypo-fontello' => array(
				'append'	=> '?v=3',
				'folder'  	=> AB()->plugin_url() . '/assets/fonts/entypo-fontello',
				'include' 	=> AB()->plugin_path() . '/assets/fonts/entypo-fontello',
				'config'	=> 'charmap.php',
				'compat'	=> 'charmap-compat.php', // Needed to make the theme compatible with the old version of the font
				'full_path'	=> 'true' // Tells the script to not prepend the wp_upload dir path to these urls
			),
		));

		// Actions
		add_action( 'wp_head',    array( __CLASS__, 'iconfont_style' ) );
		add_action( 'admin_head', array( __CLASS__, 'iconfont_style' ) );
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
	 * @param string   $zipfile
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
		$temp_dir = trailingslashit( AB_UPLOAD_DIR . 'axisfonts-temp' );
		$temp_url = trailingslashit( AB_UPLOAD_URL . 'axisfonts-temp' );

		$file_svg  = self::get_filename( 'svg' );
		$file_json = self::get_filename( 'json' );

		// If we got no SVG file, remove it?
		if ( empty( $file_svg ) || empty( $file_json ) ) {
			self::delete_files( $temp_dir );
			exit( 'SVG or JSON file with font information is needed to create the necessary config files.' );
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

			// Existence
			$font_dir = trailingslashit( AB_UPLOAD_DIR . self::$font_name );
			if ( is_dir( $font_dir ) ) {
				self::delete_files( $temp_dir );
				die( 'It seems that the font with the same name is already exists! Please upload the font with different name.' );
			}

			if ( ! empty( self::$svg_config ) && self::$font_name != 'unknown' ) {
				self::charmap_file();
				self::rename_files();
				self::add_iconfont();
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
	 * Add Iconfont
	 */
	public static function add_iconfont() {
		$fonts = get_option( 'axisbuilder_custom_iconfonts' );

		if ( empty( $fonts ) ) {
			$fonts = array();
		}

		$fonts[ self::$font_name ] = array(
			'include' => self::$font_name,
			'folder'  => self::$font_name,
			'config'  => 'charmap.php',
		);

		update_option( 'axisbuilder_custom_iconfonts', $fonts );
	}

	/**
	 * Remove Iconfont
	 */
	public static function remove_iconfont( $fontname ) {
		$fonts = get_option( 'axisbuilder_custom_iconfonts' );

		if ( isset( $fonts[ $fontname ] ) ) {
			unset( $fonts[ $fontname ] );
			update_option( 'axisbuilder_custom_iconfonts', $fonts );
		}
	}

	/**
	 * Retrieve the filename based on the extension name.
	 * @param  string $extension The extension to search.
	 * @return string The filename if found.
	 */
	private static function get_filename( $extension ) {
		$files = scandir( AB_UPLOAD_DIR . 'axisfonts-temp' );
		foreach ( $files as $file ) {
			$ext = ! empty( $extension ) ? '.' . strtolower( $extension ) : '';
			if ( strpos( strtolower( $file ), $ext ) !== false && $file[0] != '.' ) {
				return $file;
			}
		}
	}

	/**
	 * Create files/directories
	 * @param string $folder
	 */
	public static function create_files( $folder ) {
		if ( ! is_dir( $folder ) ) {
			wp_mkdir_p( trailingslashit( $folder ) );
		}

		// Permission ;)
		@chmod( $folder, 0777 );
	}

	/**
	 * Delete files/directories
	 * @param string $folder
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
			if ( ( strpos( $path_parts['filename'], '.dev' ) === false ) && in_array( $path_parts['extension'], $font_ext ) ) {
				rename( $file, trailingslashit( $path_parts['dirname'] ) . self::$font_name . '.' . $path_parts['extension'] );
			}
		}

		// Delete files/directories
		self::delete_files( $font_dir );

		// Rename files/directories
		rename( $temp_dir, $font_dir );
	}

	/**
	 * Load Iconfonts list
	 */
	public static function load_iconfont_list() {
		if ( ! empty( self::$iconlist ) ) {
			return self::$iconlist;
		}

		$custom_fonts = get_option( 'axisbuilder_custom_iconfonts' );
		if ( empty( $custom_fonts ) ) {
			$custom_fonts = array();
		}

		$font_configs = array_merge( self::$default_iconfont, $custom_fonts );

		// If we got any include the charmaps and add the chars to an array
		$upload_dir = wp_upload_dir();
		$basedir    = trailingslashit( $upload_dir['basedir'] );
		$baseurl    = trailingslashit( $upload_dir['baseurl'] );

		foreach ( $font_configs as $key => $value ) {
			if ( empty( $value['full_path'] ) ) {
				$font_configs[$key]['include'] = $basedir . $font_configs[$key]['include'];
				$font_configs[$key]['folder']  = $baseurl . $font_configs[$key]['folder'];
			}
		}

		// Cache the Result
		self::$iconlist = $font_configs;

		return $font_configs;
	}

	/**
	 * Load Iconfonts charlist
	 */
	public static function load_charlist() {
		if ( ! empty( self::$charlist ) ) {
			return self::$charlist;
		}

		$charset      = array();
		$font_configs = self::load_iconfont_list();

		// If we got any include the charmaps and add the chars to an array
		$upload_dir = wp_upload_dir();
		$basedir    = trailingslashit( $upload_dir['basedir'] );

		foreach ( $font_configs as $value ) {
			$chars = array();
			include( $value['include'] . '/' . $value['config'] );

			if ( ! empty( $chars ) ) {
				$charset = array_merge( $charset, $chars );
			}
		}

		// Cache the Result
		self::$charlist = $charset;

		return $charset;
	}

	/**
	 * Outputs some styles in the wp <head> to load iconfonts font-face
	 */
	public function iconfont_style() {
		$output       = '';
		$font_configs = self::load_iconfont_list();

		if ( current_user_can( 'manage_axisbuilder' ) && ! empty( $font_configs ) ) {
			$output .= '<style type="text/css">';
			foreach ( $font_configs as $font_name => $font_list ) {
				$append  = empty( $font_list['append'] ) ? '' : $font_list['append'];
				$qmark   = empty( $append ) ? '?' : $append;
				$fstring = $font_list['folder'] . '/' . $font_name;

				$output .= "
@font-face {
	font-family: '{$font_name}';
	src:	url('{$fstring}.eot{$append}');
	src:	url('{$fstring}.eot{$qmark}#iefix') format('embedded-opentype'),
		url('{$fstring}.woff{$append}') format('woff'),
		url('{$fstring}.ttf{$append}') format('truetype'),
		url('{$fstring}.svg{$append}#{$font_name}') format('svg');
	font-weight: normal;
	font-style: normal;
}
#top .axisbuilder-font-{$font_name}, body .axisbuilder-font-{$font_name}, html body [data-axisbuilder_iconfont='{$font_name}']:before { font-family: '{$font_name}'; }\n\r";
			}

			$output .= "</style>\n\r";
		}

		echo $output;
	}
}

AB_Iconfonts::init();
