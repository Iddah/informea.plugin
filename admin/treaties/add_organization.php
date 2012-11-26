<?php
$countryOb = new imea_countries_page();
?>
<div class="wrap">
	<div id="breadcrumb">
		You are here:
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_treaties">Manage treaties</a>
		&raquo;
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_treaties&act=treaty_add_organization">Add new organization</a>
	</div>
	<div id="icon-tools" class="icon32"><br></div>
	<h2>Add new organization</h2>
	<p>
		Please enter the details below and press the <b>Add treaty</b> button
	<p>
	<?php if ($page_data->actioned) { ?>
	<div class="updated settings-error" id="setting-error-settings_updated">
		<?php if ($page_data->success) { ?>
			<p><strong>Organization was successfully added!</p>
		<?php } ?>
		<?php if (!$page_data->success) { ?>
			<p><strong>Error adding organization!</strong>
				<ul>
				<?php
					foreach ($page_data->errors as $inpname => $inp_err) {
					echo "<li>$inpname : $inp_err</li>";
				} ?>
				</ul>
			</p>
		<?php } ?>
	</div>
	<?php } ?>

	<form action="" method="post" enctype="multipart/form-data">
		<?php wp_nonce_field('informea-admin_treaty_add_organization'); ?>
		<input type="hidden" name="page" value="informea_treaty" />
		<input type="hidden" name="act" value="treaty_add_organization" />

		<table>
			<tr>
				<td><label for="short_title">Name *</label></td>
				<td>
					<input type="text" size="60" id="name" name="name"
						value="<?php if (!$page_data->success) echo $page_data->get_value('name');?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="description">Description</label>
				</td>
				<td>
					<textarea rows="5" cols="40" id="description" name="description"
						><?php if (!$page_data->success) echo $page_data->get_value('description');?></textarea>
				</td>
			</tr>
			<tr>
				<td><label for="address">Address</label></td>
				<td>
					<input type="text" size="60" id="address" name="address"
						value="<?php if (!$page_data->success) echo $page_data->get_value('address');?>" />
				</td>
			</tr>
			<tr>
				<td><label for="city">City</label></td>
				<td>
					<input type="text" size="60" id="city" name="city"
						value="<?php if (!$page_data->success) echo $page_data->get_value('city');?>" />
				</td>
			</tr>
			<tr>
				<td><label for="id_country">Country</label></td>
				<td>
					<select id="id_country" name="id_country">
						<option value="">-- Please select --</option>
						<?php
							foreach($countryOb->get_countries() as $c) {
								$selected = $c->id == get_request_int('id_country') ? ' selected="selected"' : '';
						?>
						<option value="<?php echo $c->id; ?>"<?php echo $selected; ?>><?php echo $c->name; ?></option>
						<?php } ?>
					</select>
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
				<td><label for="depository">Depository</label></td>
				<td>
					<input type="text" size="60" id="depository" name="depository"
						value="<?php if (!$page_data->success) echo $page_data->get_value('depository');?>" />
				</td>
			</tr>
		</table>
		<p>
		* - Required field(s)
		</p>
		<p class="submit">
			<input name="actioned" type="submit" class="button-primary" value="<?php esc_attr_e('Add organization'); ?>" />
		</p>
	</form>
</div>
