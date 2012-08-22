<?php
	header('Content-Type: application/rss+xml');
	$base_url = get_bloginfo('url');
	$events = $page_data->get_events_list();
	$countries_data = new imea_countries_page(1, array());
	$max_pub_date = 0;

	$w = new InformeaRSSWriter();
	$w->set_channel_field('title', 'InforMEA - Events');
	$w->set_channel_field('link', $base_url . '/events');
	$w->set_channel_field('self', $base_url . '/events/rss');
	$w->set_channel_field('description', 'The United Nations Environmental Law and Conventions Portal');
	$w->set_feed_image($base_url . '/wp-content/themes/informea/images/logo-black.png', 'InforMEA logo', $base_url, 80, 67);

	$link = "";
	foreach ($events as $event) {
		if ($event->event_url) {
			$link = $event->event_url;
		} else {
			list($year, $month, $day) = explode('-', $event->start);
			$link = get_bloginfo('url')."/events?filter=Search&amp;fe_month=".$month."&amp;fe_year=".$year."&amp;id=".$event->id;
		}
		// RSS does not really supports events (without namespace extension). So we put pubDate as event start date
		$pubDate = date('r', strtotime($event->rec_created));

		$max_pub_date = max($event->rec_created, $max_pub_date);

		$image_url = '';
		if ($event->image){
			$image_url = $event->image;
		} else {
			if ($event->logo_medium) {
				$image_url = $event->logo_medium;
			}
		}

		$status = $page_data->decode_status($event->status);
		$description = '&#60;p>';
		if ($image_url) {
			$description .= '&#60;a href='.$link.'>&#60;img src='.$image_url.' align="left" style="border: 0; padding: 5px;" />&#60;/a>';
		}
		if ($event->description) {
			$description .= "<![CDATA[".$event->description."]]>";
		}
		$description .= '&#60;/p>';
		$description .= '&#60;strong class="event-interval">' . show_event_interval($event) . $page_data->event_place($event);
		if($event->id_country) {
			$country = $countries_data->get_country_for_id($event->id_country);
			if($country) {
				$description .= " - " . $country->name;
			}
		}
		$description .= '&#60;/strong>';
		if(!empty($status)) {
			$description .= ' (' . $status . ')';
		}
		$description .= '&#60;br />Source: &#60;strong>' . $event->short_title . '&#60;/strong>';
		if(!empty($status)) {
			$description .= '&#60;br />Status: &#60;strong>' . $status . '&#60;/strong>';
		}
		if(!empty($event->location)) {
			$description .= '&#60;br />Location: &#60;strong>' . $event->location . '&#60;/strong>';
		}
		if(!empty($event->city)) {
			$description .= '&#60;br />City: &#60;strong>' . $event->city . '&#60;/strong>';
		}
		if ($image_url) {
			$description .= '&#60;br />&#60;a href='.$link.'>Visit event page&#60;/a>';
		}


		$guid = 'informea-org-event-id-' . $event->id;
		$categories = array($event->short_title, 'Events');

		$w->add_item($guid, $event->title, $link, $pubDate, $categories, $description);
	}
	$w->set_channel_field('pubDate', date('r', strtotime($max_pub_date)));

	echo $w->get_rss();
?>
