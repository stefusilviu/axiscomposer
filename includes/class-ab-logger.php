<?php
/**
 * AxisBuilder Logger
 *
 * Allows log files to be written to for debugging purposes.
 *
 * @class       AB_Logger
 * @package     AxisBuilder/Classes
 * @category    Class
 * @author      AxisThemes
 * @since       1.0.0
 */
class AB_Logger {

	/**
	 * @var array Stores open file _handles.
	 * @access private
	 */
	private $_handles;

	/**
	 * Class Constructor Method.
	 */
	public function __construct() {
		$this->_handles = array();
	}

	/**
	 * Class Destructor Method.
	 */
	public function __destruct() {
		foreach ( $this->_handles as $handle ) {
			@fclose( escapeshellarg( $handle ) );
		}
	}

	/**
	 * Open log file for writing.
	 *
	 * @access private
	 * @param  mixed $handle
	 * @return bool  success
	 */
	private function open( $handle ) {
		if ( isset( $this->_handles[ $handle ] ) ) {
			return true;
		}

		if ( $this->_handles[ $handle ] = @fopen( ab_get_log_file_path( $handle ), 'a' ) ) {
			return true;
		}

		return false;
	}


	/**
	 * Add a log entry to chosen file.
	 *
	 * @param mixed $handle
	 * @param mixed $message
	 */
	public function add( $handle, $message ) {
		if ( $this->open( $handle ) && is_resource( $this->_handles[ $handle ] ) ) {
			$time = date_i18n( 'm-d-Y @ H:i:s -' ); // Grab Time
			@fwrite( $this->_handles[ $handle ], $time . " " . $message . "\n" );
		}
	}

	/**
	 * Clear entries from chosen file.
	 *
	 * @param mixed $handle
	 */
	public function clear( $handle ) {
		if ( $this->open( $handle ) && is_resource( $this->_handles[ $handle ] ) ) {
			@ftruncate( $this->_handles[ $handle ], 0 );
		}
	}
}
