<?php
class InformeaRSSWriter {
    protected $channel_fields = array('title' => '', 'link' => '',
        'description' => '', 'pubDate' => '', 'image' => '',
        'language' => 'en-gb', 'self' => '');
    protected $items = array();

    public function set_channel_field($name, $value) {
        $this->channel_fields[$name] = $value;
    }

    public function set_feed_image($url, $title, $link = null, $width = 0, $height = 0) {
        if ($url !== null) {
            $ret = "<image>\n";
            if ($title !== null) {
                $ret .= "<title>$title</title>\n";
            }
            if ($width !== null) {
                $ret .= "<width>$width</width>\n";
            }
            if ($height !== null) {
                $ret .= "<height>$height</height>\n";
            }
            if ($link !== null) {
                $ret .= "<link>$link</link>\n";
            }
            if ($link !== null) {
                $ret .= "<url>$link</url>\n";
            }
            $ret .= "</image>\n";
            $channel_fields['image'] = $ret;
        }
    }

    public function add_item($guid, $title, $link, $pubDate, $categories = array(), $description = '') {
        $ret = "	<item>\n";
        if (strpos($guid, 'http') === 0) {
            $ret .= sprintf("		<guid>%s</guid>\n", esc_attr($guid));
        } else {
            $ret .= sprintf("		<guid isPermaLink=\"false\">%s</guid>\n", esc_attr($guid));
        }
        $ret .= sprintf("		<title>%s</title>\n", esc_attr($title));
        if(!empty($description)) {
            $description = str_replace('<![CDATA[', '', $description);
            $description = str_replace(']]>', '', $description);
            $description = esc_attr($description);
            $ret .= sprintf("		<description><![CDATA[%s]]></description>\n", $description);
        }
        $ret .= sprintf("		<link>%s</link>\n", $link);
        if (!empty($categories)) {
            foreach ($categories as $cat) {
                $ret .= sprintf("		<category>%s</category>\n", esc_attr($cat));
            }
        } else {
            $ret .= "		<category>Uncategorized</category>\n";
        }
        $ret .= "		<pubDate>$pubDate</pubDate>\n";
        $ret .= "	</item>\n";
        $this->items[] = $ret;
    }

    public function get_rss() {
        $self = $this->channel_fields['self'];
        $ret = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
        $ret .= "<rss version=\"2.0\" xmlns:atom=\"http://www.w3.org/2005/Atom\">\n";
        $ret .= "<channel>\n";
        $ret .= "	<title>" . $this->channel_fields['title'] . "</title>\n";
        $ret .= "	<link>" . $this->channel_fields['link'] . "</link>\n";
        $ret .= !empty($self) ? "	<atom:link href=\"$self\" rel=\"self\" type=\"application/rss+xml\" />\n" : '';
        $ret .= "	<description>" . $this->channel_fields['description'] . "</description>\n";
        $ret .= "	<language>" . $this->channel_fields['language'] . "</language>\n";
        $ret .= "	<pubDate>" . $this->channel_fields['pubDate'] . "</pubDate>\n";
        $ret .= "	<generator>InformeaRSSWriter v1.0</generator>\n";
        $ret .= implode("\n", $this->items);
        $ret .= "</channel>\n</rss>";
        return $ret;
    }


    public static function meetings_rss() {
        $page_data = new informea_meetings();
        header('Content-Type: application/rss+xml');
        $base_url = get_bloginfo('url');
        $meetings = informea_meetings::get_meetings_rss();
        $countries_data = new imea_countries_page(1, array());
        $max_pub_date = 0;

        $w = new InformeaRSSWriter();
        $w->set_channel_field('title', 'InforMEA - Meetings');
        $w->set_channel_field('link', $base_url . '/meetings');
        $w->set_channel_field('self', $base_url . '/meetings/rss/');
        $w->set_channel_field('description', 'The United Nations Environmental Law and Conventions Portal');
        $w->set_feed_image($base_url . '/wp-content/themes/informea/images/logo-black.png', 'InforMEA logo', $base_url, 80, 67);

        foreach ($meetings as $meeting) {
            if ($meeting->event_url) {
                $link = $meeting->event_url;
            } else {
                list($year, $month, $day) = explode('-', $meeting->start);
                $link = get_bloginfo('url') . "/meetings?filter=Search&amp;fe_month=" . $month . "&amp;fe_year=" . $year . "&amp;id=" . $meeting->id;
            }
            // RSS does not really supports meetings (without namespace extension). So we put pubDate as event start date
            $pubDate = date('r', strtotime($meeting->rec_created));

            $max_pub_date = max($meeting->rec_created, $max_pub_date);

            $image_url = '';
            if ($meeting->image) {
                $image_url = $meeting->image;
            } else {
                if ($meeting->logo_medium) {
                    $image_url = $meeting->logo_medium;
                }
            }
            $status = $page_data->decode_status($meeting->status);
            $description = '&#60;p>';
            if ($image_url) {
                $description .= '&#60;a href=' . $link . '>&#60;img src=' . $image_url . ' align="left" style="border: 0; padding: 5px;" />&#60;/a>';
            }
            if ($meeting->description) {
                $description .= "<![CDATA[" . $meeting->description . "]]>";
            }
            $description .= '&#60;/p>';
            $description .= '&#60;strong class="event-interval">' . show_event_interval($meeting) . $page_data->event_place($meeting);
            if ($meeting->id_country) {
                $country = $countries_data->get_country_for_id($meeting->id_country);
                if ($country) {
                    $description .= " - " . $country->name;
                }
            }
            $description .= '&#60;/strong>';
            if (!empty($status)) {
                $description .= ' (' . $status . ')';
            }
            $description .= '&#60;br />Source: &#60;strong>' . $meeting->short_title . '&#60;/strong>';
            if (!empty($status)) {
                $description .= '&#60;br />Status: &#60;strong>' . $status . '&#60;/strong>';
            }
            if (!empty($meeting->location)) {
                $description .= '&#60;br />Location: &#60;strong>' . $meeting->location . '&#60;/strong>';
            }
            if (!empty($meeting->city)) {
                $description .= '&#60;br />City: &#60;strong>' . $meeting->city . '&#60;/strong>';
            }
            if ($image_url) {
                $description .= '&#60;br />&#60;a href=' . $link . '>Visit event page&#60;/a>';
            }
            $guid = 'informea-org-event-id-' . $meeting->id;
            $categories = array('Meetings');
            if(!empty($meeting->short_title)) {
                $categories[] = $meeting->short_title;
            }
            $w->add_item($guid, $meeting->title, $link, $pubDate, $categories, $description);
        }
        $w->set_channel_field('pubDate', date('r', strtotime($max_pub_date)));
        echo $w->get_rss();
    }


    public static function news_rss() {
        $page_data = new imea_news_page();
        header('Content-Type: application/rss+xml');
        $base_url = get_bloginfo('url');
        $max_pub_date = 0;
        $w = new InformeaRSSWriter();
        $w->set_channel_field('title', 'InforMEA - News');
        $w->set_channel_field('link', $base_url . '/meetings');
        $w->set_channel_field('self', $base_url . '/news/rss/');
        $w->set_channel_field('description', 'The United Nations Environmental Law and Conventions Portal');
        $w->set_feed_image($base_url . '/wp-content/themes/informea/images/logo-black.png', 'InforMEA logo', $base_url, 80, 67);
        $guids = array();
        $errors = array();
        $posts = $page_data->get_rss_posts();
        foreach ($posts as $highlight) {
            $guid = $highlight->permalink;
            if (in_array($guid, $guids)) {
                $errors[] = $highlight;
                continue;
            } else {
                $guids[] = $guid;
            }
            if (strpos($guid, 'htts://') !== false) { // Broken link in CBD feed
                $errors[] = $highlight;
                continue;
            }
            if (strpos($highlight->image, 'http://') !== false) {
                $img_src = $highlight->image;
            } else {
                $img_src = $base_url . '/wp-content/uploads/pictures/highlight_thumbnail/' . $highlight->image;
            }
            $title = esc_html(strip_tags($highlight->title));
            $link = $highlight->permalink;
            $pubDate = date('r', strtotime($highlight->date));
            $max_pub_date = max($highlight->date, $max_pub_date);
            $description = '&#60;p>&#60;a href="' . $link . '">&#60;img src="' . $img_src . '" width="75" height="75" align="left" style="border: 0; padding: 5px;" />&#60;/a><![CDATA[' . $highlight->summary . ']]>&#60;/p>';
            $w->add_item($guid, $title, $link, $pubDate, $highlight->categories, $description);
        }
        $w->set_channel_field('pubDate', date('r'));
        echo $w->get_rss();
    }
}
