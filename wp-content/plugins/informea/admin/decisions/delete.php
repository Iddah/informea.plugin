<?php
wp_enqueue_script('jquery');
wp_enqueue_script('jquery-ui-core');
wp_enqueue_script('jquery-ui-widget');
wp_enqueue_script('jquery-ui-datepicker');
wp_enqueue_script('wp-ajax-response');

wp_register_style('jquery-ui-darkness', plugins_url('/informea/admin/css/ui-darkness/jquery-ui-1.7.3.custom.css'));
wp_enqueue_style('jquery-ui-darkness');

$id_treaty = get_request_int('id_treaty');
$id_event = get_request_int('id_event');
$treatyOb = new imea_treaties_page();
$eventsOb = new imea_events_page();
$decisionsOb = new imea_decisions_page();
?>
<script type="text/javascript">

	jQuery(document).ready(function() {
		jQuery('#id_treaty').change(function() {
			jQuery('#delete_decision').submit();
		});
	});

</script>
<style type="text/css">
	.zebra { background-color: #F0F0F0; }
</style>
<div id="breadcrumb">
	You are here:
	<a href="<?php echo bloginfo('url');?>/wp-admin/admin.php?page=informea_decisions">Manage decisions</a>
	&raquo;
	Delete decisions
</div>
<div id="icon-tools" class="icon32"><br></div>
<h2>Delete decision</h2>
<div class="wrap nosubsub">
	<p>
		From this page you delete existing decisions from the database.
	</p>
	<?php
		include(dirname(__FILE__) . '/../operation.html.php');
	?>
	<div class="form-wrap">
		<form id="delete_decision" action="" method="post" enctype="multipart/form-data" class="validate">
			<?php wp_nonce_field('decision_delete'); ?>

			<div class="form-field form-required">
				<label for="id_treaty">Select treaty *</label>
				<select id="id_treaty" name="id_treaty" value="">
					<option value="">-- Please select --</option>
					<?php
						foreach($decisionsOb->get_treaties_w_decisions() as $treaty) {
							$selected = $treaty->id == $id_treaty ? ' selected="selected"' : '';
					?>
					<option value="<?php echo $treaty->id; ?>"<?php echo $selected; ?>><?php echo $treaty->short_title; ?></option>
					<?php } ?>
				</select>
			</div>
		<?php
		if($id_treaty) {
			$decisions = $decisionsOb->get_decisions_for_treaty($id_treaty);
				if(count($decisions)) {
		?>
			<p class="submit">
				<input type="submit" name="actioned" value="Delete selected" onclick="return confirm('Remove selected decisions and ALL their associated data (documents, tags, paragraphs etc.)? This CANNOT be undone!');" />
			</p>
			<table class="widefat">
				<thead>
					<tr>
						<th style="width: 40px;">Del</th>
						<th>Short title</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach($decisions as $idx => $decision) {
							$css = $idx % 2 == 0 ? ' class="zebra"' : '';
					?>
					<tr<?php echo $css; ?>>
						<td>
							<input type="checkbox" id="id_decision_<?php echo $decision->id; ?>" name="id_decision[]" value="<?php echo $decision->id; ?>" />
						</td>
						<td>
							<label for="id_decision_<?php echo $decision->id; ?>">
							<?php echo $decision->number; ?>
							<?php echo $decision->short_title; ?>
							</label>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			<p class="submit">
				<input type="submit" name="actioned" value="Delete selected" onclick="return confirm('Remove selected decisions and ALL their associated data (documents, tags, paragraphs etc.)? This CANNOT be undone!');" />
			</p>
		<?php
				}
			}
		?>
		</form>
	</div><!--/form-wrap -->

</div>
