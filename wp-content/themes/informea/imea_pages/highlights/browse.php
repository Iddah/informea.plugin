<div id="container">
	<div id="page-title">
		<img src="<?php bloginfo('template_directory'); ?>/images/icon-highlights-large.png" alt="" title="<?php _e('Highlights', 'informea'); ?>" />
		<h1><?php _e('Highlights', 'informea'); ?></h1>
		<h2><?php _e('News &amp; highlights', 'informea'); ?></h2>
	</div>
	<div id="content" role="main">
		<div class="left details-column-1">
			<?php include(dirname(__FILE__) . '/portlet.search.php');?>
			<?php
				if (!isset($cloud_items_nr)){
					$cloud_items_nr = 10;
				}
				$terms = new Thesaurus();
				$top_concepts = $terms->get_top_concepts();
				$popular_terms = $page_data->get_popular_terms(NULL, 3);
				$cloud_terms = array_merge($top_concepts, $popular_terms);
			?>
			<div class="highlights-browse-terms-cloud">
				<?php include(dirname(__FILE__) . '/../portlet.terms-cloud.inc.php'); ?>
			</div>
			<?php include(dirname(__FILE__) . '/portlet.rss.php');?>
		</div>
		<div class="left details-column-2">
			<?php include(dirname(__FILE__) . '/inc.select_topic.php'); ?>
			<div class="entry-content">
		<?php
			$imea_url = get_bloginfo('url');
			foreach($page_data->non_empty_categories as $category) {
				$posts = $page_data->get_category_posts($category, 1);
				if(count($posts->posts)) {
		?>
					<h3>
						<a href="<?php bloginfo('url'); ?>/highlights/<?php echo $category->slug;?>"><?php echo $category->title; ?></a>
					</h3>
					<ul class="highlight-stories">
					<?php
					foreach($posts->posts as $ni) {
						$permalink = $ni->permalink;
						$summary = $ni->summary;
						$show_img = strpos($summary, 'img') <= 0;
						$target = 'target="_blank" ';
						$is_local = strpos($permalink, $imea_url);
						if(is_int($is_local) && $is_local >= 0 ) {
							$target = '';
						}
						include(dirname(__FILE__) . '/inc.highlight.php');
					} ?>
					</ul>
		<?php
				}
			} ?>
			</div>
		</div>
		<div class="left details-column-3">
		</div>
	</div><!-- #content -->
</div><!-- #container -->


