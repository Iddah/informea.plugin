<?php if( current_user_can('manage_options') ) { ?>
	<br />
	<a class="button edit-button edit-paragraph" href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_edit_article_paragraph&id_treaty=<?php echo $treaty->id; ?>&id_treaty_article=<?php echo $article->id; ?>&id_treaty_article_paragraph=<?php echo $paragraph->id; ?>">
		<span>Edit paragraph</span>
	</a>

	<a class="button insert-button edit-paragraph" href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_add_article_paragraph&id_treaty=<?php echo $treaty->id; ?>&id_treaty_article=<?php echo $article->id; ?>&order=<?php echo ($paragraph->order + 1); ?>">
		<span>Insert paragraph below</span>
	</a>

	<?php if($paragraph->id != $pfirst->id) { ?>
	<a class="button up-button edit-paragraph move-<?php echo $paragraph->id; ?>" href="javascript:void(0);">
		<span><span class="icon"></span>&nbsp;</span>
	</a>
	<?php } ?>
	<?php if($paragraph->id != $plast->id) { ?>
	<a class="button down-button edit-paragraph move-<?php echo $paragraph->id; ?>" href="javascript:void(0);" title="Move this paragraph down">
		<span><span class="icon"></span>&nbsp;</span>
	</a>
	<?php } ?>
	<a class="button delete-button delete-<?php echo $paragraph->id; ?>" href="javascript:void(0);" onclick="delete_paragraph(<?php echo $paragraph->id;?>);" title="Delete this paragraph">
		<span>Delete</span>
	</a>

	<form id="move-paragraph-<?php echo $paragraph->id; ?>" action="<?php echo bloginfo('url'); ?>/treaties?id_treaty=<?php echo $treaty->id; ?>&id_treaty_article=<?php echo $article->id; ?>#<?php echo $para_id; ?>" method="post">
		<input type="hidden" name="id_treaty" value="<?php echo $treaty->id; ?>" />
		<input type="hidden" name="id_treaty_article" value="<?php echo $article->id; ?>" />
		<input type="hidden" name="id_paragraph" value="<?php echo $paragraph->id; ?>" />
		<input type="hidden" name="action" value="" id="direction-<?php echo $paragraph->id; ?>" />
	</form>
	<form id="delete-paragraph-<?php echo $paragraph->id; ?>" action="<?php echo bloginfo('url'); ?>/treaties/<?php echo $treaty->id; ?>/?id_treaty_article=<?php echo $article->id; ?>#article_<?php echo $article->id; ?>" method="post">
		<input type="hidden" name="id_paragraph" value="<?php echo $paragraph->id; ?>" />
		<input type="hidden" name="action" value="delete_paragraph" />
		<?php wp_nonce_field('treaty_delete_paragraph'); ?>
	</form>
<?php } ?>
