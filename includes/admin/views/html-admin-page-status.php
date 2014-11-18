<?php
/**
 * Admin View: Page - Status
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="wrap axisbuilder">
	<h2 class="nav-tab-wrapper axis-nav-tab-wrapper">
		<?php
			$tabs = array(
				'status' => __( 'System Status', 'axisbuilder' ),
				'tools'  => __( 'Tools', 'axisbuilder' ),
				'logs'   => __( 'Logs', 'axisbuilder' ),
			);
			foreach ( $tabs as $name => $label ) {
				echo '<a href="' . admin_url( 'admin.php?page=ab-status&tab=' . $name ) . '" class="nav-tab ';
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
			case "logs" :
				AB_Admin_Status::status_logs();
			break;
			default :
				AB_Admin_Status::status_report();
			break;
		}
	?>
</div>
