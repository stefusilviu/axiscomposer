<?php
/**
 * Abstract Integration class
 *
 * Extended by individual integrations to offer additional functionality.
 *
 * @class       AC_Integration
 * @extends     AC_Settings_API
 * @package     AxisComposer/Abstracts
 * @category    Abstract Class
 * @author      AxisThemes
 * @since       1.0.0
 */
abstract class AC_Integration extends AC_Settings_API {

	/**
	 * Admin Options
	 *
	 * Setup the gateway settings screen.
	 * Override this in your gateway.
	 */
	public function admin_options() { ?>

		<h3><?php echo isset( $this->method_title ) ? $this->method_title : __( 'Settings', 'axiscomposer' ) ; ?></h3>

		<?php echo isset( $this->method_description ) ? wpautop( $this->method_description ) : ''; ?>

		<table class="form-table">
			<?php $this->generate_settings_html(); ?>
		</table>

		<!-- Section -->
		<div><input type="hidden" name="section" value="<?php echo $this->id; ?>" /></div>

		<?php
	}
}
