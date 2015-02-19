<?php
/**
 * Admin View: Page - Iconfonts
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="axisbuilder-iconfonts" class="wrap">
	<h2>
		<?php _e( 'Icon Fonts Manager', 'axisbuilder' ); ?>
		<a href="#add-iconfont" class="add-new-h2 add-iconfont" data-title="<?php _e( 'Choose a Font ZIP File', 'axisbuilder' ) ?>" data-button="<?php _e( 'Insert Font Zip File', 'axisbuilder' ) ?>" data-type="application/zip, application/octet-stream" data-target="iconfont_upload" data-trigger="insert_iconfont_zip">
		<?php _e( 'Add New', 'axisbuilder' ); ?>
		</a> &nbsp;<span class="spinner"></span>
	</h2>
	<div id="msg"></div>
	<?php if ( is_array( get_option( 'axisbuilder_custom_iconfonts' ) ) ) : ?>
	<div class="metabox-holder meta-search">
		<div class="postbox">
			<h3>
				<input class="search-icon" type="search" placeholder="Search" />
				<span class="search-count"></span>
			</h3>
		</div>
	</div>
	<?php self::get_iconfonts(); ?>
	<?php else: ?>
	<div class="error">
		<p><?php _e( 'No font icons uploaded. Upload some font icons to display here.', 'axisbuilder' ); ?></p>
	</div>
	<?php endif; ?>
</div>
