<?php

/* Path to the WordPress codebase you'd like to test. Add a backslash in the end. */
define( 'ABSPATH', '/c/xampp/htdocs/axiscomposer/wp-content/plugins/axiscomposer' );

// Test with multisite enabled.
// Alternatively, use the tests/phpunit/multisite.xml configuration file.
// define( 'WP_TESTS_MULTISITE', true );

// Force known bugs to be run.
// Tests with an associated Trac ticket that is still open are normally skipped.
// define( 'WP_TESTS_FORCE_KNOWN_BUGS', true );

// Test with WordPress debug mode (default).
define( 'WP_DEBUG', true );

// ** MySQL settings ** //

// This configuration file will be used by the copy of WordPress being tested.
// wordpress/wp-config.php will be ignored.

// WARNING WARNING WARNING!
// These tests will DROP ALL TABLES in the database with the prefix named below.
// DO NOT use a production database or one that is shared with something else.

define( 'DB_NAME', 'axiscomposer_test_1' );
define( 'DB_USER', 'test' );
define( 'DB_PASSWORD', 'test' );
define( 'DB_HOST', 'localhost' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

$table_prefix  = 'actests_';   // Only numbers, letters, and underscores please!

define( 'WP_TESTS_DOMAIN', 'axiscomposer.com' );
define( 'WP_TESTS_EMAIL', 'admin@axiscomposer.com' );
define( 'WP_TESTS_TITLE', 'AxisComposer Unit Tests' );

define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );
