<?php
	$items = $results->get_results();
?>
<br />
<div class="tab-menu">
	<ul>
		<li>
			<a class="<?php echo $tab == 3 ? 'tab-active' : 'tab'; ?>"  href="javascript:setTab(3);">Global treaties</a>
		</li>
		<li>
			<a class="<?php echo $tab == 5 ? 'tab-active' : 'tab'; ?>" href="javascript:setTab(5);"><?php _e('Regional treaties', 'informea'); ?></a>
		</li>
		<li>
			<a class="tab" href="javascript:setTab(4);"><?php _e('Decisions', 'informea'); ?></a>
		</li>
	</ul>
</div>
<?php
if(count($items)) {
?>
<table id="search_tab5">
<?php
	foreach($items as $id_treaty => $result) {
		$title = $result->get_title(3);
		// $description = $result->get_description(5);
		$content = $result->get_content(3);
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
<?php } ?>
</table>

<?php
// Inject JS into footer
function js_inject_search_results_tab5() {
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
</script>
<?php
}
add_action('js_inject', 'js_inject_search_results_tab5');
?>
<?php
} else {
	echo '<br /><p>No treaty article matched this search, maybe decisions? Try the other tab...</p>';
}
?>
