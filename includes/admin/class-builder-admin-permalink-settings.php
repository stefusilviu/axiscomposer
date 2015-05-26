<?php
/**
 * Adds settings to the permalinks admin settings page.
 *
 * @class       AB_Admin_Permalink_Settings
 * @package     AxisBuilder/Admin
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Admin_Permalink_Settings Class
 * @todo Refactor the Permalink Settings Class Smartly xD
 */
class AB_Admin_Permalink_Settings {

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
		add_settings_section( 'axisbuilder-permalink', __( 'Portfolio permalink base', 'axisbuilder' ), array( $this, 'settings' ), 'permalink' );

		// Add our settings
		add_settings_field(
			'axisbuilder_portfolio_category_slug',           // id
			__( 'Portfolio category base', 'axisbuilder' ),  // setting title
			array( $this, 'portfolio_category_slug_input' ), // display callback
			'permalink',                                     // settings page
			'optional'                                       // settings section
		);
		add_settings_field(
			'axisbuilder_portfolio_tag_slug',                // id
			__( 'Portfolio tag base', 'axisbuilder' ),       // setting title
			array( $this, 'portfolio_tag_slug_input' ),      // display callback
			'permalink',                                     // settings page
			'optional'                                       // settings section
		);
	}

	/**
	 * Show a slug input box.
	 */
	public function portfolio_category_slug_input() {
		$permalinks = get_option( 'axisbuilder_permalinks' );
		?>
		<input name="axisbuilder_portfolio_category_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['category_base'] ) ) echo esc_attr( $permalinks['category_base'] ); ?>" placeholder="<?php echo _x( 'portfolio-category', 'slug', 'axisbuilder') ?>" />
		<?php
	}

	/**
	 * Show a slug input box.
	 */
	public function portfolio_tag_slug_input() {
		$permalinks = get_option( 'axisbuilder_permalinks' );
		?>
		<input name="axisbuilder_portfolio_tag_slug" type="text" class="regular-text code" value="<?php if ( isset( $permalinks['tag_base'] ) ) echo esc_attr( $permalinks['tag_base'] ); ?>" placeholder="<?php echo _x( 'portfolio-tag', 'slug', 'axisbuilder' ) ?>" />
		<?php
	}

	/**
	 * Show the settings.
	 */
	public function settings() {
		echo wpautop( __( 'These settings control the permalinks used for portfolio. These settings only apply when <strong>not using "default" permalinks above</strong>.', 'axisbuilder' ) );

		$permalinks = get_option( 'axisbuilder_permalinks' );
		$portfolio_permalink = $permalinks['portfolio_base'];

		// Get base slug
		$base_slug      = _x( 'project', 'default-slug', 'axisbuilder' );
		$portfolio_base = _x( 'portfolio', 'default-slug', 'axisbuilder' );

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
					<th><label><input name="portfolio_permalink" type="radio" value="<?php echo $structures[0]; ?>" class="abtog" <?php checked( $structures[0], $portfolio_permalink ); ?> /> <?php _e( 'Default', 'axisbuilder' ); ?></label></th>
					<td><code><?php echo home_url(); ?>/?portfolio=sample-portfolio</code></td>
				</tr>
				<tr>
					<th><label><input name="portfolio_permalink" type="radio" value="<?php echo $structures[1]; ?>" class="abtog" <?php checked( $structures[1], $portfolio_permalink ); ?> /> <?php _e( 'Portfolio', 'axisbuilder' ); ?></label></th>
					<td><code><?php echo home_url(); ?>/<?php echo $portfolio_base; ?>/sample-portfolio/</code></td>
				</tr>
				<tr>
					<th><label><input name="portfolio_permalink" type="radio" value="<?php echo $structures[2]; ?>" class="abtog" <?php checked( $structures[2], $portfolio_permalink ); ?> /> <?php _e( 'Project base', 'axisbuilder' ); ?></label></th>
					<td><code><?php echo home_url(); ?>/<?php echo $base_slug; ?>/sample-portfolio/</code></td>
				</tr>
				<tr>
					<th><label><input name="portfolio_permalink" type="radio" value="<?php echo $structures[3]; ?>" class="abtog" <?php checked( $structures[3], $portfolio_permalink ); ?> /> <?php _e( 'Category base Project', 'axisbuilder' ); ?></label></th>
					<td><code><?php echo home_url(); ?>/<?php echo $base_slug; ?>/portfolio-category/sample-portfolio/</code></td>
				</tr>
				<tr>
					<th><label><input name="portfolio_permalink" id="axisbuilder_custom_selection" type="radio" value="custom" class="tog" <?php checked( in_array( $portfolio_permalink, $structures ), false ); ?> />
						<?php _e( 'Custom Base', 'axisbuilder' ); ?></label></th>
					<td>
						<input name="portfolio_permalink_structure" id="axisbuilder_permalink_structure" type="text" value="<?php echo esc_attr( $portfolio_permalink ); ?>" class="regular-text code"> <span class="description"><?php _e( 'Enter a custom base to use. A base <strong>must</strong> be set or WordPress will use default instead.', 'axisbuilder' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>
		<script type="text/javascript">
			jQuery( function() {
				jQuery('input.abtog').change(function() {
					jQuery( '#axisbuilder_permalink_structure' ).val( jQuery( this ).val() );
				});

				jQuery( '#axisbuilder_permalink_structure' ).focus( function(){
					jQuery( '#axisbuilder_custom_selection' ).click();
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
			$axisbuilder_portfolio_category_slug = ac_clean( $_POST['axisbuilder_portfolio_category_slug'] );
			$axisbuilder_portfolio_tag_slug      = ac_clean( $_POST['axisbuilder_portfolio_tag_slug'] );

			$permalinks = get_option( 'axisbuilder_permalinks' );

			if ( ! $permalinks ) {
				$permalinks = array();
			}

			$permalinks['category_base'] = untrailingslashit( $axisbuilder_portfolio_category_slug );
			$permalinks['tag_base']      = untrailingslashit( $axisbuilder_portfolio_tag_slug );

			// Portfolio base
			$portfolio_permalink = ac_clean( $_POST['portfolio_permalink'] );

			if ( $portfolio_permalink == 'custom' ) {
				// Get permalink without slashes
				$portfolio_permalink = trim( ac_clean( $_POST['portfolio_permalink_structure'] ), '/' );

				// This is an invalid base structure and breaks pages
				if ( '%portfolio_cat%' == $portfolio_permalink ) {
					$portfolio_permalink = _x( 'portfolio', 'slug', 'axisbuilder' ) . '/' . $portfolio_permalink;
				}

				// Prepending slash
				$portfolio_permalink = '/' . $portfolio_permalink;
			} elseif ( empty( $portfolio_permalink ) ) {
				$portfolio_permalink = false;
			}

			$permalinks['portfolio_base'] = untrailingslashit( $portfolio_permalink );

			update_option( 'axisbuilder_permalinks', $permalinks );
		}
	}
}

new AB_Admin_Permalink_Settings();
