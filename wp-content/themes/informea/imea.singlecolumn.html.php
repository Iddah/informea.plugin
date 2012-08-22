<?php
/**
 * Template Name: InforMEA single column page template
 * @package InforMEA
 * @subpackage InforMEA-Teme
 * @since 0.1
 * This is the controller for the 'Terms' section pages.
 */
global $post;
$page_data = new imea_highlights_page();
add_filter('body_class', function ($classes) {
	$classes[] = 'col-1';
	return $classes;
});
get_header();
?>
<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
<div id="container">
	<h1><?php the_title(); ?></h1>
	<div id="content" role="main">
		<div class="left details-column-2">
			<p>
				<?php the_content(); ?>
			</p>
			<div class="entry-meta">
			</div><!-- .entry-meta -->
		</div>
<?php endwhile; // end of the loop. ?>
	<div class="clear"></div>
	</div><!-- #content -->
</div><!-- #container -->
<?php get_footer(); ?>
