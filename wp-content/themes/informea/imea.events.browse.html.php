<?php
/**
 * Template Name: Browse events (L2)
 * @package InforMEA
 * @subpackage InforMEA-Theme
 * @since 0.1
 * This is the controller for the 'Events' section pages.
 */
add_filter('breadcrumbtrail', array('imea_events_page', 'breadcrumbtrail'));
$page_data = new imea_events_page(array('id_treaty' => 1, 'start' => 'str', 'end' => 'str', 'order' => 'str', 'sort' => 'str'));
if(isset($show_rss) && $show_rss) {
	include(dirname(__FILE__) . '/imea_pages/events/rss.php');
	return;
}
$expand = get_request_variable('expand', 'list');

$events = $page_data->get_events_list();
$orgs = $page_data->get_treaties();
$base_url = get_bloginfo('url') . '/events';
$id_treaty = intval($page_data->req_get('id_treaty'));
$fe_month = intval(get_request_value('fe_month', 0));
$fe_year = intval(get_request_value('fe_year', 0));

$years = $page_data->get_years_interval();
$months = array (1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
	5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug', 9 => 'Sep',
	10 => 'Oct', 11 => 'Nov', 12 => 'Dec' );

if($page_data->empty_search()) {
	$h2_title = __('Upcoming events in the next 3 months', 'informea');
} else {
	$m = $page_data->get_months_fullname();
	$mn = isset($m[$fe_month]) ? $m[$fe_month] : '';

	$h2_title = '';
	if(!empty($id_treaty)) {
		$o = $orgs[$id_treaty];
		$h2_title .= ' ' . $o->short_title;
	}

	$h2_title .= empty($h2_title) ? __(' Events', 'informea') : __(' events', 'informea');

	if(!empty($mn) || !empty($fe_year)) {
		$h2_title .= __(' in ', 'informea') . ' ' . $mn . ' ' . $fe_year;
	}
}

add_filter('body_class', function ($classes) {
	$classes[] = 'col-2';
	return $classes;
});
get_header();
?>
<div id="container">
	<div id="page-title">
		<img src="<?php bloginfo('template_directory'); ?>/images/icon-events-large.png" alt="" title="<?php _e('Events', 'informea'); ?>" />
		<h1><?php the_title(); ?></h1>
		<h2><?php echo $h2_title; ?></h2>
	</div>
	<div id="content" role="main">
		<?php
			if($expand == 'calendar') {
				include(dirname(__FILE__) . '/imea_pages/events/browse.calendar.php');
			} else {
				include(dirname(__FILE__) . '/imea_pages/events/browse.listing.php');
			}
		?>
		<div class="clear"></div>
	</div><!-- #content -->
</div><!-- #container -->
<?php get_footer(); ?>
