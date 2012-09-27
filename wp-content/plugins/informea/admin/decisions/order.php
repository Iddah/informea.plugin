<?php
$id_treaty = get_request_int('id_treaty');
$decisionOb = new imea_decisions_page(NULL);
?>
<style>
	.sortable {
		list-style-type: none;
		margin: 0;
		padding: 2px;
		min-height: 50px;
		background-color: #4facdc;
	}
	.sortable li {
		background-color: white;
		border: 1px solid dark-grey;
		margin: 0 3px 3px 3px;
		padding: 3px;
		padding-left: 5px;
	}
	li.ui-state-highlight { height: 20px; background-color: #FFCC33 !important; }
	.decision-order { float: left; min-width: 15px; padding: 0 5px; text-align: center; margin-right: 15px; border: 1px solid #cccccc; }
	span.decision-number { min-width: 130px; float: left; padding: 0 5px; }
</style>
<script src="<?php bloginfo('template_directory'); ?>/scripts/jquery-min.js"></script>
<script src="<?php bloginfo('template_directory'); ?>/scripts/ui.js"></script>
<script type="text/javascript">
	var ajax_reorder_url = ajaxurl + '?action=order_decisions';
	$(function() {
		$( "#decision-listing" ).sortable(
		{
			dropOnEmpty: false,
			placeholder: 'ui-state-highlight',
			stop: function(event, ui) {
				var items = [];
				$('#decision-listing li').each(function(idx, li) {
					var id = $(li).attr('id').replace('decision-', '');
					items.push(id);
				});
				$.ajax({
					url: ajax_reorder_url,
					dataType: "text",
					type: 'POST',
					cache: false,
					data: {
						id_treaty: $('#id_treaty option:selected').val(),
						_nonce: '<?php echo wp_create_nonce('secret_order_decisions'); ?>',
						decisions: items.join(',')
					},
					success: function(data) {
						if(data != '1') {
							alert('Sorting went wrong on server. Please report this issue immediately!');
						}
					},
					error: function(error) {
						alert(error.message);
					}
				});
			}
		});
		$( "#decision-listing" ).disableSelection();
	});
</script>

<h2>Decision Ordering</h2>
<form action="">
	<input type="hidden" name="page" value="informea_decisions" />
	<input type="hidden" name="act" value="decision_order" />
	<label for="id_treaty">Choose MEA</label>
	<select id="id_treaty" name="id_treaty" value="">
		<option value="">-- Please select treaty --</option>
		<?php
			foreach($decisionOb->get_treaties_w_decisions('b.`display_order`') as $treaty) {
				$selected = $treaty->id == $id_treaty ? ' selected="selected"' : '';
		?>
		<option value="<?php echo $treaty->id; ?>"<?php echo $selected; ?>><?php echo $treaty->short_title; ?></option>
		<?php } ?>
	</select>
	<input type="submit" name="submit" value="Refresh" />
</form>

<?php if(!empty($id_treaty)) { ?>
<p>
	<strong>Hint:</strong>
	<em>Drag and drop decisions to their correct order</em>
</p>
<ul id="decision-listing" class="droptrue sortable">
<?php foreach($decisionOb->get_decisions_for_treaty_meeting($id_treaty, 'a.`display_order`') as $decision) { ?>
	<li id="decision-<?php echo $decision->id;?>" class="action" title="Decision order after last refresh: <?php echo $decision->display_order; ?>">
		<span class="decision-number"><?php echo esc_attr($decision->number); ?></span>
		(<?php echo $decision->ob_meeting_title; ?>)
		-
		<?php echo esc_attr($decision->short_title); ?>
	</li>
<?php } ?>
</ul>
<?php } ?>
