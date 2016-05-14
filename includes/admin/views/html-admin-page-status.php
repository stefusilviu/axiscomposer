<?php
/**
 * Admin View: Page - Status
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_tab = ! empty( $_REQUEST['tab'] ) ? sanitize_title( $_REQUEST['tab'] ) : 'status';
$tabs        = apply_filters( 'axiscomposer_admin_status_tabs', array(
	'status' => __( 'System Status', 'axiscomposer' ),
	'tools'  => __( 'Tools', 'axiscomposer' ),
	'logs'   => __( 'Logs', 'axiscomposer' )
) );

?>
<div class="wrap axiscomposer">
	<nav class="nav-tab-wrapper axis-nav-tab-wrapper">
		<?php
			foreach ( $tabs as $name => $label ) {
				echo '<a href="' . admin_url( 'admin.php?page=ac-status&tab=' . $name ) . '" class="nav-tab ';
				if ( $current_tab == $name ) echo 'nav-tab-active';
				echo '">' . $label . '</a>';
			}
		?>
	</nav>
	<h1 class="screen-reader-text"><?php echo esc_html( $tabs[ $current_tab ] ); ?></h1>
	<?php
		switch ( $current_tab ) {
			case 'tools' :
				AC_Admin_Status::status_tools();
			break;
			case 'logs' :
				AC_Admin_Status::status_logs();
			break;
			default :
				if ( array_key_exists( $current_tab, $tabs ) && has_action( 'axiscomposer_admin_status_content_' . $current_tab ) ) {
					do_action( 'axiscomposer_admin_status_content_' . $current_tab );
				} else {
					AC_Admin_Status::status_report();
				}
			break;
		}
	?>
</div>
