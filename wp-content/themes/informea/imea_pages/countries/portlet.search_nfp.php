<div class="related-box">
	<!-- div class="box-title">Quick search</div -->
	<div class="box-content filter-content">
		<div class="title">
			<span>Find focal point</span>
		</div>
		<form id="id_form_search_nfp" action="" method="get">
			<input id="id_search_nfp" name="search_nfp" class="quick-nfp-input" type="text" size="28" />
		</form>
		<div class="clear"></div>
	</div>
</div>
<?php // JavaScript injection
	function js_inject_search_nfp() {
?>
<script type="text/javascript">
var ajax_url_nfp = '<?php bloginfo('url'); ?>/wp-admin/admin-ajax.php?action=nfp_autocomplete';
$(document).ready(function() {
	$('#id_search_nfp').autocomplete({
		source: function( request, response ) {
			$.ajax({
				url: ajax_url_nfp,
				dataType: "json",
				data: {
					maxRows: 10,
					key: request.term
				},
				success: function( data ) {
					response($.map(data, function( item ) {
						return {
							label: item.label,
							value: item.id_country,
							id_contact: item.id_contact,
							id_country: item.id_country,
							id_treaty: item.id_treaty
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
			$('#id_search_nfp').val(data.item.label);
			window.location = blog_dir + '/countries/' + data.item.id_country + '/nfp?showall=true&id_contact=' + data.item.id_contact + '#contact-bookmark-' + data.item.id_treaty;
			return false;
		}
	});

	$('#id_search_nfp').keydown(function(e){
		return e.keyCode != 13;
	});
});
</script>
<?php
	}
	add_action('js_inject', 'js_inject_search_nfp');
?>


