<?php
$page_countries = new imea_countries_page();
$page_treaties = new imea_treaties_page();
$terms = array();
$treaties = $page_data->get_enabled_treaties();
$countries = $page_countries->get_countries();
?>
<link rel='stylesheet' href='<?php bloginfo('template_directory'); ?>/ui.css' type='text/css' media='screen' />
<link rel='stylesheet' id='thickbox-css'  href='<?php bloginfo('url'); ?>/wp-includes/js/thickbox/thickbox.css?ver=20090514' type='text/css' media='all' />
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
			$('#image_img').attr('src', imgurl);
			$('#image').val(imgurl);
			tb_remove();
		}
	});
</script>

<div class="wrap">
	<div id="breadcrumb">
		You are here:
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_events">Manage events</a>
		&raquo;
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_events&act=event_add_event">Add new event</a>
	</div>
	<div id="icon-tools" class="icon32"><br></div>
	<h2>Add new event</h2>

	<p class="red">
		<em>Please fill in as much details as you know about the event!</em>
	</p>
	<?php if ($page_data->actioned) { ?>
	<div class="updated settings-error" id="setting-error-settings_updated">
		<?php if ($page_data->success) { ?>
			<p><strong>Event was successfully created!</strong></p>
		<?php } ?>
		<?php if (!$page_data->success) { ?>
			<p><strong>Error adding event!</strong>
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
		<?php wp_nonce_field('informea-admin_event_add_event'); ?>
		<input type="hidden" name="page" value="informea_event" />
		<input type="hidden" name="act" value="event_add_event" />
		<table>
			<tr>
				<td><label for="id_treaty">Treaty *</label></td>
				<td>
					<select id="id_treaty" name="id_treaty" onchange="document.getElementById('id_event').value = '';document.getElementById('sel_form').submit();">
						<option value="">-- Please select --</option>
					<?php
						foreach($treaties as $row) {
							$checked = (!$page_data->success and get_request_int('id_treaty') == $row->id) ? ' selected="selected"' : '';
							echo "<option value=\"{$row->id}\"'$checked>{$row->short_title}</option>";
						}
					?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="event_url">URL</label></td>
				<td>
					<input type="text" size="60" id="event_url" name="event_url" value="<?php if (!$page_data->success) echo $page_data->get_value('event_url');?>" />
				</td>
			</tr>
			<tr>
				<td><label for="title">Title *</label></td>
				<td>
					<input type="text" size="60" id="title" name="title" value="<?php if (!$page_data->success) echo $page_data->get_value('title');?>" />
				</td>
			</tr>
			<tr>
				<td><label for="description">Description</label></td>
				<td>
					<textarea name="description" cols="60" rows="5" id="description" value=""><?php if (!$page_data->success) echo $page_data->get_value('description');?></textarea>
				</td>
			</tr>
			<tr>
				<td><label for="start">Start date *</label></td>
				<td>
					<input type="text" size="60" id="start" name="start" value="<?php if (!$page_data->success) echo $page_data->get_value('start');?>" />
				</td>
			</tr>
			<tr>
				<td><label for="end">End date</label></td>
				<td>
					<input type="text" size="60" id="end" name="end" value="<?php if (!$page_data->success) echo $page_data->get_value('end');?>" />
				</td>
			</tr>
			<tr>
				<td><label for="repetition">Repetition</label></td>
				<td>
					<select id="repetition" name="repetition" >
						<option value="">-- Please select --</option>
					<?php
						foreach($page_data->get_repetition_enum() as $key => $value) {
							$selected = (!$page_data->success and get_request_value('repetition') == $key) ? ' selected="selected"' : '';
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
							$selected = (!$page_data->success and get_request_value('kind') == $key) ? ' selected="selected"' : '';
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
							$selected = (!$page_data->success and get_request_value('type') == $key) ? ' selected="selected"' : '';
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
							$selected = (!$page_data->success and get_request_value('access') == $key) ? ' selected="selected"' : '';
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
							$selected = (!$page_data->success and get_request_value('status') == $key) ? ' selected="selected"' : '';
					?>
						<option value="<?php echo $key; ?>"<?php echo $selected;?>><?php echo $value;?></option>
					<?php } ?>
					</select>
				</td>
			 </tr>
			<tr>
				<td><label for="image">Image</label></td>
				<td>
					<img id="image_img" src="" />
					<input type="hidden" id="image" name="image" value="" />
					<input type="button" id="upload_image_button" class="button" value="Select" />
				</td>
			</tr>
			<tr>
				<td><label for="image_copyright">Image Copyright</label></td>
				<td>
					<input type="text" size="60" id="image_copyright" name="image_copyright" value="<?php if (!$page_data->success) echo $page_data->get_value('image_copyright');?>" />
				</td>
			</tr>
			<tr>
				<td><label for="location">Event Location</label></td>
				<td>
					<input type="text" size="60" id="location" name="location" value="<?php if (!$page_data->success) echo $page_data->get_value('location');?>" />
				</td>
			</tr>
			<tr>
				<td><label for="city">City</label></td>
				<td>
					<input type="text" size="60" id="city" name="city" value="<?php if (!$page_data->success) echo $page_data->get_value('city');?>" />
				</td>
			</tr>
			<tr>
				<td><label for="id_country">Country *</label></td>
				<td>
					<select id="id_country" name="id_country">
						<option value="">-- Please select --</option>
						<?php
							foreach($countries as $row) {
								$checked = (!$page_data->success and get_request_int('id_country') == $row->id) ? ' selected="selected"' : '';
								echo "<option value=\"{$row->id}\"$checked>{$row->name}</option>";
							}
						?>
					</select>
				</td>
			</tr>
		</table>
		<p> * - Required field(s) </p>
		<p class="submit">
			<input name="actioned" type="submit" class="button-primary" value="<?php esc_attr_e('Add event'); ?>" />
		</p>
	</form>
</div>
