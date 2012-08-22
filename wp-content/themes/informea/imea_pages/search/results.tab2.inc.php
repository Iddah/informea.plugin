<div class="clear"></div>
<table id="search_tab2">
<?php
	foreach($results->get_results() as $idx => $result) {
		$title = $result->get_title(2);
		$description = $result->get_description(2);
		$content = $result->get_content(2);
		$icon = $result->get_icon(1);
		$url = $result->get_item_url();
?>
	<tr>
		<td class="expand">
			<a id="toggle-result-<?php echo $idx; ?>" class="toggle-result closed" href="javascript:void(0);">&nbsp;</a>
		</td>
		<td class="icon"><?php echo $icon; ?></td>
		<td class="data">
			<a id="title-result-<?php echo $idx; ?>" class="toggle-result" href="javascript:void(0);"><?php echo $title; ?></a>
			<a href="<?php echo $url; ?>"><img class="middle" src="<?php bloginfo('template_directory'); ?>/images/external.png" /></a>
			<div class="description"><?php echo $description; ?></div>
			<?php if(!empty($content)) { ?>
			<div id="result-<?php echo $idx; ?>" class="hidden">
				<?php echo $content; ?>
			</div>
			<?php } ?>
		</td>
	</tr>
<?php } ?>
</table>

<?php
// Inject JS into footer
function js_inject_search_results_tab2() {
	global $search2;
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
add_action('js_inject', 'js_inject_search_results_tab2');
?>
