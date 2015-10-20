<?php
/**
 * Tools for AxisComposer
 *
 * @class    AC_CLI_Tool
 * @version  1.0.0
 * @package  AxisComposer/CLI
 * @category CLI
 * @author   AxisThemes
 */
class AC_CLI_Tool extends WP_CLI_Command {

	/**
	 * Update pagebuilder status.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : Post ID.
	 *
	 * --<field>=<value>
	 * : One or more fields to update.
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields are available for update command:
	 *
	 * * status
	 *
	 * ## EXAMPLES
	 *
	 *     wp ac tool update 123
	 *
	 *     wp ac tool update 123 --status=<active|inactive>
	 *
	 * @since 1.0.0
	 */
	public function update( $args, $assoc_args ) {
		try {
			$post_status = get_post_status( $args[0] );
			if ( false === $post_status ) {
				throw new AC_CLI_Exception( 'axiscomposer_cli_invalid_id', sprintf( __( 'Invalid Post ID: %s', 'axiscomposer' ), $args[0] ) );
			}

			$id   = absint( $args[0] );
			$type = array( 'active', 'inactive' );
			$data = apply_filters( 'axiscomposer_cli_update_pagebuilder_data', $assoc_args, $id );

			// Set pagebuilder status meta
			if ( isset( $data['status'] ) ) {

				// Validate status types
				if ( ! in_array( ac_clean( $data['status'] ), $type ) ) {
					throw new AC_CLI_Exception( 'axiscomposer_cli_invalid_status_type', sprintf( __( 'Invalid status type - the status type must be any of these: %s', 'axiscomposer' ), implode( ', ', $type ) ) );
				}

				update_post_meta( $id, '_pagebuilder_status', $data['status'] );

				$editor_toggle = ( 'active' == $data['status'] ) ? 'Pagebuilder' : 'Default Editor';

				WP_CLI::success( "$editor_toggle activated for Post ID $id." );
			} else {
				WP_CLI::run_command( array( 'ac', 'tool', 'update', $args[0] ), array( 'status' => 'active' ) );
			}
		} catch ( AC_CLI_Exception $e ) {
			WP_CLI::error( $e->getMessage() );
		}
	}
}
