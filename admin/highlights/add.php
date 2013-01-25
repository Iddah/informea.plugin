<?php 

$req_cat_syndication = get_request_value('cat_syndication', array(), FALSE);
$req_cat_mea = get_request_value('cat_mea', array(), FALSE);
?>
<div class="wrap">
	<div id="breadcrumb">
		You are here:
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_highlights">Manage highlights</a>
		&raquo;
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_highlights&act=highlight_add_highlight">Add new</a>
	</div>
	<div id="icon-tools" class="icon32"><br></div>
	<h2>Add new highlight</h2>
	<?php if ($page_data->actioned) { ?>
	<div class="updated settings-error" id="setting-error-settings_updated">
		<?php if ($page_data->success) { ?>
			<strong>Highlight was successfully created!</strong>
		<?php } ?>
		<?php if (!$page_data->success) { ?>
			<strong>Error adding highlight!</strong>
			<ul>
			<?php foreach($page_data->errors as $inpname => $inp_err) : ?>
				<li><?php echo $inp_err; ?></li>
			<?php endforeach; ?>
			</ul>
		<?php } ?>
	</div>
	<?php } ?>

	<form action="" method="post">
		<?php wp_nonce_field('informea-admin_highlight_add_highlight'); ?>
		<input type="hidden" name="page" value="informea_highlights" />
		<input type="hidden" name="act" value="highlight_add_highlight" />

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="title">Title *</label>
				</th>
				<td>
					<input id="title" name="title" value="<?php if (!$page_data->success) echo $page_data->get_value('title');?>" size="60" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="link">Link *</label>
				</th>
				<td>
					<input id="link" name="link" value="<?php if (!$page_data->success) echo $page_data->get_value('link');?>"  size="80" />
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="cat_mea">MEA *</label>
				</th>
				<td>
					<select id="cat_mea" name="cat_mea[]" multiple="multiple" size="15" style="width: 200px;">
						<option value="">-- Please select --</option>
						<?php foreach($page_data->get_meas_subcategories() as $id => $name) :
							$sel = (!$page_data->success && in_array($id, $req_cat_mea)) ? ' selected="selected"' : '';
						?>
							<option value="<?php echo $id; ?>"<?php echo $sel; ?>><?php echo $name; ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description">
						Use CTRL+click to select multiple items
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<label for="cat_syndication">Topic *</label>
				</th>
				<td>
					<select id="cat_syndication" name="cat_syndication[]" multiple="multiple" size="10" style="width: 200px;">
						<option value="">-- Please select --</option>
						<?php foreach($page_data->get_syndication_subcategories() as $id => $name) :
							$sel = (!$page_data->success && in_array($id, $req_cat_syndication)) ? ' selected="selected"' : '';
						?>
							<option value="<?php echo $id; ?>"<?php echo $sel; ?>><?php echo $name; ?></option>
						<?php endforeach; ?>
					</select>
					<p class="description">
						Use CTRL+click to select multiple items
					</p>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input name="actioned" type="submit" class="button-primary" value="<?php esc_attr_e('Add highlight'); ?>" />
		</p>
	</form>
</div>
