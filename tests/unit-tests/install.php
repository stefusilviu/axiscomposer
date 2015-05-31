<?php
/**
 * Test AC Install class
 *
 * @since 1.0
 */
class AC_Tests_Install extends AC_Unit_Test_Case {

	/**
	 * Test check version
	 */
	public function test_check_version() {
		update_option( 'axiscomposer_version', AC()->version - 1 );
		AC_Install::check_version();

		$this->assertTrue( did_action( 'axiscomposer_updated' ) === 1 );

		update_option( 'axiscomposer_version', AC()->version );
		AC_Install::check_version();

		$this->assertTrue( did_action( 'axiscomposer_updated' ) === 1 );
	}

	/**
	 * Test - install
	 */
	public function test_install() {
		// clean existing install first
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			define( 'WP_UNINSTALL_PLUGIN', true );
		}
		include( dirname( dirname( dirname( __FILE__ ) ) ) . '/uninstall.php' );

		AC_Install::install();

		$this->assertTrue( get_option( 'axiscomposer_version' ) === AC()->version );
	}

	/**
	 * Test - create roles
	 */
	public function test_create_roles() {
		// Clean existing install first
		if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
			define( 'WP_UNINSTALL_PLUGIN', true );
		}
		include( dirname( dirname( dirname( __FILE__ ) ) ) . '/uninstall.php' );

		AC_Install::create_roles();
	}

	/**
	 * Test - remove roles
	 */
	public function test_remove_roles() {
		AC_Install::remove_roles();
	}

	/**
	 * Test - in_plugin_update_message
	 */
	public function test_in_plugin_update_message() {
		ob_start();
		AC_install::in_plugin_update_message( array( 'Version' => '1.0.0' ) );
		$result = ob_get_clean();
		$this->assertTrue( is_string( $result ) );
	}

}
