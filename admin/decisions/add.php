<?php
wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-widget');
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script('wp-ajax-response');

wp_register_style('jquery-ui-darkness', plugins_url('/informea/admin/css/ui-darkness/jquery-ui-1.7.3.custom.css'));
wp_enqueue_style('jquery-ui-darkness');

$id_treaty = get_request_int('id_treaty');
$id_organization = get_request_int('id_organization');
$id_meeting = get_request_int('id_meeting');
$treatyOb = new imea_treaties_page();
$eventsOb = new imea_events_page();
$decisionOb = new imea_decisions_page();
$languages = array('en' => 'English', 'fr' => 'French', 'ar' => 'Arabic', 'es' => 'Spanish', 'ru' => 'Russian', 'zh' => 'Chinese');
?>
<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#published').datepicker({dateFormat : 'yy-mm-dd', maxDate: "today"});
		jQuery('#updated').datepicker({dateFormat : 'yy-mm-dd', maxDate: "today"});
	});
</script>
<style type="text/css">
	#id_meeting { max-width: 400px; }
</style>
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
		Insert new decision into the database
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
				<div id="col-right">
					<div class="form-field">
						<div>
							<label for="updated">Updated</label>
							<input type="text" id="updated" name="updated"
								value="<?php echo get_request_value('updated', ''); ?>" style="width: 150px;"
								tabindex="10" />
							<p>(YYYY-MM-DD)</p>
						</div>
					</div>

					<div class="clear"></div>

					<div class="form-field">
						<label for="long_title">Long title</label>
						<input type="text" id="long_title" name="long_title"
							value="<?php echo get_request_value('long_title', ''); ?>"
							 tabindex="11" />
					</div>

					<div class="form-field">
						<label for="summary">Summary</label>
						<textarea id="summary" name="summary" rows="5" cols="20" tabindex="12"><?php echo get_request_value('summary', ''); ?></textarea>
					</div>

					<div class="form-field">
						<label for="body">Text</label>
						<textarea id="body" name="body" rows="15" cols="20" tabindex="13"><?php echo get_request_value('body', ''); ?></textarea>
					</div>

					<h3>Upload documents</h3>
					<input type="file" name="document[]" tabindex="14" />
					<select name="language[]" tabindex="15">
						<?php foreach($languages as $key => $label) { ?>
						<option value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
					<br />
					<input type="file" name="document[]" tabindex="16" />
					<select name="language[]" tabindex="17">
						<?php foreach($languages as $key => $label) { ?>
						<option value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
					<br />
					<input type="file" name="document[]" tabindex="18" />
					<select name="language[]" tabindex="19">
						<?php foreach($languages as $key => $label) { ?>
						<option value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
					<br />
					<input type="file" name="document[]" tabindex="20" />
					<select name="language[]" tabindex="21">
						<?php foreach($languages as $key => $label) { ?>
						<option value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
					<br />
					<input type="file" name="document[]" tabindex="22" />
					<select name="language[]" tabindex="23">
						<?php foreach($languages as $key => $label) { ?>
						<option value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
					<br />
					<input type="file" name="document[]" tabindex="24" />
					<select name="language[]" tabindex="25">
						<?php foreach($languages as $key => $label) { ?>
						<option value="<?php echo $key; ?>"><?php echo $label; ?></option>
						<?php } ?>
					</select>
				</div>

				<div id="col-left">
					<div class="form-field">
						<label for="id_organization">Select organization</label>
						<select id="id_organization" name="id_organization" tabindex="1">
							<option value="">-- Please select --</option>
							<?php
								foreach($treatyOb->get_organizations() as $org) {
									$selected = $org->id == $id_organization ? ' selected="selected"' : '';
							?>
							<option value="<?php echo $org->id; ?>"<?php echo $selected; ?>><?php echo $org->name; ?></option>
							<?php } ?>
						</select>
					</div>

					<div class="form-field">
						<label for="id_treaty">Select treaty</label>
						<select id="id_treaty" name="id_treaty" tabindex="2">
							<option value="">-- Please select --</option>
							<?php
								foreach($treatyOb->get_treaties() as $treaty) {
									$selected = $treaty->id == $id_treaty ? ' selected="selected"' : '';
							?>
							<option value="<?php echo $treaty->id; ?>"<?php echo $selected; ?>><?php echo $treaty->short_title; ?></option>
							<?php } ?>
						</select>
					</div>

					<div class="form-field">
						<label for="link">Link</label>
						<input type="text" id="link" name="link" value="<?php echo get_request_value('link', ''); ?>"  tabindex="3" />
						<p>Link to online version of the decision, on the convention's website</p>
					</div>

					<div class="form-field form-required">
						<label for="short_title">Short title *</label>
						<input type="text" id="short_title" name="short_title" value="<?php echo get_request_value('short_title', ''); ?>"  tabindex="4" />
					</div>

					<div class="form-field form-required">
						<label for="number">Number *</label>
						<input type="text" id="number" name="number" value="<?php echo get_request_value('number', ''); ?>" style="width: 200px;" tabindex="5" />
					</div>

					<div class="form-field form-required">
						<div>
							<label for="published">Publish date *</label>
							<input type="text" id="published" name="published" value="<?php echo get_request_value('published', ''); ?>" style="width: 150px;" tabindex="6" />
							<p>Date when decision was published by the Convention (YYYY-MM-DD)</p>
						</div>
					</div>

					<div class="form-field form-required">
						<label for="decision_type">Type *</label>
						<select id="decision_type" name="decision_type" tabindex="7">
							<option value="">-- Please select --</option>
							<?php
								foreach($decisionOb->get_allowed_type() as $key) {
									$selected = get_request_value('decision_type') == $key ? ' selected="selected"' : '';
							?>
								<option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo ucfirst($key); ?></option>
							<?php } ?>
						</select>
					</div>

					<div class="form-field">
						<label for="status">Status *</label>
						<select id="status" name="status" tabindex="8">
							<option value="">-- Please select --</option>
							<?php
								foreach($decisionOb->get_allowed_status() as $key) {
									$selected = get_request_value('status') == $key ? ' selected="selected"' : '';
							?>
								<option value="<?php echo $key; ?>"<?php echo $selected; ?>><?php echo ucfirst($key); ?></option>
							<?php } ?>
						</select>
					</div>

					<div class="form-field">
						<label for="id_meeting">Meeting</label>
						<select id="id_meeting" name="id_meeting" tabindex="9">
							<option value="">-- Please select --</option>
							<?php foreach($decisionOb->get_meetings_add_decision() as $label => $group) : ?>
								<optgroup label="<?php echo $label; ?>">
								<?php foreach($group as $event) : 
									$selected = $event->id == $id_meeting ? ' selected="selected"' : '';
								?>
									<option value="<?php echo $event->id; ?>"<?php echo $selected; ?>><?php echo $event->title; ?></option>
								<?php endforeach; ?>
								</optgroup>
							<?php endforeach; ?>
						</select>
						<p>(If meeting is missing, add it <a href="<?php bloginfo('url');?>/wp-admin/admin.php?page=informea_events&act=event_add_event">here</a>, then add decision)</p>
					</div>

				</div><!--/col-left -->
			</div><!--/col-container -->
			<p class="submit">
				<input type="submit" name="actioned" value="Insert decision" class="button button-primary" tabindex="26" />
			</p>
		</form>
	</div><!--/form-wrap -->

</div>
