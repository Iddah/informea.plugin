<?php
/**
 * Template Name: Browse treaties (L2)
 * @package InforMEA
 * @subpackage Theme
 * @since 0.1
 * This is the controller for the 'Treaties' section pages.
 */

$id_treaty = get_request_variable('id_treaty', 1); // Already integer. Taken from $wp_query->query_vars
if(!$id_treaty) {
	$ob = new imea_treaties_page(NULL);
	$odata_name = get_request_variable('treaty', NULL); // Already integer. Taken from $wp_query->query_vars
	$treaty = $ob->get_treaty_by_odata_name($odata_name);
	if($treaty != null) {
		$id_treaty = $treaty->id;
	} else {
		$id_treaty = null;
		$treaty = null;
	}
}

$page_data = new imea_treaties_page($id_treaty, array());

if($page_data->get_action() == 'delete_paragraph') {
	$id_paragraph = get_request_int('id_paragraph');
	$page_data->delete_paragraph($id_paragraph);
}
if($page_data->get_action() == 'delete_article') {
	$id_article = get_request_int('id_article');
	$page_data->delete_article($id_article);
}

$expand = get_request_variable('expand', 'str', 'treaty'); // or decisions

// Properly return 404 before sending headers, if treaty not found
if ($page_data->is_404()) {
	exit;
}

if($expand === 'print'){
	include(dirname(__FILE__) . '/imea_pages/treaties/details.tab.treaty_print.php');
	exit(0);
}

add_filter('informea_page_title', array('imea_treaties_page', 'informea_page_title'));
add_filter('breadcrumbtrail', array('imea_treaties_page', 'breadcrumbtrail'));

add_filter('body_class', function ($classes) {
	global $page_data;
	if($page_data->is_index()) {
		$classes[] = 'col-2';
		$classes[] = 'treaties-l2';
	} else {
		$classes[] = 'col-2';
		$classes[] = 'treaties-l3';
	}
	return $classes;
});

$expand = get_request_variable('expand', 'str', 'treaty'); // or decisions
get_header();
?>
<div id="container">
	<div id="page-title">
		<img src="<?php bloginfo('template_directory'); ?>/images/icon-treaties-large.png" alt="" title="<?php _e('Treaties', 'informea'); ?>" />
		<h1><?php the_title(); ?></h1>
		<h2><?php _e('Multilateral Environmental Agreements', 'informea'); ?></h2>
	</div>
	<div id="content" role="main">
		<?php
		if( $page_data->is_index() ) {
			include(dirname(__FILE__) . '/imea_pages/treaties/browse.php');
		} else {
			include(dirname(__FILE__) . '/imea_pages/treaties/details.php');
		}
		?>
		<div class="clear"></div>
	</div><!-- #content -->
</div><!-- #container -->

<?php
if(in_array($page_data->get_action(), array('delete_article', 'delete_paragraph')) && $page_data->actioned) {
function js_inject_delete_paragraph() {
	global $page_data;
?>
	<script type="text/javascript">
	$(document).ready(function() {
		alert('<?php echo implode($page_data->errors); ?>');
	});
	</script>
<?php
}
add_action('js_inject', 'js_inject_delete_paragraph');
}
?>

<?php get_footer(); ?>
