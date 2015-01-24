<?php
/**
 * Admin View: Page - Icon-Fonts Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div class="wrap">
	<h2>
		<?php _e( 'Icon Fonts Manager', 'amity' ); ?>
		<a href="#smile_upload_icon" class="add-new-h2 smile_upload_icon" data-target="iconfont_upload" data-title="Upload/Select Fontello Font Zip" data-type="application/octet-stream, application/zip" data-button="Insert Fonts Zip File" data-trigger="smile_insert_zip" data-class="media-frame ">
		<?php _e( 'Upload New Icons', 'amity' ); ?>
		</a> &nbsp;<span class="spinner"></span>
	</h2>
	<div id="msg"></div>
	<?php if ( is_array( $iconfonts ) ) : ?>
	<div class="metabox-holder meta-search">
		<div class="postbox">
			<h3>
				<?php wp_create_nonce ( 'axis-iconfonts' ); ?>
				<input class="search-icon" type="search" placeholder="Search" />
				<span class="search-count"></span>
			</h3>
		</div>
	</div>
	<?php self::get_iconfont_sets(); ?>
	<?php else: ?>
	<div class="error">
		<p><?php _e( 'No font icons uploaded. Upload some font icons to display here.', 'amity' ); ?></p>
	</div>
	<?php endif; ?>
</div>
