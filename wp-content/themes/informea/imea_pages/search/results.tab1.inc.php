<?php
$dir = $search->get_sort_direction();
?>
<div class="toolbar">
	<form action="">
		<label for="view-mode"><?php _e('Order', 'informea'); ?></label>
		<select id="view-mode" name="view-mode" onchange="var dir = $(this).val(); if(dir == 'desc') { sort_descending(); } else { sort_ascending(); };">
			<?php $selected = ($dir == 'DESC') ? 'selected="selected "' : ''; ?>
			<option <?php echo $selected;?>value="desc"><?php _e('Newest first', 'informea'); ?></option>
			<?php $selected = ($dir == 'ASC') ? 'selected="selected "' : ''; ?>
			<option <?php echo $selected;?>value="asc"><?php _e('Oldest first', 'informea'); ?></option>
		</select>
	</form>
</div>
<?php
echo $search->render();
// Inject JS into footer
function js_inject_search_results_tab1() {
?>
<script type="text/javascript">
    var is_loading = false;
    var current_page = 0;
    var search_end = false;
	$(document).ready(function() {
        init_toggle();
        $(window).scroll(function() {
            if(is_loading) {
                return;
            }
            current = $(window).scrollTop() + $('#footer').height();
            max = $(document).height() - $(window).height();
            if(current > max) {
                if(search_end) {
                    return;
                }
                $('#search_results').append(
                    '<li id="loader" class="center"><img src="<?php echo bloginfo('template_directory'); ?>/images/loading-big.gif" /></li>'
                )
                is_loading = true;
                current_page += 1;
                var data = $("#filter").serialize();
                data += '&action=search_more_results';
                data += '&q_page=' + current_page;
                $.post(ajax_url, data, function(response) {
                    search_end = (response == '');
                    $('#loader').remove();
                    $('#search_results').append(response);
                    init_toggle();
                    is_loading = false;
                });
                return false;
            }
        });
	});

    function init_toggle() {
        $.each($('a.toggle-result'), function(i, item) {
            if(!$(this).hasClass('processed')) {
                $(this).addClass('processed');
                $(this).click(function(e){
                    e.preventDefault();
                    var id = $(this).attr('id').split('-')[2];
                    var entity = $(this).attr('id').split('-')[1];
                    var target = $('#result-' + entity + '-' + id);
                    var arrow = $('#arrow-' + entity + '-' + id);
                    if(target.is(':visible')) {
                        arrow.removeClass('opened').addClass('closed');
                    } else {
                        arrow.removeClass('closed').addClass('opened');
                    }
                    target.toggle(100);
                });
            }
        });

        $.each($('a.ajax-expand'), function(i, item) {
            if(!$(this).hasClass('processed')) {
                $(this).addClass('processed');
                $(this).click(function(e) {
                    e.preventDefault();
                    var id = $(this).attr('id').split('-')[2];
                    var entity = $(this).attr('id').split('-')[1];
                    var target = $('#result-' + entity + '-' + id);
                    var arrow = $('#arrow-' + entity + '-' + id);
                    if(target.is(':visible')) {
                        arrow.removeClass('opened').addClass('closed');
                        target.toggle(100);
                        // Do nothing, just collapse
                    } else {
                        var data = { action: 'search_highlight', 'q_freetext' : $('#filter_q_freetext').val(), entity: entity, id: id };
                        arrow.removeClass('closed').removeClass('opened').addClass('loading');
                        if(target.text() == '') {
                            $.post(ajax_url, data, function(response) {
                                if(response == '') {
                                    target.append('This treaty has decisions listed here');
                                } else {
                                    target.append(response);
                                }
                                arrow.removeClass('loading').removeClass('closed').addClass('opened');
                            });
                        } else {
                            arrow.removeClass('loading').removeClass('closed').addClass('opened');
                        }
                        target.toggle(100);
                    }
                });
            }
        });
    }

	function sort_descending() {
		$('#q_sort_direction_filters').attr('value', 'DESC');
		doFilter();
	}

	function sort_ascending() {
		$('#q_sort_direction_filters').attr('value', 'ASC');
		doFilter();
	}
</script>
<?php
}
add_action('js_inject', 'js_inject_search_results_tab1');
?>
