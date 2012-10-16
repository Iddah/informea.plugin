<?php
$count = 0;
$sel_terms = $search->ui_get_selected_terms();
$terms_page = new Thesaurus(NULL);
$tab = $search->get_q_tab();

$sterms = $search->get_terms();
// Inject JS into footer
function js_inject_search_filters_inc() {
?>
<script type="text/javascript">
$(document).ready(function() {
	$( "#q_term_filters" ).combobox();
	//$( "#q_term_filters" ).toggle();
	$('h5.refine-title').bind('click', function(e) {
		e.preventDefault();
		toggleRefineSection($(this));
		return false;
	});
	//register_autocomplete_terms('filter_q_freetext', 'filter', true);
});

function filtersUIDeselectTerm(id){
	$('.filters-span-term-'+id).animate({ backgroundColor: "#FFD1D7" }, "fast");
	$('.filters-span-term-'+id).fadeOut('fast', function(){ $(this).remove() });
	// Deselect the term from combo-box
	$('#q_term_filters option:selected').each(function(idx, el) {
		if($(el).attr('value') == id) {
			$(el).removeAttr('selected');
		}
	});
}

function doFilter() {
	$('#filter').submit();
}

function setPageSize(num) {
	$('#q_page_size_filters').val(num);
	$('#filter').submit();
}

function setTab(num) {
	$('#q_tab_filters').val(num);
	$('#filter').submit();
}

function toggleRefineSection(e) {
	var parent = $('.refine-content', e.parent());
	if(parent.is(':visible') == true){
		e.removeClass('expanded');
		e.addClass('collapsed');
	}else {
		e.removeClass('collapsed');
		e.addClass('expanded');
	}
	parent.slideToggle();
	return false;
}
</script>
<?php
}
add_action('js_inject', 'js_inject_search_filters_inc');
?>
<form method="get" action="<?php bloginfo('url'); ?>/search" id="filter">
	<input type="hidden" id="q_page_size_filters" name="q_page_size" value="<?php echo $search->get_page_size(); ?>" />
	<input type="hidden" id="q_tab_filters" name="q_tab" value="<?php echo $search->get_q_tab(); ?>" />
	<input type="hidden" id="q_page_filters" name="q_page" value="<?php $search->get_page(); ?>" />
	<input type="hidden" id="q_sort_direction_filters" name="q_sort_direction" value="<?php $search->get_sort_direction(); ?>" />

	<h4 class="refine-title">
		<img src="<?php bloginfo('template_directory'); ?>/images/refine-search-icon.png" alt="Refine icon" title="Refine search here" /> Refine your search here
	</h4>
	<span class="input-wrap">
		<label for="filter_q_freetext">Free search text</label>
		<div class="clear"></div>
		<input type="text" size="20" id="filter_q_freetext" name="q_freetext" class="free-text left medium" value="<?php echo esc_attr($search->ui_get_freetext()); ?>" />
		<a class="button orange search-explorer-submit" title="Search events" href="javascript:void(0);" onclick="$(this).closest('form').submit();">
			<span><?php _e('Search', 'informea');?></span>
		</a>

	</span>
	<div class="clear"></div>
	<div class="refine-section">
		<h5 class="refine-title expanded">Type keyword</h5>
		<div class="refine-content">
			<select id="q_term_filters" name="q_term[]" multiple="multiple" class="hidden">
			<?php
				$terms = $terms_page->suggest_vocabulary_terms();
				foreach($terms as $term) {
					$search->ui_write_option($term->id, $term->term, in_array(intval($term->id), $sterms));
				}
			?>
			</select>
			<?php $visible = count($sel_terms) > 1 ? '' : 'hidden'; ?>
			<div id="filter_and_or_radiobuttons" class="<?php echo $visible; ?>">
				<input type="radio" id="q_term_or_and_filters" name="q_term_or" value="and"<?php $search->ui_radio_terms_or(); ?> />
				<label for="q_term_or_and_filters">AND</label>
				<input type="radio" id="q_term_or_or_filters" name="q_term_or" value="or"<?php $search->ui_radio_terms_or(true); ?> />
				<label for="q_term_or_or_filters">OR</label>
			</div>
			<div class="selected-terms-holder">
				<?php foreach($sel_terms as $term) { ?>
				<div class="term-content filters-span-term-<?php echo $term->id;?>" title="<?php echo $term->term; ?>"><?php echo subwords($term->term, 3); ?>&nbsp;<a href="javascript:filtersUIDeselectTerm(<?php echo $term->id; ?>);"><img class="closebutton" src="<?php $search->img_s_gif() ?>" alt="" title="" /></a></div>
				<?php } ?>
			</div>
		</div>
		<div class="clear"></div>
		<div class="refine-section">
			<h5 class="refine-title expanded">Restrict results to</h5>
			<div class="refine-content">
				<div class="refine-content-section">
					<input type="checkbox" id="filter_q_use_decisions" name="q_use_decisions" value="1" <?php $search->ui_check_use_decisions();?> />
					<label for="filter_"><?php _e('Decisions/Resolutions', 'informea'); ?></label>
					<br />
					<input type="checkbox" id="filter_q_use_treaties"  name="q_use_treaties" value="1" <?php $search->ui_check_use_treaties();?> />
					<label for="filter_q_use_treaties"><?php _e('Treaties', 'informea'); ?></label>
					<br />
					<input type="checkbox" id="filter_q_use_meetings" name="q_use_meetings" value="1" <?php $search->ui_check_use_meetings();?> />
					<label for="filter_q_use_meetings"><?php _e('Meetings', 'informea'); ?></label>
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<div class="refine-section">
		<h5 class="refine-title expanded">Show results for conventions</h5>

		<div class="refine-content">
			<?php include(dirname(__FILE__) . '/filters.treaties.inc.php'); ?>
		</div>
		<div class="clear"></div>
	</div>
<?php if($tab == 1) { ?>
	<div class="refine-section">
		<h5 class="refine-title expanded">Restrict to the following timeline</h5>

		<div class="refine-content">
			<?php include(dirname(__FILE__) . '/filters.interval.inc.php'); ?>
		</div>
		<div class="clear"></div>
	</div>
<?php } ?>
</form>
