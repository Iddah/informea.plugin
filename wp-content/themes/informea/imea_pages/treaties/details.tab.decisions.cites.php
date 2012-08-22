Go to
<ul>
  <li><a href="#Decisions">Decisions</a></li>
  <li><a href="#Resolutions">Resolutions</a></li>
</ul>
<?php
  $decisions = $page_data->get_cites_decisions();
  $data = array('Decisions' => $page_data->get_cites_decisions(), 'Resolutions' => $page_data->get_cites_resolutions());
  foreach($data as $title => $decisions) {
?>
<a name="<?php echo $title; ?>"></a>
<h2><?php echo $title; ?></h2>
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
			<a href="<?php bloginfo('url'); ?>/wp-admin/admin.php?page=informea_decisions&act=decision_edit_tags&id_decision=<?php echo $decision->id; ?>&id_treaty=<?php echo $decision->id_treaty; ?>" class="button">
				<span>Edit tags</span>
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
<?php } ?>
