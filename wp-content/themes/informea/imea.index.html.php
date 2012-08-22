<?php
/**
 * Template Name: Index page
 * @package InforMEA
 * @subpackage InforMEA-Teme
 * @since 0.1
 * This is the controller for the 'Terms' section pages.
 */
add_filter('body_class', function ($classes) { $classes[] = 'col-3'; return $classes; });
get_header();

function js_inject_index() {
?>
<script type="text/javascript">
$(document).ready(function() {
	$('#lofslidecontent45').lofJSidernews({
		direction		: 'opacity',
		interval 		: 4000,
		easing			: 'easeInOutQuad',
		duration		: 800,
		auto		 	: true,
		maxItemDisplay  : 3,
		startItem		: 0,
		navPosition		: 'horizontal',
		navigatorHeight : 15,
		navigatorWidth	: 25,
		mainWidth		: 480
	});

	$('#news_ticker').vTicker({
		speed: 500,
		pause: 3000,
		animation: 'fade',
		mousePause: false,
		showItems: 4,
		height : 350
	});
});
</script>
<?php
}
add_action('js_inject', 'js_inject_index');
?>

<div id="container">
	<div id="content" role="main">
	<?php
		include(dirname(__FILE__) . '/imea_pages/index.html.php');
	?>
	<div class="clear"></div>
	</div><!-- #content -->
</div><!-- #container -->
<?php get_footer(); ?>
