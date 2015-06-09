<?php
/**
 * Admin View: Page - Status
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_tab = ! empty( $_REQUEST['tab'] ) ? sanitize_title( $_REQUEST['tab'] ) : 'status';
?>
<div class="wrap axiscomposer">
	<div class="icon32 icon32-axiscomposer-status" id="icon-axiscomposer"><br /></div><h2 class="nav-tab-wrapper axis-nav-tab-wrapper">
		<?php
			$tabs = array(
				'status' => __( 'System Status', 'axiscomposer' ),
				'tools'  => __( 'Tools', 'axiscomposer' )
			);
			foreach ( $tabs as $name => $label ) {
				echo '<a href="' . admin_url( 'admin.php?page=ac-status&tab=' . $name ) . '" class="nav-tab ';
				if ( $current_tab == $name ) echo 'nav-tab-active';
				echo '">' . $label . '</a>';
			}
		?>
	</h2>
	<?php
		switch ( $current_tab ) {
			case "tools" :
				AC_Admin_Status::status_tools();
			break;
			default :
				AC_Admin_Status::status_report();
			break;
		}
	?>
</div>
