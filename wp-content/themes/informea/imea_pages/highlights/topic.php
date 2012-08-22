<?php
	$replace_title_with = "Related Events";
	if ($query_category->treaties){
		$treaty_ids = $query_category->treaties;
	}
?>
<div id="container">
	<div id="page-title">
		<img src="<?php bloginfo('template_directory'); ?>/images/icon-highlights-large.png" alt="" title="<?php _e('Highlights', 'informea'); ?>" />
		<h1><?php _e('Highlights', 'informea'); ?></h1>
		<h2><?php _e('News &amp; highlights', 'informea'); ?></h2>
	</div>
	<div id="content" role="main">
		<?php include(dirname(__FILE__) . '/portlet.rss.php');?>
		<div class="left details-column-1">
			<?php include(dirname(__FILE__) . '/portlet.search.php');?>
			<?php
				if(isset($treaty_ids)){
					if (!isset($cloud_items_nr)){
						$cloud_items_nr = 15;
					}

					$cloud_terms = $page_data->get_popular_terms_for_ids($treaty_ids, $cloud_items_nr);
			?>
				<div class="highlights-browse-terms-cloud">
					<?php include(dirname(__FILE__) . '/../portlet.terms-cloud.inc.php'); ?>
				</div>
			<?php
				}
			?>
		<div id="highlight_events">
			<?php
			    include(dirname(__FILE__) . '/../portlet.events.inc.php');
			?>
		</div>
		</div>
		<div class="left details-column-2">
			<?php include(dirname(__FILE__) . '/inc.select_topic.php'); ?>
			<div class="entry-content">
				<h3><?php echo $query_category->title; ?></h3>
			<?php
			if(count($posts->posts)) {
			?>
				<ul class="highlight-stories">
				<?php
				foreach($posts->posts as $ni) {
					$permalink = $ni->permalink;
					$target = 'target="_blank"';
					$summary = $ni->summary;
					$show_img = strpos($summary, 'img') <= 0;
					$is_local = strpos($permalink, $imea_url);
					if(is_int($is_local) && $is_local >= 0 ) {
						$target = '';
					}
					include(dirname(__FILE__) . '/inc.highlight.php');
				}
				?>
				</ul>
				<?php
					if($posts->max_num_pages > 1 && $posts->max_num_pages > $p) {
				?>
					<a class="link" href="<?php bloginfo('url');?>/highlights/<?php echo $query_category->slug; ?>/<?php echo ($page + 1); ?>">&laquo; Older</a>
				<?php
					}
				?>
				<?php
					if($p > 0) { ?>
					<a class="link right" href="<?php bloginfo('url');?>/highlights/<?php echo $query_category->slug; ?>/<?php echo ($page - 1); ?>">Newer &raquo;</a>
				<?php } ?>
		<?php
			} else {
		?>
				<br />
				<p>
				There are no highlights available in this category yet. You may be interested in other topics:
				<ul>
					<?php foreach($page_data->non_empty_categories as $c) { ?>
						<li><a href="<?php bloginfo('url'); ?>/highlights/<?php echo $c->slug; ?>"><?php echo $c->title; ?></a></li>
					<?php } ?>
				</ul>
				</p>
		<?php
			}
		?>
			</div>
		</div>
	</div><!-- #content -->
</div><!-- #container -->


