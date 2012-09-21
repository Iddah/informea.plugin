<?php
/**
 * @package InforMEA
 * @subpackage Theme
 * @since 1.0
 */
$evt_class = new imea_events_page();
$page_data = new imea_index_page();
$terms_ob = new Thesaurus(null);
$slider_news = $page_data->get_slider_news();

$highlights_ob = new imea_highlights_page();
$highlights = $highlights_ob->get_index_news(1);
$highlights = array_slice($highlights, 0, 3);
$search2 = new InformeaSearch2($_GET);

$countries_data = new imea_countries_page(null);
$featured_country = $countries_data->get_featured_country();
?>

<?php
	function js_inject_index_treaty_arrays() {
		global $search2;
?>
	<script type="text/javascript">
		var arr_treaties_biodiversity = [<?php echo implode(',', $search2->ui_get_biodiversity_ids()); ?>];
		var arr_treaties_chemicals = [<?php echo implode(',', $search2->ui_get_chemicals_ids()); ?>];
		var arr_treaties_climate = [<?php echo implode(',', $search2->ui_get_climate_ids()); ?>];
		var arr_treaties_other = [<?php echo implode(',', $search2->ui_get_other_treaties()); ?>];
	</script>
<?php

	}
	add_action('js_inject', 'js_inject_index_treaty_arrays');

?>

<div class="col3-left col3">
	<div>
		<div class="index-custom-title">What is InforMEA?</div>
		<p class="justify">
			InforMEA harvests COP decisions, news, events, membership, national
			focal points and reports from MEAs. Information is organised by terms
			from MEA COP agendas. InforMEA is a project of the MEA Information and
			Knowledge Management (IKM) Initiative with the support from the United
			Nations Environment Programme (UNEP).
		</p>
	</div>
	<div class="clear"></div>
	<a href="http://informea.org/about?tab=4767">New Members Join InforMEA</a>
	<div class="clear"></div>
	<div class="portlet">
		<div class="pre-title">
			<div class="title">
				<span><?php _e('Latest news', 'informea'); ?></span>
			</div>
		</div>
		<div class="content">
			<ul>
			<?php
				foreach($highlights as $news_item) {
					$permalink = $news_item->permalink;
					$target = 'target="_blank"';
					$is_local = strpos($permalink, get_bloginfo('url'));
					if(is_int($is_local) && $is_local >= 0 ) {
						$target = '';
					}
			?>
				<li>
					<img alt="image" src="<?php echo $news_item->image; ?>" />
					<div class="item">
						<?php echo $news_item->date_formatted; ?>
						<br />
						<a title="<?php echo $news_item->title; ?>. Click to read the full story" rel="bookmark" href="<?php echo $news_item->permalink; ?>" <?php echo $target; ?> class="news-title"><?php echo subwords($news_item->title, 5); ?></a>
						<br />
						<?php _e('Source:', 'informea'); ?> <strong><?php echo $news_item->source; ?></strong>
					</div>
					<div class="clear"></div>
				</li>
			<?php
				}
			?>
			</ul>
		</div>
	</div>

	<div class="clear"></div>
	<div class="portlet">
		<div class="pre-title">
			<div class="title">
				<span><?php _e('Featured Tools', 'informea'); ?></span>
			</div>
		</div>
		<div class="featured content">
			<ul>
				<li><a href="http://geg.informea.org/" target="_blank">GEGs Website</a></li>
				<li><a href="http://www.cites.org/eng/news/sundry/2011/20110607_VC.shtml" target="_blank">CITES Virtual College</a></li>
				<li><a href="http://ecolex.org/" target="_blank">ECOLEX</a></li>
			</ul>
		</div>
	</div>


</div>
<div class="col3-center col3">
	<?php include(dirname(__FILE__) . '/index.search.php'); ?>
	<div id="lofslidecontent45" class="lof-slidecontent" style="width:480px; height:200px;">
		<div class="preload">
			<div></div>
		</div>

		<div class="lof-main-outer" style="width:480px; height:225px;">
		<?php
			if(count($slider_news)) { //Slider pictures
			shuffle($slider_news);
		?>
		<ul class="lof-main-wapper">
			<?php foreach ($slider_news as $news ) { ?>
			<li>
				<a href="<?php echo bloginfo('url')."/highlights"?>"><img src="<?php echo $news->image_url ;?>" title="<?php echo esc_attr($news->image_title . '. &copy ' . $news->image_copyright); ?>" /></a>
				<?php if($news->has_content) { ?>
				<div class="lof-main-item-desc">
					<h3><?php _e('News', 'informea'); ?></h3> - <span class="italic"><?php echo $news->date; ?></span>
					<p>
					<?php echo $news->title; ?>&nbsp;&nbsp;<a target="_blank" href="<?php echo $news->url; ?>"><?php _e('read more &raquo;', 'informea'); ?> </a>
					</p>
				</div>
				<?php } ?>
			</li>
			<?php } ?>
		</ul>
		</div>
		<div class="lof-navigator-wapper">
			<div class="lof-navigator-outer">
				<ul class="lof-navigator">
					<?php foreach(range(1, count($slider_news)) as $no) { ?>
					<li><span><?php echo $no; ?></span></li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<?php } ?>
	</div>
	<div class="clear"></div>
	<div class="spotlight">
	    <!-- removed title for spotlights -->
		<!--div class="box-title">
			<span><?php _e('In the spotlight', 'informea'); ?></span>
		</div-->
		<div class="box-content">
			<?php
				$hob = new imea_highlights_page();
				$categories = $hob->get_categories();
			?>
			<div id="highlight-topics">
				<div class="row">
					<div class="row-item left">
						<?php $h = $categories['climate-change']; ?>
						<a class="link" href="<?php echo $h->link; ?>"><img src="<?php echo $h->image; ?>" style="width: 100px; height: 100px;" /></a>
						<div><a class="link" href="<?php echo $h->link; ?>"><?php echo $h->title; ?></a></div>
					</div>
					<div class="row-item left">
						<?php $h = $categories['biological-diversity']; ?>
						<a class="link" href="<?php echo $h->link; ?>"><img src="<?php echo $h->image; ?>" style="width: 100px;" /></a>
						<div><a class="link" href="<?php echo $h->link; ?>"><?php echo $h->title; ?></a></div>
					</div>
					<div class="row-item left">
						<?php $h = $categories['species']; ?>
						<a class="link" href="<?php echo $h->link; ?>"><img src="<?php echo $h->image; ?>" style="width: 100px;" /></a>
						<div><a class="link" href="<?php echo $h->link; ?>"><?php echo $h->title; ?></a></div>
					</div>
					<div class="row-item left">
						<?php $h = $categories['wetlands-national-heritage-sites']; ?>
						<a class="link" href="<?php echo $h->link; ?>"><img src="<?php echo $h->image; ?>" style="width: 100px;" /></a>
						<div><a class="link" href="<?php echo $h->link; ?>"><?php echo $h->title; ?></a></div>
					</div>
				</div>
				<div class="clear"></div>
				<div class="row second">
					<div class="row-item left">
						<?php $h = $categories['chemicals-waste']; ?>
						<a class="link" href="<?php echo $h->link; ?>"><img src="<?php echo $h->image; ?>" style="width: 100px;" /></a>
						<div><a class="link" href="<?php echo $h->link; ?>"><?php echo $h->title; ?></a></div>
					</div>
					<div class="row-item left">
						<?php $h = $categories['international-cooperation']; ?>
						<a class="link" href="<?php echo $h->link; ?>"><img src="<?php echo $h->image; ?>" style="width: 100px;" /></a>
						<div><a class="link" href="<?php echo $h->link; ?>"><?php echo $h->title; ?></a></div>
					</div>
					<div class="row-item left">
						<?php $h = $categories['financing-trade']; ?>
						<a class="link" href="<?php echo $h->link; ?>"><img src="<?php echo $h->image; ?>" style="width: 100px;" /></a>
						<div><a class="link" href="<?php echo $h->link; ?>"><?php echo $h->title; ?></a></div>
					</div>
					<div class="row-item left">
						<?php $h = $categories['drylands']; ?>
						<a class="link" href="<?php echo $h->link; ?>"><img src="<?php echo $h->image; ?>" style="width: 100px;" /></a>
						<div><a class="link" href="<?php echo $h->link; ?>"><?php echo $h->title; ?></a></div>
					</div>
				</div>
				<div class="clear"></div>
			</div>
		</div>
	</div>
</div>

<div class="col3-right col3">
	<?php
		$top_concepts = $terms_ob->get_top_concepts();
		$popular_terms = $page_data->get_popular_terms(NULL, 2);
		$cloud_terms = array_merge($top_concepts, $popular_terms);

		include(dirname(__FILE__) . '/portlet.terms-cloud.inc.php');
	?>

	<div class="clear"></div>
	<div class="portlet calendar-frontpage">
		<div class="pre-title">
			<div class="title"><?php _e('Calendar of events', 'informea'); ?></div>
		</div>
		<div class="content">
			<?php include_once(dirname(__FILE__) . '/events/calendar.inc.php'); ?>
			<div class="clear"></div>
		</div>
	</div>

	<div class="clear"></div>
	<div class="portlet">
		<div class="pre-title">
			<div class="title"><?php _e('Featured Country', 'informea'); ?></div>
		</div>
		<div id="country_portlet" class="country content">
			<h3><?php echo $featured_country->name?></h3>
			<img title="<?php echo $featured_country->name?>" alt="" src="<?php echo bloginfo('url')."/".$featured_country->icon_large?>">
			<div class="clear">
				<a href="<?php echo bloginfo('url')."/countries/". $featured_country->id ?>">view profile</a>
			</div>
		</div>
	</div>


<?php if(false) { ?>
	<div class="related-box events front-page">
		<div class="box-content">
			<ul>
			<?php
				$mea_events = $page_data->get_meetings();
				foreach($mea_events as $event) {
					$interval = show_event_interval($event);
					$image = !empty($event->image) ? $event->image : $event->logo_medium;
			?>
				<li>
				<?php if(!empty($image)) { ?>
					<img align="left" class="right-portlet-image" src="<?php echo $image; ?>" <?php echo (!empty($event->image_copyright)) ? 'title="' . esc_attr($event->image_copyright) . '" ' : '';?>/>
				<?php } ?>
					<p>
						<strong><?php echo $interval; ?></strong>
						<br />
						<?php
							if(empty($event->event_url)) {
								echo $event->title;
							} else {
						?>
					<a class="link" target="_blank" href="<?php echo $event->event_url; ?>"><?php echo $event->title; ?></a>
					<?php } ?>
					</p>
				</li>
				<?php } ?>
			</ul>
			<a href="<?php echo bloginfo('url')."/events"?>" class="more-link">More events &raquo;</a>
		</div>
	</div>
	<div class="clear"></div>
<?php } ?>
</div>
<div class="clear"></div>
