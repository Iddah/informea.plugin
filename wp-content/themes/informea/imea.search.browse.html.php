<?php
/**
 * Template Name: Search results
 * @package InforMEA
 * @subpackage InforMEA-Teme
 * @since 0.1
 * This is the controller for the 'Search results' pages.
 */
$start = microtime_float();
$request = $_REQUEST;
$search2 = new InformeaSearch2($request);
$tab = $search2->get_q_tab();
$results = $search2->search();
$total_results = $results->get_count();
$dir = $search2->get_sort_direction();
$page_size = $search2->get_page_size();


add_filter('body_class', function ($classes) {
	$classes[] = 'col-2';
	return $classes;
});
get_header();

$tab1_disabled = ($tab == '1') ? 'disabled' : '';
$tab2_disabled = ($tab == '2') ? 'disabled' : '';
$tab3_disabled = ($tab == '3' || $tab == '4') ? 'disabled' : '';
define('INFORMEA_SEARCH_PAGE', true);
?>
<div id="container">
	<br />
	<div id="content" role="main">
		<div class="left details-column-1 search-left-column">
			<?php include(dirname(__FILE__) . '/imea_pages/search/filters.inc.php'); ?>
			<div class="clear"></div>
		</div>
		<div class="left details-column-2 search-center-column">
			<h1>
				<?php _e('Search results', 'informea'); ?>
			</h1>
			<?php
				if($total_results > 0 || in_array($tab, array('3', '4', '5'))) {
			?>
			<div class="view-mode">
				<form action="">
					<label for="view-mode"><?php _e('View', 'informea'); ?></label>
					<select id="view-mode" name="view-mode" onchange="setTab($(this).val());">
						<?php $selected = ($tab == '1') ? 'selected="selected "' : ''; ?>
						<option <?php echo $selected;?>value="1"><?php _e('as timeline', 'informea'); ?></option>
						<?php $selected = ($tab == '2') ? 'selected="selected "' : ''; ?>
						<option <?php echo $selected;?>value="2"><?php _e('grouped by treaty', 'informea'); ?></option>
						<?php $selected = ($tab == '3' ||  $tab == '4') ? 'selected="selected "' : ''; ?>
						<option <?php echo $selected;?>value="3"><?php _e('treaties/decisions', 'informea'); ?></option>
					</select>
				</form>
			</div>
			<?php
					include(dirname(__FILE__) . "/imea_pages/search/results.tab$tab.inc.php");
				} else {
					echo 'Oops, no results have been found.';
					$q = $search2->get_freetext();
					if(!empty($q)) {
						echo ' <a target="_blank" href="https://www.google.com/?#sclient=psy-ab&hl=en&source=hp&q=' . esc_attr($q) . '+site:informea.org">Try Google?</a>';
					}
				}
			?>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div><!-- #content -->
</div><!-- #container -->
<?php
	microtime_float($start, 'Entire search took');
function js_inject_about() {
?>
<script type="text/javascript">
$(document).ready(function() {
});

function details(itemId) {
	var ctrl = $('#' + itemId);
	var ctrl_link = $('#link-' + itemId);
	if(ctrl.is(':visible')) {
		ctrl.slideUp('fast');
		ctrl_link.removeClass('opened');
		ctrl_link.addClass('closed');
	} else {
		ctrl.slideDown('fast');
		ctrl_link.removeClass('closed');
		ctrl_link.addClass('opened');
	}
}
</script>
<?php
}
add_action('js_inject', 'js_inject_about');
?>

<?php get_footer();

