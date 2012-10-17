<?php



add_action('wp_ajax_nopriv_search_highlight', 'ajax_search_highlight');
add_action('wp_ajax_search_highlight', 'ajax_search_highlight');

add_action('wp_ajax_nopriv_search_more_results', 'ajax_search_more_results');
add_action('wp_ajax_search_more_results', 'ajax_search_more_results');

function ajax_search_highlight() {
    $ret = '';
    $search = InformeaSearch3::get_plain_searcher();
    $id_entity = $search->get_request_int('id');
    $entity_type = $search->get_request_value('entity');
    $items = $search->solr_highlight($id_entity, $entity_type);
    foreach($items as $item) {
        $ret .= '<div class="highlight">' . $item . '</div>';
    }

	header('Content-Type:text/html');
    echo $ret;
	die();
}

function ajax_search_more_results() {
    $goptions = get_option('informea_options');
    $options = array(
        'hostname' => $goptions['solr_server'],
        'path' => $goptions['solr_path'],
        'port' => $goptions['solr_port']
    );
    $search = new InformeaSearch3Tab1($_REQUEST, $options);

	header('Content-Type:text/html');
    $ret = $search->render_ajax();
    echo $ret;
	die();
}

/**
 *
 * START ===> FREETEXT? === [yes] ===> SOLR:solr ===> KEYWORDSEARCH? === [yes] ===> DB:db ===> MERGE(solr, db): results ===> ...
 *                    \                                   /       \                              /
 *                     \========= [no] ==================          ================ NO ==========
 */
class InformeaSearch3 extends AbstractSearch {

    protected $results = null;

    public static function get_searcher() {
		$goptions = get_option('informea_options');
		$options = array(
			'hostname' => $goptions['solr_server'],
			'path' => $goptions['solr_path'],
			'port' => $goptions['solr_port']
		);
        $tab = get_request_int('q_tab', 2);
        $type = 'InformeaSearch3Tab' . $tab;
        return new $type($_REQUEST, $options);
    }

    public static function get_plain_searcher() {
		$goptions = get_option('informea_options');
		$options = array(
			'hostname' => $goptions['solr_server'],
			'path' => $goptions['solr_path'],
			'port' => $goptions['solr_port']
		);
        return new InformeaSearch3($_REQUEST, $options);
    }

    protected $solr = null;

    /**
     * Construct new search object
     * @param array $request HTTP request parameters
     * @param array $solr_cfg SOLR configuration. Array must contain the following
     * values: array('hostname' => 'localhost', 'path' => '/informea-solr', 'port' => '8081');
     */
	public function __construct($request, $solr_cfg = array()) {
		parent::__construct($request);
        $cfg = array_merge(
                array('hostname' => '127.0.0.1', 'path' => '/informea-solr', 'port' => '8081'),
                $solr_cfg
        );
        $this->solr = new SolrClient($cfg);
	}


	/**
	 * @return dictionary with search results. Primary entities are
	 * keys: treaties, events, decisions.
	 */
	public function search() {
        $ret_db = $this->db_search();
        $ret_dec = $this->solr_search();
        $ret = $this->merge_results($ret_db, $ret_dec);
        return $ret;
	}


    /**
     * Render the results by choosing the correct renderer
     * @param boolean $all Render all results (for tab 1). Default FALSE
     * @return string HTML content
     */
    public function render($all = FALSE) {
        if($this->results == null) {
            $this->search($all);
        }
        $tab = $this->get_q_tab();
        $klass = 'InformeaSearchRendererTab'.$tab;
        $renderer = new $klass();
        return $renderer->render($this->results);
    }


    private function merge_results($db, $solr) {
        $ret = $solr;
        foreach($db['treaties'] as $id_treaty => $arr_treaty) {
            // Merge articles
            if(!isset($ret['treaties'][$id_treaty])) {
                $ret['treaties'][$id_treaty] = array('articles' => array(), 'decisions' => array());
            }
            if(!empty($arr_treaty['articles'])) {
                $ret['treaties'][$id_treaty]['articles'] += $arr_treaty['articles'];
            }

            // Merge decisions
            if(!empty($arr_treaty['decisions'])) {
                foreach($arr_treaty['decisions'] as $id_decision => $arr_decision) {
                    if(!isset($ret['treaties'][$id_treaty]['decisions'][$id_decision])) {
                        $ret['treaties'][$id_treaty]['decisions'][$id_decision] = array('paragraphs' => array(), 'documents' => array());
                    }
                    $ret['treaties'][$id_treaty]['decisions'][$id_decision]['paragraphs'] += $arr_decision['paragraphs'];
                    $ret['treaties'][$id_treaty]['decisions'][$id_decision]['documents'] += $arr_decision['documents'];
                }
            }
        }
        return $ret;
    }


    /**
     * Search the database, if we have tagging
     * @global object $wpdb WordPress database access
     * @return array Array with results in the form:
     * array(id_treaty :
     *      array(
     *          'articles' => array(1 : array( id_paragraph, ...)),
     *          'decisions' : array(1 :
     *              'paragraphs' => array(id_paragraph, ...)
     *          )
     *      )
     * )
     */
    protected function db_search() {
        $ret = array();
        if(!$this->is_using_terms()) {
            return array('treaties' => array());
        }
        global $wpdb;
        $terms = $this->get_terms();
        $sql_filter = sprintf(' WHERE a.id IN (%s)', implode(",", $terms));
        $sql = sprintf("
                SELECT a.id as id_term, 'treaty_paragraph' as `type`, b.id_treaty_article_paragraph as `id_entity`, c.id_treaty_article as `id_parent` FROM voc_concept a
                INNER JOIN ai_treaty_article_paragraph_vocabulary b ON a.id = b.id_concept
                INNER JOIN ai_treaty_article_paragraph c ON b.id_treaty_article_paragraph = c.id %s
                UNION
                SELECT a.id AS id_term, 'article' as `type`, b.id_treaty_article as `id_entity`, c.id_treaty as `id_parent` FROM voc_concept a
                INNER JOIN ai_treaty_article_vocabulary b ON a.id = b.id_concept
                INNER JOIN ai_treaty_article c ON b.id_treaty_article = c.id %s
                UNION
                SELECT a.id AS id_term, 'treaty' as `type`, b.id_treaty as `id_entity`, NULL as `id_parent` FROM voc_concept a
                INNER JOIN ai_treaty_vocabulary b ON a.id = b.id_concept %s
                UNION
                SELECT a.id AS id_term, 'decision_paragraph' as `type`, b.id_decision_paragraph as `id_entity`, c.id_decision as `id_parent` FROM voc_concept a
                INNER JOIN ai_decision_paragraph_vocabulary b ON a.id = b.id_concept
                INNER JOIN ai_decision_paragraph c ON b.id_decision_paragraph = c.id %s
                UNION
                SELECT a.id AS id_term, 'decision' as `type`, b.id_decision as `id_entity`, c.id_treaty as `id_parent` FROM voc_concept a
                INNER JOIN ai_decision_vocabulary b ON a.id = b.id_concept
                INNER JOIN ai_decision c ON b.id_decision = c.id %s
            ", $sql_filter, $sql_filter, $sql_filter, $sql_filter, $sql_filter
        );
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            $id_entity = $row->id_entity;
            switch($row->type) {
                case 'treaty':
                    $this->results_add_treaty($ret, $id_entity);
                    break;
                case 'article':
                    $this->results_add_article($ret, $row);
                    break;
                case 'treaty_paragraph':
                    $this->results_add_treaty_paragraph($ret, $row);
                    break;
                case 'decision':
                    $this->results_add_decision($ret, $row);
                    break;
                case 'decision_paragraph':
                    $this->results_add_decision_paragraph($ret, $row);
                    break;
            }
        }
        return array('treaties' => $ret);
    }


    /* SOLR Search */

    /**
     * Search the SOLR, if we have free text
     * @return array Array with results in the form:
     * array('treaties' => array(
     *      id_treaty :
     *            array(
     *                'articles' => array(1 : array( id_paragraph, ...)),
     *                'decisions' : array(1 :
     *                      'paragraphs' => array(id_paragraph, ...),
     *                      'documents' => array(id_document, ...)
     *                )
     *              )
     *      ),
     *      'events' => array(id_event1, ....)
     * )
     */
    protected function solr_search() {
        $ret = array('treaties' => array(), 'events' => array());
        $phrase = $this->get_freetext();
        if(!$this->is_dirty_search() || empty($phrase) || $phrase == '*' || $phrase == '?') {
            return $ret;
        }

		$query = new SolrQuery($phrase);
		$query->addField('id')->addField('entity_type')->addField('decision_id')->addField('treaty_article_id')->addField('treaty_id');
		$query->addFilterQuery($this->solr_entity_filter());
		$query->setRows(99999);
		try {
			$q_resp = $this->solr->query($query);
			$q_resp->setParseMode(SolrQueryResponse::PARSE_SOLR_DOC);
			$resp = $q_resp->getResponse();
			if(empty($resp->response->docs)) {
                return $ret;
            }
            foreach($resp->response->docs as $doc) {
                $id_entity = intval($doc->getField('id')->values[0]);
                $type = $doc->getField('entity_type')->values[0];
                $ob = new stdClass();
                switch($type) {
                    case 'treaty';
                        $this->results_add_treaty($ret['treaties'], $id_entity);
                        break;
                    case 'treaty_article';
                        $ob->id_entity = $id_entity;
                        $ob->id_parent = $doc->treaty_id->values[0]; // id_treaty
                        $this->results_add_article($ret['treaties'], $ob);
                        break;
                    case 'treaty_article_paragraph';
                        $ob->id_entity = $id_entity;
                        $ob->id_parent = $doc->treaty_article_id->values[0]; // id_article
                        $this->results_add_treaty_paragraph($ret['treaties'], $ob);
                        break;
                    case 'decision';
                        $ob->id_entity = $id_entity;
                        $ob->id_parent = CacheManager::get_treaty_for_decision($id_entity); // id_treaty
                        $this->results_add_decision($ret['treaties'], $ob);
                        break;
                    case 'decision_paragraph';
                        $ob->id_entity = $id_entity;
                        $ob->id_parent = $doc->decision_id->values[0]; // id_decision
                        $this->results_add_decision_paragraph($ret['treaties'], $ob);
                        break;
                    case 'decision_document';
                        $ob->id_entity = $id_entity;
                        $ob->id_parent = $doc->decision_id->values[0]; // id_decision
                        $this->results_add_decision_document($ret['treaties'], $ob);
                        break;
                    case 'event':
                        $ret['events'][] = $id_entity;
                        break;
                    default:
                        throw new Exception('Unknown entity type:' . $type);
                }
            }
		} catch(Exception $e) {
			error_log('Failed Solr query');
			error_log(print_r($e, true));
		}
        return $ret;
    }


	protected function solr_entity_filter() {
		$arr = array();
		if($this->is_use_decisions()) {
			$arr[] = 'entity_type:decision';
			$arr[] = 'entity_type:decision_paragraph';
			$arr[] = 'entity_type:decision_document';
		}
		if($this->is_use_meetings()) {
			$arr[] = 'entity_type:event';
		}
		if($this->is_use_treaties()) {
			$arr[] = 'entity_type:treaty';
			$arr[] = 'entity_type:treaty_article';
			$arr[] = 'entity_type:treaty_article_paragraph';
		}
		if(!count($arr)) {
			$arr[] = 'entity_type:dummy_yield_zero_results';
		}
		return '(' . implode(' OR ', $arr) . ')';
	}


    public function solr_highlight($id_entity, $entity_type, $fragment_size = 500) {
        $ret = array();
        $phrase = $this->get_freetext();
        if(!$this->is_dirty_search() || empty($phrase) || $phrase == '*' || $phrase == '?') {
            return $ret;
        }
        $excerpt = '';
		$query = new SolrQuery($phrase);
		$query->addField('id')->addField('entity_type')->addField('decision_id')->addField('treaty_article_id')->addField('treaty_id');
		$query->addFilterQuery('entity_type:' . $entity_type . ' AND id:' . $id_entity);
		$query->setRows(99999);
        $query->setHighlight(true);
        $query->addHighlightField('text');
        $query->setHighlightSnippets(5);
        $query->setHighlightSimplePre('$$$$$');
        $query->setHighlightSimplePost('#####');
        $query->setHighlightFragsize($fragment_size);
        $query->setHighlightMaxAnalyzedChars(200000); // To analyse entire document
        $uid = "$id_entity $entity_type";
        try {
			$q_resp = $this->solr->query($query);
			$q_resp->setParseMode(SolrQueryResponse::PARSE_SOLR_DOC);
			$resp = $q_resp->getResponse();
			if(isset($resp->response->docs[0])) {
				if(isset($resp->highlighting)) {
					if($resp->highlighting->offsetExists($uid)) {
						$field = $resp->highlighting->offsetGet($uid);
						$field = $field->offsetGet('text');
						if($field) {
							foreach($field as $snippet) {
								$excerpt .= htmlspecialchars(strip_tags($snippet));
							}
							$excerpt = str_replace('$$$$$', '<strong class="highlight">', $excerpt);
							$excerpt = str_replace('#####', '</strong>', $excerpt);
							$excerpt = str_replace('&nbsp;', ' ', $excerpt);
                            $excerpt = trim($excerpt);
                            $ret[] = $excerpt;
						}
					}
				}
			}
		} catch(Exception $e) {
			error_log('Failed Solr query');
			error_log(print_r($e, true));
		}
        return $ret;
    }
}



/**
 * Handle results for the first tab - ordered chronologically + paginated
 */
class InformeaSearch3Tab1 extends InformeaSearch3 {

    /**
     * Construct new search object
     * @param array $request HTTP request parameters
     * @param array $solr_cfg SOLR configuration. Array must contain the following
     * values: array('hostname' => 'localhost', 'path' => '/informea-solr', 'port' => '8081');
     */
	public function __construct($request, $solr_cfg = array()) {
		parent::__construct($request, $solr_cfg);
	}


    /**
     * Do the search and return renderable items
     * @param boolean $all Return all results, ignoring pagination
     * @return array Array of loaded entities
     */
    public function search($all = TRUE) {
        $results = parent::search();
        $treaties = $this->is_use_treaties() ? array_keys($results['treaties']) : array();
        $cache_decisions = array();
        $decisions = array();
        if($this->is_use_decisions()) {
            foreach(array_keys($results['treaties']) as $id_treaty) {
                $decisions += array_keys($results['treaties'][$id_treaty]['decisions']);
                $cache_decisions += $results['treaties'][$id_treaty]['decisions'];
            }
        }
        $events = $this->is_use_meetings() ? $results['events'] : array();
        $ids = $this->sort_and_paginate($treaties, $decisions, $events, $all);
        $ret = array();
        foreach($ids as $skeleton) {
            if($skeleton->type == 'decision') {
                $cached = $cache_decisions[$skeleton->id_entity];
                $ret[] = CacheManager::load_decision_hierarchy($skeleton->id_entity, $cached);
            } else if($skeleton->type == 'treaty') {
                $cached = $results['treaties'][$skeleton->id_entity];
                $cached['decisions'] = array();
                $ret[] = CacheManager::load_treaty_hierarchy($skeleton->id_entity, $cached);
            } else if($skeleton->type == 'event') {
                $ret[] = CacheManager::load_event($skeleton->id_entity);
            } else {
                throw new Exception($skeleton->type);
            }
        }
        $this->results = $ret;
        return $this->results;
    }


    public function render_ajax() {
        if($this->results == null) {
            $this->search(FALSE);
        }
        $renderer = new InformeaSearchRendererTab1Ajax();
        return $renderer->render($this->results);
    }


    protected function sort_and_paginate($treaties, $decisions, $events, $all = FALSE) {
        global $wpdb;
        // Apply sorting and pagination
        $where_treaty = count($treaties) > 0 ? sprintf('WHERE id IN (%s)', implode(',', $treaties)) : ' WHERE 1 <> 1 ';
        $where_decision = count($decisions) > 0 ? sprintf('WHERE id IN (%s)', implode(',', $decisions)) : ' WHERE 1 <> 1 ';
        $where_events = count($events) > 0 ? sprintf('WHERE id IN (%s)', implode(',', $events)) : ' WHERE 1 <> 1 ';
        $start = $this->get_page_size() * $this->get_page();
        $end = $this->get_page_size();
        $limit = $all ? '' : sprintf(' LIMIT %s, %s', $start, $end);
        $sql = sprintf("
            SELECT * FROM (
                SELECT id as `id_entity`, 'treaty' as `type`, `start` as `date` FROM ai_treaty %s
                UNION
                SELECT id as `id_entity`, 'decision' as `type`, published as `date` FROM ai_decision %s
                UNION
                SELECT id as `id_entity`, 'event' as `type`, start as `date` FROM ai_event %s
            ) soup ORDER BY `date` %s %s",
            $where_treaty,
            $where_decision,
            $where_events,
            $this->get_sort_direction(),
            $limit
        );
        return $wpdb->get_results($sql);
    }
}

/**
 * Handle results for the second tab - tree structure with treaties as roots
 */
class InformeaSearch3Tab2 extends InformeaSearch3 {

    /**
     * Construct new search object
     * @param array $request HTTP request parameters
     * @param array $solr_cfg SOLR configuration. Array must contain the following
     * values: array('hostname' => 'localhost', 'path' => '/informea-solr', 'port' => '8081');
     */
	public function __construct($request, $solr_cfg = array()) {
		parent::__construct($request, $solr_cfg);
	}


    /**
     * Do the search and return the treaties (root nodes) that match the results
     * @return array Array of loaded treaties
     */
    public function search() {
        $results = parent::search();
        $ret = array();
        foreach($results['treaties'] as $id_treaty => &$data) {
            if(!$this->is_use_decisions()) {
                $data['decisions'] = array();
            }
            $ret[$id_treaty] = CacheManager::load_treaty_hierarchy($id_treaty, $data);
        }
        $this->results = $ret;
        return $this->results;
    }
}


/**
 * Handle results for the second tab - tree structure with treaties as roots
 */
class InformeaSearch3Tab3 extends InformeaSearch3 {

    /**
     * Construct new search object
     * @param array $request HTTP request parameters
     * @param array $solr_cfg SOLR configuration. Array must contain the following
     * values: array('hostname' => 'localhost', 'path' => '/informea-solr', 'port' => '8081');
     */
	public function __construct($request, $solr_cfg = array()) {
		parent::__construct($request, $solr_cfg);
	}


    /**
     * Do the search and return the treaties (root nodes) that match the results
     * @return array Array of loaded treaties
     */
    public function search() {
        $results = parent::search();
        $ret = array();
        foreach($results['treaties'] as $id_treaty => &$data) {
            if(!$this->is_use_decisions()) {
                $data['decisions'] = array();
            }
            $treatyOb = CacheManager::load_treaty($id_treaty);
            if($treatyOb->regional == '0') {
                $ret[$id_treaty] = CacheManager::load_treaty_hierarchy($id_treaty, $data);
            }
        }
        $this->results = $ret;
        return $this->results;
    }
}

/**
 * Handle results for the second tab - tree structure with treaties as roots
 */
class InformeaSearch3Tab4 extends InformeaSearch3 {

    /**
     * Construct new search object
     * @param array $request HTTP request parameters
     * @param array $solr_cfg SOLR configuration. Array must contain the following
     * values: array('hostname' => 'localhost', 'path' => '/informea-solr', 'port' => '8081');
     */
	public function __construct($request, $solr_cfg = array()) {
		parent::__construct($request, $solr_cfg);
	}


    /**
     * Do the search and return the treaties (root nodes) that match the results
     * @return array Array of loaded treaties
     */
    public function search() {
        $results = parent::search();
        $ret = array();
        foreach($results['treaties'] as $id_treaty => &$data) {
            $data['articles'] = array();
            $ret[$id_treaty] = CacheManager::load_treaty_hierarchy($id_treaty, $data);
        }
        $this->results = $ret;
        return $this->results;
    }
}


/**
 * Handle results for the second tab - tree structure with treaties as roots
 */
class InformeaSearch3Tab5 extends InformeaSearch3 {

    /**
     * Construct new search object
     * @param array $request HTTP request parameters
     * @param array $solr_cfg SOLR configuration. Array must contain the following
     * values: array('hostname' => 'localhost', 'path' => '/informea-solr', 'port' => '8081');
     */
	public function __construct($request, $solr_cfg = array()) {
		parent::__construct($request, $solr_cfg);
	}


    /**
     * Do the search and return the treaties (root nodes) that match the results
     * @return array Array of loaded treaties
     */
    public function search() {
        $results = parent::search();
        $ret = array();
        foreach($results['treaties'] as $id_treaty => &$data) {
            if(!$this->is_use_decisions()) {
                $data['decisions'] = array();
            }
            $treatyOb = CacheManager::load_treaty($id_treaty);
            if($treatyOb->regional == '1') {
                $ret[$id_treaty] = CacheManager::load_treaty_hierarchy($id_treaty, $data);
            }
        }
        $this->results = $ret;
        return $this->results;
    }
}