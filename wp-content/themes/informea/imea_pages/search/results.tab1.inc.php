<div class="toolbar">
	<form action="">
		<label for="view-mode"><?php _e('Order', 'informea'); ?></label>
		<select id="view-mode" name="view-mode" onchange="var dir = $(this).val(); if(dir == 'desc') { sort_descending(); } else { sort_ascending(); };">
			<?php $selected = ($dir == 'DESC') ? 'selected="selected "' : ''; ?>
			<option <?php echo $selected;?>value="desc"><?php _e('Newest first', 'informea'); ?></option>
			<?php $selected = ($dir == 'ASC') ? 'selected="selected "' : ''; ?>
			<option <?php echo $selected;?>value="asc"><?php _e('Oldest first', 'informea'); ?></option>
		</select>
		<label for="view-mode"><?php _e('Results per page', 'informea'); ?></label>
		<select id="page_size" name="page_size" onchange="setPageSize($(this).val());">
			<?php $selected = ($page_size == '10') ? 'selected="selected "' : ''; ?>
			<option <?php echo $selected;?>value="10"><?php _e('10', 'informea'); ?></option>
			<?php $selected = ($page_size == '20') ? 'selected="selected "' : ''; ?>
			<option <?php echo $selected;?>value="20"><?php _e('20', 'informea'); ?></option>
			<?php $selected = ($page_size == '50') ? 'selected="selected "' : ''; ?>
			<option <?php echo $selected;?>value="50"><?php _e('50', 'informea'); ?></option>
		</select>
	</form>
</div>

<div class="clear"></div>
<table id="search_tab1">
<?php
	foreach($results->get_results() as $idx => $result) {
		$icon = $result->get_icon(1);
		$title = $result->get_title(1);
		// $description = $result->get_description(1);
		$content = $result->get_content(1);
		$url = $result->get_item_url();
?>
	<tr>
		<td class="expand">
		<?php if(!empty($content)) { ?>
			<a id="toggle-result-<?php echo $idx; ?>" class="toggle-result closed" href="javascript:void(0);">&nbsp;</a>
		<?php } ?>
		</td>
		<td class="icon"><?php echo $icon; ?></td>
		<td class="data">
			<a id="title-result-<?php echo $idx; ?>" class="toggle-result" href="javascript:void(0);"><?php echo $title; ?></a>
			<a href="<?php echo $url; ?>"><img class="middle" src="<?php bloginfo('template_directory'); ?>/images/external.png" /></a>
			<?php if(!empty($content)) { ?>
			<div id="result-<?php echo $idx; ?>" class="hidden">
				<?php echo $content; ?>
			</div>
		</td>
	</tr>
	<?php } ?>
<?php } ?>
</table>

<div id="search-paginator">
<?php
	$current_page = $results->get_current_page();
	$total_pages = $results->get_pages_count();
?>
	Found: <?php echo $total_results; ?> results, showing page <?php echo ($current_page + 1); ?> of <?php echo $total_pages; ?>
	<?php if($results->has_prev_page()) { ?>
		&nbsp;<a class="link" href="javascript:prevPage(<?php echo $current_page - 1; ?>);">&laquo; Prev</a>&nbsp;
	<?php
		}
	?>
<?php
	if($results->has_next_page()) {
		if($results->has_prev_page()) {
			echo '&nbsp;&middot;&nbsp;';
		}
?>
		&nbsp;<a class="link" href="javascript:nextPage(<?php echo ($current_page + 1); ?>);">Next &raquo;</a>
<?php
	}
?>
</div>
</div>

<?php
// Inject JS into footer
function js_inject_search_results_tab1() {
	global $search;
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('a.toggle-result').click(function(e){
			e.preventDefault();
			var id = $(this).attr('id').split('-')[2];
			if($('#result-' + id).is(':visible')) {
				$('#toggle-result-' + id).removeClass('opened').addClass('closed');
			} else {
				$('#toggle-result-' + id).removeClass('closed').addClass('opened');
			}
			$('#result-' + id).toggle(100);
		});
	});

	function nextPage(page) {
		$('#q_page_filters').attr('value', page);
		doFilter();
	}

	function prevPage(page) {
		$('#q_page_filters').attr('value', page);
		doFilter();
	}

	function sort_descending() {
		$('#q_sort_direction_filters').attr('value', 'DESC');
		doFilter();
	}

	function sort_ascending() {
		$('#q_sort_direction_filters').attr('value', 'ASC');
		doFilter();
	}
</script>
<?php
}
add_action('js_inject', 'js_inject_search_results_tab1');
?>
