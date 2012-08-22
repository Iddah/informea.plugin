<?php
/**
 * This class is the data provider for the 'Countries' section of the site.
 * @package InforMEA
 * @subpackage Theme
 * @since 1.0
 */

if(!class_exists( 'imea_highlights_page')) {
require_once (dirname(__FILE__) . '/page_base.class.php');
class imea_highlights_page extends imea_page_base_page {

	public $categories;
	public $non_empty_categories;
	public $feed_mea; // Links between Feed and MEA (name)
	public $feeds; // Feeds by id
	public $mea_categories;
	public $mea_categories_slugs;
	private $treaties;
	private $categories_treaties;

	function __construct() {
		$this->categories = array();

		$ret = array();
		$ob = new StdClass();
		$ob->title = "Climate &amp; Atmosphere";
		$ob->slug = "climate-change";
		$ob->link = get_bloginfo('url') . '/highlights/' . $ob->slug;
		$ob->image = get_bloginfo('template_directory') . '/images/spotlight/climate-change.jpg' ;
		$ob->treaties = '6,7,15';
		$this->categories[$ob->slug] = $ob;

		$ob = new StdClass();
		$ob->title = "Biological Diversity";
		$ob->slug = "biological-diversity";
		$ob->link = get_bloginfo('url') . '/highlights/' . $ob->slug;
		$ob->image = get_bloginfo('template_directory') . '/images/spotlight/other-biodiversity.jpg' ;
		$ob->treaties = '1,6,9,3,4,10,16,18,14';
		$this->categories[$ob->slug] = $ob;

		$ob = new StdClass();
		$ob->title = "Species";
		$ob->slug = "species";
		$ob->link = get_bloginfo('url') . '/highlights/' . $ob->slug;
		$ob->image = get_bloginfo('template_directory') . '/images/spotlight/species.jpg' ;
		$ob->treaties = '3,4,14,10';
		$this->categories[$ob->slug] = $ob;

		$ob = new StdClass();
		$ob->title = "Wetlands &amp; National Heritage Sites";
		$ob->slug = "wetlands-national-heritage-sites";
		$ob->link = get_bloginfo('url') . '/highlights/' . $ob->slug;
		$ob->image = get_bloginfo('template_directory') . '/images/spotlight/wetlands.jpg' ;
		$ob->treaties = '16,18';
		$this->categories[$ob->slug] = $ob;

		$ob = new StdClass();
		$ob->title = "Chemicals &amp; Waste";
		$ob->slug = "chemicals-waste";
		$ob->link = get_bloginfo('url') . '/highlights/' . $ob->slug;
		$ob->image = get_bloginfo('template_directory') . '/images/spotlight/biohazard.png' ;
		$ob->treaties = '2,20,5';
		$this->categories[$ob->slug] = $ob;

		$ob = new StdClass();
		$ob->title = "International Cooperation";
		$ob->slug = "international-cooperation";
		$ob->link = get_bloginfo('url') . '/highlights/' . $ob->slug;
		$ob->image = get_bloginfo('template_directory') . '/images/spotlight/cooperation.jpg' ;
		$ob->treaties = false;
		$this->categories[$ob->slug] = $ob;

		$ob = new StdClass();
		$ob->title = "Financing &amp; Trade";
		$ob->slug = "financing-trade";
		$ob->link = get_bloginfo('url') . '/highlights/' . $ob->slug;
		$ob->image = get_bloginfo('template_directory') . '/images/spotlight/trade.png' ;
		$ob->treaties = '3,20,14,8,2';
		$this->categories[$ob->slug] = $ob;

		$ob = new StdClass();
		$ob->title = "Drylands";
		$ob->slug = "drylands";
		$ob->link = get_bloginfo('url') . '/highlights/' . $ob->slug;
		$ob->image = get_bloginfo('template_directory') . '/images/spotlight/desertification.jpg' ;
		$ob->treaties = '19';
		$this->categories[$ob->slug] = $ob;

		// Store treaties, to show treaty logo whenever we don't have a picture for a post in the highlights section,
		// but that post has a treaty attached to it, as category. Try to match category with treaty name
		$tmp = new imea_treaties_page(NULL);
		$treaties = $tmp->get_treaties();
		foreach($treaties as $treaty) {
			$this->treaties[slugify($treaty->short_title)] = $treaty;
		}


		$this->non_empty_categories = array();
		foreach($this->categories as $cat) {
			$news = $this->get_category_posts($cat, 1);
			if(count($news->posts)) {
				$this->non_empty_categories[] = $cat;
			}
		}

		$this->feed_mea = array();
		$this->feeds = array();
		$this->mea_categories = array();
		$this->mea_categories_slugs = array();
		$cat_meas = get_category_by_slug('meas');
		// Get all MEA categories
		if($cat_meas) {
			$cat_meas = $cat_meas->cat_ID;
			$cat_meas = get_categories(array('hide_empty' => 0, 'name' => 'category_parent', 'parent' => $cat_meas));
			foreach($cat_meas as $cat_mea) {
				$this->mea_categories[$cat_mea->cat_ID] = $cat_mea->name;
				$this->mea_categories_slugs[$cat_mea->cat_ID] = $cat_mea->slug;
			}
		}
		foreach (FeedWordPress::syndicated_links() as $link) {
			$syndicated_link = new SyndicatedLink($link);
			if(!isset($syndicated_link->settings['cats'])) {
				continue; // This feed does not have any category set (wrongly configured)
			}
			$this->feeds[$syndicated_link->id] = $syndicated_link;
			$link_categories = $syndicated_link->settings['cats'];
			foreach($link_categories as $cat_str) {
				$cat_id = explode('#', $cat_str);
				$cat_id = explode('}', $cat_id[1]);
				if (isset($this->mea_categories[$cat_id[0]])) {
					$this->feed_mea[$syndicated_link->id] = $this->mea_categories[$cat_id[0]];
				}
			}
		}
	}

	function is_search() {
		$highlight_search = get_request_value('highlight_search');
		$highlight_month = get_request_value('highlight_month');
		$highlight_year = get_request_value('highlight_year');
		return isset($highlight_search) || isset($highlight_month) || isset($highlight_year);
	}


	function get_categories() {
		return $this->categories;
	}

	function get_category_by_slug($slug) {
		if(isset($this->categories[$slug])) {
			return $this->categories[$slug];
		}
		return null;
	}


	function search($highlight_search, $highlight_month, $highlight_year, $limit = 10, $page = 0) {
		global $post;
		$ret = new StdClass();
		$ret->posts = array();
		$args = array('post_date' => 'DATE(NOW())',
					'post_type' => 'post',
					'orderby' => 'post_date',
					'paged' => $page,
					's' => $highlight_search,
					'order' => 'DESC');
		if($highlight_month > 0) {
			$args['monthnum'] = $highlight_month;
		}
		if($highlight_year > 0) {
			$args['year'] = $highlight_year;
		}
		$args['posts_per_page'] = $limit;
		$wpq = new WP_Query($args);
		$ret->max_num_pages = $wpq->max_num_pages;
		while ($wpq->have_posts()) {
			$wpq->the_post();
			$ob = new StdClass();
			$ob->id = get_the_ID();
			$ob->permalink = get_permalink();
			$ob->title = get_the_title();
			$ob->has_image = FALSE;
			$ob->time = strtotime($post->post_date);
			$ob->date = $post->post_date;
			$ob->date_formatted = format_mysql_date($post->post_date, 'j M Y');
			$ob->summary = $post->post_excerpt;
			$ob->content = $post->post_content;
			$ob->source = NULL;
			$custom_fields = get_post_custom();
			$feed_id = $custom_fields['syndication_feed_id'];
			$feed_id = $feed_id[0];
			$ob->source = $this->get_post_source($feed_id, $post->ID);
			$ret->posts[] = $ob;
			$feed = null;
			if(isset($this->feeds[$feed_id])) {
				$feed = $this->feeds[$feed_id];
			}
			$ob->image = $this->get_post_image($feed, $post);
		}
		return $ret;
	}

	function get_category_posts($category, $limit = 10, $page = 0) {
		global $post;
		$ret = new StdClass();
		$ret->posts = array();
		$args = array('post_date' => 'DATE(NOW())',
					'post_type' => 'post',
					'category_name' => $category->slug,
					'orderby' => 'post_date',
					'order' => 'DESC');
		if(!empty($limit)) {
			$args['posts_per_page'] = $limit;
		}
		$wpq = new WP_Query($args);
		$ret->max_num_pages = $wpq->max_num_pages;
		while ($wpq->have_posts()) {
			$wpq->the_post();
			$ob = new StdClass();
			$ob->image = null;
			$ob->id = get_the_ID();
			$ob->permalink = get_permalink();
			$ob->title = get_the_title();
			$ob->has_image = FALSE;
			$ob->time = strtotime($post->post_date);
			$ob->date = $post->post_date;
			$ob->date_formatted = format_mysql_date($post->post_date, 'j M Y');
			$ob->summary = $post->post_excerpt;
			$ob->content = $post->post_content;
			$ob->source = NULL; // Default
			$custom_fields = get_post_custom();
			$feed_id = null;
			if(isset($custom_fields['syndication_feed_id'])) {
				$feed_id = $custom_fields['syndication_feed_id'];
				$feed_id = $feed_id[0];
			}
			$ob->source = $this->get_post_source($feed_id, $post->ID);
			if(!empty($feed_id)) {
				// echo_trace();
				$ob->image = $this->get_post_image(isset($this->feeds[$feed_id]) ? $this->feeds[$feed_id] : null, $post);
			} else {
				$ob->image = $this->get_post_image(null, $post);
			}
			$ob->has_image = !empty($ob->image);
			$ret->posts[] = $ob;
		}
		return $ret;
	}

	function get_post_source($feed_id, $post_id) {
		$ret = NULL;
		$custom_fields = get_post_custom($post_id);

		$post_categories = wp_get_post_categories($post_id);
		foreach($post_categories as $post_category) {
			if(is_array($this->mea_categories) && array_key_exists($post_category, $this->mea_categories)) {
				$ret = $this->mea_categories[$post_category];
				break;
			}
		}

		if(empty($ret) && !empty($feed_id)) {
			if(isset($this->feed_mea[$feed_id])) {
				$ob->source = $this->feed_mea[$feed_id];
			}
			// If we have no source, set source to the feed title
			$feed = new SyndicatedLink($feed_id);
			if(isset($feed->settings['link/name'])) {
				$ret = $feed->settings['link/name'];
			} else if(isset($feed->settings['link/name'])) {
				$ret = $feed->settings['feed/title'];
			}
		}

		// Last fallback for source
		if(empty($ret)) {
			$ret = 'Unknown';
		}
		return $ret;
	}


	function get_post_image($syndicated_link, $post) {
		global $post;
		// Try to get image from the post itself
		$tid = get_post_thumbnail_id($post->ID);
		if(!empty($tid)) {
			$arr = wp_get_attachment_image_src($tid, 'thumbnail', TRUE);
			if(!empty($arr)) {
				return $arr[0];
			}
		}

		// Fetch post's feature image, if exists
		$custom_fields = get_post_custom();
		if(array_key_exists('featured_img_url', $custom_fields)){
			if(is_array($custom_fields['featured_img_url'])) {
				if(count($custom_fields['featured_img_url'])) {
					shuffle($custom_fields['featured_img_url']);
					return $custom_fields['featured_img_url'][0];
				}
			} else {
				return $custom_fields['featured_img_url'];
			}
		}

		// Try to get image from the feed itself
		if(!empty($syndicated_link->settings['link_image'])) {
			return $syndicated_link->settings['link_image'];
		}

		// Try to get it from the treaty (if attached to a treaty through 'categories')
		$post_categories = wp_get_post_categories($post->ID);
		foreach($post_categories as $post_category) {
			if(is_array($this->mea_categories) && array_key_exists($post_category, $this->mea_categories)) {
				$post_slug = slugify($this->mea_categories[$post_category]);
				if(array_key_exists($post_slug, $this->treaties)) {
					$treaty = $this->treaties[$post_slug];
					return $treaty->logo_medium;
				}
			}
		}

		// Put the default empty image
		return get_bloginfo('template_directory') . '/images/organization/nologo.png';
	}


	function get_index_news($limit = 2) {
		$ret = array();
		foreach($this->mea_categories_slugs as $mea_category) {
			$ob = new StdClass();
			$ob->slug = $mea_category;
			$ob = $this->get_category_posts($ob, $limit);
			if(!empty($ob->posts)) {
				foreach($ob->posts as $p) {
					if(!array_key_exists($p->id, $ret)) {
						$ret[$p->id] = $p;
					}
				}
			}
		}
		// Sort posts by their date
		usort($ret, function($a, $b) {
			if($a->time == $b->time) {
				return 0;
			}
			return ($a->time < $b->time) ? 1 : -1;
		});
		return $ret;
	}


	function get_years_interval(){
		global $wpdb;
		$ob = new StdClass();
		$ob->min = 1990;
		$ob->max = intval(date('Y'));
		$row = $wpdb->get_row("SELECT MIN(YEAR(post_date)) as min_year, MAX(YEAR(post_date)) as max_year FROM {$wpdb->prefix}posts");
		$ob->min = intval($row->min_year);
		$ob->max = intval($row->max_year);
		return $ob;
	}

	function breadcrumbtrail() {
		global $query_category;
		if(isset($query_category)) {
			return " &raquo; <a href='". get_bloginfo('url') ."/highlights'>Highlights</a> &raquo; <span class='current'>{$query_category->title}</span>";
		} else {
			return " &raquo; <span class='current'>Highlights</span>";
		}
	}


	function get_rss_posts() {
		global $post;
		$ret = array();
		$args = array('post_date' => 'DATE(NOW())',
					'post_type' => 'post',
					'orderby' => 'post_date',
					'posts_per_page' => -1,
					'order' => 'DESC');
		$wpq = new WP_Query($args);
		while ($wpq->have_posts()) {
			$wpq->the_post();
			$ob = new StdClass();
			$ob->id = get_the_ID();
			$ob->permalink = get_permalink();
			$ob->title = get_the_title();
			$ob->has_image = FALSE;
			$ob->time = strtotime($post->post_date);
			$ob->date = $post->post_date;
			$ob->date_formatted = format_mysql_date($post->post_date, 'j M Y');
			$ob->summary = $post->post_excerpt;
			$ob->content = $post->post_content;
			$ob->source = NULL; // Default
			$ob->categories = array();
			$custom_fields = get_post_custom();
			$post_categories = wp_get_post_categories($ob->id);
			foreach($post_categories as $cat_id) {
				$cat = get_category( $cat_id );
				$ob->categories[] = $cat->name;
			}

			$feed_id = null;
			if(isset($custom_fields['syndication_feed_id'])) {
				$feed_id = $custom_fields['syndication_feed_id'];
				$feed_id = $feed_id[0];
			}
			$ob->source = $this->get_post_source($feed_id, $post->ID);
			if(!empty($feed_id) && isset($this->feeds[$feed_id])) {
				$ob->image = $this->get_post_image($this->feeds[$feed_id], $post);
			} else {
				$ob->image = $this->get_post_image(null, $post);
			}
			$ob->has_image = !empty($ob->image);
			$ret[] = $ob;
		}
		return $ret;
	}

}
}
?>
