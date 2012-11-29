<?php
/**
 * This class is the data provider for the 'Treaties' section of the site.
 * @package InforMEA
 * @subpackage Theme
 * @since 1.0
 */

// Ajax functions
add_action('wp_ajax_nopriv_meas_autocomplete', 'ajax_meas_autocomplete');
add_action('wp_ajax_meas_autocomplete', 'ajax_meas_autocomplete');

add_action('wp_ajax_nopriv_get_article_tags', 'get_article_tags');
add_action('wp_ajax_get_article_tags', 'get_article_tags');

add_action('wp_ajax_nopriv_get_paragraph_tags', 'get_paragraph_tags');
add_action('wp_ajax_get_paragraph_tags', 'get_paragraph_tags');

add_action('wp_ajax_nopriv_get_treaties', 'ajax_informea_get_treaties');
add_action('wp_ajax_get_treaties', 'ajax_informea_get_treaties');


/**
 * Generate JSON object with article ids and titles for autocomplete form
 * based on `key` query string
 * @param $key string to search traties title by
 * @return JSON object
 */
function ajax_meas_autocomplete() {
	$page_data = new imea_treaties_page(null);
	$key = get_request_value('key');
	$treaties = $page_data->search_treaty_by_title($key);
	$arr = array();
	foreach($treaties as $treaty) {
		$arr[] = array('id' => $treaty->id, 'title' => $treaty->short_title);
	}
	header('Content-Type:application/json');
	echo json_encode($arr);
	die();
}

/**
 * Generate JSON object with article tags based on article id
 * @param @id_article article id from query string
 * @return JSON object
 */
function get_article_tags() {
	$id_article = get_request_int('id_article', 0);
	if($id_article > 0) {
		$arr = array();
		$ob = new imea_treaties_page(null);
		$tags = $ob->get_article_tags($id_article);
		foreach($tags as $tag) {
			$arr[] = array('id' => $tag->id, 'term' => $tag->term);
		}
		header('Content-Type:application/json');
		echo json_encode($arr);
	}
	die();
}

/**
 * Generate JSON object with paragraph tags based on paragraph id
 * @param @id_paragraph paragraph id from query string
 * @return JSON object
 */
function get_paragraph_tags() {
	$id_paragraph = get_request_int('id_paragraph', 0);
	if($id_paragraph > 0) {
		$arr = array();
		$ob = new imea_treaties_page(null);
		$tags = $ob->get_paragraph_tags($id_paragraph);
		foreach($tags as $tag) {
			$arr[] = array('id' => $tag->id, 'term' => $tag->term);
		}
		header('Content-Type:application/json');
		echo json_encode($arr);
	}
	die();
}


/**
 * Ajax function to retrieve the list of treaties.
 */
function ajax_informea_get_treaties() {
    $ob = new imea_treaties_page();
    $arr = $ob->get_all_treaties();
	$ret = array();
    // Filter out internal columns
	foreach($arr as $treaty) {
        $copy = stdclass_copy($treaty, array('id', 'short_title', 'short_title_alternative', 'long_title', 'abstract', 'logo_medium', 'odata_name', 'region'));
        $copy->decisions_count = $ob->get_decisions_count_2($treaty->id);
		$ret[] = $copy;
	}
    header('Content-Type:application/json');
    echo json_encode($ret);
    die();
}


if(!class_exists( 'imea_treaties_page')) {
	/**
	 * Treaties page class to get, set or delete treaty or treaty related info
	 */
class imea_treaties_page extends imea_page_base_page {

	private $id_treaty = NULL;
	private $sort = NULL;
	private $order = NULL;

	public $treaty = NULL;
	public $articles = NULL;
	public $paragraphs_by_article = NULL;
	public $tags = NULL;
	public $other_agreements = array();
	public $agreement = NULL;

	public $expand = NULL;

	public $category = NULL; // Which tab to show Global/Region

	/**
	 * Constructor
	 * @param $id_treaty the id of the treaty to process
	 * @param $arr_parameters array with parameters
	 */
	function __construct($id_treaty = NULL, $arr_parameters = array()) {
		parent::__construct($arr_parameters);
		$this->id_treaty = $id_treaty;
		$this->sort = get_request_variable('s_column', 'str', 'order'); // or year or depository
		$this->order = get_request_variable('s_order', 'str', 'asc'); // or desc
		$this->expand = get_request_variable('expand', 'str', 'icon'); // or grid or list

		if($id_treaty !== NULL) {
			$this->_query_treaty();
		}

		$category = get_query_var('category'); // or region
		$this->category = empty($category) ? '' : $category;
		if($this->category == 'Global') $this->category = '';
	}

	/**
	 * Get all active treaties from the database
	 * @return WP SQL result object
	 */
	function get_all_treaties() {
		global $wpdb;
		$sql = "SELECT * FROM ai_treaty WHERE enabled = 1 ORDER BY short_title";
		return $wpdb->get_results($sql);
	}

	/**
	 * Access ai_organization
	 * @return Rows from the table
	 */
	function get_organizations() {
		global $wpdb;
		return $wpdb->get_results("SELECT a.*, b.name AS country
			FROM ai_organization a
			LEFT JOIN ai_country b ON a.id_country = b.id
			ORDER BY a.name");
	}

	/**
	 * Access ai_treaty
	 * @param @id_organization id of the organisation to get the primary treaty for
	 * @return primary treaty for a certain organization
	 */
	function get_primary_treaty($id_organization) {
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare('SELECT id FROM ai_treaty WHERE ai_treaty.primary = 1 AND id_organization = %d;', intval($id_organization)));
	}

	/**
	 * Retrieve the list of treaties grouped by theme & category (region, global)
	 * @param category: the region
	 * @return array with WP SQL result objects grouped by theme
	 */
	function get_treaties_list() {
		global $wpdb;

		$f = " AND a.region = ''";
		if($this->category != '') {
			$f = $wpdb->prepare(" AND a.region = %s", $this->category);
		}

		$ret = array();
		// Get the themes
		$sql = "SELECT distinct a.theme FROM ai_treaty a WHERE a.enabled = 1 $f ORDER BY a.theme";
		$rows = $wpdb->get_results($sql);
		foreach($rows as $row) {
			$ret[$row->theme] = array();
		}

		$sql = "SELECT a.*, b.depository AS depository, a.logo_medium
				FROM ai_treaty a
				INNER JOIN ai_organization b ON a.id_organization = b.id
				WHERE enabled = 1 $f ORDER BY a.order";
		$rows = $wpdb->get_results($sql);
		foreach($rows as $row) {
			$ret[$row->theme][] = $row;
		}
		return $ret;
	}

	/**
	 * Retrieve treaties list by theme based on region
	 * @param $region region to get treaties from
	 * @return array with WP SQL result objects grouped by theme
	 */
	function get_treaties_by_region_by_theme($region = '') {
		global $wpdb;
		$data = $wpdb->get_results($wpdb->prepare("SELECT a.*, b.depository AS depository
			FROM ai_treaty a
			INNER JOIN ai_organization b ON a.id_organization = b.id
			WHERE a.enabled = 1 AND a.region = %s ORDER BY a.`theme`, a.`order`", $region));
		$ret = array();
		foreach($data as $row) {
			if(!isset($ret[$row->theme])) {
				$ret[$row->theme] = array();
			}
			$ret[$row->theme][] = $row;
		}
		return $ret;
	}

	/**
	 * Retrieve all the regions that have treaties
	 * @return WP SQL result object
	 */
	function get_regions() {
		global $wpdb;
		return $wpdb->get_col("SELECT DISTINCT(region) FROM ai_treaty WHERE region <> ''");
	}

	/**
	 * Return number of treaties from a region
	 * @param $region to check
	 * @return number of treaties
	 */
	function region_has_treaties($region) {
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ai_treaty WHERE region = %s", $region));
	}

	/**
	 * Find field to sort the database column by
	 * @return field name to sort by
	 */
	function get_sort_db_column() {
		if($this->sort == 'title') {
			return 'short_title';
		}
		return $this->sort;
	}

	/**
	 * Find out wich way to sort
	 * @param $column column that is being sorted
	 * @return 'asc' or 'desc'
	 */
	function get_sort_direction($column) {
		if($column == $this->sort) {
			if($this->order == 'asc') return 'desc';
			if($this->order == 'desc') return 'asc';
		} else {
			return 'asc';
		}
	}


	/**
	 * Retrieve information about a single treaty
	 */
	function _query_treaty() {
		global $wpdb;
		$this->paragraphs_by_article = array();
		$this->other_agreements = null;
		$this->agreement = null;
		$sql = $wpdb->prepare("SELECT a.*, b.depository AS depository FROM ai_treaty a
								INNER JOIN ai_organization b ON a.id_organization = b.id
								WHERE a.id = %d", $this->id_treaty);
		$this->treaty = $wpdb->get_row($sql);
		if($this->treaty !== NULL) {
			$this->articles = $wpdb->get_results($wpdb->prepare(
				"SELECT * FROM ai_treaty_article WHERE id_treaty = %d " .
				"ORDER BY ai_treaty_article.order",
				$this->id_treaty));

			$sql = $wpdb->prepare("SELECT b.* FROM ai_treaty_vocabulary a INNER JOIN voc_concept b on a.id_concept = b.id WHERE a.id_treaty = %d", $this->id_treaty);
			$this->tags = $wpdb->get_results($sql);

			$all_paragraphs = $wpdb->get_results($wpdb->prepare(
				"SELECT " .
				"  `ai_treaty_article_paragraph`.`id`," .
				"  `ai_treaty_article_paragraph`.`id_treaty_article`," .
				"  `ai_treaty_article_paragraph`.`official_order`," .
				"  `ai_treaty_article_paragraph`.`order`," .
				"  `ai_treaty_article_paragraph`.`indent`," .
				"  `ai_treaty_article_paragraph`.`content`" .
				"FROM ai_treaty_article_paragraph " .
				"JOIN ai_treaty_article ON ai_treaty_article.id = ai_treaty_article_paragraph.id_treaty_article " .
				"WHERE ai_treaty_article.id_treaty = %d " .
				"ORDER BY ai_treaty_article_paragraph.order",
				$this->id_treaty));

			foreach($all_paragraphs as $paragraph) {
				$this->paragraphs_by_article[$paragraph->id_treaty_article][] = $paragraph;
			}

			if ($this->treaty->primary == 1) {
				$sql = $wpdb->prepare("SELECT * FROM ai_treaty WHERE id_organization = %d AND `primary` = 0 AND enabled = 1", $this->treaty->id_organization);
				$this->other_agreements = $wpdb->get_results($sql);
			} else {
				$sql = $wpdb->prepare("SELECT * FROM ai_treaty WHERE id_organization = %d AND `primary` = 1", $this->treaty->id_organization);
				$this->agreement = $wpdb->get_row($sql);
			}
		}
	}

	/**
	 * Retrieve the tags for an paragraph
	 * @param $id_paragraph the paragraph id
	 * @return WP SQL result object
	 */
	function get_paragraph_tags($id_paragraph) {
		global $wpdb;
		$sql = $wpdb->prepare("SELECT b.* FROM ai_treaty_article_paragraph_vocabulary a INNER JOIN voc_concept b on a.id_concept = b.id WHERE a.id_treaty_article_paragraph = %d", $id_paragraph);
		return $tags = $wpdb->get_results($sql);
	}

	/**
	 * Retrieve the tags for an article
	 * @param $id_article
	 * @return WP SQL result object
	 */
	function get_article_tags($id_article) {
		global $wpdb;
		$sql = $wpdb->prepare("SELECT b.* FROM ai_treaty_article_vocabulary a INNER JOIN voc_concept b on a.id_concept = b.id WHERE a.id_treaty_article = %d", $id_article);
		return $tags = $wpdb->get_results($sql);
	}

	/**
	 * Retrieve the list of parties (countries) for this treaty)
	 * @param id_treaty Treaty
	 * @return Countries that signed the treaty
	 */
	function get_parties() {
		global $wpdb;
		$sql = "SELECT b.*, a.year FROM ai_treaty_country a INNER JOIN ai_country b ON a.id_country = b.id WHERE id_treaty = {$this->id_treaty} ORDER BY b.`name`";
		return $wpdb->get_results($sql);
	}

	/**
	 * Retrieve the list of decisions for this treaty
	 * @param id_treaty Treaty
	 * @return List of decisions for this treaty
     * @deprecated since version 1.5
	 */
	function get_decisions_count() {
		global $wpdb;
		$sql = "SELECT COUNT(*) AS cnt FROM ai_decision WHERE id_treaty = {$this->id_treaty} ORDER BY published DESC";
		$c = $wpdb->get_col($sql);
		return intval($c[0]);
	}



    /**
     * Retrieve the count of decisions for a treaty. Please note that only
     * treaties which have decisions linked to id_meeting will count.
     *
     * Obsolete meeting_title (having id_meeting NULL) decisions are not counted.
     * This method replaces get_decisions_count above which is not yet removed since we have decisions with meeting_title
     *
     * @global object $wdpb Wordpress database connection
     * @param integer $id_treaty Treaty ID
     * @return integer Count of decisions
     * @version 1.6
     */
    function get_decisions_count_2($id_treaty) {
        global $wpdb;
        $ret = $wpdb->get_var(
            $wpdb->prepare('SELECT COUNT(*) FROM ai_decision WHERE id_treaty=%d AND id_meeting IS NOT NULL', $id_treaty)
        );
        $ret = $ret + 0;
        return $ret;
    }


	/**
	 * We use two algorithms here:
	 * 		1. Decisions have no id_meeting (id_meeting is null), but have valid meeting_title.
	 * 		2. Decisions have id_meeting non-null
	 * @param id_treaty Treaty
	 * @return array with WP SQL result objects for decisions and meetings
	 */
	function group_decisions_by_meeting() {
		global $wpdb;
		$ret_dec = array();
		$ret_meet = array();

		$sql = "SELECT DISTINCT(id_meeting) FROM view_decision_meetings WHERE id_treaty = {$this->id_treaty}";
		$c = $wpdb->get_results($sql);
		if(count($c) == 1 && $c[0]->id_meeting == NULL) {
			// Case 2)
			$sql = "SELECT id, link, short_title, long_title, summary, type, status, number, id_treaty, id_meeting, meeting_title, meeting_url, real_meeting_title,
					published FROM view_decision_meetings WHERE id_treaty = {$this->id_treaty} ORDER BY display_order DESC";
			$decisions = $wpdb->get_results($sql);
			foreach($decisions as &$decision) {
				$decision->order = intval(ereg_replace("[^0-9]", "", $decision->number));
				if(!isset($ret_dec[$decision->real_meeting_title])) {
					$ret_dec[$decision->real_meeting_title] = array();
				}
				$ret_dec[$decision->real_meeting_title][] = $decision;
				$meeting = new StdClass();
				$meeting->title = $decision->real_meeting_title;
				$meeting->location = NULL;
				$meeting->city = NULL;
				$meeting->event_url = NULL;
				$meeting->start = NULL;
				$meeting->end = NULL;
				$ret_meet[$decision->real_meeting_title] = $meeting;
			}
		} else {
			// Case 1)
			if(count($c) >= 1) {
				$ids = array();
				foreach($c as $ob) {
					$ids[] = $ob->id_meeting;
				}
				$sql = 'SELECT * FROM ai_event WHERE id IN (' . implode(',', $ids) .') AND type="cop" ORDER BY start DESC, end DESC';
				$meetings = $wpdb->get_results($sql);
				foreach($meetings as $meeting) {
					$sql = "SELECT id, link, short_title, long_title, summary, type, status, number,
								id_treaty, id_meeting, meeting_title, meeting_url, real_meeting_title, published
							FROM view_decision_meetings
							WHERE id_meeting = {$meeting->id} ORDER BY display_order";
					$decisions = $wpdb->get_results($sql);
					foreach($decisions as &$decision) {
						$decision->order = intval(ereg_replace("[^0-9]", "", $decision->number));
						if(!isset($ret_dec[$meeting->id])) {
							$ret_dec[$meeting->id] = array();
						}
						$ret_dec[$meeting->id][] = $decision;
					}
					$ret_meet[$meeting->id] = $meeting;
				}
			}
		}

		$ret = array('decisions' => $ret_dec, 'meetings' => $ret_meet);
		return $ret;
	}

	/**
	 * Retrieve cites from decisions
	 * @return WP SQL result object
	 */
	function get_cites_decisions() {
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM ai_decision WHERE id_treaty=3 AND status <> 'retired' and type='decision' ORDER BY display_order DESC");
	}

	/**
	 * Retrieve resolutions from decisions
	 * @return WP SQL result object
	 */
	function get_cites_resolutions() {
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM ai_decision WHERE id_treaty=3 AND status <> 'retired' and type='resolution' ORDER BY display_order DESC");
	}

	/**
	 * Sort an array of decisions
	 */
	function sort_decisions($decisions) {
		qsort($decisions);
	}

	/**
	 * Retrieve meeting info: Venue, URL, Dates
	 * @param @id_meeting Meeting
	 * @return object with the information
	 */
	function get_meeting($id_meeting) {
		global $wpdb;
		$ret = array();
		$sql = "SELECT * FROM ai_event WHERE id = $id_meeting";
		$row = $wpdb->get_row($sql);
		$ob = new StdClass();
		// Venue
		if($row->location !== NULL) {
			$ob->venue = $row->location;
		} else if($row->city !== NULL) {
			$ob->venue = $row->city;
		} else {
			$ob->venue = NULL;
		}
		// URL
		if($row->event_url !== NULL) {
			$ob->url = $row->event_url;
		} else {
			$ob->url = NULL;
		}
		// Dates
		$dates = '';
		if($row->start !== NULL) {
			$dates .= mysql2date('d/m/Y', $row->start);
		}
		if($row->end !== NULL) {
			$dates .= ' - ' . mysql2date('d/m/Y', $row->end);
		}
		$ob->dates = $dates;
		return $ob;
	}

	/**
	 * Retrieve decision title
	 * @param $decision Decision object
	 * @return title string
	 */
	function get_title($decision) {
		$ret = $decision->long_title;
		if($ret === NULL) {
			$ret = $decision->short_title;
		}
		return $ret;
	}

	/**
	 * Retrieve treaty contacts by country
	 * @param id_treaty Treaty
	 * @return array with WP SQL result objects grouped by country
	 */
	function get_contacts() {
		global $wpdb;
		$ret = array();
		$ret['contacts'] = array();
		$sql = "SELECT * FROM view_people_treaty WHERE id_treaty = {$this->id_treaty} GROUP BY id_country ORDER BY country_name";

		$countries = $wpdb->get_results($sql);
		$ret['countries'] = $countries;

		$sql = "SELECT * FROM view_people_treaty WHERE id_treaty = {$this->id_treaty}";
		$rows = $wpdb->get_results($sql);
		$contacts = array();
		foreach($rows as $row) {
			if(!isset($contacts[$row->id_country])) {
				$contacts[$row->id_country] = array();
			}
			$contacts[$row->id_country][] = $row;
		}
		$ret['contacts'] = $contacts;
		return $ret;
	}


	/**
	 * Overriding
	 * @imea_page_base_page::is_index
	 */
	function is_index() {
		return $this->id_treaty == NULL;
	}

	/**
	 * Retrieve treaty by odata_name
	 * @param $odata_name name as comes from Odata protocol
	 * @return WP SWL result object
	 */
	function get_treaty_by_odata_name($odata_name) {
		global $wpdb;
		return $wpdb->get_row("SELECT * FROM ai_treaty WHERE odata_name = '$odata_name'");
	}

	/**
	 * Retrieve treaty by id
	 * @param $id Treaty id
	 * return WP SQL row object
	 */
	function get_treaty_by_id($id) {
		global $wpdb;
		return $wpdb->get_row("SELECT * FROM ai_treaty WHERE id = $id");
	}

	/**
	 * Retrieve all treaties by title search
	 * @param $title Treaty title
	 * @return WP SQL result object
	 */
	function search_treaty_by_title($title) {
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM ai_treaty WHERE (short_title LIKE '%$title%' OR long_title LIKE '%$title%') ORDER BY short_title");
	}

	/**
	 * Retrieve active treaties all or by title
	 * @param $title Treaty title
	 * @return WP SQL result object
	 */
	function get_treaties($title = null) {
		global $wpdb;
		if($title) {
			return $wpdb->get_results("SELECT * FROM ai_treaty WHERE enabled = 1 AND (short_title LIKE '%$title%' OR long_title LIKE '%$title%') ORDER BY short_title");
		} else {
			return $wpdb->get_results("SELECT * FROM ai_treaty WHERE enabled = 1 ORDER BY short_title");
		}
	}

	/**
	 * Retrieve treaties and organization name for each
	 * @return WP SQL result object
	 */
	function get_all_treaties_organizations() {
		global $wpdb;
		return $wpdb->get_results("SELECT a.*, b.name AS secretariat
			FROM ai_treaty a
			INNER JOIN ai_organization b ON a.id_organization = b.id
			ORDER BY a.short_title");
	}


	/**
	 * Check if invalid treaty ID and show 404
	 * @return id_treaty was set on GET but with invalid ID
	 * @return boolean, 404 if true
	 */
	function is_404() {
		global $wp_query;
		if(is_request_variable('id_treaty') && $this->treaty === NULL) {
			$wp_query->set_404();
			require TEMPLATEPATH.'/404.php';
			return TRUE;
		}
		return FALSE;
	}


	/**
	 * Append treaty to page title.
	 * Called statically by wordpress framework
	 */
	function informea_page_title() {
		global $id_treaty, $page_data;
		if($id_treaty !== NULL) {
			if($page_data->treaty !== NULL) {
				return "{$page_data->treaty->short_title} | ";
			}
		}
		return '';
	}

	/**
	 * Called statically by wordpress framework
	 */
	function breadcrumbtrail() {
		global $post, $id_treaty, $page_data;
		$ret = '';
		$tpl = " &raquo; <a href='%s'%s>%s</a>";
		if($post !== NULL) {
			if($page_data->treaty !== NULL) {
				$ret = sprintf($tpl, get_permalink(), '', $post->post_title);
				$ret .= " &raquo; <span class='current'>{$page_data->treaty->short_title}</span>";
			} else {
				$ret = " &raquo; <span class='current'>{$post->post_title}</span>";
			}
		}
		return $ret;
	}

	/**
	 * Check it current tab is global
	 */
	function is_tab_global() {
		return $this->category == '' || strtolower($this->category) == 'global';
	}

	/**
	 * Print html for 'Icon view' link
	 * @return echoes html anchor
	 */
	function link_options_bar_icon_view($additional_css_classes = '') {
		$p = $this;
		echo get_imea_anchor(array( 'title' => __('Icon view', 'informea'),
			'href' => 'treaties/' . $this->category,
			'css_cb' => function() use ($p, $additional_css_classes) {
				$ret = $additional_css_classes;
				if($p->expand == 'icon') $ret .= ' disabled';
				return $ret;
			}));
	}

	/**
	 * Print html for 'Grid view' link
	 * @return echoes html anchor
	 */
	function link_options_bar_grid_view($additional_css_classes = '') {
		$p = $this;
		echo get_imea_anchor(array( 'title' => __('Grid view', 'informea'),
			'href' => 'treaties/' . $this->category . '/grid',
			'css_cb' => function() use ($p, $additional_css_classes) {
				$ret = $additional_css_classes;
				if($p->expand == 'grid') $ret .= ' disabled';
				return $ret;
			}));
	}

	/**
	 * Print html for 'List view' link
	 * @return echoes html anchor
	 */
	function link_options_bar_list_view($additional_css_classes = '') {
		$p = $this;
		echo get_imea_anchor(array( 'title' => __('List view', 'informea'),
			'href' => 'treaties/' . $this->category . '/list',
			'css_cb' => function() use ($p, $additional_css_classes) {
				$ret = $additional_css_classes;
				if($p->expand == 'list') $ret .= ' disabled';
				return $ret;
			}));
	}

	/**
	 * Return meeting summary
	 * @param $meeting Meeting object
	 * @return html string
	 */
	function decisions_meeting_summary($meeting) {
		$ret = '';
		if($meeting->location) {
			$ret = $meeting->location;
		}
		if($meeting->city) {
			$ret .= (strlen($ret) > 0 ? ', ' : '') . $meeting->city;
		}
		$ret .= (strlen($ret) > 0 ? ', ' : '') . show_event_interval($meeting);
		if($meeting->event_url) {
			$ret .= " [<a href='{$meeting->event_url}' target='_blank' title='Visit event page on convention website'>view</a>]";
		}
		if(strlen($ret) > 0) {
			$ret = 'Held in ' . $ret;
		}
		return $ret;
	}

	/**
	 * Check how many countries a treaty has been in
	 * @param id_treaty Treaty
	 * @return boolean
	 */
	public function has_coverage() {
		global $wpdb;
		$c = $wpdb->get_var($wpdb->prepare('SELECT COUNT(*) FROM `ai_treaty_country` WHERE `id_treaty` = %d', $this->id_treaty));
		return intval($c) > 0;
	}

	/**
	 * Delete a paragraph by it's id from the database
	 * @param $id_paragraph id of paragraph to delete
	 */
	function delete_paragraph($id_paragraph) {
		global $wpdb;
		global $user;
		$this->actioned = true;

		$this->security_check('treaty_delete_paragraph');
		if(!current_user_can('manage_options')) {
			$this->errors = array('You do not have the proper privileges');
			$this->success = false;
			return;
		}
		$wpdb->query('BEGIN');

		try {
			$wpdb->query($wpdb->prepare('DELETE FROM `ai_treaty_article_paragraph_vocabulary` WHERE `id_treaty_article_paragraph` = %d', $id_paragraph));
			$this->check_db_error();
			$wpdb->query($wpdb->prepare('DELETE FROM `ai_treaty_article_paragraph` WHERE `id` = %d', $id_paragraph));
			$this->check_db_error();
			$this->add_activity_log('delete', 'treaty', 'Removed paragraph width id #' . $id_paragraph);
			$wpdb->query('COMMIT');
			$this->success = true;
			$this->errors = array('The paragraph was successfully removed');
			$this->_query_treaty();
		} catch (Exception $e) {
			$this->success = false;
			$this->errors = array('An error occurred while removing the paragraph');
			$wpdb->query('ROLBACK');
		}
	}

	/**
	 * Delete an article by it's id from the database
	 * @param $id_article id of the article to delete
	 */
	function delete_article($id_article) {
		global $wpdb;
		global $user;
		$this->actioned = true;

		$this->security_check('treaty_delete_article');
		if(!current_user_can('manage_options')) {
			$this->errors = array('You do not have the proper privileges');
			$this->success = false;
			return;
		}
		$wpdb->query('BEGIN');

		try {
			$wpdb->query($wpdb->prepare('DELETE FROM ai_treaty_article_paragraph_vocabulary WHERE id_treaty_article_paragraph IN (SELECT id FROM ai_treaty_article_paragraph WHERE id_treaty_article = %d)', $id_article));
			$this->check_db_error();

			$wpdb->query($wpdb->prepare('DELETE FROM ai_treaty_article_paragraph WHERE id_treaty_article = %d', $id_article));
			$this->check_db_error();

			$wpdb->query($wpdb->prepare('DELETE FROM ai_treaty_article_vocabulary WHERE id_treaty_article = %d', $id_article));
			$this->check_db_error();

			$wpdb->query($wpdb->prepare('DELETE FROM ai_treaty_article WHERE id = %d', $id_article));
			$this->check_db_error();

			$this->add_activity_log('delete', 'treaty', 'Removed article width id #' . $id_article);
			$wpdb->query('COMMIT');
			$this->success = true;
			$this->errors = array('The article was successfully removed');
			$this->_query_treaty();
		} catch (Exception $e) {
			$this->success = false;
			$this->errors = array('An error occurred while removing the article');
			$wpdb->query('ROLBACK');
		}
	}

	/* Administration area */
	/**
	 * Validate the validate_treaty_edit_treaty form
	 * @return TRUE If form successfully validated
	 */
	function validate_treaty_edit_treaty() {
		$this->actioned = true;
		if($this->security_check('informea-admin_treaty_edit_treaty')) {
			$val = new FormValidator();
			$val->addValidation("id_organization", "req", "Please fill in the organization");
			$val->addValidation("short_title", "req", "Please fill in the short title");
			$val->addValidation("short_title_alternative", "req", "Please fill in the alternative short title");
			$val->addValidation("year", "num", "Year must be a number");
			$val->addValidation("year", "gt=1970", "Year must be greater than 1970");
			$val->addValidation("year", "lt=2100", "Year must be less than 2100");
			$val->addValidation("number_of_parties", "num", "Number of parties must be a number");
			$val->addValidation("number_of_parties", "lt=1000", "Number of parties must be less than 1000");
			$val->addValidation("odata_name", "req", "OData name is incorrect. Cannot be empty or contain spaces!");
			$val->addValidation("odata_name", "maxlen=32", "OData name cannot be longer than 32 charactes");
			$val->addValidation("logo_medium", "req", "Logo is required!");
			$valid = $val->ValidateForm();
			if(!$valid) {
				$this->errors = $val->GetErrors();
			}

			// Validate picture size
			if(isset($_FILES['logo_medium']) && !empty($_FILES['logo_medium']['name'])) {
				$is = getimagesize($_FILES['logo_medium']['tmp_name']);
				if(!empty($is)) {
					if($is[0] != 45 || $is[1] != 55) {
						$this->errors['logo_medium'] = 'Invalid logo dimensions';
						$valid = false;
					}
				} else {
					$this->errors['logo_medium'] = 'Invalid logo file';
					$valid = false;
				}
			}
			return $valid;
		}
		return FALSE;
	}

	/**
	 * Change an existing treaty from the database
	 * @return TRUE if successfully edited
	 */
	function treaty_edit_treaty() {
		global $wpdb;
		global $current_user;
		$user = $current_user->user_login;
		@mysql_query("BEGIN", $wpdb->dbh);
		try {
			$title = stripslashes(get_request_value('short_title'));
			$id = get_request_int('id');
			$data = array('id_organization' => get_request_int('id_organization'), 'short_title' => $title);
			$data['primary'] = get_request_value('primary') !== NULL;
			$data['url'] = stripslashes(get_request_value('url'));
			$data['number_of_parties'] = get_request_int('number_of_parties', NULL);
			$data['year'] = get_request_int('year', NULL);
			$data['abstract'] = stripslashes(get_request_value('abstract'));
			$data['long_title'] = stripslashes(get_request_value('long_title'));
			$data['short_title_alternative'] = stripslashes(get_request_value('short_title_alternative'));
			$data['theme'] = get_request_value('theme');
			$data['theme_secondary'] = get_request_value('theme_secondary');
			$data['rec_updated_author'] = $user;
			$data['rec_updated'] = date('Y-m-d H:i:s', strtotime("now"));
			$data['order'] = get_request_int('order');
			$data['enabled'] = get_request_int('enabled', 0);
			$data['odata_name'] = slugify(get_request_value('odata_name', NULL));
			$data['region'] = get_request_value('region');
			$data['logo_medium'] = get_request_value('logo_medium');
			$treaty_result = $wpdb->update('ai_treaty', $data, array('id' => $id));
			$keywords_result = TRUE;
			if($treaty_result !== FALSE) {
				$keywords_result = $wpdb->query($wpdb->prepare("DELETE FROM ai_treaty_vocabulary WHERE id_treaty = %d", $id));

				if ($keywords_result !== FALSE) {
					if (isset($_POST['keywords'])) {
						foreach($_POST['keywords'] as $keyword) {
							$keywords_result = $wpdb->insert('ai_treaty_vocabulary',
								array(
									'id_treaty' => $id,
									'id_concept' => intval($keyword),
								)
							);
							if($keywords_result == FALSE) {
								break;
							}
						}
					}
				}
			}

			$this->success = ($treaty_result !== FALSE) and ($keywords_result !== FALSE);
			if($this->success) {
				@mysql_query("COMMIT", $wpdb->dbh);
				// Log the action
				$url = 	sprintf('%s/treaties/%d', get_bloginfo('url'), $id);
				$this->add_activity_log('update', 'treaty', "Edited attributes for treaty <strong>{$title}</strong>", null, $url);
			} else {
				$this->errors = array('DB' => $wpdb->last_error);
				@mysql_query("ROLLBACK", $wpdb->dbh);
			}
			return $this->success;
		} catch (Exception $e) {
			$this->success = FALSE;
			$this->errors = array('Exception' => $e->getMessage());
			@mysql_query("ROLLBACK", $wpdb->dbh);
			return FALSE;
		}
	}


	/**
	 * Access ai_treaty_article
	 * @param $id_treaty id of the treaty
	 * @return order value for a new article
	 */
	function get_next_treaty_article_order($id_treaty) {
		global $wpdb;
		$result = $wpdb->get_var($wpdb->prepare("SELECT MAX(ai_treaty_article.order) FROM ai_treaty_article WHERE id_treaty = %d;", $id_treaty));
		if (!$result) $result = 0;

		return $result + 1;
	}

	/**
	 * Construct href for a treaty
	 * @param id of the treaty
	 * @return relative URL of the treaty
	 */
	function get_treaty_url($id_treaty) {
		return sprintf('%s/treaties/%s', get_bloginfo('url'), $id_treaty);
	}


	/**
	 * Access ai_treaty_article to get the article with a specific order
	 * @param $id_treaty id of the treaty
	 * @param $order order of the article in the treaty
	 * @return order value for a new article
	 */
	function get_treaty_article_with_order($id_treaty, $order) {
		global $wpdb;
		return $wpdb->get_var($wpdb->prepare("SELECT id FROM ai_treaty_article WHERE id_treaty = %d AND ai_treaty_article.order = %d;", $id_treaty, $order));
	}


	/**
	 * Validate the validate_treaty_add_article form
	 * @return TRUE If form successfully validated
	 */
	function validate_treaty_add_article() {
		$this->actioned = TRUE;
		if($this->security_check('informea-admin_treaty_add_article')) {
			$val = new FormValidator();
			$val->addValidation("id_treaty", "req", "Please fill in the treaty");
			// $val->addValidation("title", "req", "Please fill in the title");
			$valid = $val->ValidateForm();
			if(!$valid) {
				$this->errors = $val->GetErrors();
			}
			return $valid;
		}
		return FALSE;
	}

	/**
	 * Insert new article into the database
	 * @return TRUE if successfully added
	 */
	function treaty_add_article() {
		global $wpdb;
		global $current_user;
		$user = $current_user->user_login;
		@mysql_query("BEGIN", $wpdb->dbh);
		try {
			$id_treaty = intval($_POST['id_treaty']);
			$order = intval($_POST['order']);
			$title = stripslashes(trim($_POST['title']));
			$data = array(
							'id_treaty' => $id_treaty,
							'order' => $order,
							'title' => $title,
							'rec_created' => date('Y-m-d H:i:s', strtotime("now")),
							'rec_author' => $user
						);
			if (isset($_POST['official_order'])) {
				$data['official_order'] = stripslashes(trim($_POST['official_order']));
			}
			if (isset($_POST['content'])) {
				$data['content'] = stripslashes($_POST['content']);
			}

			$up_to_order = $order;
			while ($this->get_treaty_article_with_order($id_treaty, $up_to_order)) {
				$up_to_order += 1;
			}
			for ($c_order = $up_to_order; $c_order >= $order; $c_order -= 1) {
				$wpdb->update('ai_treaty_article', array('order' => $c_order+1), array('id_treaty' => $id_treaty, 'order' => $c_order));
			}
			$article_success = $wpdb->insert('ai_treaty_article', $data);
			$this->insert_id = $wpdb->insert_id;
			$keywords_success = TRUE;
			if(($article_success !== FALSE) and isset($_POST['keywords'])) {
				foreach($_POST['keywords'] as $keyword) {
					$keywords_success = $wpdb->insert('ai_treaty_article_vocabulary', array(
							'id_treaty_article' => $this->insert_id,
							'id_concept' => intval($keyword),
						)
					);
					if($keywords_success == FALSE) {
						break;
					}
				}
			}

			$this->success = ($article_success !== FALSE) and ($keywords_success !== FALSE);
			if($this->success) {
				@mysql_query("COMMIT", $wpdb->dbh);
				// Log the action
				$t = $this->get_treaty_by_id($id_treaty);
				$url = 	sprintf('%s/treaties/%d?id_treaty_article=%d#article_%d', get_bloginfo('url'), $id_treaty, $this->insert_id, $this->insert_id);
				$this->add_activity_log('insert', 'treaty', "Added new article <strong>{$title}</strong> to treaty <strong>{$t->short_title}</strong>", null, $url);
			} else {
				$this->errors = array('DB' => $wpdb->last_error);
				@mysql_query("ROLLBACK", $wpdb->dbh);
			}
			return $this->success;
		} catch (Exception $e) {
			$this->success = FALSE;
			@mysql_query("ROLLBACK", $wpdb->dbh);
			return FALSE;
		}
	}


	/* Administration area */

	/**
	 * Validate the validate_treaty_add_organization form
	 * @return TRUE If form successfully validated
	 */
	function validate_organization($update = false) {
		global $wpdb;
		$this->actioned = true;
		if(!$update) {
			$this->security_check('informea-admin_treaty_add_organization');
		} else {
			$this->security_check('informea-admin_treaty_edit_organization');
		}

		$val = new FormValidator();
		$val->addValidation("name", "req", "Please fill in the name");
		if($update) {
			$val->addValidation("id", "req", "No organization specified");
		}
		$val->addValidation("name", "req", "Please fill in the name");
		$valid = $val->ValidateForm();
		if(!$valid) {
			$this->errors = $val->GetErrors();
		}

		if(!$update) {
			$row = $wpdb->get_row($wpdb->prepare('SELECT * FROM ai_organization WHERE name=%s', get_request_value('name')));
			if($row !== null) {
				$valid = false;
				$this->errors['name'] = 'Organization with same name already exists';
			}
		}
		return $valid;
	}

	/**
	 * Insert new organization into the database
	 * @return TRUE if successfully added
	 */
	function add_organization() {
		global $wpdb;
		global $current_user;
		$user = $current_user->user_login;

		$data = array();
		$data['name'] = get_request_value('name');
		$data['description'] = get_request_value('description');
		$data['address'] = get_request_value('address');
		$data['city'] = get_request_value('city');
		$id_country = get_request_value('id_country');
		if(!empty($id_country)) {
			$data['id_country'] = $id_country;
		}
		$data['url'] = get_request_value('url');
		$data['depository'] = get_request_value('depository');
		$data['rec_author'] = $user;
		$data['rec_created'] = date('Y-m-d H:i:s', strtotime("now"));
		$r = $wpdb->insert('ai_organization', $data);
		$this->success = $r !== FALSE;
		return $this->success;
	}

	/**
	 * Update organization from the database
	 * @return TRUE if successfully added
	 */
	function edit_organization() {
		global $wpdb;
		global $current_user;
		$user = $current_user->user_login;

		$id = get_request_int('id');

		$data = array();
		$data['name'] = get_request_value('name');
		$data['description'] = get_request_value('description');
		$data['address'] = get_request_value('address');
		$data['city'] = get_request_value('city');
		$id_country = get_request_value('id_country');
		if(!empty($id_country)) {
			$data['id_country'] = $id_country;
		}
		$data['url'] = get_request_value('url');
		$data['depository'] = get_request_value('depository');
		$data['rec_author'] = $user;
		$data['rec_created'] = date('Y-m-d H:i:s', strtotime("now"));
		$r = $wpdb->update('ai_organization', $data, array('id' => $id));
		$this->success = $r !== FALSE;
		return $this->success;
	}


	function get_organization($id) {
		global $wpdb;
		return $wpdb->get_row($wpdb->prepare('SELECT * FROM ai_organization WHERE id=%d', $id));
	}


	/**
	 * Validate the validate_treaty_add_treaty form
	 * @return TRUE If form successfully validated
	 */
	function validate_treaty_add_treaty() {
		$this->actioned = TRUE;
		if($this->security_check('informea-admin_treaty_add_treaty')) {
			$val = new FormValidator();
			$val->addValidation("id_organization", "req", "Please fill in the organization");
			$val->addValidation("short_title", "req", "Please fill in the short title");
			$val->addValidation("short_title_alternative", "req", "Please fill in the alternative short title");
			$val->addValidation("year", "num", "Year must be a number");
			$val->addValidation("year", "gt=1970", "Year must be greater than 1970");
			$val->addValidation("year", "lt=2100", "Year must be less than 2100");
			$val->addValidation("number_of_parties", "num", "Number of parties must be a number");
			$val->addValidation("number_of_parties", "lt=1000", "Number of parties must be less than 1000");
			$val->addValidation("odata_name", "req", "OData name is incorrect. Cannot be empty or contain spaces!");
			$val->addValidation("odata_name", "maxlen=32", "OData name cannot be longer than 32 charactes");
			$val->addValidation("region", "req", "Please specify the type of instrument");
			$valid = $val->ValidateForm();
			if(!$valid) {
				$this->errors = $val->GetErrors();
			}
			return $valid;
		}
		return FALSE;
	}


	/**
	 * Insert new treaty into the database
	 * @return TRUE if successfully added
	 */
	function treaty_add_treaty() {

		global $wpdb;
		global $current_user;
		$user = $current_user->user_login;
		@mysql_query("BEGIN", $wpdb->dbh);
		try {
			$title = stripslashes(get_request_value('short_title'));
			$data = array('id_organization' => get_request_int('id_organization'), 'short_title' => $title);
			$data['primary'] = get_request_value('primary') !== NULL;
			$data['long_title'] = stripslashes(get_request_value('long_title'));
			$data['short_title_alternative'] = stripslashes(get_request_value('short_title_alternative'));
			$data['year'] = get_request_int('year', NULL);
			$data['abstract'] = stripslashes(get_request_value('abstract'));
			$data['url'] = stripslashes(get_request_value('url'));
			$data['number_of_parties'] = get_request_int('number_of_parties', NULL);
			$data['theme'] = get_request_value('theme');
			$data['theme_secondary'] = get_request_value('theme_secondary');
			$data['rec_author'] = $user;
			$data['rec_created'] = date('Y-m-d H:i:s', strtotime("now"));
			$data['odata_name'] = slugify(get_request_value('odata_name'));
			$data['region'] = get_request_value('region');
			$data['logo_medium'] = get_request_value('logo_medium');
			$treaty_result = $wpdb->insert('ai_treaty', $data);
			$keywords_result = TRUE;
			if(($treaty_result !== FALSE) and isset($_POST['keywords'])) {
				$this->insert_id = $wpdb->insert_id;
				foreach($_POST['keywords'] as $keyword) {
					$keywords_result = $wpdb->insert('ai_treaty_vocabulary', array(
							'id_treaty' => $this->insert_id,
							'id_concept' => intval($keyword),
						)
					);
					if($keywords_result == FALSE) {
						break;
					}
				}
			}

			$this->success = ($treaty_result !== FALSE) and ($keywords_result !== FALSE);
			if($this->success) {
				$this->insert_id = $wpdb->insert_id;
				@mysql_query("COMMIT", $wpdb->dbh);
				// Log the action
				$url = 	sprintf('%s/treaties/%d', get_bloginfo('url'), $this->insert_id);
				$this->add_activity_log('insert', 'treaty', "Created new treaty <strong>{$title}</strong>", null, $url);

			} else {
				$this->errors = array('DB' => $wpdb->last_error);
				@mysql_query("ROLLBACK", $wpdb->dbh);
			}
			return $this->success;
		} catch (Exception $e) {
			$this->success = FALSE;
			@mysql_query("ROLLBACK", $wpdb->dbh);
			return FALSE;
		}
	}
}
}
?>
