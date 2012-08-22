<?php

// Pre-cook the search - add parameters
$request = array();
$request['q_term'] = array($term->id);
$page_treaties = new imea_treaties_page(); // Treaties
$ts = $page_treaties->get_treaties();
$request['q_treaty'] = array();
foreach($ts as $t) {
	$request['q_treaty'][] = $t->id;
}
$request['q_tab'] = 3; // Results like 3rd tab of adv search
$request['q_use_treaties'] = 1;

$search2 = new InformeaSearch2($request);
$results = $search2->search();
?>
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
			<a id="link-<?php echo $idx; ?>" class="toggle-result closed" href="javascript:details('<?php echo $idx; ?>-treaty');">&nbsp;</a>
		</td>
		<td class="icon"><?php echo $icon; ?></td>
		<td class="data">
			<a href="javascript:details('<?php echo $idx; ?>-treaty');"><?php echo $title; ?></a>
			<a href="<?php echo $url; ?>"><img class="middle" src="<?php bloginfo('template_directory'); ?>/images/external.png" /></a>
			<div class="description"><?php echo $description; ?></div>
			<?php if(!empty($content)) { ?>
			<div id="<?php echo $idx; ?>-treaty" class="hidden">
				<?php echo $content; ?>
			</div>
			<?php } ?>
		</td>
	</tr>
<?php } ?>
</table>
