<?php
function js_inject_treaty_treaty() {
	global $id_treaty;
?>
<script type="text/javascript">
var termsAjaxUrl = '<?php bloginfo('url'); ?>/wp-admin/admin-ajax.php';
var articlesCache = [];
var paragraphsCache = [];

$(document).ready(function(){
	// Go to article combo-box
	$('#go-to-article').change(function(e){
		e.preventDefault();
		var el_id = $(this).val();
		$.scrollTo( $('#article_' + el_id), 500, function(){
			$('#article-paragraphs-' + el_id).slideDown('fast');
		} );
	});
});

function onMouseOverArticle(id_article) {
	var el = $('#article_' + id_article);
	if(typeof(articlesCache[id_article]) != 'undefined' && articlesCache[id_article] != '') {
		showTermsBaloon(el);
		setTermsBalloonContent(articlesCache[id_article]);
	} else if(articlesCache[id_article] == '') {
		// Do nothing - cache hit, but no terms associated
	} else {
		$(el).css('cursor', 'wait');
		jQuery.post(
			termsAjaxUrl, { action : 'get_article_tags', id_article : id_article },
			function(response) {
				var s = '';
				var l = response.length;
				if(response.length > 0) {
					for(var i = 0; i < l; i++) {
						s += '<a class="link" href="' + blog_dir + '/terms/' + response[i].id + '">' + response[i].term + '</a>';
						if(i < l -1) { s += ', '; }
					}
					articlesCache[id_article] = s;
					showTermsBaloon(el);
					setTermsBalloonContent(s);
				} else {
					articlesCache[id_article] = '';
				}
				$(el).css('cursor', 'pointer');
			}
		);
	}
}


function onMouseOverParagraph(div_id, id_paragraph) {
	var el = $('#' + div_id);
	if(typeof(paragraphsCache[id_paragraph]) != 'undefined' && paragraphsCache[id_paragraph] != '') {
		showTermsBaloon(el);
		setTermsBalloonContent(paragraphsCache[id_paragraph]);
	} else if(paragraphsCache[id_paragraph] == '') {
		// Do nothing - cache hit, but no terms associated
	} else {
		$(el).css('cursor', 'wait');
		jQuery.post(
			termsAjaxUrl, { action : 'get_paragraph_tags', id_paragraph : id_paragraph },
			function(response) {
				var s = '';
				var l = response.length;
				if(response.length > 0) {
					for(var i = 0; i < l; i++) {
						s += '<a class="link" href="' + blog_dir + '/terms/' + response[i].id + '">' + response[i].term + '</a>';
						if(i < l -1) { s += ', '; }
					}
					paragraphsCache[id_paragraph] = s;
					showTermsBaloon(el);
					setTermsBalloonContent(s);
				} else {
					paragraphsCache[id_paragraph] = '';
				}
				$(el).css('cursor', 'auto');
			}
		);
	}
}

function showTermsBaloon(el) {
	var balloon = $('#terms_tooltip');
	var elOffset = el.offset();
	var left = elOffset.left + el.width();
	var top = elOffset.top - Math.abs(balloon.height() / 2) + Math.abs(el.height() / 2);
	$('#terms_tooltip').css({'left' : left + 'px', 'top' : top + 'px'});
	$('#terms_tooltip').show();
}

function setTermsBalloonContent(content) {
	$('#terms_tooltip_content').html(content);
	$('#terms_tooltip_content').show();
}

function hideTermsBalloon() {
	$('#terms_tooltip').hide();
}
</script>
<?php
}
add_action('js_inject', 'js_inject_treaty_treaty');
?>
<div class="tab-content">
<?php
if (!empty($page_data->articles)) {
?>
<select id="go-to-article" class="column-select">
	<option value="0"><?php _e('-- Go to specific article --', 'informea'); ?></option>
<?php
	foreach($page_data->articles as $article) {
?>
	<option value="<?php echo $article->id; ?>"><?php echo $article->official_order . ' ' . $article->title; ?></option>
<?php
	}
?>
</select>

<div class="clear"></div>

<?php
} else {
?>
<div class="info-message">
	<?php _e('This treaty has no articles or paragraphs!', 'informea'); ?>
</div>
<?php
}

if (current_user_can('manage_options')) {
?>
	<a class="button" title="Add new article to <?php echo $treaty->short_title; ?> treaty" href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_add_article&id_treaty=<?php echo $treaty->id; ?>">
		<span>Add new article</span>
	</a>
	<a class="button" title="Edit <?php echo $treaty->short_title; ?> treaty" href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_edit_treaty&id=<?php echo $treaty->id; ?>">
		<span>Edit treaty</span>
	</a>

	<div class="clear"></div>

<?php
}
if (!empty($page_data->articles)) {
?>
<span class="expand-collapse-buttons clear">
	<a href="<?php bloginfo('url'); ?>/treaties/<?php echo $treaty->odata_name; ?>/print" title="View the printable version of the treaty" target="_blank">
		<img src="<?php echo bloginfo('template_directory'); ?>/images/printer.png" alt="Print" class="middle" />
	</a>
	<?php
	/*
	<a href="javascript:void(0);" title="Email treaty">
		<img src="<?php echo bloginfo('template_directory'); ?>/images/mail.png" alt="Email" title="Email" class="middle" /> Email</a>
	<a href="javascript:void(0);" title="Save treaty" onclick="go_saveas();return false">
		<img src="<?php echo bloginfo('template_directory'); ?>/images/save.png" alt="Save" title="Save" class="middle" /> Save</a>
	<a href="javascript:void(0);" title="Share treaty">
		<img src="<?php echo bloginfo('template_directory'); ?>/images/share.png" alt="Share" title="Share" class="middle" /> Share</a>
	*/
	?>
	<div class="view-mode">
		<a href="javascript:void(0); " class="articles-expand-button <?php echo (intval($url_article_id))?'disabled':''; ?>"><?php _e('Expand all articles', 'informea'); ?></a>
		<a href="javascript:void(0); " class="articles-compress-button <?php echo (intval($url_article_id))?'':'disabled'; ?>"><?php _e('Collapse all articles', 'informea'); ?></a>
	</div>
</span>
<?php
}
?>
	<ul class="treaty-articles">
<?php
	foreach($page_data->articles as $article) {
?>
	<li>
		<h3 id="<?php echo 'article_'.$article->id;?>" onmouseover="onMouseOverArticle(<?php echo $article->id; ?>);" onmouseout="hideTermsBalloon();">
			<?php echo $article->official_order; ?> <?php echo $article->title; ?>
		</h3>

		<div class="article-paragraph" id="article-paragraphs-<?php echo $article->id; ?>">
<?php if( current_user_can('manage_options') ) { ?>
			<a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_edit_article&id_treaty=<?php echo $treaty->id; ?>&id_treaty_article=<?php echo $article->id ;?>" class="button">
				<span>Edit this article</span>
			</a>
			<a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=informea_treaties&act=treaty_add_article_paragraph&id_treaty=<?php echo $treaty->id; ?>&id_treaty_article=<?php echo $article->id; ?>" class="button">
				<span>Add new paragraph to this article</span>
			</a>
			<a class="button delete-button delete-<?php echo $article->id; ?>" href="javascript:void(0);" onclick="delete_article(<?php echo $article->id;?>);" title="Delete this article">
				<span>Delete</span>
			</a>
			<form id="delete-article-<?php echo $article->id; ?>" action="<?php echo bloginfo('url'); ?>/treaties/<?php echo $treaty->id; ?>" method="post">
				<input type="hidden" name="id_article" value="<?php echo $article->id; ?>" />
				<input type="hidden" name="action" value="delete_article" />
				<?php wp_nonce_field('treaty_delete_article'); ?>
			</form>
			<br />
			<br />
<?php } ?>
			<?php
			if (isset($page_data->paragraphs_by_article[$article->id])) {
				foreach($page_data->paragraphs_by_article[$article->id] as $paragraph) {
					$paragraphs = $page_data->paragraphs_by_article[$article->id];
					$pfirst = $paragraphs[0];
					$plast = $paragraphs[count($paragraphs) - 1];
					$para_id = "article_{$article->id}_paragraph_{$paragraph->id}";
					$content = trim(preg_replace(array('/^<p>/ix', '/<\/p>$/ix'), '', $paragraph->content));
			?>
					<p id="<?php echo $para_id;?>"
						class="article-paragraph-indent-<?php echo $paragraph->indent; ?> paragraph-content" onmouseover="onMouseOverParagraph('<?php echo $para_id; ?>', <?php echo $paragraph->id; ?>);" onmouseout="hideTermsBalloon();">
					<?php
						echo $paragraph->official_order . ' '. $content;
						include(dirname(__FILE__) . '/details.tab.treaty_admin.php');
					?>
					</p>
<?php
				}
			} else {
				echo $article->content;
			}
?>
		</div>
<?php
	}
?>
	</ul>
</div>
<table id="terms_tooltip" class="bubbletip" cellspacing="0" cellpadding="0" style="margin: 0; padding: 0; font-size: 12px; line-height : 13px; display : none; width: 400px; height: 108px; position: absolute;" onmouseover="$(this).show();" onmouseout="$(this).hide();">
<tbody>
	<tr>
		<td class="bt-topleft"></td>
		<td class="bt-top"></td>
		<td class="bt-topright" style="padding-right: 20px;"></td>
	</tr>
	<tr>
		<td class="bt-left-tail">
			<div class="bt-left" style="height: 1px; "></div>
			<div class="bt-left-tail"></div>
			<div class="bt-left" style="height: 1px; "></div>
		</td>
		<td class="bt-content">
			<div id="terms_tooltip_content" class="hidden" style="display: block; width : 340px; display : none;">
			</div>
		</td>
		<td class="bt-right"></td>
	</tr>
	<tr>
		<td class="bt-bottomleft"></td>
		<td class="bt-bottom"></td>
		<td class="bt-bottomright"></td>
	</tr>
</tbody>
</table>
