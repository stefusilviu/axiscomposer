<?php
/**
 * Admin View: Page - Iconfont
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="axiscomposer-iconfont" class="wrap">
	<h2>
		<?php _e( 'Iconfont Manager', 'axiscomposer' ); ?>
		<a href="#add-iconfont" id="btn-ok" class="add-new-h2 add-iconfont" data-choose="<?php _e( 'Choose a Font ZIP File', 'axiscomposer' ) ?>" data-update="<?php _e( 'Insert Font Zip File', 'axiscomposer' ) ?>" data-mime="application/zip, application/octet-stream" data-target="iconfont_upload" data-trigger="insert_iconfont_zip">
		<?php _e( 'Add New', 'axiscomposer' ); ?>
		</a> &nbsp;<span class="spinner"></span>
	</h2>
	<div id="msg"></div>
</div>
