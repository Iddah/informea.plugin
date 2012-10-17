<?php
/**
 * Template Name: Search results
 * @package InforMEA
 * @subpackage InforMEA-Teme
 * @since 0.1
 * This is the controller for the 'Search results' pages.
 */
$start = microtime_float();
$tab = get_request_int('q_tab', 2);
define('INFORMEA_SEARCH_PAGE', true);
$search = InformeaSearch3::get_searcher();
add_filter('body_class', function ($classes) {
	$classes[] = 'col-2';
	return $classes;
});
get_header();
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
				if(in_array($tab, array(1, 2, 3, 4, 5))) {
			?>
			<div class="view-mode">
				<form action="">
					<label for="view-mode"><?php _e('View', 'informea'); ?></label>
					<select id="view-mode" name="view-mode" onchange="setTab($(this).val());">
						<option <?php echo $tab == 1 ? 'selected="selected "' : '';?>value="1"><?php _e('as timeline', 'informea'); ?></option>
						<option <?php echo $tab == 2 ? 'selected="selected "' : '';?>value="2"><?php _e('grouped by treaty', 'informea'); ?></option>
					</select>
				</form>
			</div>
			<?php
					include(dirname(__FILE__) . "/imea_pages/search/results.tab$tab.inc.php");
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

