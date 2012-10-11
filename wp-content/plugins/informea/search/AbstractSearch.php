<?php

/**
 * Basic search functionality such as extracting search parameters from request
 */
class AbstractSearch {
	private static $CHECKBOX_CHECKED = ' checked="checked"';
	private static $SELECT_SELECTED = ' selected="selected"';
	private static $EMPTY_STRING = '';
	private static $CSS_CHECKBOX_CHECKED = ' checked';
	private static $CSS_CHECKBOX_UNCHECKED = ' unchecked';

    protected static $INPUT_DATE_FORMAT = '%d/%m/%Y';
	protected static $OUTPUT_DATE_FORMAT = '%Y-%m-%d';
	protected $request;

    /**
     * Constructor
     * @param type $request Array with request variables. Can be $_REQUEST
     */
	public function __construct($request) {
		$this->request = $request;
	}


    /**
     * Do we have freetext search?
     * @return boolean True or False
     */
	public function is_dirty_search() {
        return strlen($this->get_freetext()) > 0;
	}


	/**
	 * Retrieve search parameter
	 * @param string $name Parameter name
	 * @param mixed $default Default value
	 * @param boolean $trim Trim resulting string
	 * @return mixed parameter value (string, integer, array) or default value
	 */
	function get_request_value($name, $default = NULL, $trim = TRUE) {
		$ret = $default;
		if (!empty($this->request[$name])) {
            $ret = $this->request[$name];
            if(is_numeric($ret)) {
                $ret = $ret + 0;
            } else if(is_array($ret)) {
                if($trim) {
                    foreach($ret as &$item) {
                        $item = trim($item);
                    }
                }
            } else if(is_string($ret)) {
                if($trim) {
                    $ret = trim($ret);
                }
            }
		}
		return $ret;
	}


    /**
     * Retrieve integer from request
     * @param string $name Parameter name
     * @param integer $default Default value
     * @return integer Parameter value or default
     */
    function get_request_int($name, $default = 1) {
        $ret = $this->get_request_value($name, $default, FALSE);
        return intval($ret);
    }


	/**
	 * Get the list of treaties to filter by
	 * @return array Array with IDs of the selected treaties
	 */
	function get_treaties() {
		$ret = array();
		$tmp_treaties = $this->get_request_value('q_treaty');
        if(!empty($tmp_treaties)) {
            foreach($tmp_treaties as $id) {
                $ret[] = intval($id);
            }
        }
		return $ret;
	}


	/**
     * Checks to see if we have AND or OR between terms
     * @return boolean TRUE if we have OR between terms.
	 */
	function is_terms_or() {
		return $this->get_request_value('q_term_or') == 'or';
	}


	/**
	 * If non-null, user selected some terms to lookup entities
     * @return array Array with terms we want to add to the search
	 */
	function get_terms() {
		$ret = array();
		$terms = $this->get_request_value('q_term', array(), false);
		foreach($terms as $id) {
			$ret[] = intval($id);
		}
		return $ret;
	}


	/**
     * Checks to see if we have terms entered
	 * @return true if we have filtering on terms
	 */
	function is_using_terms() {
		$r = $this->get_terms();
        return !empty($r);
	}


	/**
	 * Get full text search string
     * @param string $default Default values. Empty string.
     * @return string Text to search
	 */
	function get_freetext($default = '') {
		$ret = $this->get_request_value('q_freetext', $default);
		return stripslashes($ret);
	}

	/**
	 * Get the minimal (oldest) filter date
     * @param boolean $solr If true, format according to SOLR standard
	 */
	function get_start_date($solr = false) {
		$ret = null;
		$sm = $this->get_request_int('q_start_month', 0);
		$sy = $this->get_request_int('q_start_year', 0);
		if($sm > 0 && $sm < 13 && $sy > 0) {
			if($solr) {
				$ret = sprintf("%04d-%02d-01T00:00:00Z", $sy, $sm);
			} else {
				$ret = sprintf("%04d-%02d-01", $sy, $sm);
			}
		}
		return $ret;
	}

	/**
	 * Get the maximal (newest) filter date
     * @param boolean $solr If true, format according to SOLR standard
	 */
	function get_end_date($solr = false) {
		$ret = null;
		$em = $this->get_request_int('q_end_month', 0);
		$ey = $this->get_request_int('q_end_year', 0);
		if($em > 0 && $em < 13 && $ey > 0) {
			if($solr) {
				$ret = sprintf("%04d-%02d-31T00:00:00Z", $ey, $em);
			} else {
				$ret = sprintf("%04d-%02d-31", $ey, $em);
			}
		}
		return $ret;
	}


	/**
	 * Get status of checkbox "search in treaties"
     * @return boolean True if paramter on request
	 */
	function is_use_treaties() {
		return isset($this->request['q_use_treaties']);
	}


	/**
	 * Get status of checkbox "search in decisions"
     * @return boolean True if paramter on request
	 */
	function is_use_decisions() {
		return isset($this->request['q_use_decisions']);
	}


	/**
	 * Get status of checkbox "search in meetings"
     * @return boolean True if paramter on request
	 */
	function is_use_meetings() {
		return isset($this->request['q_use_meetings']);
	}


	/**
	 * Get the currently selected tab
     * @return integer The current tab. Default 1
	 */
	function get_q_tab() {
        $ret = $this->get_request_int('q_tab', 1);
        return $ret == 0 ? 1 : $ret;
	}


	/**
	 * Get the current results page
     * @return integer The current results page. Default 0
	 */
	function get_page() {
		return $this->get_request_int('q_page', 0);
	}


	/**
	 * Get the number of results returned per page
     * @return integer The results per page. Default 10
	 */
	function get_page_size() {
		$ret = $this->get_request_int('q_page_size', 10);
        return $ret == 0 ? 10 : $ret;
	}


	/**
	 * Get the sorting direction for results.
	 * @return string Possible values (ASC/DESC). Default DESC.
	 */
	function get_sort_direction() {
		$order = $this->get_request_value('q_sort_direction', 'DESC');
		if(strcasecmp('ASC', $order) == 0) {
			return 'ASC';
		} else {
			return 'DESC';
		}
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


    /* Various utilities */

    /**
     * Add an treaty to the search results
     * @param array $results Reference to the actual results
     * @param string $id_treaty ID of the treaty
     */
    function results_add_treaty(&$results, $id_treaty) {
        if(!array_key_exists($id_treaty, $results)) {
            $results[$id_treaty] = array(
                'articles' => array(),
                'decisions' => array()
            );
        }
    }


    /**
     * Add an article to the search results (via attached treaty)
     * @param array $results Reference to the actual results
     * @param type $ob Actual RAW object (id_entity = article id & id_parent = id_treaty)
     */
    function results_add_article(&$results, $ob) {
        $id_article = intval($ob->id_entity);
        $id_treaty = intval($ob->id_parent);
        // Add treaty if it doesn't exist
        $this->results_add_treaty($results, $id_treaty);
        if(!array_key_exists($id_article, $results[$id_treaty]['articles'])) {
            $results[$id_treaty]['articles'][$id_article] = array();
        }
    }


    /**
     * Add treaty article paragraph to the list of results
     * @param type $results Reference to the actual results
     * @param type $ob Actual RAW object (id_entity = paragraph id & id_parent = article id)
     */
    function results_add_treaty_paragraph(&$results, $ob) {
        $id_paragraph = intval($ob->id_entity);
        $id_article = intval($ob->id_parent);
        $id_treaty = CacheManager::get_treaty_for_treaty_paragraph($id_paragraph);
        $this->results_add_treaty($results, $id_treaty);

        $article = new stdClass();
        $article->id_entity = $id_article;
        $article->id_parent = $id_treaty;
        $this->results_add_article($results, $article);

        if(!array_key_exists($id_paragraph, $results[$id_treaty]['articles'][$id_article])) {
            $results[$id_treaty]['articles'][$id_article][] = $id_paragraph;
        }
    }


    /**
     * Add decision to the list of results
     * @param array $results Reference to the actual results
     * @param type $ob Actual RAW object (id_entity = id_decision & id_parent = id_treaty)
     */
    function results_add_decision(&$results, $ob) {
        $id_treaty = intval($ob->id_parent);
        $id_decision = intval($ob->id_entity);
        $this->results_add_treaty($results, $id_treaty);
        if(!array_key_exists($id_decision, $results[$id_treaty]['decisions'])) {
            $results[$id_treaty]['decisions'][$id_decision] = array();
        }
    }


    /**
     * Add decision paragraph to the list of results
     * @param type $results Reference to the actual results
     * @param type $ob Actual RAW object (id_entity = id_paragraph & id_parent = id_decision)
     */
    function results_add_decision_paragraph(&$results, $ob) {
        $id_paragraph = intval($ob->id_entity);
        $id_decision = intval($ob->id_parent);
        $id_treaty = CacheManager::get_treaty_for_decision_paragraph($id_paragraph);
        $this->results_add_treaty($results, $id_treaty);

        $decision = new stdClass();
        $decision->id_entity = $id_decision;
        $decision->id_parent = $id_treaty;
        $this->results_add_decision($results, $decision);

        if(!array_key_exists($id_paragraph, $results[$id_treaty]['decisions'][$id_decision])) {
            $results[$id_treaty]['decisions'][$id_decision][] = $id_paragraph;
        }
    }


	/* ==== USER INTERFACE METHODS ==== */
	function ui_get_freetext_default() {
		return '';
	}


	function ui_get_freetext($default = null) {
		return $this->get_freetext($default);
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

	function ui_get_treaties_ids() {
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
