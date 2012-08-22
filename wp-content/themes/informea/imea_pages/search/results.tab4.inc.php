<?php
 $items = $results->get_results();
?>
<div class="tab-menu">
	<ul>
		<li>
			<a class="tab"  href="javascript:setTab(3);"><?php _e('Global treaties', 'informea'); ?></a>
		</li>
		<li>
			<a class="tab" href="javascript:setTab(5);"><?php _e('Regional treaties', 'informea'); ?></a>
		</li>
		<li>
			<a class="tab-active" href="javascript:setTab(4);">Decisions <?php // echo '(' . count($items) . ')'; ?></a>
		</li>
	</ul>
</div>
<?php if(count($items)) { ?>
<table id="search_tab4">
<?php
	foreach($items as $id_treaty => $result) {
		$title = $result->get_title(4);
		// $description = $result->get_description(4);
		$content = $result->get_content(4);
		$icon = $result->get_icon(1);
		$url = $result->get_item_url();
?>
	<tr>
		<td class="expand">
			<a id="toggle-result-<?php echo $id_treaty; ?>" class="toggle-result closed" href="javascript:void(0);">&nbsp;</a>
		</td>
		<td class="icon"><?php echo $icon; ?></td>
		<td class="data">
			<a id="title-result-<?php echo $id_treaty; ?>" class="toggle-result" href="javascript:void(0);"><?php echo $title; ?></a>
			<a href="<?php echo $url; ?>"><img class="middle" src="<?php bloginfo('template_directory'); ?>/images/external.png" /></a>
			<div id="result-<?php echo $id_treaty; ?>" class="hidden">
				<?php echo $content; ?>
			</div>
		</td>
	</tr>
<?php } ?>
</table>
<?php } else { ?>
No decisions have been found
<?php } ?>
<?php
// Inject JS into footer
function js_inject_search_results_tab4() {
	global $search2;
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('a.toggle-result').click(function(e){
			e.preventDefault();

			resultID = $(this).attr('id').split('-')[2];
			if($('#result-' + resultID).is(':visible') == true){
				$('img', this).attr('src', '<?php $search2->img('images/expand.gif'); ?>');
				$('#result-' + resultID).slideUp(100);
			}else {
				$('img', this).attr('src', '<?php $search2->img('images/collapse.gif'); ?>');
				$('#result-' + resultID).slideDown(100);
			}
		});
	});
</script>
<?php
}
add_action('js_inject', 'js_inject_search_results_tab4');
?>
