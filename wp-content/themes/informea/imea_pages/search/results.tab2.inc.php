<div class="clear"></div>
<?php
echo $search->render();
// Inject JS into footer
function js_inject_search_results_tab2() {
?>
<script type="text/javascript">
	$(document).ready(function() {
		$('a.toggle-result').click(function(e){
			e.preventDefault();
			var id = $(this).attr('id').split('-')[2];
			var entity = $(this).attr('id').split('-')[1];
            var target = $('#result-' + entity + '-' + id);
			if(target.is(':visible')) {
                $('#arrow-' + entity + '-' + id).removeClass('opened').addClass('closed');
			} else {
                $('#arrow-' + entity + '-' + id).removeClass('closed').addClass('opened');
			}
            target.toggle(100);
		});

        $('a.ajax-expand').click(function(e){
            e.preventDefault();
            var id = $(this).attr('id').split('-')[2];
            var entity = $(this).attr('id').split('-')[1];
            var target = $('#result-' + entity + '-' + id);
            if(target.is(':visible')) {
                if($(this).hasClass('arrow')) {
                    $(this).removeClass('opened').addClass('closed');
                }
                $('#arrow-' + entity + '-' + id).removeClass('opened').addClass('closed');
                target.toggle(100);
                // Do nothing, just collapse
            } else {
                var data = { action: 'search_highlight', 'q_freetext' : $('#filter_q_freetext').val(), entity: entity, id: id };
                if(target.text() == '') {
                    $.post(ajax_url, data, function(response) {
                        target.append(response);
                        $('#arrow-' + entity + '-' + id).removeClass('closed').addClass('opened');
                    });
                } else {
                    $('#arrow-' + entity + '-' + id).removeClass('closed').addClass('opened');
                }
                target.toggle(100);
            }

        });
    });
</script>
<?php
}
add_action('js_inject', 'js_inject_search_results_tab2');
?>
