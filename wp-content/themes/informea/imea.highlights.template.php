<?php
/**
 * Template Name: Informea highlights page template
 * A 3-column custom page template with sidebar.
 *
 * @package WordPress
 * @subpackage InforMEA
 * @since InforMEA 1.0
 */
//$evt_class = new imea_events_page();
$page_data = new imea_highlights_page();
$query_category = $page_data->get_category_by_slug(get_query_var('highlight_category'));
$imea_url = get_bloginfo('url');
add_filter('breadcrumbtrail', array('imea_highlights_page', 'breadcrumbtrail'));
if(isset($show_rss) && $show_rss){
	include(dirname(__FILE__) . '/imea_pages/highlights/rss.php');
	return;
}
if (!$query_category){
    add_filter('body_class', function ($classes) {
	    $classes[] = 'col-2';
    	return $classes;
    });
}
else {
    add_filter('body_class', function ($classes) {
	    $classes[] = 'col-2';
    	return $classes;
    });
}
get_header();

if($page_data->is_search()) {
	$highlight_search = get_request_value('highlight_search');
	$highlight_month = get_request_value('highlight_month');
	$highlight_year = get_request_value('highlight_year');
	$p = intval(get_query_var('page'));
	$posts = $page_data->search($highlight_search, $highlight_month, $highlight_year, 10, $p);
	include(dirname(__FILE__) . '/imea_pages/highlights/search.php');
} else {
	if(!$query_category) {
		include(dirname(__FILE__) . '/imea_pages/highlights/browse.php');
	} else {
		// Display news from a single source (channel) - i.e. Species
		$p = intval(get_query_var('page'));
		$posts = $page_data->get_category_posts($query_category, 10, $p);
		include(dirname(__FILE__) . '/imea_pages/highlights/topic.php');
	}
}


get_footer();
?>

