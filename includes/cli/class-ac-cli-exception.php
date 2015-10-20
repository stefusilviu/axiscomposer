<?php
/**
 * AxisComposer CLI Exception Class.
 *
 * Extends Exception to provide additional data.
 *
 * @class    AC_CLI_Exception
 * @version  1.0.0
 * @package  AxisComposer/CLI
 * @category CLI
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_CLI_Exception Class
 */
class AC_CLI_Exception extends Exception {

	/** @var string sanitized error code */
	protected $error_code;

	/**
	 * Setup exception, requires 3 params:
	 *
	 * error code - machine-readable, e.g. `axiscomposer_invalid_post_id`
	 * error message - friendly message, e.g. 'Post ID is invalid'
	 *
	 * @since 1.0.0
	 * @param string $error_code
	 * @param string $error_message user-friendly translated error message
	 */
	public function __construct( $error_code, $error_message ) {
		$this->error_code = $error_code;
		parent::__construct( $error_message );
	}

	/**
	 * Returns the error code
	 *
	 * @since  1.0.0
	 * @return string
	 */
	public function getErrorCode() {
		return $this->error_code;
	}
}
