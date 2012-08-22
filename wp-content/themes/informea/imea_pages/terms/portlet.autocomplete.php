<div class="related-box">
	<div class="box-content filter-content">
		<div class="title">
			<span>Find term</span>
		</div>
		<form id="id_form_terms_autocomplete" action="" method="get">
			<input id="id_term_autocomplete" name="id_term" class="quick-term-input" type="text" size="28" />
		</form>
		<div class="clear"></div>
	</div>
</div>
<?php // JavaScript injection
	function js_inject_terms_autocomplete() {
?>
<script type="text/javascript">
var ajax_url = '<?php bloginfo('url'); ?>/wp-admin/admin-ajax.php';
$(document).ready(function() {
	$('#id_term_autocomplete').autocomplete({
		source: function( request, response ) {
			$.ajax({
				url: ajax_url + '?action=suggest_terms',
				dataType: "json",
				data: {
					maxRows: 10,
					key: request.term
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
			$('#id_term_autocomplete').val(data.item.label);
			window.location = blog_dir + '/terms/' + data.item.value + '/treaties';
			return false;
		}
	});

	$('#id_term_autocomplete').keydown(function(e){
		if(e.keyCode == 13) {
			if($('#termsTree_substantives').length > 0) {
				var sval = $.trim($('#id_term_autocomplete').val());
				var selItemId = tree_substantives.getSelectedItemId();
				var curIdx = $.inArray(selItemId, allnodes_substantives);
				if(curIdx >= 0) {
					var new_arr = allnodes_substantives.slice(curIdx + 1);
					focusNextItem(tree_substantives, new_arr, sval, $('#filter_substantives_next'), $('#filter_substantives'));
				}
			}
			return false;
		} else {
			if($('#termsTree_substantives').length > 0) {
				var sval = $.trim($('#id_term_autocomplete').val());
				if(sval.length > 1) {
					focusNextItem(tree_substantives, allnodes_substantives, sval, $('#filter_substantives_next'), $('#filter_substantives'));
				}
			}
		}
		return true;
	});

});
</script>
<?php
	}
	add_action('js_inject', 'js_inject_terms_autocomplete');
?>


