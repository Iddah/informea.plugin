<div class="wrap">
	<div id="breadcrumb">
		You are here:
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea">InforMEA Configuration</a>
	</div>
	<div class="icon32" id="icon-tools"><br /></div>
	<h2><?php echo __("InforMEA Configuration", "imea_admin_vas_h2")?></h2>

	<strong>
	<em>
		When using JavaScript optimizer and CSS optimizer make sure your files (wp-content/themes/informea/scripts/script_min.js
		and wp-content/themes/informea/style_min.css) are up-to-date by running "./compress.sh" script from website root dir.
	</em>
	</strong>

	<form method="post" action="options.php" enctype="multipart/form-data">
		<?php settings_fields('informea_options'); ?>
		<?php do_settings_sections('informea'); ?>
		<p class="submit">
			<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		</p>
	</form>
</div>
