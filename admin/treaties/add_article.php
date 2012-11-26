<?php
$id_treaty = get_request_value('id_treaty');
$thesaurus = new Thesaurus();
$order = get_request_value('order');
if ($id_treaty !== NULL and $order == NULL or $page_data->get_value('select') or $page_data->get_value('submit')) {
	$order = $page_data->get_next_treaty_article_order($id_treaty);
}

include (dirname(__FILE__) . '/../tinymce.inc.php');

?>
<div class="wrap">
	<div id="breadcrumb">
		You are here:
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_treaties">Manage treaties</a>
		&raquo;
		<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_treaties&act=treaty_add_article">Add new article to treaty</a>
	</div>
	<div id="icon-tools" class="icon32"><br></div>
	<h2>Add new article</h2>

	<?php if (!$id_treaty) { ?>
		<p>Select the treaty to which you want to add a new article</p>
	<?php } else { ?>
		<p>
			View the selected <a href="<?php echo $page_data->get_treaty_url($id_treaty);?>">treaty</a>.
		</p>
		<p>
			Add a new article to the treaty.
			Please enter the details below and press the
			<b><?php esc_attr_e('Add article'); ?></b> button.
		</p>
		<p>
			The articles are added to each treaty in the order of submission.
			<em>Please insert the articles in the order they appear in the treaty.</em>
		</p>
		<b>
			You can either add the content of the article here, or go to <a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_add_article_paragraph">Add new paragraph to an article</a> and add the article content paragraph by paragraph.
		</b>
	<?php } ?>

	<?php if ($page_data->actioned) { ?>
	<div class="updated settings-error" id="setting-error-settings_updated">
		<?php if ($page_data->success) { ?>
			<p><strong>The article was created and added to the treaty!</strong></p>
		<?php } ?>
		<?php if (!$page_data->success) { ?>
			<p><strong>Error adding article!</strong>
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
		<?php wp_nonce_field('informea-admin_treaty_add_article'); ?>
		<input type="hidden" name="page" value="informea_treaty" />
		<input type="hidden" name="act" value="treaty_add_article" />

		<table>
			<tr>
				<td><label for="id_treaty">Treaty *</label></td>
				<td>
				<select id="id_treaty" name="id_treaty">
					<option value="">-- Please select --</option>
					<?php foreach($page_data->get_all_treaties_organizations() as $row) {
						$checked = ($id_treaty == $row->id) ? ' selected="selected"' : '';
						echo "<option value='{$row->id}'$checked>{$row->short_title}</option>";
					} ?>
				</select>
				<input name="select" type="submit" class="button-primary" value="<?php esc_attr_e('select'); ?>" />
				</td>
			</tr>

			<?php if($id_treaty) { ?>
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
				<td><label for="title">Title *</label></td>
				<td>
					<input type="text" size="60" id="title" name="title"
						value="<?php if (!$page_data->success) echo $page_data->get_value('title');?>" />
				</td>
			</tr>
			<tr>
				<td>
					<label for="content">Content</label>
				</td>
				<td>
					<textarea rows="5" cols="40" id="content" name="content"><?php if (!$page_data->success) echo $page_data->get_value('content');?></textarea>
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
					foreach($thesaurus->get_voc_concept() as $row) {
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

		<?php if($id_treaty) {?>
		<p class="submit">
			<input name="actioned" type="submit" class="button-primary" value="<?php esc_attr_e('Add article'); ?>" />
		</p>
		<?php } ?>
	</form>
</div>
