<?php
$id_treaty = get_request_value('id_treaty');
$id_event = get_request_value('id_event');
if ($id_event) {
	$event = $page_data->get_event($id_event);
} else {
	$event = NULL;
}
//$terms = $page_data->get_event_concept();
?>
<link rel='stylesheet' href='<?php bloginfo('template_directory'); ?>/ui.css' type='text/css' media='screen' />
<script src="<?php bloginfo('template_directory'); ?>/scripts/jquery-min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/scripts/ui.js"></script>
<script type="text/javascript">
	$().ready(function() {
		$('#start').datepicker({'dateFormat' : 'yy-mm-dd'});
		$('#end').datepicker({'dateFormat' : 'yy-mm-dd'});

		$('#upload_image_button').click(function() {
			tb_show('', 'media-upload.php?post_id=&amp;type=image&TB_iframe=true');
			return false;
		});

		window.send_to_editor = function(html) {
			var imgurl = jQuery('img',html).attr('src');
			$('#image').val(imgurl);
			tb_remove();
		}
	});
</script>
<div class="wrap">
	<div id="breadcrumb">
		You are here:
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_events">Manage Events</a>
		&raquo;
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_events&act=event_edit_event">Edit Event</a>
	</div>
	<div id="icon-tools" class="icon32"><br></div>
	<h2>Edit event</h2>

	Select treaty, then event from the drop-down lists and edit the selected event.

	<?php if ($page_data->actioned) { ?>
	<div class="updated settings-error" id="setting-error-settings_updated">
		<?php if ($page_data->success) { ?>
			<p><strong>Event was successfully updated!</strong></p>
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
	<form action="" method="post" id="sel_form">
		<?php wp_nonce_field('informea-admin_event_edit_event'); ?>
		<input type="hidden" name="page" value="informea_event" />
		<input type="hidden" name="act" value="event_edit_event" />
		<label for="id_treaty">Treaty *</label>
		<select id="id_treaty" name="id_treaty" onchange="document.getElementById('id_event').value = '';document.getElementById('sel_form').submit();">
			<option value="">-- Please select --</option>
			<?php foreach($page_data->get_treaties_with_events() as $row) {
				$checked = ($id_treaty == $row->id) ? ' selected="selected"' : '';
				echo "<option value='{$row->id}'$checked>{$row->short_title}</option>";
			}?>
		</select>
		<br />
		<label for="id_event">Event *</label>
		<select id="id_event" name="id_event" onchange="document.getElementById('sel_form').submit();">
			<option value="">-- Please select --</option>
			<?php foreach($page_data->get_events($id_treaty) as $row) {
				$checked = ($id_event == $row->id) ? ' selected="selected"' : '';
				echo "<option value='{$row->id}'$checked>{$row->title}</option>";
			} ?>
		</select>
	<?php if(!empty($event)) { ?>
			<table>
				<tr>
					<td><label for="event_url">URL</label></td>
					<td><input type="text" size="60" id="event_url" name="event_url" value="<?php echo $event->event_url;?>" /></td>
				</tr>
				<tr>
					<td><label for="title">Title *</label></td>
					<td><textarea name="title" cols="60" id="title"><?php echo $event->title;?></textarea></td>
				</tr>
				<tr>
					<td><label for="description">Description</label></td>
					<td>
						<input type="text" size="60" id="description" name="description" value="<?php echo $event->description;?>" />
					</td>
				</tr>
				<tr>
					<td><label for="long_title">Start date *</label></td>
					<td><input type="text" size="60" id="start" name="start" value="<?php echo $event->start;?>" /></td>
				</tr>
				<tr>
					<td><label for="year">End date</label></td>
					<td><input type="text" size="60" id="end" name="end" value="<?php echo $event->end;?>" /></td>
				</tr>
				<tr>
					<td><label for="repetition">Repetition</label></td>
					<td>
						<select id="repetition" name="repetition" >
							<option value="">-- Please select --</option>
						<?php
							foreach($page_data->get_repetition_enum() as $key => $value) {
								$selected = ($event->repetition == $key) ? ' selected="selected"' : '';
						?>
							<option value="<?php echo $key; ?>"<?php echo $selected;?>><?php echo $value;?></option>
						<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="kind">Kind</label></td>
					<td>
						<select id="kind" name="kind">
							<option value="">-- Please select --</option>
						<?php
							foreach($page_data->get_kind_enum() as $key => $value) {
								$selected = ($event->kind == $key) ? ' selected="selected"' : '';
						?>
							<option value="<?php echo $key; ?>"<?php echo $selected;?>><?php echo $value;?></option>
						<?php } ?>
						</select>
						</td>
					</tr>
					<tr>
				</tr>
				<tr>
					<td><label for="type">Type</label></td>
					<td>
						<select id="type" name="type">
							<option value="">-- Please select --</option>
						<?php
							foreach($page_data->get_type_enum() as $key => $value) {
								$selected = ($event->type == $key) ? ' selected="selected"' : '';
						?>
							<option value="<?php echo $key; ?>"<?php echo $selected;?>><?php echo $value;?></option>
						<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="access">Access</label></td>
					<td>
						<select id="access" name="access">
							<option value="">-- Please select --</option>
						<?php
							foreach($page_data->get_access_enum() as $key => $value) {
								$selected = ($event->access == $key) ? ' selected="selected"' : '';
						?>
							<option value="<?php echo $key; ?>"<?php echo $selected;?>><?php echo $value;?></option>
						<?php } ?>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="status">Status</label></td>
					<td>
						<select id="status" name="status">
							<option value="">-- Please select --</option>
						<?php
							foreach($page_data->get_status_enum() as $key => $value) {
								$selected = ($event->status == $key) ? ' selected="selected"' : '';
						?>
							<option value="<?php echo $key; ?>"<?php echo $selected;?>><?php echo $value;?></option>
						<?php } ?>
						</select>
					</td>
				 </tr>
				<tr>
					<td><label for="image">Image</label></td>
					<td>
						<input type="text" id="image" name="image" value="<?php echo $event->image; ?>" />
						<input type="button" id="upload_image_button" class="button" value="Select" />
					</td>
				</tr>
				<tr>
					<td><label for="image_copyright">Image Copyright</label></td>
					<td>
						<input type="text" size="60" id="image_copyright" name="image_copyright" value="<?php echo $event->image_copyright; ?>" />
					</td>
				</tr>
				<tr>
					<td><label for="location">Event Location</label></td>
					<td><input type="text" size="40" id="location" name="location" value="<?php echo $event->location;?>" /></td>
				</tr>
				<tr>
					<td><label for="city">City</label></td>
					<td><input type="text" size="40" id="city" name="city" value="<?php echo $event->city;?>" /></td>
				</tr>

				<tr>
					<td><label for="id_country">Country</label></td>
					<td><select id="id_country" name="id_country" >
						<option value="">-- Please select --</option>
						<?php foreach($page_data->get_countries() as $row) {
							$checked = ($event->id_country == $row->id) ? ' selected="selected"' : '';
							echo "<option value='{$row->id}'$checked>{$row->name}</option>";
						} ?>
					</select>
					</td>
				</tr>
			</table>
			<p>
			* - Required field(s)
			</p>
			<input name="actioned" type="submit" class="button-primary" value="<?php esc_attr_e('Save changes'); ?>" style="float: left;"  />
			<?php if($page_data->can_delete($event)) { ?>
			<input name="delete" type="submit" class="button-primary" value="<?php esc_attr_e('Delete this event'); ?>" style="float: left; margin-left: 200px;"  onclick="return confirm('Are you sure?');" />
			<?php } ?>
		</form>
	<?php } ?>
</div>
