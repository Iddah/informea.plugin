<div class="related-box">
	<!-- div class="box-title">Quick search</div -->
	<div class="box-content filter-content">
		<div class="title">
			<span>Select country</span>
		</div>
		<form id="id_form_countries_autocomplete" action="" method="get">
			<input id="id_country_autocomplete" name="id_country" class="quick-country-input" type="text" size="28" />
		</form>
		<div class="clear"></div>
	</div>
</div>
<?php // JavaScript injection
	function js_inject_countries_autocomplete() {
?>
<script type="text/javascript">
var ajax_url_country = '<?php bloginfo('url'); ?>/wp-admin/admin-ajax.php?action=countries_autocomplete';
$(document).ready(function() {
	$('#id_country_autocomplete').autocomplete({
		source: function( request, response ) {
			$.ajax({
				url: ajax_url_country,
				dataType: "json",
				data: {
					maxRows: 10,
					key: request.term
				},
				success: function( data ) {
					response($.map(data, function( item ) {
						return {
							label: item.name,
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
			$('#id_country_autocomplete').val(data.item.label);
			window.location = blog_dir + '/countries/' + data.item.value;
			return false;
		}
	});

	$('#id_country_autocomplete').keydown(function(e){
		return e.keyCode != 13;
	});
});
</script>
<?php
	}
	add_action('js_inject', 'js_inject_countries_autocomplete');
?>


