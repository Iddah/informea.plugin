<?php
/**
 * Template Name: InforMEA newsletter template
 * @package InforMEA
 * @subpackage InforMEA-Teme
 * @since 0.1
 * Default template
 */
get_header();
the_post();
?>
<div id="container">
	<div id="page-title">
		<img src="<?php bloginfo('template_directory'); ?>/images/icon-newsletter-large.png" alt="" title="<?php _e('Newsletter', 'informea'); ?>" />
		<h1><?php the_title(); ?></h1>
		<h2><?php _e('Subscribe to receive InforMEA News', 'informea'); ?></h2>
	</div>
	<div class="left details-column-1"></div>
	<div id="content" role="main">
		<div class="left details-column-2 newsletter">
			<div class="newsletter-content">
				<?php the_content(); ?>
			</div>
			<a href="<?php bloginfo('url'); ?>">&laquo; Go back</a>
		</div>
		<div class="clear"></div>
		<div class="left details-column-3">
			<div class="clear"></div>
		</div>
	</div><!-- #content -->
</div><!-- #container -->
<?php get_footer(); ?>
