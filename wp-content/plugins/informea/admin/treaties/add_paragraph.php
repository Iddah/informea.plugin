<?php
$id_treaty = get_request_value('id_treaty');
$id_treaty_article = get_request_value('id_treaty_article');
$order = get_request_value('order');
if ($id_treaty_article !== NULL and $order == NULL or $page_data->get_value('select') or $page_data->get_value('submit')) {
	$order = $page_data->get_next_treaty_article_paragraph_order($id_treaty_article);
}
include (dirname(__FILE__) . '/../tinymce.inc.php');
?>
<div class="wrap">
	<div id="breadcrumb">
		You are here:
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_treaties">Manage treaties</a>
		&raquo;
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_treaties&act=treaty_add_article_paragraph">Add new paragraph to an article</a>
	</div>
	<div id="icon-tools" class="icon32"><br></div>
	<h2>Add new paragraph to an article</h2>

	<?php if(!$id_treaty_article) { ?>
		<p>Select the article to which you want to add a new paragraph</p>
	<?php } else {
		$id_treaty = $page_data->get_treaty_id_from_article_id($id_treaty_article);
		?>
		<p>
			View the selected <a href="<?php echo $page_data->get_article_url_in_treaty($id_treaty, $id_treaty_article);?>">article</a>.
		</p>
		<p>
			Add a new paragraph to the article.
			Please enter the details below and press the
			<b><?php esc_attr_e('Add paragraph'); ?></b> button.
		</p>
		<p>
			Paragraphs are added to selected articles in the order of submission.
			Please insert them in the order they appear in the article.
		</p>
	<?php } ?>

	<?php if ($page_data->actioned) { ?>
	<div class="updated settings-error" id="setting-error-settings_updated">
		<?php if ($page_data->success) { ?>
			<p><strong>The paragraph was created and added to the article!</strong></p>
		<?php } ?>
		<?php if (!$page_data->success) { ?>
			<p><strong>Error adding article paragraph!</strong>
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
		<?php wp_nonce_field('informea-admin_treaty_add_article_paragraph'); ?>
		<input type="hidden" name="page" value="informea_treaty" />
		<input type="hidden" name="act" value="treaty_add_article_paragraph" />

		<table>
			<tr>
				<td><label for="id_treaty_article">Article *</label></td>
				<td>
					<select id="id_treaty_article" name="id_treaty_article">
						<option value="">-- Please select --</option>
						<?php foreach($page_data->get_treaty_article_in_treaty($id_treaty) as $row) {
							$checked = ($id_treaty_article == $row->id) ? ' selected="selected"' : '';
							echo "<option value='{$row->id}'$checked>{$row->treaty_title} - {$row->order}.{$row->title}</option>";
						} ?>
					</select>
					<input name="select" type="submit" class="button-primary" value="<?php esc_attr_e('select'); ?>" />
				</td>
			</tr>

			<?php if($id_treaty_article) {?>
			<tr>
				<td><label for="order">Internal order (readonly)*</label></td>
				<td>
					<input type="text" size="3" id="order" name="order" readonly="readonly"
						value="<?php echo $order;?>" />
				</td>
			</tr>
			<tr>
				<td><label for="official_order">Order</label></td>
				<td>
					<input type="text" size="10" id="official_order" name="official_order"
						value="<?php echo $page_data->get_value('official_order');?>" />
				</td>
			</tr>
			<tr>
				<td><label for="indent">Indent *</label></td>
				<td>
					<select id="indent" name="indent">
						<?php
						foreach(array(1, 2, 3, 4) as $i) {
							$checked = (!$page_data->success and intval($page_data->get_value('indent')) == $i) ? ' selected="selected"' : '';
							echo "<option value='{$i}'$checked>{$i}</option>";
						}
						?>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<label for="content">Content *</label>
				</td>
				<td>
					<textarea rows="5" cols="40" id="content" name="content"
						><?php if (!$page_data->success) echo $page_data->get_value('content');?></textarea>
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
					foreach($page_data->get_voc_concept() as $row) {
						$checked = (!$page_data->success and is_array($keywords) and in_array($row->id, $keywords)) ? ' selected="selected"' : '';
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

		<?php if($id_treaty_article) {?>
		<p class="submit">
			<input name="actioned" type="submit" class="button-primary" value="<?php esc_attr_e('Add paragraph'); ?>" />
		</p>
		<?php } ?>
	</form>
</div>
