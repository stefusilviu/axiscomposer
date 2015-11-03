<?php
/**
 * Update AC to 1.0.0
 *
 * @author   AxisThemes
 * @category Admin
 * @package  AxisComposer/Admin/Updates
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * TinyMCE Plugin settings.
 * Maintain the old tinyMCE logic for upgrades.
 */
update_option( 'axiscomposer_tinymce_enabled', 'no' );
