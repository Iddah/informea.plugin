<?php
function _date_parse_error2($errno, $errstr, $errfile, $errline) {
	throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}

class AbstractSearch {
	private static $CHECKBOX_CHECKED = ' checked="checked"';
	private static $SELECT_SELECTED = ' selected="selected"';
	private static $EMPTY_STRING = '';
	private static $CSS_CHECKBOX_CHECKED = ' checked';
	private static $CSS_CHECKBOX_UNCHECKED = ' unchecked';


	protected static $INPUT_DATE_FORMAT = '%d/%m/%Y';
	protected static $OUTPUT_DATE_FORMAT = '%Y-%m-%d';
	protected $request;

	private $dirty_terms;
	private $filtering_terms;
	private $cache_solr_query;
	private $search_terms_parsed = false;

	public function __construct($request) {
		$this->search_terms_parsed = false;
		$this->request = $request;
	}

	/**
	 * Discover search terms and return them as array
	 */
	private function parse_query_string() {
		$freetext = $this->get_freetext('*');
		if($freetext == '*') { return $freetext; }
		$exploded = array();
		$str = ''; $in_quote = false;
		$freetext = str_split($freetext);
		$fl_1 = count($freetext) - 1;
		foreach($freetext as $idx => $ch) {
			if($ch == ',') {
				if(!$in_quote) { // End of keyword: string 1,# string2
					$str = trim($str);
					if(!empty($str)) { // Do not put empty strings
						$exploded[] = $str;
					}
					$str = '';
					continue;
				} else {
					$str .= $ch;
				}
			} else if($ch == '"') {
				$str .= $ch;
				$in_quote = !$in_quote;
				if($in_quote == false) { // Just finished the word: "string 1"#, string 2
					$exploded[] = str_replace('"', '', $str);
					$str = '';
					continue;
				}
			} else if($idx == $fl_1) { // End of string: string 1, string 2#
				$str .= $ch; $exploded[] = $str; break;
			} else {
				$str .= $ch;
			}
		}

		// Search inside Solr for synonyms of the specified terms
		//$base_terms = $this->get_request_value('q_term', array(), false);
		// Get their synonyms
		//global $wpdb;
		//foreach($base_terms as $id_term) {
		//	//var_dump($id_term);
		//	$synonyms = $wpdb->get_col($wpdb->prepare('SELECT CONCAT(\'"\', synonym, \'"\') FROM voc_synonym WHERE id_concept=%d', $id_term));
		//	$exploded = array_merge($exploded, $synonyms);
		//}

		return $exploded;
	}

	public function get_dirty_terms() {
		$this->parse_terms();
		return $this->dirty_terms;
	}


	public function get_solr_query() {
		$this->parse_terms();
		return $this->cache_solr_query;
	}


	public function is_dirty_search() {
		$this->parse_terms();
		return !empty($this->dirty_terms);
	}

	private function parse_terms() {
		if($this->search_terms_parsed) {
			return;
		}
		// $db = debug_backtrace();
		// var_dump($db[1]['file'] . ' ' . $db[1]['line']); echo '<hr />';
		global $wpdb;
		$this->dirty_terms = array();
		$this->filtering_terms = array();

		$freetext = $this->parse_query_string();
		if($freetext == '*') { return $freetext; }

		// Clean the terms and look for their synonyms
		foreach($freetext as &$el) {
			$el = trim($el);
			$clean = $el;
			if(!empty($clean)) {
				$this->dirty_terms[] = $clean;
			}
		}

		$cc = count($this->dirty_terms);
		if($cc > 1) {
			$this->cache_solr_query = '(';
			foreach($this->dirty_terms as $idx => $w) {
				$this->cache_solr_query .= '"' . $w . '"';
				if($idx < $cc - 1) {
					$this->cache_solr_query .= ' AND ';
				}
			}
			$this->cache_solr_query .= ')';
		} else if(count($this->dirty_terms) == 1) {
			$this->cache_solr_query = '"' . $this->dirty_terms[0] . '"';
		}
		$this->search_terms_parsed = true;
	}


	function clear_cache() {}

	/**
	 * Retrieve request parameter also from GET
	 * @param $name Parameter name
	 * @param $default Default value
	 * @param $trim Trim resulting string
	 * @return parameter value or empty string if not set
	 */
	function get_request_value($name, $default = NULL, $trim = TRUE) {
		$ret = $default;
		if (isset($this->request[$name]) && $this->request[$name] != '') {
			$ret = $this->request[$name];
			if($trim) {
				$ret = trim($ret);
			}
		}
		return $ret;
	}

	function get_request_int($name, $default = NULL) {
		$ret = $this->get_request_value($name, $default, TRUE);
		if(!empty($ret)) {
			$ret = intval($ret);
		}
		return $ret;
	}


	/* ==== PROCESS REQUEST PARAMETERS ==== */

	/**
	 * Get the list of treaties to filter
	 * @return An array with IDs of the selected treaties
	 */
	function get_treaties() {
		$ret = array();
		$tmp_treaties = $this->get_request_value('q_treaty', array(), false);
		foreach($tmp_treaties as $id) { $ret[] = intval($id); }
		return $ret;
	}

	/**
	 * If true, when multiple terms are returned by get_terms, then
	 * use OR operator to expand the results. Otherwise is AND (Default)
	 */
	function is_terms_or() {
		return $this->get_request_value('q_term_or') == 'or';
	}

	/**
	 * If non-null, user selected some terms to filter entities.
	 * If size > 1, then lookup is_terms_or/is_terms_and to see how to
	 * handle relationship between terms (expand - or/restrict - and)
	 */
	function get_terms() {
		$this->parse_terms();
		$ret = array();
		$terms = $this->get_request_value('q_term', array(), false);
		// Sanitize input
		foreach($terms as $id) {
			$ret[] = intval($id);
		}
		// Merge with terms from the input box
		return array_unique(array_merge($ret, array_keys($this->filtering_terms)));
	}

	/**
	 * @return true if we have filtering on terms
	 */
	function is_using_terms() {
		$terms = $this->get_terms();
		return !empty($terms);
	}

	/**
	 * If non-null, user entered dirty word search (and use it to lookup in documents, titles etc. - depends on selected entities)
	 */
	function get_freetext($default = '') {
		$ret = $this->get_request_value('q_freetext');
		return stripslashes($ret);
	}

	/**
	 * If non-null, lookup entities that are dated in time after this date (>=)
	 */
	function get_start_date($solr = false) {
		$ret = null;
		$sm = !empty($this->request['q_start_month']) ? intval($this->request['q_start_month']) : null;
		$sy = !empty($this->request['q_start_year']) ? intval($this->request['q_start_year']) : null;
		if($sm !== null && $sy !== null) {
			if($solr) {
				$ret = sprintf("%04d-%02d-01T00:00:00Z", $sy, $sm);
			} else {
				$ret = sprintf("%04d-%02d-01", $sy, $sm);
			}
		}
		return $ret;
	}

	/**
	 * If non-null, lookup entities that are dated in time up to this date (<=)
	 */
	function get_end_date($solr = false) {
		$ret = null;
		$em = !empty($this->request['q_end_month']) ? intval($this->request['q_end_month']) : null;
		$ey = !empty($this->request['q_end_year']) ? intval($this->request['q_end_year']) : null;
		if($em !== null && $ey !== null) {
			if($solr) {
				$ret = sprintf("%04d-%02d-31T00:00:00Z", $ey, $em);
			} else {
				$ret = sprintf("%04d-%02d-31", $ey, $em);
			}
		}
		return $ret;
	}

	/**
	 * Lookup into 'treaties' - Checkbox treaties
	 */
	function is_use_treaties() {
		$tab = $this->get_q_tab();
		return isset($this->request['q_use_treaties']);
	}

	/**
	 * Lookup into 'decisions/recommendations' - Checkbox decisions
	 */
	function is_use_decisions() {
		$tab = $this->get_q_tab();
		return isset($this->request['q_use_decisions']);
	}

	/**
	 * Lookup into 'meetings' - Checkbox meetings
	 */
	function is_use_meetings() {
		$tab = $this->get_q_tab();
		return isset($this->request['q_use_meetings']);
	}


	/**
	 * Which of the three tabs are displayed to the user
	 */
	function get_q_tab() {
		return $this->get_request_int('q_tab', 1);
	}

	/**
	 * Get the start page index of results - for pagination
	 */
	function get_page($default = 0) {
		return $this->get_request_int('q_page', $default);
	}

	/**
	 * Get the number of results returned per page
	 */
	function get_page_size($default = 10) {
		return $this->get_request_int('q_page_size', $default);
	}

	/**
	 * Get the sorting direction for results.
	 * Analyze q_sort_direction request parameter. Possible values (ASC/DESC). Default DESC.
	 * Sanitizes input.
	 */
	function get_sort_direction() {
		$order = $this->get_request_value('q_sort_direction', 'DESC');
		if(strcasecmp('ASC', $order) == 0) {
			return 'ASC';
		} else {
			return 'DESC';
		}
	}

	protected function parse_date($ds, $input_format = null, $output_format = null) {
		$ret = null;
		$iformat = $input_format === null ? AbstractSearch::$INPUT_DATE_FORMAT : $input_format;
		$oformat = $output_format === null ? AbstractSearch::$OUTPUT_DATE_FORMAT : $output_format;

		$ret = null;
		set_error_handler("_date_parse_error2");
		try {
			$d = strptime($ds, $iformat);
			$ts = mktime($d['tm_hour'], $d['tm_min'], $d['tm_sec'], ++$d['tm_mon'], $d['tm_mday'], $d['tm_year'] + 1900);
			$ret = strftime($oformat, $ts);
		} catch(ErrorException $e) {
			// Handle the error? or log it? - TODO
		}
		restore_error_handler();
		return $ret;
	}


	/**
	 * Get the search form (HTML form)
	 * @param boolean $get If true, form is an GET URL, otherwise it's an POST form
	 * @param array $exclude Strings with id of the fields to exclude
	 */
	function get_search_form($get = true, $exclude = array()) {
		$ret = '';
		if($get) {
			$ret = '?1=1';
		}

		$params = $this->get_search_parameters($exclude);
		foreach($params as $key => $value) {
			if(is_array($value)) {
				foreach($value as $ivalue) {
					if($get) {
						$ret .= $this->_create_get_param($key, $ivalue);
					} else {
						$ret .= $this->_create_post_param($key, $ivalue) . "\n";
					}
				}
			} else {
				if($get) {
					$ret .= $this->_create_get_param($key, $value);
				} else {
					$ret .= $this->_create_post_param($key, $value) . "\n";
				}
			}
		}
		return $ret;
	}

	protected function get_search_parameters($exclude = array()) {
		$params = array();
		// Prepare all request parameters
		if(!in_array('q_treaty', $exclude)) {
			$treaties = $this->get_treaties();
			if(!empty($treaties)) {
				foreach($treaties as $id) {
					$params['q_treaty'][] = $id;
				}
			}
		}

		$q_term_or = $this->is_terms_or();
		if(!in_array('q_term_or', $exclude) && !empty($q_term_or)) {
			$params['q_term_or'] = $q_term_or;
		}

		if(!in_array('q_term', $exclude)) {
			$q_terms = $this->get_terms();
			if(!empty($q_terms)) {
				foreach($q_terms as $id) {
					$params['q_term'][] = $id;
				}
			}
		}

		$q_freetext = $this->get_freetext();
		if(!in_array('q_freetext', $exclude) && !empty($q_freetext)) {
			$params['q_freetext'] = $q_freetext;
		}

		if(!in_array('q_start_month', $exclude) && isset($this->request['q_start_month'])) {
			$v = intval($this->request['q_start_month']);
			if($v > 0 && $v < 13) {
				$params['q_start_month'] = $v;
			}
		}

		if(!in_array('q_start_year', $exclude) && isset($this->request['q_start_year'])) {
			$v = intval($this->request['q_start_year']);
			if($v > 0) {
				$params['q_start_year'] = $v;
			}
		}

		if(!in_array('q_end_month', $exclude) && isset($this->request['q_end_month'])) {
			$v = intval($this->request['q_end_month']);
			if($v > 0 && $v < 13) {
				$params['q_end_month'] = $v;
			}
		}

		if(!in_array('q_end_year', $exclude) && isset($this->request['q_end_year'])) {
			$v = intval($this->request['q_end_year']);
			if($v > 0) {
				$params['q_end_year'] = $v;
			}
		}

		$q_use_treaties = $this->is_use_treaties();
		if(!in_array('q_use_treaties', $exclude) && !empty($q_use_treaties)) {
			$params['q_use_treaties'] = '1';
		}

		$q_use_meetings = $this->is_use_meetings();
		if(!in_array('q_use_meetings', $exclude) && !empty($q_use_meetings)) {
			$params['q_use_meetings'] = '1';
		}

		$q_use_decisions = $this->is_use_decisions();
		if(!in_array('q_use_decisions', $exclude) && !empty($q_use_decisions)) {
			$params['q_use_decisions'] = '1';
		}

		$q_use_term_or_fulltext = $this->is_use_term_or_fulltext();
		if(!in_array('q_use_term_or_fulltext', $exclude) && !empty($q_use_term_or_fulltext)) {
			$params['q_use_term_or_fulltext'] = $q_use_term_or_fulltext;
		}

		$q_tab = $this->get_selected_tab();
		if(!in_array('q_tab', $exclude) && !empty($q_tab)) {
			$params['q_tab'] = $q_tab;
		}

		$q_page = $this->get_page();
		if(!in_array('q_page', $exclude) && $q_page !== null) {
			$params['q_page'] = $q_page;
		}

		$q_page_size = $this->get_page_size();
		if(!in_array('q_page_size', $exclude) && !empty($q_page_size)) {
			$params['q_page_size'] = $q_page_size;
		}

		$q_sort_direction = $this->get_sort_direction();
		if(!in_array('q_sort_direction', $exclude) && !empty($q_sort_direction)) {
			$params['q_sort_direction'] = $q_sort_direction;
		}
		return $params;
	}

	private function _create_get_param($key, $value) {
		return "&$key=$value";
	}

	private function _create_post_param($key, $value) {
		return "<input type=\"hidden\" name=\"$key\" value=\"$value\" />";
	}

	/* ==== END PROCESS REQUEST PARAMETERS ==== */




	/* ==== USER INTERFACE METHODS ==== */
	function ui_get_freetext_default() {
		return '';
	}

	function ui_get_freetext($default = null) {
		$ret = '';
		$terms = $this->get_dirty_terms();
		$c = count($terms);
		foreach($terms as $i => $t) {
			$ret .= $t;
			if($i < $c - 1) {
				$ret .= ',';
			}
		}
		return $ret;
	}


	public function ui_check_page_size_10() {
		echo $this->get_page_size() == 10 ? AbstractSearch::$CHECKBOX_CHECKED : AbstractSearch::$EMPTY_STRING;
	}

	public function ui_check_page_size_20() {
		echo $this->get_page_size() == 20 ? AbstractSearch::$CHECKBOX_CHECKED : AbstractSearch::$EMPTY_STRING;
	}

	public function ui_check_page_size_50() {
		echo $this->get_page_size() == 50 ? AbstractSearch::$CHECKBOX_CHECKED : AbstractSearch::$EMPTY_STRING;
	}

	public function ui_check_use_decisions() {
		if(!defined('INFORMEA_SEARCH_PAGE')) {
			echo AbstractSearch::$CHECKBOX_CHECKED; // for explorer
		}
		echo $this->is_use_decisions() ? AbstractSearch::$CHECKBOX_CHECKED : AbstractSearch::$EMPTY_STRING;
	}

	public function ui_checkbox_css_decisions() {
		if(!defined('INFORMEA_SEARCH_PAGE')) {
			echo AbstractSearch::$CSS_CHECKBOX_CHECKED; // for explorer
		}
		echo $this->is_use_decisions() ? AbstractSearch::$CSS_CHECKBOX_CHECKED : AbstractSearch::$CSS_CHECKBOX_UNCHECKED;
	}

	public function ui_check_use_treaties() {
		if(!defined('INFORMEA_SEARCH_PAGE')) {
			echo AbstractSearch::$CHECKBOX_CHECKED; // for explorer
		}
		echo $this->is_use_treaties() ? AbstractSearch::$CHECKBOX_CHECKED : AbstractSearch::$EMPTY_STRING;
	}

	public function ui_checkbox_css_treaties() {
		if(!defined('INFORMEA_SEARCH_PAGE')) {
			echo AbstractSearch::$CSS_CHECKBOX_CHECKED; // for explorer
		}
		echo $this->is_use_treaties() ? AbstractSearch::$CSS_CHECKBOX_CHECKED : AbstractSearch::$CSS_CHECKBOX_UNCHECKED;
	}

	public function ui_check_use_meetings() {
		echo $this->is_use_meetings() ? AbstractSearch::$CHECKBOX_CHECKED : AbstractSearch::$EMPTY_STRING;
	}

	public function ui_checkbox_css_meetings() {
		echo $this->is_use_meetings() ? AbstractSearch::$CSS_CHECKBOX_CHECKED : AbstractSearch::$CSS_CHECKBOX_UNCHECKED;
	}

	public function ui_write_option($value, $label, $selected) {
		$s = $selected ? AbstractSearch::$SELECT_SELECTED : AbstractSearch::$EMPTY_STRING;
		echo "<option value=\"$value\"$s>$label</option>";
	}

	public function ui_radio_terms_or($negate = false) {
		if($negate) {
			echo $this->is_terms_or() ? AbstractSearch::$CHECKBOX_CHECKED : AbstractSearch::$EMPTY_STRING;
		} else {
			echo $this->is_terms_or() ? AbstractSearch::$EMPTY_STRING : AbstractSearch::$CHECKBOX_CHECKED;
		}
	}

	// Miscellaneous
	public function img_s_gif() {
		echo get_bloginfo('template_directory') . '/images/s.gif';
	}

	public function img($relative_path) {
		echo get_bloginfo('template_directory') . '/' . $relative_path;
	}


	/**
	 * @return boolean True if all treaties are checked, false otherwise
	 */
	function ui_is_alltreaties() {
		return true; // TODO
	}


	/* IMPORTANT HARDCODED AREA FOR INDEX & EXPLORER TREE OF TREATIES */
	function ui_get_treaties() {
		return array(
			'Biodiversity' => array(
				1 => array('title' => 'CBD', 'theme' => 'Biological Diversity',
					'children' => array(
						8 => array('title' => 'Cartagena Protocol', 'theme' => ''),
						9 => array('title' => 'Nagoya Protocol', 'theme' => '')
					)
				),
				3 => array('title' => 'CITES', 'theme' => 'Trade in Wildlife', 'children' => array()),
				4 => array('title' => 'CMS', 'theme' => 'Migratory Species', 'children' => array(
						10 => array('title' => 'AEWA', 'theme' => 'Water Birds')
					)
				),
				14 => array('title' => 'ITPGRFA', 'theme' => 'Plant', 'children' => array()),
				18 => array('title' => 'Ramsar', 'theme' => 'Wetlands', 'children' => array()),
				16 => array('title' => 'WHC', 'theme' => 'World Heritage', 'children' => array()),
			),
			'Chemicals / Waste' => array(
				2 => array('title' => 'Basel', 'theme' => 'Hazardous Wastes', 'children' => array()),
				20 => array('title' => 'Rotterdam', 'theme' => 'Pesticides', 'children' => array()),
				5 => array('title' => 'Stockholm', 'theme' => 'Org. Polutants', 'children' => array())
			),
			'Climate / Atmosphere' => array(
				15 => array('title' => 'UNFCCC', 'theme' => 'Climate Change', 'children' => array(
					17 => array('title' => 'Kyoto Protocol', 'theme' => ''))
				),
				19 => array('title' => 'UNCCD', 'theme' => 'Desertification', 'children' => array()),
				6 => array('title' => 'Vienna', 'theme' => 'Ozone', 'children' => array(
						7 => array('title' => 'Montreal Protocol', 'theme' => '')
					)
				)
			),
		);
	}

	public function ui_get_biodiversity_ids() { return array(1, 8, 9, 3, 4, 10, 14, 18, 16); }

	public function ui_get_chemicals_ids() { return array(2, 20, 5); }

	public function ui_get_climate_ids() { return array(15, 17, 19, 6, 7); }

	public function ui_get_other_treaties() {
		global $wpdb;
		$ret = array();
		$official = array(1, 8, 9, 3, 4, 10, 14, 18, 16, 2, 20, 5, 15, 17, 19, 6, 7);
		$other = $wpdb->get_results('SELECT * FROM `ai_treaty` WHERE `enabled` = 1 AND id NOT IN (' . implode(',', $official) . ')');
		foreach($other as $ob) {
			$ret[] = $ob->id;
		}
		return $ret;
	}

	function ui_get_treaties_ids($rec_children = null) {
		// return array(1, 8, 9, 3, 4, 10, 14, 18, 16, 2, 20, 5, 15, 17, 19, 6, 7);
		global $wpdb;
		return $wpdb->get_col('SELECT id FROM `ai_treaty` WHERE `enabled` = 1');
	}

	function ui_is_checked_treaty($id_section) {
		if(!defined('INFORMEA_SEARCH_PAGE')) {
			return true; // for explorer
		}
		$treaties = $this->get_treaties();
		if($id_section == 'theme-0') {
			$specific = array(1, 8, 9, 3, 4, 10, 14, 18, 16);
			return count(array_intersect($specific, $treaties)) > 0;
		}
		if($id_section == 'theme-1') {
			$specific = array(2, 20, 5);
			return count(array_intersect($specific, $treaties)) > 0;
		}
		if($id_section == 'theme-2') {
			$specific = array(15, 17, 19, 6, 7);
			return count(array_intersect($specific, $treaties)) > 0;
		}
		return in_array($id_section, $treaties);
	}

	function ui_get_terms() {
		global $wpdb;
		return $wpdb->get_results("SELECT * FROM voc_concept WHERE id_source = 9 ORDER BY term");
	}

	/* END * IMPORTANT HARDCODED AREA FOR INDEX & EXPLORER TREE OF TREATIES */

	function ui_get_months() {
		return array(1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
				5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
				9 => 'Sep', 10 => 'Oct',  11 => 'Nov', 12 => 'Dec');
	}

	function ui_get_start_month() {
		return isset($this->request['q_start_month']) ? intval($this->request['q_start_month']) : null;
	}

	function ui_get_end_month() {
		return isset($this->request['q_end_month']) ? intval($this->request['q_end_month']) : null;
	}

	function ui_get_start_year() {
		return isset($this->request['q_start_year']) ? intval($this->request['q_start_year']) : null;
	}

	function ui_compute_years() {
		global $wpdb;
		$ey = intval($wpdb->get_var('SELECT GREATEST(MAX(YEAR(`published`)), MAX(YEAR(`start`))) FROM ai_decision, ai_treaty;'));
		$sy = intval($wpdb->get_var('SELECT LEAST(MIN(YEAR(`published`)), MIN(YEAR(`start`))) FROM ai_decision, ai_treaty'));
		return range($sy, $ey);
	}

	function ui_get_end_year() {
		return isset($this->request['q_end_year']) ? intval($this->request['q_end_year']) : null;
	}

	function ui_get_selected_terms() {
		global $wpdb;
		$ret = array();
		$terms = $this->get_terms();
		if(count($terms)) {
			$ret = $wpdb->get_results('SELECT * FROM voc_concept WHERE id IN (' . implode(', ', $terms) . ')');
		}
		return $ret;
	}

	function ui_get_enabled_treaties() {
		global $wpdb;
		return $wpdb->get_col('SELECT id FROM ai_treaty WHERE enabled = 1');
	}

	/* ==== END USER INTERFACE METHODS ==== */

	function dump_request() {
		echo "<table border='1' style='border-collapse: collapse' width='100%'><tr><th>Parameter</th><th>code</th><th>Value</th></tr>";
		echo "<tr><td width='20%'>Treaties</td><td width='15%'>q_treaty</td><td>[" . implode(',', $this->get_treaties()) . "]</td></tr>";
		echo "<tr><td>Use AND/OR between terms</td><td>is_terms_or</td><td>" . ($this->is_terms_or() ? 'OR' : 'AND') . "</td></tr>";
		echo "<tr><td>Terms</td><td>q_term</td><td>[" . implode(',', $this->get_terms_with_synonyms()) . "]</td></tr>";
		echo "<tr><td>Free text search</td><td>q_freetext</td><td>" . $this->get_freetext() . "</td></tr>";
		echo "<tr><td>Start date</td><td>q_start_*</td><td>" . $this->get_start_date() . "</td></tr>";
		echo "<tr><td>End date</td><td>q_end_*</td><td>" . $this->get_end_date() . "</td></tr>";
		echo "<tr><td>Search treaties</td><td>q_use_treaties</td><td>" . ($this->is_use_treaties() ? 'true' : 'false') . "</td></tr>";
		echo "<tr><td>Search decisions</td><td>q_use_decisions</td><td>" . ($this->is_use_decisions() ? 'true' : 'false') . "</td></tr>";
		echo "<tr><td>Search meetings</td><td>q_use_meetings</td><td>" . ($this->is_use_meetings() ? 'true' : 'false') . "</td></tr>";
		echo "<tr><td>Look into terms OR fulltext</td><td>q_use_term_or_fulltext</td><td>" . ($this->is_use_term_or_fulltext() ? 'true' : 'false') . "</td></tr>";
		echo "<tr><td>Tab</td><td>q_tab</td><td>" . $this->get_selected_tab() . "</td></tr>";
		echo "<tr><td>Page</td><td>q_page</td><td>" . $this->get_page() . "</td></tr>";
		echo "<tr><td>Page size</td><td>q_page_size</td><td>" . $this->get_page_size() . "</td></tr>";
		echo "<tr><td>Sort direction</td><td>q_sort_direction</td><td>" . $this->get_sort_direction() . "</td></tr>";
		echo "</table>";
	}

}
