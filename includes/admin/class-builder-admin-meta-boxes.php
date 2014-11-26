<?php
/**
 * AxisBuilder Meta Boxes
 *
 * Sets up the write panels used by builder and custom post types.
 *
 * @class       AB_Admin_Meta_Boxes
 * @package     AxisBuilder/Admin/Meta Boxes
 * @category    Admin
 * @author      AxisThemes
 * @since       1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * AB_Admin_Meta_Boxes Class
 */
class AB_Admin_Meta_Boxes {

	private static $meta_box_errors = array();

	/**
	 * Hook in tabs.
	 */
	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 30 );

		add_action( 'admin_print_footer_scripts', array( $this, 'metabox_not_sortable_script' ), 99 );
	}

	/**
	 * Add WC Meta boxes
	 */
	public function add_meta_boxes() {

		$screens = get_builder_core_supported_screens();

		foreach ( $screens as $screen ) {
			add_meta_box( 'axisbuilder-editor', __( 'Axis Builder', 'axisbuilder' ), array( $this, 'create_meta_box' ), $screen, 'normal', 'high' );
			add_meta_box( 'axisbuilder-layout', __( 'Layout Settings', 'axisbuilder' ), array( $this, 'create_meta_box' ), $screen, 'side', 'default' );

			// Filters for classes and columns
			add_filter( 'postbox_classes_' . $screen . '_axisbuilder-editor', array( $this, 'custom_postbox_classes' ) );
		}
	}

	public function create_meta_box() {
		echo "string";
	}

	/**
	 * Filter the postbox classes for a specific screen and screen ID combo.
	 * @param  array $classes An array of postbox classes.
	 * @return array
	 */
	public function custom_postbox_classes( $classes ) {
		if( ! in_array( 'not-sortable', $classes ) ) {
			$classes[] = 'not-sortable';
		}

		return $classes;
	}

	function metabox_not_sortable_script() {
		?>
			<script type="text/javascript">
			/* <![CDATA[ */
			jQuery( function( $ ){
				$( '.meta-box-sortables' )
					// define the cancel option of sortable to ignore sortable element for boxes with '.not-sortable' css class
					.sortable( 'option', 'cancel', '.not-sortable .hndle, :input, button' )
					// and then refresh the instance
					.sortable( 'refresh' );
			});
			/* ]]> */
			</script>
		<?php
	}

}

new AB_Admin_Meta_Boxes();
