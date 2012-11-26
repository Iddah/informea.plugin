<?php
$id_treaty = get_request_value('id_treaty');
$id_treaty_article = get_request_value('id_treaty_article');
if ($id_treaty_article) {
	$article_row = $page_data->get_treaty_article_row($id_treaty_article);
	$id_treaty = $article_row->id_treaty;
	$order = $article_row->order;
} else {
	$article_row = NULL;
}

if (($page_data->get_value('select_article') and $article_row !== NULL)
	or ($page_data->get_value('submit') and $page_data->success)
	or (!$page_data->get_value('select_article') and !$page_data->get_value('submit') and $article_row !== NULL)) {
	$official_order = $article_row->official_order;
	$title = $article_row->title;
	$content = $article_row->content;
	$keywords = $page_data->get_keywords_for_treaty_article($id_treaty_article);
} else if ($page_data->get_value('submit') and !$page_data->success) {
	$official_order = $page_data->get_value('official_order');
	$title = $page_data->get_value('title');
	$content = $page_data->get_value('content');
	$keywords = $page_data->get_value('keywords');
}

include (dirname(__FILE__) . '/../tinymce.inc.php');
?>
<div class="wrap">
	<div id="breadcrumb">
		You are here:
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_treaties">Manage treaties</a>
		&raquo;
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_treaties&act=treaty_edit_article">Edit an article from treaty</a>
	</div>
	<div id="icon-tools" class="icon32"><br></div>
	<h2>Edit article from treaty</h2>

	<?php if (!$id_treaty_article) { ?>
		<p>Select the article you want to edit:</p>
	<?php } else { ?>
		<p>
			View the selected <a href="<?php echo $page_data->get_article_url_in_treaty($id_treaty, $id_treaty_article);?>">article</a>.
		</p>
		<p>
			Change this article.
			Please enter the details below and press the
			<b><?php esc_attr_e('Submit changes'); ?></b> button.
		</p>
	<?php } ?>

	<?php if ($page_data->actioned) { ?>
	<div class="updated settings-error" id="setting-error-settings_updated">
		<?php if ($page_data->success) { ?>
			<p><strong>Article was successfully changed!</strong></p>
		<?php } ?>
		<?php if (!$page_data->success) { ?>
			<p><strong>Error changing article!</strong>
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
		<table>
			<tr>
				<td><label for="id_treaty_article">Article *</label></td>
				<td>
				<select id="id_treaty_article" name="id_treaty_article">
					<option value="">-- Please select --</option>
					<?php foreach($page_data->get_treaty_article_in_treaty($id_treaty) as $row) {
						$checked = ($id_treaty_article == $row->id) ? ' selected="selected"' : '';
						$stitle = subwords($row->title, 10);
						echo "<option value='{$row->id}'$checked>{$row->treaty_title} - {$stitle}</option>";
					} ?>
				</select>
				<input name="select_article" type="submit" class="button-primary" value="<?php esc_attr_e('select'); ?>" />
				</td>
			</tr>
		</table>
		<p>
		* - Required field(s)
		</p>
	</form>

	<?php if ($id_treaty_article) { ?>
	<form action="" method="post">
		<?php wp_nonce_field('informea-admin_treaty_edit_article'); ?>
		<input type="hidden" name="page" value="informea_treaty" />
		<input type="hidden" name="act" value="treaty_edit_article" />
		<input type="hidden" name="id_treaty_article" value="<?php echo $id_treaty_article;?>" />

		<table>
			<tr>
				<td><label for="order">Internal order (readonly)*</label></td>
				<td>
					<input type="text" size="3" id="order" name="order" disabled="disabled"
						value="<?php echo $order;?>" />
				</td>
			</tr>
			<tr>
				<td><label for="official_order">Order</label></td>
				<td>
					<input type="text" size="10" id="official_order" name="official_order"
						value="<?php echo $official_order;?>" />
				</td>
			</tr>
			<tr>
				<td><label for="title">Title *</label></td>
				<td>
					<input type="text" size="60" id="title" name="title"
						value="<?php echo $title;?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="content">Content</label>
				</td>
				<td>
					<textarea rows="5" cols="40" id="content" name="content"
						><?php echo $content;?></textarea>
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
					foreach($page_data->get_voc_concept() as $row) {
						$checked = (is_array($keywords) and in_array($row->id, $keywords)) ? ' selected="selected"' : '';
						echo "<option value='{$row->id}'$checked>{$row->term}</option>";
					}
					?>
					</select>
				</td>
			</tr>

		</table>
		<p>
		* - Required field(s)
		</p>

		<p class="submit">
			<input name="actioned" type="submit" class="button-primary" value="<?php esc_attr_e('Submit changes'); ?>" />
		</p>
		<?php } ?>
	</form>
	<?php if ($id_treaty_article) { ?>
	<form action="" method="post">
		<?php wp_nonce_field('treaty_delete_article'); ?>
		<input type="hidden" name="act" value="treaty_delete_article" />
		<input type="hidden" name="id_article" value="<?php echo $id_treaty_article;?>" />
		<input name="delete" type="submit" class="button" value="<?php esc_attr_e('Delete'); ?>" onclick="return confirm('Delete cannot be undone. Are you sure?');" />
	</form>
	<?php } ?>
</div>
