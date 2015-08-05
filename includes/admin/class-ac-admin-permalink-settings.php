<?php
/**
 * Adds settings to the permalinks admin settings page.
 *
 * @class    AC_Admin_Permalink_Settings
 * @version  1.0.0
 * @package  AxisComposer/Admin
 * @category Admin
 * @author   AxisThemes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AC_Admin_Permalink_Settings Class
 * @todo Refactor the Permalink Settings Class Smartly xD
 */
class AC_Admin_Permalink_Settings {

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		$this->settings_init();
		$this->settings_save();
	}

	/**
	 * Init our settings.
	 */
	public function settings_init() {
		// Add a section to the permalinks page
		add_settings_section( 'axiscomposer-permalink', __( 'Portfolio permalink base', 'axiscomposer' ), array( $this, 'settings' ), 'permalink' );

		// Add our settings
		add_settings_field(
			'axiscomposer_portfolio_category_slug',          // id
			__( 'Portfolio category base', 'axiscomposer' ), // setting title
			array( $this, 'portfolio_category_slug_input' ), // display callback
			'permalink',                                     // settings page
			'optional'                                       // settings section
		);
		add_settings_field(
			'axiscomposer_portfolio_tag_slug',               // id
			__( 'Portfolio tag base', 'axiscomposer' ),      // setting title
			array( $this, 'portfolio_tag_slug_input' ),      // display callback
			'permalink',                                     // settings page
			'optional'                                       // settings section
		);
	}

	/**
	 * Show a slug input box.
	 */
	public function portfolio_category_slug_input() {
		$permalinks = get_option( 'axiscomposer_permalinks' );
		?>
		<input name="axiscomposer_portfolio_category_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['category_base'] ) ) echo esc_attr( $permalinks['category_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'portfolio-category', 'slug', 'axiscomposer') ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function portfolio_tag_slug_input() {
		$permalinks = get_option( 'axiscomposer_permalinks' );
		?>
		<input name="axiscomposer_portfolio_tag_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['tag_base'] ) ) echo esc_attr( $permalinks['tag_base'] ); ?>" placeholder="<?php echo esc_attr_x( 'portfolio-tag', 'slug', 'axiscomposer' ) ?>" />
		<?php
	}

	/**
	 * Show the settings.
	 */
	public function settings() {
		echo wpautop( __( 'These settings control the permalinks used for portfolio. These settings only apply when <strong>not using "default" permalinks above</strong>.', 'axiscomposer' ) );

		$permalinks = get_option( 'axiscomposer_permalinks' );
		$portfolio_permalink = $permalinks['portfolio_base'];

		// Get base slug
		$base_slug      = _x( 'project', 'default-slug', 'axiscomposer' );
		$portfolio_base = _x( 'portfolio', 'default-slug', 'axiscomposer' );

		$structures = array(
			0 => '',
			1 => '/' . trailingslashit( $portfolio_base ),
			2 => '/' . trailingslashit( $base_slug ),
			3 => '/' . trailingslashit( $base_slug ) . trailingslashit( '%portfolio_cat%' )
		);
		?>
		<table class="form-table">
			<tbody>
				<tr>
					<th><label><input name="portfolio_permalink" type="radio" value="<?php echo $structures[0]; ?>" class="actog" <?php checked( $structures[0], $portfolio_permalink ); ?> /> <?php _e( 'Default', 'axiscomposer' ); ?></label></th>
					<td><code><?php echo home_url(); ?>/?portfolio=sample-portfolio</code></td>
				</tr>
				<tr>
					<th><label><input name="portfolio_permalink" type="radio" value="<?php echo $structures[1]; ?>" class="actog" <?php checked( $structures[1], $portfolio_permalink ); ?> /> <?php _e( 'Portfolio', 'axiscomposer' ); ?></label></th>
					<td><code><?php echo home_url(); ?>/<?php echo $portfolio_base; ?>/sample-portfolio/</code></td>
				</tr>
				<tr>
					<th><label><input name="portfolio_permalink" type="radio" value="<?php echo $structures[2]; ?>" class="actog" <?php checked( $structures[2], $portfolio_permalink ); ?> /> <?php _e( 'Project base', 'axiscomposer' ); ?></label></th>
					<td><code><?php echo home_url(); ?>/<?php echo $base_slug; ?>/sample-portfolio/</code></td>
				</tr>
				<tr>
					<th><label><input name="portfolio_permalink" type="radio" value="<?php echo $structures[3]; ?>" class="actog" <?php checked( $structures[3], $portfolio_permalink ); ?> /> <?php _e( 'Category base Project', 'axiscomposer' ); ?></label></th>
					<td><code><?php echo home_url(); ?>/<?php echo $base_slug; ?>/portfolio-category/sample-portfolio/</code></td>
				</tr>
				<tr>
					<th><label><input name="portfolio_permalink" id="axiscomposer_custom_selection" type="radio" value="custom" class="tog" <?php checked( in_array( $portfolio_permalink, $structures ), false ); ?> />
						<?php _e( 'Custom Base', 'axiscomposer' ); ?></label></th>
					<td>
						<input name="portfolio_permalink_structure" id="axiscomposer_permalink_structure" type="text" value="<?php echo esc_attr( $portfolio_permalink ); ?>" class="regular-text code"> <span class="description"><?php _e( 'Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.', 'axiscomposer' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<script type="text/javascript">
			jQuery( function() {
				jQuery('input.actog').change(function() {
					jQuery( '#axiscomposer_permalink_structure' ).val( jQuery( this ).val() );
				});

				jQuery( '#axiscomposer_permalink_structure' ).focus( function(){
					jQuery( '#axiscomposer_custom_selection' ).click();
				});
			} );
		</script>
		<?php
	}

	/**
	 * Save the settings.
	 */
	public function settings_save() {

		if ( ! is_admin() ) {
			return;
		}

		// We need to save the options ourselves; settings api does not trigger save for the permalinks page
		if ( isset( $_POST['permalink_structure'] ) || isset( $_POST['category_base'] ) && isset( $_POST['portfolio_permalink'] ) ) {
			// Cat and tag bases
			$axiscomposer_portfolio_category_slug = ac_clean( $_POST['axiscomposer_portfolio_category_slug'] );
			$axiscomposer_portfolio_tag_slug      = ac_clean( $_POST['axiscomposer_portfolio_tag_slug'] );

			$permalinks = get_option( 'axiscomposer_permalinks' );

			if ( ! $permalinks ) {
				$permalinks = array();
			}

			$permalinks['category_base'] = untrailingslashit( $axiscomposer_portfolio_category_slug );
			$permalinks['tag_base']      = untrailingslashit( $axiscomposer_portfolio_tag_slug );

			// Portfolio base
			$portfolio_permalink = ac_clean( $_POST['portfolio_permalink'] );

			if ( $portfolio_permalink == 'custom' ) {
				// Get permalink without slashes
				$portfolio_permalink = trim( ac_clean( $_POST['portfolio_permalink_structure'] ), '/' );

				// This is an invalid base structure and breaks pages
				if ( '%portfolio_cat%' == $portfolio_permalink ) {
					$portfolio_permalink = _x( 'portfolio', 'slug', 'axiscomposer' ) . '/' . $portfolio_permalink;
				}

				// Prepending slash
				$portfolio_permalink = '/' . $portfolio_permalink;
			} elseif ( empty( $portfolio_permalink ) ) {
				$portfolio_permalink = false;
			}

			$permalinks['portfolio_base'] = untrailingslashit( $portfolio_permalink );

			update_option( 'axiscomposer_permalinks', $permalinks );
		}
	}
}

new AC_Admin_Permalink_Settings();
