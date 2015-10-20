<?php
/**
 * Manage AxisComposer from CLI.
 *
 * @class    AC_CLI
 * @version  1.0.0
 * @package  AxisComposer/CLI
 * @category CLI
 * @author   AxisThemes
 */
class AC_CLI extends WP_CLI_Command {}

WP_CLI::add_command( 'ac',      'AC_CLI' );
WP_CLI::add_command( 'ac tool', 'AC_CLI_Tool' );
