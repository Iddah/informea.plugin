<div class="related-box">
	<!-- div class="box-title">Quick search</div -->
	<div class="box-content filter-content">
		<div class="title">
			<span>Search treaty</span>
		</div>
		<form id="id_form_meas_autocomplete" action="" method="get">
			<input id="id_mea_autocomplete" name="id_mea" class="quick-mea-input" type="text" size="28" />
		</form>
		<div class="clear"></div>
	</div>
</div>
<?php // JavaScript injection
	function js_inject_meas_autocomplete() {
?>
<script type="text/javascript">
var ajax_url = '<?php bloginfo('url'); ?>/wp-admin/admin-ajax.php?action=meas_autocomplete';
$(document).ready(function() {
	$('#id_mea_autocomplete').autocomplete({
		source: function( request, response ) {
			$.ajax({
				url: ajax_url,
				dataType: "json",
				data: {
					maxRows: 5,
					key: request.term
				},
				success: function( data ) {
					response($.map(data, function( item ) {
						return {
							label: item.title,
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
			$('#id_mea_autocomplete').val(data.item.label);
			window.location = blog_dir + '/treaties/' + data.item.value;
			return false;
		}
	});

	$('#id_mea_autocomplete').keydown(function(e){
		return e.keyCode != 13;
	});
});
</script>
<?php
	}
	add_action('js_inject', 'js_inject_meas_autocomplete');
?>


