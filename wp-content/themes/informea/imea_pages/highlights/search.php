<div id="container">
	<div id="page-title">
		<img src="<?php bloginfo('template_directory'); ?>/images/icon-highlights-large.png" alt="" title="<?php _e('Highlights', 'informea'); ?>" />
		<h1><?php _e('Highlights', 'informea'); ?></h1>
		<h2><?php _e('News &amp; highlights', 'informea'); ?></h2>
	</div>
	<div id="content" role="main">
		<div class="left details-column-1">
			<?php include(dirname(__FILE__) . '/portlet.search.php');?>
			<?php include(dirname(__FILE__) . '/portlet.rss.php');?>
		</div>
		<div class="left details-column-2">
			<div class="entry-content">
				<h3><?php _e('Search results', 'informea'); ?></h3>
		<?php
			if(count($posts->posts)) {
		?>
			<ul class="highlight-stories">
		<?php
			$imea_url = get_bloginfo('url');
			foreach($posts->posts as $ni) {
				$summary = $ni->summary;
				$show_img = strpos($summary, 'img') <= 0;
				$permalink = $ni->permalink;
				$target = 'target="_blank" ';
				$is_local = strpos($permalink, $imea_url);
				if(is_int($is_local) && $is_local >= 0 ) {
					$target = '';
				}
				include(dirname(__FILE__) . '/inc.highlight.php');
			} ?>
			</ul>
			<?php
				if($posts->max_num_pages > 1 && $posts->max_num_pages > $p) {
			?>
				<a class="link" href="<?php bloginfo('url');?>/highlights/?highlight_search=<?php echo $highlight_search; ?>&highlight_month=<?php echo $highlight_month; ?>&highlight_year=<?php echo $highlight_year; ?>&page=<?php echo ($page + 1); ?>">&laquo; Older</a>
			<?php
				}
			?>
			<?php
				if($p > 0) { ?>
				<a class="link right" href="<?php bloginfo('url');?>/highlights/?highlight_search=<?php echo $highlight_search; ?>&highlight_month=<?php echo $highlight_month; ?>&highlight_year=<?php echo $highlight_year; ?>&page=<?php echo ($page - 1); ?>">Newer &raquo;</a>
			<?php } ?>
		<?php } else { ?>
			<br />
			<p>
			No results found for your search query.
			</p>
		<?php } ?>
			</div>
		</div>
		<div class="left details-column-3">
		</div>
	</div><!-- #content -->
</div><!-- #container -->


