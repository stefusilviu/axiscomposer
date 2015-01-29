<?php
/**
 * Add some contextual help tabs.
 *
 * @class       AB_Admin_Help
 * @package     AxisBuilder/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'AB_Admin_Help' ) ) :

/**
 * AB_Admin_Help Class
 */
class AB_Admin_Help {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'current_screen', array( $this, 'add_tabs' ), 50 );
	}

	/**
	 * Add Contextual help tabs.
	 */
	public function add_tabs() {
		$screen = get_current_screen();

		if ( ! in_array( $screen->id, axisbuilder_get_screen_ids() ) ) {
			return;
		}

		$screen->add_help_tab( array(
			'id'        => 'axisbuilder_docs_tab',
			'title'     => __( 'Documentation', 'axisbuilder' ),
			'content'   =>

				'<p>' . __( 'Thank you for using Axis Builder :) Should you need help using or extending Axis Builder please read the documentation.', 'axisbuilder' ) . '</p>' .

				'<p><a href="' . 'http://docs.axisthemes.com/documentation/plugins/axis-builder/' . '" class="button button-primary">' . __( 'Axis Builder Documentation', 'axisbuilder' ) . '</a> <a href="' . 'http://docs.axisthemes.com/apidocs/axis-builder/' . '" class="button">' . __( 'Developer API Docs', 'axisbuilder' ) . '</a></p>'

		) );

		$screen->add_help_tab( array(
			'id'        => 'axisbuilder_support_tab',
			'title'     => __( 'Support', 'axisbuilder' ),
			'content'   =>

				'<p>' . sprintf( __( 'After %sreading the documentation%s, for further assistance you can use our %scommunity forum%s if you get stuck. For help with premium add-ons from AxisThemes, or if you are a AxisThemes customer, you can %suse our helpdesk%s.', 'axisbuilder' ), '<a href="http://docs.axisthemes.com/documentation/plugins/axis-builder/">', '</a>', '<a href="http://wordpress.org/support/plugin/axis-builder">', '</a>', '<a href="http://support.axisthemes.com">', '</a>' ) . '</p>' .

				'<p>' . __( 'Before asking for help we recommend checking the status page to identify any problems with your configuration.', 'axisbuilder' ) . '</p>' .

				'<p><a href="' . admin_url( 'admin.php?page=axisbuilder-status' ) . '" class="button button-primary">' . __( 'System Status', 'axisbuilder' ) . '</a> <a href="' . 'http://wordpress.org/support/plugin/axis-builder' . '" class="button">' . __( 'Community Support', 'axisbuilder' ) . '</a> <a href="' . 'http://support.woothemes.com' . '" class="button">' . __( 'AxisThemes Customer Support', 'axisbuilder' ) . '</a></p>'

		) );

		$screen->add_help_tab( array(
			'id'        => 'axisbuilder_bugs_tab',
			'title'     => __( 'Found a bug?', 'axisbuilder' ),
			'content'   =>

				'<p>' . sprintf( __( 'If you find a bug within Axis Builder core you can create a ticket via <a href="%s">Github issues</a>. Ensure you read the <a href="%s">contribution guide</a> prior to submitting your report. Be as descriptive as possible and please include your <a href="%s">system status report</a>.', 'axisbuilder' ), 'https://github.com/axisthemes/axis-builder/issues?state=open', 'https://github.com/axisthemes/axis-builder/blob/master/CONTRIBUTING.md', admin_url( 'admin.php?page=axisbuilder-status' ) ) . '</p>' .

				'<p><a href="' . 'https://github.com/axisthemes/axis-builder/issues?state=open' . '" class="button button-primary">' . __( 'Report a bug', 'axisbuilder' ) . '</a> <a href="' . admin_url('admin.php?page=axisbuilder-status') . '" class="button">' . __( 'System Status', 'axisbuilder' ) . '</a></p>'

		) );

		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'axisbuilder' ) . '</strong></p>' .
			'<p><a href="' . 'http://axisthemes.com/axis-builder/' . '" target="_blank">' . __( 'About Axis Builder', 'axisbuilder' ) . '</a></p>' .
			'<p><a href="' . 'http://wordpress.org/extend/plugins/axis-builder/' . '" target="_blank">' . __( 'WordPress.org Project', 'axisbuilder' ) . '</a></p>' .
			'<p><a href="' . 'https://github.com/axisthemes/axis-builder' . '" target="_blank">' . __( 'Github Project', 'axisbuilder' ) . '</a></p>' .
			'<p><a href="' . 'http://axisthemes.com/product-category/themes/axis-builder/' . '" target="_blank">' . __( 'Official Themes', 'axisbuilder' ) . '</a></p>' .
			'<p><a href="' . 'http://axisthemes.com/product-category/extensions/axisbuilder/' . '" target="_blank">' . __( 'Official Extensions', 'axisbuilder' ) . '</a></p>'
		);
	}
}

endif;

return new AB_Admin_Help();
