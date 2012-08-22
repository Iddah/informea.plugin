<?php
$id_treaty = get_request_int('id_treaty');
$id_people = get_request_int('id_people', null);
$people = null;
$treaties = array();
if(!empty($id_people)) {
	$people = $page_data->get_nfp($id_people);
	$treaties = $page_data->get_contact_treaties($people);
}
?>
<style>
.red  { color: red; }
</style>
<div class="wrap">
	<div id="breadcrumb">
		You are here:
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_nfp">Manage focal points</a>
		&raquo;
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_nfp&act=edit_nfp">Edit focal point</a>
	</div>
	<div id="icon-tools" class="icon32"><br></div>
	<h2>Edit focal point</h2>

	<p>
		From this page you can edit the focal points
	</p>
	<?php if ($page_data->actioned) { ?>
	<div class="updated settings-error" id="setting-error-settings_updated">
		<?php if ($page_data->success) { ?>
			<p>
				<strong>Successfully updated the contact</strong>
			</p>
		<?php } else {?>
			<p><?php var_dump($page_data->errors); ?></p>
		<?php } ?>
	</div>
	<?php } ?>


	<form action="" method="get" id="sel_form">
		<?php wp_nonce_field('edit_nfp'); ?>
		<input type="hidden" name="page" value="informea_nfp" />
		<input type="hidden" name="act" value="edit_nfp" />
		Convention: <select id="id_treaty" name="id_treaty" onchange="document.getElementById('sel_form').submit();">
			<option value="">-- Please select --</option>
			<?php
			foreach($page_data->get_treaties_w_contacts($id_treaty) as $row) {
				$selected = ($id_treaty == $row->id) ? ' selected="selected"' : '';
				echo "<option value='{$row->id}'$selected>{$row->short_title}</option>";
			}
			?>
		</select>
	</form>
	<form action="" method="post" id="edit_form">
		<?php wp_nonce_field('edit_nfp'); ?>
		<br />
		Contact:
		<select id="id_people" name="id_people" onchange="document.getElementById('edit_form').submit();">
			<option value="">-- Please select --</option>
			<?php
			foreach($page_data->get_people_for_treaty($id_treaty) as $row) {
				$selected = ($id_people == $row->id) ? ' selected="selected"' : '';
				$name = $page_data->label_contact($row);
				echo "<option value='{$row->id}'$selected>{$name}</option>";
			}?>
		</select>
		<?php if(!empty($people)) { ?>
		<table>
			<tr>
				<td><label for="original_id">Original ID</label></td>
				<td><input type="text" id="original_id" name="" value="<?php echo $people->original_id; ?>" disabled="disabled" /></td>
			</tr>
			<tr>
				<td><label for="prefix">Prefix</label></td>
				<td><input type="text" id="prefix" name="prefix" value="<?php echo $people->prefix; ?>" /></td>
			</tr>
			<tr>
				<td><label for="first_name">First name</label></td>
				<td><input type="text" id="first_name" name="first_name" value="<?php echo $people->first_name; ?>" /></td>
			</tr>
			<tr>
				<td><label for="last_name">Last name</label></td>
				<td><input type="text" id="last_name" name="last_name" value="<?php echo $people->last_name; ?>" /></td>
			</tr>
			<tr>
				<td><label for="position">Position</label></td>
				<td><input type="text" id="position" name="position" size="40" value="<?php echo $people->position; ?>" /></td>
			</tr>
			<tr>
				<td><label for="institution">Institution</label></td>
				<td><input type="text" id="institution" name="institution" size="40" value="<?php echo esc_attr($people->institution); ?>" /></td>
			</tr>
			<tr>
				<td><label for="department">Department</label></td>
				<td><input type="text" id="department" name="department" size="40" value="<?php echo $people->department; ?>" /></td>
			</tr>
			<tr>
				<td><label for="address">Address</label></td>
				<td><textarea type="text" id="address" name="address" size="40" rows="5" cols="30"><?php echo $people->address; ?></textarea></td>
			</tr>
			<tr>
				<td><label for="email">E-mail</label></td>
				<td><input type="text" id="email" name="email" value="<?php echo $people->email; ?>" /></td>
			</tr>
			<tr>
				<td><label for="telephone">Telephone</label></td>
				<td><input type="text" id="telephone" name="telephone" value="<?php echo $people->telephone; ?>" /></td>
			</tr>
			<tr>
				<td><label for="fax">Fax</label></td>
				<td><input type="text" id="fax" name="fax" size="40" value="<?php echo $people->fax; ?>" /></td>
			</tr>
			<tr>
				<td><label for="is_primary">Primary NFP?</label></td>
				<td><input type="checkbox" id="is_primary" name="is_primary" <?php echo !empty($people->is_primary) ? 'checked="checked"' : '' ; ?> /></td>
			</tr>
			<tr>
				<td><label for="id_country">Country</label></td>
				<td>
					<select id="id_country" name="id_country">
						<option value="">-- Please select --</option>
						<?php
						foreach($page_data->get_countries() as $c) {
							$selected = ($people->id_country == $c->id) ? ' selected="selected"' : '';
							echo "<option value='{$c->id}'$selected>{$c->name}</option>";
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="treaty">Treaties</label></td>
				<td>
					<?php
						foreach($page_data->get_treaties() as $idx => $treaty) {
							$checked = in_array($treaty->id, $treaties) ? 'checked="checked"' : '';
							if($idx % 4 == 0) { echo '<br />'; }
					?>
						<input type="checkbox" id="treaty-<?php echo $treaty->id; ?>" name="treaty[]" value="<?php echo $treaty->id; ?>" <?php echo $checked; ?> />
						<label for="treaty-<?php echo $treaty->id; ?>"><?php echo $treaty->short_title; ?></label>
					<?php
						}
					?>
				</td>
			</tr>
		</table>
		<?php $c = count($treaties); if($c > 1) { ?>
		<strong class="red">Warning: This contact comes from <?php echo $c; ?> different sources and it might be duplicated! You can try to identify its duplicate and update it accordingly</strong>
		<?php } ?>
		<div class="clear"></div>
		<input type="submit" class="button-primary" name="actioned" style="float: left;" value="Save changes" />
		<input type="submit" class="button-primary" style="float: left; margin-left: 200px;" name="delete" value="Delete this contact" onclick="return confirm('Are you sure?');" />
		<?php } ?>
	</form>
</div>
