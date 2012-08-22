<?php
	header('Content-Type: application/rss+xml');
	$base_url = get_bloginfo('url');
	$max_pub_date = 0;
	$debug = get_request_value('debug');

	$w = new InformeaRSSWriter();
	$w->set_channel_field('title', 'InforMEA - Highlights');
	$w->set_channel_field('link', $base_url . '/events');
	$w->set_channel_field('self', $base_url . '/highlights/rss');
	$w->set_channel_field('description', 'The United Nations Environmental Law and Conventions Portal');
	$w->set_feed_image($base_url . '/wp-content/themes/informea/images/logo-black.png', 'InforMEA logo', $base_url, 80, 67);

	$guids = array();
	$errors = array();
	$posts = $page_data->get_rss_posts();
	foreach ($posts as $highlight) {
		$guid = $highlight->permalink;
		if(in_array($guid, $guids)) {
			$errors[] = $highlight;
			continue;
		} else {
			$guids[] = $guid;
		}
		if(strpos($guid, 'htts://') !== false) { // Broken link in CBD feed
			$errors[] = $highlight;
			continue;
		}
		if(strpos($highlight->image, 'http://') !== false) {
			$img_src = $highlight->image;
		} else {
			$img_src = $base_url . '/wp-content/uploads/pictures/highlight_thumbnail/' . $highlight->image;
		}
		//$title = htmlentities($highlight->title, ENT_NOQUOTES, 'UTF-8');
		$title = esc_html(strip_tags($highlight->title));
		//var_dump($highlight);
		$link = $highlight->permalink;
		$pubDate = date('r', strtotime($highlight->date));
		$max_pub_date = max($highlight->date, $max_pub_date);
		$description = '&#60;p>&#60;a href="' . $link . '">&#60;img src="' . $img_src . '" width="75" height="75" align="left" style="border: 0; padding: 5px;" />&#60;/a><![CDATA[' . $highlight->summary . ']]>&#60;/p>';
		$w->add_item($guid, $title, $link, $pubDate, $highlight->categories, $description);
	}
	$w->set_channel_field('pubDate', date('r'));
	echo $w->get_rss();
	if($debug) {
		var_dump($errors);
	}
?>
