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
	<?php if ( is_array( get_option( 'axiscomposer_custom_iconfonts' ) ) ) : ?>
	<!-- <div class="metabox-holder meta-search">
		<div class="postbox">
			<h3>
				<input class="search-icon" type="search" placeholder="Search" />
				<span class="search-count"></span>
			</h3>
		</div>
	</div> -->
	<?php self::get_iconfonts(); ?>
	<?php else: ?>
	<div class="error">
		<p><?php _e( 'No font icons uploaded. Upload some font icons to display here.', 'axiscomposer' ); ?></p>
	</div>
	<?php endif; ?>
</div>
