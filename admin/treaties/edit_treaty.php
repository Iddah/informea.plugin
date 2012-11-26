<?php
$id = get_request_value('id');
if ($id) {
	$treaty = $page_data->get_treaty_by_id($id);
} else {
	$treaty = NULL;
}
$thesaurus = new Thesaurus(null);
$terms = $thesaurus->get_voc_concept();

wp_enqueue_script('media-upload');
wp_enqueue_script('thickbox');
wp_enqueue_script('jquery');
wp_enqueue_style('thickbox');
?>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#upload_image_button').click(function() {
			tb_show('', 'media-upload.php?post_id=&amp;type=image&TB_iframe=true');
			return false;
		});
		window.send_to_editor = function(html) {
			var imgurl = jQuery('img',html).attr('src');
			jQuery('#logo_medium').val(imgurl);
			jQuery('#logo_medium_img').attr('src', imgurl);
			tb_remove();
		}
	});
</script>

<div class="wrap">
	<div id="breadcrumb">
		You are here:
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_treaties">Manage treaties</a>
		&raquo;
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_treaties&act=treaty_edit_treaty">Edit treaty</a>
	</div>
	<div id="icon-tools" class="icon32"><br></div>
	<h2>Edit treaty</h2>


	<?php if(!$treaty) { ?>
	<form action="" method="post">
		<?php wp_nonce_field('informea-admin_treaty_edit_treaty'); ?>
		<label for="id">Treaty *</label>
		<select id="id" name="id">
			<option value="">-- Please select --</option>
			<?php foreach($page_data->get_all_treaties_organizations() as $row) {
				$checked = ($id == $row->id) ? ' selected="selected"' : '';
				echo "<option value='{$row->id}'$checked>{$row->short_title}</option>";
			} ?>
		</select>
		<input name="select_treaty" type="submit" class="button-primary" value="<?php esc_attr_e('select'); ?>" />
	</form>
	<?php } else { ?>
		<?php if ($page_data->actioned) { ?>
		<div class="updated settings-error" id="setting-error-settings_updated">
			<?php if ($page_data->success) { ?>
				<p><strong>Treaty was successfully updated!</strong> See the front-page view <a href="<?php bloginfo('url'); ?>/treaties/<?php echo $treaty->odata_name; ?>">here</a>.</p>
			<?php } ?>
			<?php if (!$page_data->success) { ?>
				<p><strong>Error changing treaty!</strong>
					<ul>
					<?php foreach($page_data->errors as $inpname => $inp_err) {
						echo "<li>$inpname : $inp_err</li>";
					} ?>
					</ul>
				</p>
			<?php } ?>
		</div>
		<?php } ?>
		<form action="" method="post">
			<?php wp_nonce_field('informea-admin_treaty_edit_treaty'); ?>
			<input type="hidden" name="page" value="informea_treaty" />
			<input type="hidden" name="act" value="treaty_edit_treaty" />
			<table>
				<tr>
					<td><label for="id">Treaty *</label></td>
					<td>
					<select id="id" name="id">
						<option value="">-- Please select --</option>
						<?php foreach($page_data->get_all_treaties() as $row) {
							$checked = ($id == $row->id) ? ' selected="selected"' : '';
							echo "<option value='{$row->id}'$checked>{$row->short_title}</option>";
						} ?>
					</select>
					<input name="select_treaty" type="submit" class="button-primary" value="<?php esc_attr_e('select'); ?>" />
					</td>
				</tr>
				<tr>
					<td><label for="id_organization">Organization *</label></td>
					<td>
					<select id="id_organization" name="id_organization">
						<option value="">-- Please select --</option>
						<?php foreach($page_data->get_organizations() as $row) {
							$checked = ($treaty->id_organization == $row->id) ? ' selected="selected"' : '';
							echo "<option value='{$row->id}'$checked>{$row->name}</option>";
						} ?>
					</select>
					</td>
				</tr>
				<tr>
					<td><label for="primary">Primary</label></td>
					<td>
						<?php
							$id_primary_treaty = $page_data->get_primary_treaty($treaty->id_organization);
							if (!is_null($id_primary_treaty) and $id_primary_treaty != $id) {
								$disabled = ' disabled="disabled"';
								$checked = '';
							} else {
								$checked = $treaty->primary ? ' checked="checked"' : '';
								$disabled = '';
							}
						?>
						<input type="checkbox" id="primary" name="primary"
							title="Is primary treaty for the organization."<?php echo $disabled;?><?php echo $checked;?>/>
					</td>
				</tr>
				<tr>
					<td><label for="short_title">Short title *</label></td>
					<td><input type="text" size="60" id="short_title" name="short_title" value="<?php echo $treaty->short_title;?>" /></td>
				</tr>
				<tr>
					<td><label for="short_title_alternative">Alternative short title *</label></td>
					<td>
						<input type="text" size="60" id="short_title_alternative" name="short_title_alternative" value="<?php echo $treaty->short_title_alternative;?>" /> (How title appears on treaties page)
					</td>
				</tr>
				<tr>
					<td><label for="long_title">Long title</label></td>
					<td><input type="text" size="60" id="long_title" name="long_title" value="<?php echo $treaty->long_title;?>" /></td>
				</tr>
				<tr>
					<td><label for="year">Year</label></td>
					<td><input type="text" size="5" id="year" name="year" value="<?php echo ($treaty->year > 0) ? $treaty->year : '';?>" /></td>
				</tr>
				<tr>
					<td><label for="abstract">Abstract</label></td>
					<td><textarea rows="5" cols="40" id="abstract" name="abstract"><?php echo $treaty->abstract;?></textarea></td>
				</tr>
				<tr>
					<td><label for="url">URL</label></td>
					<td><input type="text" size="60" id="url" name="url" value="<?php echo $treaty->url;?>" /></td>
				</tr>
				<tr>
					<td><label for="region">Region *</label></td>
					<td>
						<input type="region" name="region" value="<?php echo $treaty->region;?>" />
					</td>
				</tr>
				<tr>
					<td><label for="logo_medium">Logo *</label></td>
					<td>
						<img id="logo_medium_img" src="<?php echo $treaty->logo_medium; ?>" />
						<input type="hidden" id="logo_medium" name="logo_medium" value="<?php echo $treaty->logo_medium; ?>" />
						<input type="button" id="upload_image_button" class="button" value="Select" />
						Size must be 40 x 55 pixels (width x height).
					</td>
				</tr>
				<tr>
					<td><label for="number_of_parties">Number of parties</label></td>
					<td><input type="text" size="5" id="number_of_parties" name="number_of_parties" value="<?php echo ($treaty->number_of_parties > 0) ? $treaty->number_of_parties : '';?>" /></td>
				</tr>
				<tr>
					<td><label for="theme">Primary theme</label></td>
					<td><input type="text" size="60" id="theme" name="theme" value="<?php echo $treaty->theme;?>" /></td>
				</tr>
				<tr>
					<td><label for="theme_secondary">Secondary theme</label></td>
					<td><input type="text" size="60" id="theme_secondary" name="theme_secondary" value="<?php echo $treaty->theme_secondary;?>" /></td>
				</tr>
				<tr>
					<td><label for="enabled">Enabled</label></td>
					<td>
						<input type="checkbox" id="enabled" name="enabled" <?php echo $treaty->enabled ? 'checked="checked"' : ''; ?> value="1" />
						(If checked, appears everywhere on portal. Disable with caution!)
					</td>
				</tr>
				<tr>
					<td><label for="order">Order</label></td>
					<td>
						<input type="text" size="5" id="order" name="order" value="<?php echo $treaty->order;?>" />
						(number used to order the items on 'treaties' page. Make it higher to move the treaty to end of the list)
					</td>
				</tr>
				<tr>
					<td><label for="odata_name">OData identification</label></td>
					<td>
						<input type="text" size="20" id="odata_name" name="odata_name" value="<?php echo $treaty->odata_name;?>" />
						(OData identification. Do not modify unless you are sure what you are doing. No spaces and maximum 32 characters please!)
					</td>
				</tr>
				<tr>
					<td><label for="keywords">Keywords</label></td>
					<td>
						<br />
						<em class="error">Use <strong>(Ctrl, Shift) + Click</strong> to select/deselect multiple item(s) and range of terms</em>
						<br />
						<select id="keywords" name="keywords[]" size="12" multiple="multiple"
							style="height: 25em;">
						<?php
						$keywords = $thesaurus->get_keywords_for_treaty($id);
						foreach($thesaurus->get_voc_concept() as $row) {
							$checked = (is_array($keywords) and in_array($row->id, $keywords)) ? ' selected="selected"' : '';
							echo "<option value='{$row->id}'$checked>{$row->term}</option>";
						}
						?>
						</select>
					</td>
				</tr>
			</table>
			<p>
			* - Required field(s)
			</p>
			<input name="actioned" type="submit" class="button-primary" value="<?php esc_attr_e('Submit changes'); ?>" />
		</form>
	<?php } ?>
</div>
