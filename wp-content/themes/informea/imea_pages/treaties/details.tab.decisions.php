<?php
	$showall = get_request_variable('showall', 'str'); // showall
?>
<div class="tab-content">
<?php 
if($showall) { 
?>
<script type="text/javascript">
$(document).ready(function() {
	var trDec = $('#tr-' + window.location.hash.replace('#', ''));
	if(trDec) {
		trDec.css('background-color', '#FFE974');
		trDec.attr('class', '');
		var scp = trDec.position().top - 200;
		$.scrollTo('' + scp + 'px', 100);
	}
});
</script>
<?php 
} 
if($treaty->id == '19' || $treaty->id == '17') { 
?>
<div class="warning">
	<div class="content">
		<br />
		<strong>Disclaimer:</strong> Please note that the decisions from UNCCD and UNFCCC and the Kyoto Protocol, other than those listed here, will be accessible in the course of 2012
	</div>
</div>
<?php
} else {
	if($treaty->odata_name == 'cites') {
		include(dirname(__FILE__) . '/details.tab.decisions.cites.php');
	} else {
?>
<ul class="collapsible">
<?php
	$d = $page_data->group_decisions_by_meeting();
	$meetings = $d['decisions'];
	$meetings_obs = $d['meetings'];

	$showall_title = ($showall !== NULL) ? 'opened' : 'closed';
	$showall_content = ($showall !== NULL) ? 'visible' : 'hidden';
	foreach($meetings as $meeting_id => &$decisions) {
		$meeting = $meetings_obs[$meeting_id];
		$meeting_title = subwords($meeting->title, 7);
		$meeting_summary = $page_data->decisions_meeting_summary($meeting);
?>
	<li>
		<div class="title">
			<a href="javascript:void(0);" class="<?php echo $showall_title; ?> tooltip" title="<?php _e('Click to see decisions taken at this meeting', 'informea'); ?>">
				<?php echo $meeting_title; ?>
			</a>
		</div>
		<div class="content <?php echo $showall_content; ?>">
			<span class="decisions-meeting-summary"><?php echo $meeting_summary; ?></span>
			<div class="list-item-details dec-table-holder">
				<table class="default grey decision-listing">
					<thead>
					<tr>
						<th class="no"><?php _e('No.', 'informea'); ?></th>
						<th class="title"><?php _e('Title', 'informea'); ?></th>
						<th class="type"><?php _e('Type (Status)', 'informea'); ?></th>
						<th class="tags"><?php _e('Terms', 'informea'); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php
						$icount = 0;
						$page_data->sort_decisions($decisions);
						foreach($decisions as $decision) {
							$documents = $page_data->get_decision_documents($decision->id);
							$tags = $page_data->get_decision_tags($decision->id);
					?>
					<tr id="tr-decision-<?php echo $decision->id; ?>" class="<?php echo($icount % 2)?'odd':'even'; ?>">
						<td class="middlecenter">
							<a name="decision-<?php echo $decision->id; ?>"></a><?php echo $decision->number; ?>
						</td>
						<td>
							<span>
								<?php echo ucwords(strtolower($page_data->get_title($decision))); ?>
								<br />
								<?php if(!empty($documents) || !empty($decision->link)) { ?>
								<?php if(!empty($decision->link)) { ?>
									<a href="<?php echo $decision->link; ?>" target="_blank" title="<?php _e('Click to see this decision on Convention website', 'informea'); ?>"><img src="<?php bloginfo('template_directory'); ?>/images/globe.png" alt="<?php _e('External link', 'informea'); ?>" /></a>
								<?php } ?>
								<?php foreach($documents as $doc) { ?>
									<a class="middle" target="_blank" href="<?php bloginfo('url') ?>/download?entity=decision_document&id=<?php echo $doc->id; ?>" title="<?php _e('Click to download document', 'informea'); echo !empty($doc->language) ? ' (' . $doc->language . ')' : ''; ?>">
										<img src="<?php echo $doc->icon_url; ?>" alt="<?php _e('External link', 'informea'); ?>" /> <?php echo !empty($doc->language) ? ' (' . $doc->language . ')' : '';?>
									</a>
								<?php } ?>
								<?php } ?>
							</span>
						</td>
						<td class="middlecenter type">
							<?php echo $decision->type; ?>
							(<?php $status = decode_decision_status($decision->status); echo $status; ?>)
						</td>
						<td class="tagging-td">
							<span class="dec-tags-holder">
						<?php
							if(count($tags)) {
								$last = end($tags);
								foreach($tags as $tag) {
						?>
							<a href="<?php bloginfo('url'); ?>/terms/<?php echo $tag->id; ?>"><?php echo $tag->term; ?></a><?php if($last !== $tag) { echo ','; } ?>
						<?php
								}
							}
						?>
							</span>
						<?php if( current_user_can('manage_options') ) { ?>
							<a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=informea_decisions&act=decision_edit&id_decision=<?php echo $decision->id; ?>&id_treaty=<?php echo $decision->id_treaty; ?>" class="button">
								<span>Edit decision</span>
							</a>
						<?php } ?>
						</td>
					</tr>
					<?php
							$icount++;
						}
					?>
					</tbody>
				</table>
			</div>
		</div>
		<div class="clear"></div>
	</li>
<?php
	}
?>
</ul>
<?php
}
} 
?>
</div>
