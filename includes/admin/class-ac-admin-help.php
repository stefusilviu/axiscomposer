<?php
/**
 * Add some contextual help tabs.
 *
 * @class    AC_Admin_Help
 * @version  1.0.0
 * @package  AxisComposer/Admin
 * @category Admin
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Admin_Help Class
 */
class AC_Admin_Help {

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

		if ( ! in_array( $screen->id, ac_get_screen_ids() ) ) {
			return;
		}

		$screen->add_help_tab( array(
			'id'        => 'axiscomposer_docs_tab',
			'title'     => __( 'Documentation', 'axiscomposer' ),
			'content'   =>
				'<h2>' . __( 'Documentation', 'axiscomposer' ) . '</h2>' .
				'<p>' . __( 'Should you need help understanding, using, or extending AxisComposer, please read our documentation. You will find all kinds of resources including snippets, tutorials and much more.' , 'axiscomposer' ) . '</p>' .
				'<p><a href="' . 'http://docs.axisthemes.com/documentation/plugins/axiscomposer/?utm_source=AxisComposerPlugin&utm_medium=Help&utm_content=Docs&utm_campaign=Onboarding' . '" class="button button-primary">' . __( 'AxisComposer Documentation', 'axiscomposer' ) . '</a> <a href="' . 'http://docs.axisthemes.com/ac-apidocs/?utm_source=AxisComposerPlugin&utm_medium=Help&utm_content=APIDocs&utm_campaign=Onboarding' . '" class="button">' . __( 'Developer API Docs', 'axiscomposer' ) . '</a></p>'
		) );

		$screen->add_help_tab( array(
			'id'        => 'axiscomposer_support_tab',
			'title'     => __( 'Support', 'axiscomposer' ),
			'content'   =>
				'<h2>' . __( 'Support', 'axiscomposer' ) . '</h2>' .
				'<p>' . sprintf( __( 'After %sreading the documentation%s, for further assistance you can use the %scommunity forums%s on WordPress.org to talk with other users. If however you are a AxisThemes customer, or need help with premium add-ons sold by AxisThemes, please %suse our helpdesk%s.', 'axiscomposer' ), '<a href="http://docs.axisthemes.com/documentation/plugins/axiscomposer/?utm_source=AxisComposerPlugin&utm_medium=Help&utm_content=Docs&utm_campaign=Onboarding">', '</a>', '<a href="https://wordpress.org/support/plugin/axiscomposer">', '</a>', '<a href="http://www.axisthemes.com/my-account/tickets/?utm_source=AxisComposerPlugin&utm_medium=Help&utm_content=Tickets&utm_campaign=Onboarding">', '</a>' ) . '</p>' .
				'<p>' . __( 'Before asking for help we recommend checking the system status page to identify any problems with your configuration.', 'axiscomposer' ) . '</p>' .
				'<p><a href="' . admin_url( 'admin.php?page=ac-status' ) . '" class="button button-primary">' . __( 'System Status', 'axiscomposer' ) . '</a> <a href="' . 'https://wordpress.org/support/plugin/axiscomposer' . '" class="button">' . __( 'WordPress.org Forums', 'axiscomposer' ) . '</a> <a href="' . 'http://www.axisthemes.com/my-account/tickets/?utm_source=AxisComposerPlugin&utm_medium=Help&utm_content=Tickets&utm_campaign=Onboarding' . '" class="button">' . __( 'AxisThemes Customer Support', 'axiscomposer' ) . '</a></p>'
		) );

		$screen->add_help_tab( array(
			'id'        => 'axiscomposer_bugs_tab',
			'title'     => __( 'Found a bug?', 'axiscomposer' ),
			'content'   =>
				'<h2>' . __( 'Found a bug?', 'axiscomposer' ) . '</h2>' .
				'<p>' . sprintf( __( 'If you find a bug within AxisComposer core you can create a ticket via <a href="%s">Github issues</a>. Ensure you read the <a href="%s">contribution guide</a> prior to submitting your report. To help us solve your issue, please be as descriptive as possible and include your <a href="%s">system status report</a>.', 'axiscomposer' ), 'https://github.com/axisthemes/axiscomposer/issues?state=open', 'https://github.com/axisthemes/axiscomposer/blob/master/CONTRIBUTING.md', admin_url( 'admin.php?page=ac-status' ) ) . '</p>' .
				'<p><a href="' . 'https://github.com/axisthemes/axiscomposer/issues?state=open' . '" class="button button-primary">' . __( 'Report a bug', 'axiscomposer' ) . '</a> <a href="' . admin_url( 'admin.php?page=ac-status' ) . '" class="button">' . __( 'System Status', 'axiscomposer' ) . '</a></p>'

		) );

		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'axiscomposer' ) . '</strong></p>' .
			'<p><a href="' . 'http://www.axisthemes.com/axiscomposer/?utm_source=AxisComposerPlugin&utm_medium=Help&utm_content=AxisComposerPage&utm_campaign=Onboarding' . '" target="_blank">' . __( 'About AxisComposer', 'axiscomposer' ) . '</a></p>' .
			'<p><a href="' . 'http://wordpress.org/extend/plugins/axiscomposer/' . '" target="_blank">' . __( 'WordPress.org Project', 'axiscomposer' ) . '</a></p>' .
			'<p><a href="' . 'https://github.com/axisthemes/axiscomposer' . '" target="_blank">' . __( 'Github Project', 'axiscomposer' ) . '</a></p>' .
			'<p><a href="' . 'http://www.axisthemes.com/product-category/themes/axiscomposer/?utm_source=AxisComposerPlugin&utm_medium=Help&utm_content=ACThemes&utm_campaign=Onboarding' . '" target="_blank">' . __( 'Official Themes', 'axiscomposer' ) . '</a></p>' .
			'<p><a href="' . 'http://www.axisthemes.com/product-category/extensions/axiscomposer/?utm_source=AxisComposerPlugin&utm_medium=Help&utm_content=ACExtensions&utm_campaign=Onboarding' . '" target="_blank">' . __( 'Official Extensions', 'axiscomposer' ) . '</a></p>'
		);
	}
}

new AC_Admin_Help();
