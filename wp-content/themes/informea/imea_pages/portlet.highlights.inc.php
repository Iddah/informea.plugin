<?php
	$related_news = new StdClass();
	$highlights = new imea_highlights_page();
	if(isset($treaty)) {
		$ob = new StdClass();
		$ob->slug = $treaty->short_title;
		$related_news = $highlights->get_category_posts($ob, 5);
	} else {
		$related_news->posts = $highlights->get_index_news(1);
	}
	if(!empty($related_news->posts)) {
?>
	<div class="portlet">
		<div class="pre-title">
			<div class="title rss">
				<span>
					<?php _e('MEA Highlights', 'informea'); ?>
					<a href="<?php echo bloginfo('url')."/highlights/rss"?>">
						<img alt="RSS Subscription" title="RSS Subscription" src="<?php bloginfo('template_directory'); ?>/images/rss.png" class="icon" />
					</a>
				</span>
			</div>
		</div>
		<div class="content">
			<ul>
		<?php
			$posts = $related_news->posts;
			$count = 0;
			foreach($posts as $item) {
		?>
				<li>
				<?php if($item->has_image) { ?>
					<img src="<?php echo $item->image; ?>" title="<?php esc_attr_e($item->source); ?>" />
				<?php } ?>
					<div class="item">
						<a target="_blank" href="<?php echo $item->permalink; ?>" rel="bookmark"
							title="<?php the_title_attribute(); ?>. This link will open into a new window"><?php echo subwords($item->title, 8); ?></a>
					</div>
					<div class="clear"></div>
				</li>
		<?php
				$count ++;
			}
		?>
			</ul>
		</div>
	</div>
<?php } ?>
