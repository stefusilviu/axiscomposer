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

		// add_action( 'admin_print_footer_scripts', array( $this, 'metabox_not_sortable_script' ), 99 );
		add_action( 'admin_head', array( $this, 'metabox_not_sortable_style' ) );
	}

	/**
	 * Add WC Meta boxes
	 */
	public function add_meta_boxes() {

		$screens = get_builder_core_supported_screens();

		foreach ( $screens as $screen ) {
			add_meta_box( 'axisbuilder-builder', __( 'Axis Builder', 'axisbuilder' ), array( $this, 'create_meta_box' ), $screen, 'normal', 'high' );
			add_meta_box( 'axisbuilder-layouts', __( 'Layout Settings', 'axisbuilder' ), array( $this, 'create_meta_box' ), $screen, 'side', 'default' );

			// Filters for classes and columns
			add_filter( 'postbox_classes_' . $screen . '_axisbuilder-builder', array( $this, 'metabox_not_sortable' ) );
			add_filter( 'manage_' . $screen . '_columns', array($this, 'manage_builder_columns' ), 100 );
		}
	}

	public function create_meta_box() {
		echo "string";
	}

	/**
	 * Register Columns in screen options toggle
	 *
	 * @param  array $columns Menu item columns
	 * @return array
	 */
	public function manage_builder_columns( $columns ) {

		unset( $columns['comments'] );

		return $columns;

		// var_dump($columns);
	}

	public function metabox_not_sortable( $classes ) {
		if( ! in_array( 'not-sortable', $classes ) ) {
			$classes[] = 'not-sortable';
		}

		return $classes;
	}

	function metabox_not_sortable_script() {
		?>
			<script type="text/javascript">
			/* <![CDATA[ */
			jQuery( function( $ ) {
				$( ".meta-box-sortables" )
					// define the cancel option of sortable to ignore sortable element
					// for boxes with '.not-sortable' css class
					.sortable( 'option', 'cancel', '.not-sortable .hndle, :input, button' )
					// and then refresh the instance
					.sortable( 'refresh' );

			});
			/* ]]> */
			</script>
		<?php
	}

	function metabox_not_sortable_style() {
		echo '<style type="text/css">
		.postbox.not-sortable h3.hndle { cursor: default !important }
		</style>';
	}

}

new AB_Admin_Meta_Boxes();
