<?php
/**
 * Admin View: Page - Status
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_tab = ! empty( $_REQUEST['tab'] ) ? sanitize_title( $_REQUEST['tab'] ) : 'status';
?>
<div class="wrap axisbuilder">
	<div class="icon32 icon32-axisbuilder-status" id="icon-axisbuilder"><br /></div><h2 class="nav-tab-wrapper axisbuilder-nav-tab-wrapper">
		<?php
			$tabs = array(
				'status' => __( 'System Status', 'axisbuilder' ),
				'tools'  => __( 'Tools', 'axisbuilder' )
			);
			foreach ( $tabs as $name => $label ) {
				echo '<a href="' . admin_url( 'admin.php?page=axisbuilder-status&tab=' . $name ) . '" class="nav-tab ';
				if ( $current_tab == $name ) echo 'nav-tab-active';
				echo '">' . $label . '</a>';
			}
		?>
	</h2><br/>
	<?php
		switch ( $current_tab ) {
			case "tools" :
				AB_Admin_Status::status_tools();
			break;
			default :
				AB_Admin_Status::status_report();
			break;
		}
	?>
</div>
