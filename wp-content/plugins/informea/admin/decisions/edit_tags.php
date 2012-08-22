<?php
$id_decision = get_request_int('id_decision');
$id_treaty = get_request_int('id_treaty');
$treatiesOb = new imea_treaties_page();

$treaties = $treatiesOb->get_treaties();
$decisions = $page_data->get_decision_in_treaty_list($id_treaty);
$keywords = $page_data->get_decision_tags($id_decision);
// var_dump($keywords);
?>
<div class="wrap">
	<div id="breadcrumb">
		You are here:
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_decisions">Manage decisions</a>
		&raquo;
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_decisions&act=decision_edit_tags">Edit decision tags</a>
	</div>
	<div id="icon-tools" class="icon32"><br></div>
	<h2>Edit decision tags</h2>

	<p>
		From this page you can edit the tags associated with a decision.
		<?php if($id_decision && $id_treaty) { ?>
		<a href="<?php bloginfo('url');?>/treaties/<?php echo $id_treaty; ?>/decisions/?showall=true#decision-<?php echo $id_decision; ?>">Go to decision in front-end</a>
		<?php } ?>
	</p>
	<?php if ($page_data->actioned) { ?>
	<div class="updated settings-error" id="setting-error-settings_updated">
		<?php if ($page_data->success) { ?>
			<p>
				<strong>Successfully updated the tags for this decision</strong>
			</p>
		<?php } else {?>
			<p><?php var_dump($page_data->errors); ?></p>
		<?php } ?>
	</div>
	<?php } ?>


	<form action="" method="post" id="tagging_form">
		<?php wp_nonce_field('edit_decision_tags'); ?>
		<input type="hidden" name="page" value="informea_decisions" />
		<input type="hidden" name="act" value="decision_edit_tags" />
		<table>
			<tr>
				<td><label for="id_treaty">Treaty *</label></td>
				<td>
					<select id="id_treaty" name="id_treaty" onchange="document.getElementById('tagging_form').submit();">
						<option value="">-- Please select --</option>
						<?php
						foreach($page_data->get_treaties_w_decisions() as $row) {
							$selected = ($id_treaty == $row->id) ? ' selected="selected"' : '';
							echo "<option value='{$row->id}'$selected>{$row->short_title}</option>";
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="id_decision">Decision *</label></td>
				<td>
					<select id="id_decision" name="id_decision" onchange="document.getElementById('tagging_form').submit();">
						<option value="">-- Please select --</option>
						<?php
						foreach($page_data->get_decisions_for_treaty($id_treaty) as $row) {
							$label = subwords($row->number . ' - ' . $row->short_title, 30);
							$selected = ($id_decision == $row->id) ? ' selected="selected"' : '';
							echo "<option value='{$row->id}'$selected>{$label}</option>";
						}?>
					</select>
				</td>
			</tr>

			<?php if(!empty($id_decision)) { ?>
			<tr>
				<td><label for="keywords">Keywords</label></td>
				<td>
					<br />
					<em class="error">Use <strong>(Ctrl, Shift) + Click</strong> to select/deselect multiple item(s) and range of terms</em>
					<br />
					<select id="keywords" name="keywords[]" size="12" multiple="multiple" style="height: 25em;">
					<?php
					$terms = new Thesaurus(null);
					foreach($terms->get_voc_concept() as $row) {
						$checked = array_key_exists(intval($row->id), $keywords) ? ' selected="selected"' : '';
						echo "<option value='{$row->id}'$checked>{$row->term}</option>";
					}
					?>
					</select>
				</td>
			</tr>
			<?php } ?>
		</table>
		<?php if(!empty($id_decision)) { ?>
		<input type="submit" name="actioned" value="Save changes" class="button-primary" />
		<?php } ?>
	</form>

</div>
