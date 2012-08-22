<?php
/**
 * Template Name: Browse terms (L2)
 * @package InforMEA
 * @subpackage InforMEA-Teme
 * @since 0.1
 * This is the controller for the 'Terms' section pages.
 */
$id_term = get_request_variable('id_term', 1); // Already integer. Taken from $wp_query->query_vars
$page_data = new Thesaurus($id_term, array('id_ns' => 1, 'id_concept' => 1, 'feature' => 'str', 'mode' => 'str'));

// Properly return 404 before sending headers, if country not found
if ($page_data->is_404()) {
	exit;
}
add_filter('informea_page_title', array('Thesaurus', 'informea_page_title'));
add_filter('breadcrumbtrail', array('Thesaurus', 'breadcrumbtrail'));
add_filter('body_class', function ($classes) {
	global $page_data;
	if($page_data->is_tabular_view()) {
		$classes[] = 'col-1';
	} else {
		if($page_data->is_index()) {
			$classes[] = 'col-2';
		} else {
			$classes[] = 'col-2';
		}
	}
	return $classes;
});
$expand = get_request_variable('expand', 'str', 'theme'); // grid or alphabetical
$tab = get_request_variable('tab', 'str', 'treaties'); // or decisions

// Inject the CSS into the header for terms trees
if($page_data->is_index() && $expand == 'theme') {
	function css_inject_terms_tree() {
		echo ('<link rel="stylesheet" type="text/css" href="'.get_bloginfo('template_directory').'/scripts/dhtmlxtree/dhtmlxtree.css">');
		echo '<link rel="stylesheet" type="text/css" href="'.get_bloginfo('template_directory').'/scripts/dhtmlxmenu/skins/dhtmlxmenu_dhx_skyblue.css">';
	}
	add_filter('css_inject', 'css_inject_terms_tree');
}

get_header();
?>
<div id="container">
	<?php if($page_data->is_index()) { ?>
	<div id="page-title">
		<img src="<?php bloginfo('template_directory'); ?>/images/icon-terms-large.png" alt="" title="<?php _e('Terms', 'informea'); ?>" />
		<h1><?php the_title(); ?></h1>
		<h2><?php _e('Analytical index of MEA terms', 'informea'); ?></h2>
	</div>
	<?php
		} else {
		$term = $page_data->term;
	?>

	<div class="warning w500px right">
		<div class="content">
			<?php echo __('Please note that Treaty portions and decisions are manually tagged and some omissions may occur. To ensure comprehensive results, combine terms from the analytical index with terms used in a free-text search which can be entered in the search fields found in the Explorer.', 'informea'); ?>
		</div>
	</div>

	<div id="page-title" class="left">
		<img src="<?php bloginfo('template_directory'); ?>/images/icon-terms-large.png" alt="" title="<?php _e('Treaties', 'informea'); ?>" />
		<h1>
			<?php echo $term->term ?>
			<?php if( current_user_can('manage_options') ) { ?>
			<a class="button" href="<?php echo bloginfo('url'); ?>/wp-admin/admin.php?page=thesaurus&act=voc_relationships&id_term=<?php echo $term->id;?>" title="Go to admin area and edit this term">
				<span>Edit term</span>
			</a>
			<?php } ?>
		</h1>
		<h2><?php _e('Term details page', 'informea'); ?></h2>
	</div>
	<?php } ?>
	<div class="clear"></div>
	<div id="content" role="main">
<?php
	if($page_data->is_tabular_view()) {
		include(dirname(__FILE__) . '/imea_pages/terms/browse.tabular.php');
	} else {
		if($page_data->is_index()) {
			include(dirname(__FILE__) . '/imea_pages/terms/browse.listing.php');
		} else {
			$page_data->inc_popularity();
			include(dirname(__FILE__) . '/imea_pages/terms/details.php');
		}
	}
?>
		<div class="clear"></div>
	</div><!-- #content -->
</div><!-- #container -->
<?php get_footer(); ?>
