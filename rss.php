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
		if($url !== null) {
			$ret = "<image>\n";
			if($title !== null) {
				$ret .= "<title>$title</title>\n";
			}
			if($width !== null) {
				$ret .= "<width>$width</width>\n";
			}
			if($height !== null) {
				$ret .= "<height>$height</height>\n";
			}
			if($link !== null) {
				$ret .= "<link>$link</link>\n";
			}
			if($link !== null) {
				$ret .= "<url>$link</url>\n";
			}
			$ret .= "</image>\n";
			$channel_fields['image'] = $ret;
		}
	}

	public function add_item($guid, $title, $link, $pubDate, $categories = array(), $description = '') {
		$ret = "	<item>\n";
		if(strpos($guid, 'http') === 0) {
			$ret .= "		<guid>$guid</guid>\n";
		} else {
			$ret .= "		<guid isPermaLink=\"false\">$guid</guid>\n";
		}
		$ret .= "		<title>$title</title>\n";
		$ret .= (!empty($description)) ? "		<description>$description</description>\n" : '';
		$ret .= "		<link>$link</link>\n";
		if(!empty($categories)) {
			foreach($categories as $cat) {
				$ret .= "		<category>$cat</category>\n";
			}
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
}
