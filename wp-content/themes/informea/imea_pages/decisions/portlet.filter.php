<?php
	$search2 = new InformeaSearch2($_GET);
	$themes = $search2->ui_get_treaties();
	if(!isset($portlet_decision_filter_title)) {
		$portlet_decision_filter_title = 'Search decisions';
	}
	if(!isset($q_use_decisions)) { $q_use_decisions = true; }
	if(!isset($q_use_treaties)) { $q_use_treaties = false; }
	if(!isset($q_tab)) { $q_tab = 4; }
?>
<div class="filter decisions">
	<div class="filter-content">
		<form action="<?php bloginfo('url'); ?>/search" method="GET" id="filter-decisions">
			<?php if($q_use_decisions) { ?><input type="hidden" name="q_use_decisions" value="1"><?php } ?>
			<?php if($q_use_treaties) { ?><input type="hidden" name="q_use_treaties" value="1"><?php } ?>
			<input type="hidden" name="q_tab" value="<?php echo $q_tab; ?>">
			<div class="filter-terms filter-terms-decisions">
				<div class="title">
					<span><?php echo $portlet_decision_filter_title; ?></span>
				</div>
				<div class="content">
					<input type="text" size="25" id="q_freetext_decisions" name="q_freetext" />
				</div>
				<?php
					$ts = $search2->ui_get_treaties_ids();
					foreach($ts as $t_id) {
				?>
					<input type="hidden" name="q_treaty[]" value="<?php echo $t_id; ?>" />
				<?php
					}
				?>
			</div>
			<a class="button blue" href="javascript:void(0);" onclick="$('#filter-decisions').submit();">
				<span>Search</span>
			</a>
		</form>
	</div>
</div>
<?php
	function js_inject_decisions_portlet_filter() {
?>
<script type="text/javascript">
var ajax_url = '<?php bloginfo('url'); ?>/wp-admin/admin-ajax.php';
$(document).ready(function() {
	$('#q_freetext_decisions').autocomplete({
		source: function( request, response ) {
			var filter_td_key = $('#q_freetext_decisions').val().split('",');
			filter_td_key = filter_td_key[filter_td_key.length -1 ];
			$.ajax({
				url: ajax_url + '?action=suggest_terms',
				dataType: "json",
				data: {
					maxRows: 10,
					key: filter_td_key
				},
				success: function( data ) {
					response($.map(data, function( item ) {
						return {
							label: item.term,
							value: item.id
						}
					}));
				}
			});
		},
		minLength: 10,
		delay : 100,
		minLength : 1,
		focus : function(event, ui) {
			return false;
		},
		select : function(ui, data) {
			var filter_td_key = $('#q_freetext_decisions').val().split('",');
			filter_td_key.pop();
			filter_td_key.push('"' + data.item.label + '",');
			$('#q_freetext_decisions').val(filter_td_key.join('",'));
			return false;
		}
	});
});
</script>
<?php
}
add_filter('js_inject', 'js_inject_decisions_portlet_filter');
?>
