<?php
/**
 * Template Name: Browse countries (L2)
 * @package InforMEA
 * @subpackage InforMEA-Teme
 * @since 0.1
 * This is the controller for the 'Countries' section pages.
 */

$portlet = get_request_value('portlet','none');
if ('country_portlet' == $portlet) {
	$random = get_request_value('use_random','true');
	$use_random = true;
	if ('false' == $random)
		$use_random = false;
	include(dirname(__FILE__) . '/imea_pages/countries/portlet.featured_country.php');
	exit;
}
$id_country = get_request_variable('id_country', 1); // Already integer. Taken from $wp_query->query_vars
$page_data = new imea_countries_page($id_country, array());
$mode = get_request_variable('mode', 'str', 'map'); // grid, map or alphabetical

// Properly return 404 before sending headers, if country not found
if ($page_data->is_404()) {
	exit;
}

add_filter('informea_page_title', array('imea_countries_page', 'informea_page_title'));
add_filter('breadcrumbtrail', array('imea_countries_page', 'breadcrumbtrail'));
add_filter('body_class', function ($classes) {
	global $page_data, $mode;
	if($mode == 'grid') {
		$classes[] = 'col-1';
	} else {
		$classes[] = 'col-2';
	}
	return $classes;
});

get_header();
?>
<div id="container">
<?php
	if($page_data->is_index()) {
?>
	<div id="page-title">
		<img src="<?php bloginfo('template_directory'); ?>/images/icon-countries-large.png" alt="" title="<?php _e('Countries', 'informea'); ?>" class="middle" />
		<h1><?php the_title(); ?></h1>
		<h2><?php _e('Parties to MEAs', 'informea'); ?></h2>
	</div>
<?php
	} else if($page_data->country) {
		$name = $page_data->country->name;
		$country = $page_data->country;
		$icon_url = get_bloginfo('url') . '/' . $page_data->country->icon_large;
?>
	<div id="page-title" class="country-profile">
		<img src="<?php echo $icon_url; ?>" />
		<h1><?php echo $name; ?></h1>
		<h2><?php echo $country->long_name; ?> <?php _e('profile', 'informea'); ?></h2>
	</div>
	<div class="separator-10px clear"></div>
<?php
	}
?>
	<div id="content" role="main">
	<?php
		if($page_data->is_index()) {
			include(dirname(__FILE__) . "/imea_pages/countries/browse.$mode.php");
		} else {
			include(dirname(__FILE__) . '/imea_pages/countries/profile.php');
		}
	?>
		<div class="clear"></div>
	</div><!-- #content -->
</div><!-- #container -->
<?php get_footer(); ?>
