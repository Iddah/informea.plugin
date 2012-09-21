<?php
/**
 * Template Name: Browse decisions (L2)
 * @package InforMEA
 * @subpackage InforMEA-Teme
 * @since 0.1
 * This is the controller for the 'Decisions' section pages.
 */
add_filter('breadcrumbtrail', array('imea_decisions_page', 'breadcrumbtrail'));
add_filter('body_class', function ($classes) { $classes[] = 'col-2'; return $classes; });
get_header();
$page_data = new imea_decisions_page(array());

if(!isset($warning_text)) { $warning_text = __('The decisions from UNCCD and UNFCCC and the Kyoto Protocol, other than those listed here, will be accessible in the course of 2012', 'informea'); }

?>
<div id="container">
	<div id="page-title" class="left">
		<img src="<?php bloginfo('template_directory'); ?>/images/icon-decisions-large.png" alt="" title="<?php _e('Decisions', 'informea'); ?>" />
		<h1><?php the_title(); ?></h1>
		<h2><?php _e('Browse MEA decisions', 'informea'); ?></h2>
	</div>

	<div class="warning w300px right">
		<div class="content">
			<?php echo $warning_text; ?>
		</div>
	</div>
	<div class="clear"></div>
	<div id="content" role="main">
	<?php
		include(dirname(__FILE__) . '/imea_pages/decisions/browse.listing.php');
	?>
	<div class="clear"></div>
	</div><!-- #content -->
</div><!-- #container -->
<?php get_footer(); ?>
