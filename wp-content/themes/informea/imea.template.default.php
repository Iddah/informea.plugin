<?php
/**
 * Template Name: InforMEA default template
 * @package InforMEA
 * @subpackage InforMEA-Teme
 * @since 0.1
 * Default template
 */
get_header();
if ( have_posts() ) while ( have_posts() ) : the_post();
?>
<div id="container">
	<br />
	<div id="content" role="main">
		<div>
			<h1><?php the_title(); ?></h1>
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
<?php endwhile; // end of the loop. ?>
	<div class="clear"></div>
	</div><!-- #content -->
</div><!-- #container -->
<?php get_footer(); ?>
