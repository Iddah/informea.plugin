<?php
wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-widget');
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script('wp-ajax-response');

wp_register_style('jquery-ui-darkness', plugins_url('/informea/admin/css/ui-darkness/jquery-ui-1.7.3.custom.css'));
wp_enqueue_style('jquery-ui-darkness');

$id_treaty = get_request_int('id_treaty');
$id_event = get_request_int('id_event');
$treatyOb = new imea_treaties_page();
$eventsOb = new imea_events_page();
$languages = array('en' => 'English', 'fr' => 'French', 'ar' => 'Arabic', 'es' => 'Spanish', 'ru' => 'Russian', 'zh' => 'Chinese');
?>
<script type="text/javascript">

	jQuery(document).ready(function() {

		<?php if(empty($id_treaty)) { ?>
		jQuery('#id_treaty').change(function() {
			jQuery('#add_decision').submit();
		});
		<?php } ?>

		jQuery('#published').datepicker({dateFormat : 'yy-mm-dd', maxDate: "today"});
		jQuery('#updated').datepicker({dateFormat : 'yy-mm-dd', maxDate: "today"});
	});

</script>
<div id="breadcrumb">
	You are here:
	<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_decisions">Manage decisions</a>
	&raquo;
	Add new decision
</div>
<div id="icon-tools" class="icon32"><br></div>
<h2>Add new decision</h2>
<div class="wrap nosubsub">
	<p>
		From this page you add new decisions into database. Fill in details below and add decision into the database
	</p>
	<?php
		include(dirname(__FILE__) . '/../operation.html.php');
		if($page_data->actioned && $page_data->success) {
			echo '<p><a href="' . get_bloginfo('url') . '/wp-admin/admin.php?page=informea_decisions&act=decision_add">Add new decision</a></p>';
		}
	?>
	<div class="form-wrap">
		<form id="add_decision" action="" method="post" enctype="multipart/form-data" class="validate">
			<?php wp_nonce_field('decision_add'); ?>
			<div id="col-container">
				<?php if(!empty($id_treaty)) { ?>
				<div id="col-right">

					<div class="form-field">
						<div style="float: left">
							<label for="published">Published *</label>
							<input type="text" id="published" name="published" value="<?php echo get_request_value('published', ''); ?>" style="width: 150px;" />
							<p>(YYYY-MM-DD)</p>
						</div>

						<div style="float: left; margin-left: 15px;">
							<label for="updated">Updated</label>
							<input type="text" id="updated" name="updated" value="<?php echo get_request_value('updated', ''); ?>" style="width: 150px;" />
							<p>(YYYY-MM-DD)</p>
						</div>
					</div>

					<div class="clear"></div>

					<div class="form-field">
						<label for="long_title">Long title</label>
						<input type="text" id="long_title" name="long_title" value="<?php echo get_request_value('long_title', ''); ?>" />
					</div>

					<div class="form-field">
						<label for="summary">Summary</label>
						<textarea id="summary" name="summary" rows="5" cols="20"><?php echo get_request_value('summary', ''); ?></textarea>
					</div>

					<div class="form-field">
						<label for="body">Text</label>
						<textarea id="body" name="body" rows="15" cols="20"><?php echo get_request_value('body', ''); ?></textarea>
					</div>

					<h3>Upload documents</h3>
					<input type="file" name="document[]" />
					<select name="language[]">
						<?php foreach($languages as $key => $label) { ?>
						<option value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
					<br />
					<input type="file" name="document[]" />
					<select name="language[]">
						<?php foreach($languages as $key => $label) { ?>
						<option value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
					<br />
					<input type="file" name="document[]" />
					<select name="language[]">
						<?php foreach($languages as $key => $label) { ?>
						<option value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
					<br />
					<input type="file" name="document[]" />
					<select name="language[]">
						<?php foreach($languages as $key => $label) { ?>
						<option value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
					<br />
					<input type="file" name="document[]" />
					<select name="language[]">
						<?php foreach($languages as $key => $label) { ?>
						<option value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
					<br />
					<input type="file" name="document[]" />
					<select name="language[]">
						<?php foreach($languages as $key => $label) { ?>
						<option value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
				</div>
				<?php } ?>
				<div id="col-left">
					<div class="form-field form-required">
						<label for="id_treaty">Select treaty *</label>
						<select id="id_treaty" name="id_treaty" value="">
							<option value="">-- Please select --</option>
							<?php
								foreach($treatyOb->get_treaties() as $treaty) {
									$selected = $treaty->id == $id_treaty ? ' selected="selected"' : '';
							?>
							<option value="<?php echo $treaty->id; ?>"<?php echo $selected; ?>><?php echo $treaty->short_title; ?></option>
							<?php } ?>
						</select>
					</div>

					<?php if(!empty($id_treaty)) { ?>
					<div class="form-field">
						<label for="link">Link</label>
						<input type="text" id="link" name="link" value="<?php echo get_request_value('link', ''); ?>" />
					</div>

					<div class="form-field form-required">
						<label for="short_title">Short title *</label>
						<input type="text" id="short_title" name="short_title" value="<?php echo get_request_value('short_title', ''); ?>" />
					</div>

					<div class="form-field form-required">
						<div style="float: left">
							<label for="number">Number *</label>
							<input type="text" id="number" name="number" value="<?php echo get_request_value('number', ''); ?>" style="width: 200px;" />
						</div>

						<div style="float: left">
							<label for="decision_type">Type *</label>
							<select id="decision_type" name="decision_type" value="">
								<option value="">-- Please select --</option>
								<?php
									foreach(array('decision', 'resolution', 'recommendation') as $key) {
										$selected = get_request_value('decision_type') == $key ? ' selected="selected"' : '';
								?>
									<option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo ucfirst($key); ?></option>
								<?php } ?>
							</select>
						</div>

						<div style="float: left">
							<label for="status">Status *</label>
							<select id="status" name="status">
								<option value="">-- Please select --</option>
								<?php
									foreach(array('draft', 'active', 'amended', 'retired', 'revised') as $key) {
										$selected = get_request_value('status') == $key ? ' selected="selected"' : '';
								?>
									<option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo ucfirst($key); ?></option>
								<?php } ?>
							</select>
						</div>
					</div>

					<div class="clear"></div>

					<div class="form-field">
						<label for="id_meeting">Meeting</label>
						<select id="id_meeting" name="id_meeting">
							<option value="">-- Please select --</option>
							<?php
								foreach($eventsOb->get_events($id_treaty, 'a.`start` DESC') as $event) {
									$selected = $event->id == $id_event ? ' selected="selected"' : '';
							?>
							<option value="<?php echo $event->id; ?>"<?php echo $selected; ?>><?php echo $event->title; ?></option>
							<?php } ?>
						</select>
						<p>(If no meetings are present, you can go and <a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=informea_events&act=event_add_event">add the meeting for the treaty, first</a>, then come back here)</p>
					</div>

					<div class="form-field">
						<label for="meeting_title">Meeting title</label>
						<input type="text" id="meeting_title" name="meeting_title" value="" />
					</div>

					<div class="form-field">
						<label for="meeting_url">Meeting url</label>
						<input type="text" id="meeting_url" name="meeting_url" value="" />
					</div>
					<?php } ?>
				</div><!--/col-left -->
			</div><!--/col-container -->
			<?php if(!empty($id_treaty)) { ?>
			<p class="submit">
				<input type="submit" name="actioned" value="Insert decision" class="button button-primary" />
			</p>
			<?php } ?>
		</form>
	</div><!--/form-wrap -->

</div>
