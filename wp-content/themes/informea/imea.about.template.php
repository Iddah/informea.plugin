<?php
/**
 * Template Name: Informea about page template
 * A 3-column custom page template with sidebar.
 *
 * @package WordPress
 * @subpackage InforMEA
 * @since InforMEA 1.0
 */
add_filter('body_class', function ($classes) { $classes[] = 'col-2'; return $classes; });
get_header();
$about = get_page_by_title('about');
$subpages = get_pages(array('child_of' => $about->ID, 'sort_column' => 'menu_order', 'sort_order' => 'ASC'));
$current_page = null;
if (get_the_ID() == $about->ID) {
    $post = get_page_by_title('introduction');
}

function js_inject_about() {
?>
<script type="text/javascript">
$(document).ready(function() {
	// Slide the banners
	var images = $('#banners').find('img');
	var index = 0;
	var previous = 0;
	if(images.length > 0) {
		$(images).each(function(idx, el) { $(el).hide(); });
		$(images[0]).fadeIn('fast');
		setInterval(function() {
			index++;
			previous = index - 1;
			if(index > images.length - 1) { index = 0; }
			if(index == 0) { previous = images.length - 1; }

			$(images[previous]).fadeOut('fast', function() { $(images[index]).fadeIn('slow'); });
		}, 5000);
	}
});
</script>
<?php
}
add_action('js_inject', 'js_inject_about');
?>

<style type="text/css">
.subpage-content li {
	/*giving UL's LIs generated disc markers*/
	list-style: disc outside !important;
	margin-left: 15px;
}
</style>
<div id="container">
	<div id="page-title">
		<img src="<?php bloginfo('template_directory'); ?>/images/icon-about-large.png" alt="" title="<?php _e('About our project', 'informea'); ?>" />
		<h1><?php the_title(); ?></h1>
		<h2><?php _e('About InforMEA project', 'informea'); ?></h2>
	</div>
	<div id="content" role="main">
		<div id="banners" class="left details-column-1">
			<ul>
				<?php dynamic_sidebar('About page - banners'); ?>
			</ul>
		</div>
		<div class="details-column-2">
			<div class="tab-menu">
				<ul>
					<?php
						foreach($subpages as $sp) {
							$css_class = 'tab';
							if(get_the_ID() == $sp->ID) {
								$css_class = 'tab-active';
								$current_page = $sp;
							}
					?>
						<li>
							<a class="<?php echo $css_class; ?>" href="<?php echo get_page_link($sp->ID) ;?>"><?php echo $sp->post_title; ?></a>
						</li>
					<?php } ?>
				</ul>
			</div>
			<div class="clear"></div>
			<div class="subpage-content">
			<?php
				while(have_posts()) {
					the_post();
					the_content();
					echo '<div class="clear"></div>';
					wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'twentyten' ), 'after' => '</div>' ) );
					echo '<div class="clear"></div>';
					edit_post_link( __( 'Edit', 'twentyten' ), '<span class="edit-link">', '</span>' );
				}
			?>
			</div>
			<div class="clear"></div>
		</div>
	</div><!-- #content -->
</div><!-- #container -->

<?php get_footer(); ?>
