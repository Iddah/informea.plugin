<?php
wp_enqueue_script('media-upload');
wp_enqueue_script('thickbox');
wp_enqueue_script('jquery');
wp_enqueue_style('thickbox');

$id_organization = get_request_value('id_organization');
$thesaurus = new Thesaurus();
$terms = $thesaurus->get_voc_concept();
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
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_treaties&act=treaty_add_treaty">Add new treaty</a>
	</div>
	<div id="icon-tools" class="icon32"><br></div>
	<h2>Add new treaty</h2>

	<?php if (!$id_organization) { ?>
		<p>Select the organization that owns the treaty:</p>
	<?php } else { ?>
	<p>
		Please enter the details below and press the
		<b><?php esc_attr_e('Add treaty'); ?></b> button.
	</p>
	<p>
		If the organization already has a primary treaty, the primary field is disabled.
	</p>
	<?php } ?>

	<?php if ($page_data->actioned) { ?>
	<div class="updated settings-error" id="setting-error-settings_updated">
		<?php if ($page_data->success) { ?>
			<p><strong>Treaty was successfully created! See the treaties page <a href="<?php bloginfo('url'); ?>/treaties">here</a>.</p>
		<?php } ?>
		<?php if (!$page_data->success) { ?>
			<p><strong>Error adding treaty!</strong>
				<ul>
				<?php foreach($page_data->errors as $inpname => $inp_err) {
					echo "<li>$inpname : $inp_err</li>";
				} ?>
				</ul>
			</p>
		<?php } ?>
	</div>
	<?php } ?>

	<form action="" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field('informea-admin_treaty_add_treaty'); ?>
		<input type="hidden" name="page" value="informea_treaty" />
		<input type="hidden" name="act" value="treaty_add_treaty" />

		<table>
			<tr>
				<td><label for="id_organization">Organization *</label></td>
				<td>
				<select id="id_organization" name="id_organization">
					<option value="">-- Please select --</option>
					<?php foreach($page_data->get_organizations() as $row) {
						$checked = ($id_organization == $row->id) ? ' selected="selected"' : '';
						echo "<option value='{$row->id}'$checked>{$row->name}</option>";
					} ?>
				</select>
				<input name="select" type="submit" class="button-primary" value="<?php esc_attr_e('select'); ?>" />
				</td>
			</tr>

			<?php if ($id_organization) { ?>
			<tr>
				<td><label for="primary">Primary</label></td>
				<td>
					<?php
						$checked = $page_data->get_value('primary') ? ' checked="checked"' : '';
						$disabled = $page_data->get_primary_treaty($id_organization) ? ' disabled="disabled"': '';
					?>
					<input type="checkbox" id="primary" name="primary"
						title="Is primary treaty for the organization."<?php echo $disabled;?><?php if (!$page_data->success) echo $checked;?>/>
				</td>
			</tr>
			<tr>
				<td><label for="short_title">Short title *</label></td>
				<td>
					<input type="text" size="60" id="short_title" name="short_title"
						value="<?php if (!$page_data->success) echo $page_data->get_value('short_title');?>" />
				</td>
			</tr>
			<tr>
				<td><label for="short_title_alternative">Alternative short title *</label></td>
				<td>
					<input type="text" size="60" id="short_title_alternative" name="short_title_alternative"
						value="<?php if (!$page_data->success) echo $page_data->get_value('short_title_alternative');?>" /> (How title appears on treaties page)
				</td>
			</tr>
			<tr>
				<td><label for="long_title">Long title</label></td>
				<td>
					<input type="text" size="60" id="long_title" name="long_title"
						value="<?php if (!$page_data->success) echo $page_data->get_value('long_title');?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="year">Year</label>
				</td>
				<td>
					<input type="text" size="5" id="year" name="year"
						value="<?php if (!$page_data->success) echo $page_data->get_value('year');?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="abstract">Abstract</label>
				</td>
				<td>
					<textarea rows="5" cols="40" id="abstract" name="abstract"
						><?php if (!$page_data->success) echo $page_data->get_value('abstract');?></textarea>
				</td>
			</tr>
			<tr>
				<td><label for="url">URL</label></td>
				<td>
					<input type="text" size="60" id="url" name="url"
						value="<?php if (!$page_data->success) echo $page_data->get_value('url');?>" />
				</td>
			</tr>
			<tr>
				<td><label for="region">Region *</label></td>
				<td>
					<input type="region" name="region" value="<?php if (!$page_data->success) echo $page_data->get_value('region');?>" />
				</td>
			</tr>
			<tr>
				<td><label for="logo_medium">Logo *</label></td>
				<td>
					<img id="logo_medium_img" src="" />
					<input type="hidden" id="logo_medium" name="logo_medium" value="<?php if (!$page_data->success) echo $page_data->get_value('logo_medium');?>" />
					<input type="button" id="upload_image_button" class="button" value="Select" />
					Size must be 40 x 55 pixels (width x height).
				</td>
			</tr>
			<tr>
				<td><label for="number_of_parties">Number of parties</label></td>
				<td>
					<input type="text" size="5" id="number_of_parties" name="number_of_parties"
						value="<?php if (!$page_data->success) echo $page_data->get_value('number_of_parties');?>" />
				</td>
			</tr>
			<tr>
				<td><label for="theme">Primary theme</label></td>
				<td>
					<input type="text" size="20" id="theme" name="theme"
						value="<?php if (!$page_data->success) echo $page_data->get_value('theme');?>" />
				</td>
			</tr>
			<tr>
				<td><label for="theme_secondary">Secondary theme</label></td>
				<td>
					<input type="text" size="20" id="theme_secondary" name="theme_secondary"
						value="<?php if (!$page_data->success) echo $page_data->get_value('theme_secondary');?>" />
				</td>
			</tr>
			<tr>
				<td><label for="odata_name">OData ID *</label></td>
				<td>
					<input type="text" id="odata_id" name="odata_name" size="20"
						value="<?php if (!$page_data->success) echo $page_data->get_value('odata_name');?>" />
					Unique identification for OData harvesting (put here short string identifying the treaty. No spaces, max. 32 characters, please. examples: barc_prevention, prevention_emerg, cbd, ozone etc.)
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
					$keywords = $page_data->get_value('keywords');
					foreach($terms as $row) {
						$checked = (!$page_data->success and $keywords !== NULL and in_array($row->id, $keywords)) ? ' selected="selected"' : '';
						echo "<option value='{$row->id}'$checked>{$row->term}</option>";
					}
					?>
					</select>
				</td>
			</tr>
			<?php } ?>

		</table>
		<p>
		* - Required field(s)
		</p>

		<?php if ($id_organization) { ?>
		<p class="submit">
			<input name="actioned" type="submit" class="button-primary" value="<?php esc_attr_e('Add treaty'); ?>" />
		</p>
		<?php } ?>
	</form>
</div>
