<?php
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
	<br />
	<div id="content" role="main">
		<div class="left details-column-1">
			<div id="page-title">
				<h1><?php _e('Highlights', 'informea'); ?></h1>
				<h2><?php _e('MEA news &amp; highlights', 'informea'); ?></h2>
				<div>
					<img src="<?php bloginfo('template_directory'); ?>/images/icon-highlights-large.png" alt="" title="<?php _e('Highlights', 'informea'); ?>" />
				</div>
			</div>
			<?php include(dirname(__FILE__) . '/portlet.search.php');?>
		</div>
		<?php $img_url = $page_data->get_post_image(null, $post); ?>
		<div class="left details-column-2">
			<h1 ><?php the_title(); ?></h1>
			<img src="<?php echo $img_url; ?>" />
			<p>
				<?php the_content(); ?>
			</p>
			<div class="entry-meta">
			<?php // twentyten_posted_on(); ?>
			</div><!-- .entry-meta -->


			<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) ); ?>
			<div class="entry-utility">
				<?php twentyten_posted_in(); ?>
				<?php edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' ); ?>
			</div><!-- .entry-utility -->

		</div>
		<div class="left details-column-3">
		</div>
<?php endwhile; // end of the loop. ?>
	<div class="clear"></div>
	</div><!-- #content -->
</div><!-- #container -->
<?php get_footer(); ?>
