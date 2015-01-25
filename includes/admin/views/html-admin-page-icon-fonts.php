<?php
/**
 * Admin View: Page - Icon-Fonts Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="axisbuilder-iconfonts" class="wrap">
	<h2>
		<?php _e( 'Icon Fonts Manager', 'amity' ); ?>
		<a href="#add-iconfont" class="add-new-h2 add-iconfont" data-title="<?php _e( 'Insert Font', 'axisbuilder' ) ?>" data-button="<?php _e( 'Insert Fonts Zip File', 'axisbuilder' ) ?>" data-type="application/octet-stream, application/zip" data-target="iconfont_upload" data-trigger="smile_insert_zip">
		<?php _e( 'Add New', 'amity' ); ?>
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
