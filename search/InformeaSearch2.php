<?php
include_once ("AbstractSearch.php");
include_once ("SearchResult2.php");
include_once ("SolrInvoker.php");

// Results container - implements also pagination features
class InformeaSearchResult2 {
	private $results;
	private $count;
	private $search;


	function __construct($results, $count, $search) {
		$this->results = $results;
		$this->count = intval($count);
		$this->search = $search;
	}


	public function get_count() {
		return $this->count;
	}


	public function get_results() {
		return $this->results;
	}


	public function get_pages_count() {
		$page_size = $this->search->get_page_size();
		if($page_size !== null) {
			return ceil($this->count / $page_size);
		}
		return 1;
	}


	public function has_next_page() {
		$current_page = $this->search->get_page();
		return $current_page + 1 < $this->get_pages_count();
	}


	public function has_prev_page() {
		$current_page = $this->search->get_page();
		return $current_page > 0;
	}


	public function get_current_page() {
		return $this->search->get_page();
	}
}


class InformeaSearch2 extends AbstractSearch {

	public $solr_invoker = null;

	public function __construct($request) {
		parent::__construct($request);
		global $wpdb;
		$this->db = $wpdb;
		$goptions = get_option('informea_options');
		$options = array(
			'hostname' => $goptions['solr_server'],
			'path' => $goptions['solr_path'],
			'port' => $goptions['solr_port']
		);
		$this->solr_invoker = new InformeaSolrInvoker($request, $options);
	}


	function /* InformeaSearchResult2 */ search($debug = false) {
		$ret = null;
		if(!$this->is_use_decisions() && !$this->is_use_treaties() && !$this->is_use_meetings()) {
			imea_log('invalid search - no entities specified');
			return new InformeaSearchResult2(array(), 0, $this);
		}

		$results = $this->solr_invoker->search($debug);
		$interim = $this->build_hierarchy($results);
		$interim = $this->handle_tagged_items($interim);
		$tab = $this->get_q_tab($ret);
		if($tab == 1) {
			$ret = $this->results_tab_1($interim);
		} else if($tab == 2) {
			$this->sort_treaties($interim->treaties);
			$ret = new InformeaSearchResult2($interim->treaties, count($interim->treaties), $this);
		} else if($tab == 3) {
			// treaties w/ only decisions, no treaty data
			$interim->decisions = array();
			foreach($interim->treaties as $treaty) {
				$treaty->decisions = array();
				if(empty($treaty->articles)) {
					unset($interim->treaties[$treaty->id]);
				}
			}
			foreach($interim->treaties as $id => &$t) {
				if($t->db->regional == 1) {
					unset($interim->treaties[$id]);
				}
			}
			$this->sort_treaties($interim->treaties);
			$ret = new InformeaSearchResult2($interim->treaties, count($interim->treaties), $this);
		} else if($tab == 5) {
			// treaties w/ only decisions, no treaty data
			$interim->decisions = array();
			foreach($interim->treaties as $treaty) {
				$treaty->decisions = array();
				if(empty($treaty->articles)) {
					unset($interim->treaties[$treaty->id]);
				}
			}
			foreach($interim->treaties as $id => &$t) {
				if($t->db->regional != 1) {
					unset($interim->treaties[$id]);
				}
			}
			$this->sort_treaties($interim->treaties);
			$ret = new InformeaSearchResult2($interim->treaties, count($interim->treaties), $this);
		} else if($tab == 4) {
			// treaties w/o decisions, only treaty data
			$interim->articles = array();
			foreach($interim->treaties as $treaty) {
				$treaty->articles = array();
				if(empty($treaty->decisions)) {
					unset($interim->treaties[$treaty->id]);
				}
			}
			$this->sort_treaties($interim->treaties);
			$ret = new InformeaSearchResult2($interim->treaties, count($interim->treaties), $this);
		}
		return $ret;
	}

	function sort_treaties(/* array */ &$treaties) {
		$global = array();
		$regional = array();
		foreach($treaties as $id => $treaty) {
			if($treaty->db->regional == 1) {
				$regional[$id] = $treaty;
			} else {
				$global[$id] = $treaty;
			}
		}
		usort($global, function($a, $b) { return ($a == $b) ? 0 : ($a < $b) ? -1 : 1;  });
		usort($regional, function($a, $b) { return ($a == $b) ? 0 : ($a < $b) ? -1 : 1;  });
		$treaties = array_merge($global, $regional);
	}

	/*
	 * Order the results by date (ascending or descending)
	 */
	function results_tab_1($interim) {
		global $wpdb;
		$count = 0;
		$results = array();
		$meetings_ob = array();
		$meetings = array();
		$decisions = array();

		// In first tab remove those treaties that have only decisions set
		foreach($interim->treaties as $id => $t) {
			if(empty($t->articles)) {
				unset($interim->treaties[$id]);
			}
		}
		// Do the final ordering/pagination
		$fsql = " FROM (";
		if($this->is_use_treaties()) {

			// Find all the treaties yielded in results (even those with paragraphs)
			$fsql .= "SELECT 'treaty' AS `entity_type`, C.`id` AS `entity_pk`, C.`id` AS `id_treaty`, C.`start` AS `entity_date`
						FROM `ai_treaty` C WHERE C.id IN (-1 " . (count($interim->treaties) > 0 ? ',' . implode(',', array_keys($interim->treaties)) : '') . ")";
		}
		if($this->is_use_meetings()) {
			$meetings_ob = array();
			if(!empty($interim->events)) {
				$meetings_ob = $this->cache_table('SELECT a.*, b.`short_title` AS `treaty` FROM `ai_event` a INNER JOIN `ai_treaty` b ON b.`id` = a.`id_treaty`  WHERE 1 = 1 ', array_keys($interim->events));
			}
			foreach($meetings_ob as $ob) {
				$meetings[$ob->id] = new MeetingSearchResult2($ob->id, $this, $ob, true);
			}
			// Compute the meetings only for this view
			if($this->is_use_treaties()) {
				$fsql .= ' UNION ALL ';
			}
			$fsql .= "SELECT 'event' AS `entity_type`, E.`id` AS `entity_pk`, E.`id_treaty` AS `id_treaty`, E.`start` AS `entity_date`
						FROM `ai_event` E WHERE E.id IN (-1 " . (count($meetings) > 0 ? ',' . implode(',', array_keys($meetings)) : '') . ")";
		}
		if($this->is_use_decisions()) {
			if($this->is_use_treaties() || $this->is_use_meetings()) {
				$fsql .= ' UNION ALL ';
			}
			$fsql .= "SELECT 'decision' AS `entity_type`, D.`id` AS `entity_pk`, D.`id_treaty` AS `id_treaty`, D.`published` AS `entity_date`
						FROM `ai_decision` D WHERE D.id IN (-1 " . (count($interim->decisions) > 0 ? ',' . implode(',', array_keys($interim->decisions)) : '') . ")";
		}
		$fsql .= ") ftable WHERE 1=1 ";

		// Filtering by treaty
		$ts = $this->get_treaties();
		if(!empty($ts)) { // Somehow redundant due to build_hierarchy
			$fsql .= ' AND `id_treaty` IN (' . implode(',', $this->get_treaties()) . ') ';
		} else {
			$fsql .= ' AND TRUE = FALSE ';
		}

		// Filtering by date
		if($this->get_start_date()) {
			$fsql .= ' AND entity_date >= STR_TO_DATE(\'' . $this->get_start_date() . '\', \'%Y-%m-%d\') ';
		}

		if($this->get_end_date()) {
			$fsql .= ' AND entity_date <= STR_TO_DATE(\'' . $this->get_end_date() . '\', \'%Y-%m-%d\') ';
		}

		// Count
		$csql = 'SELECT COUNT(*) ' . $fsql;
		$count = $wpdb->get_var($csql);
		$fsql = "SELECT * " . $fsql . " ORDER BY ftable.entity_date " . $this->get_sort_direction();

		$page = $this->get_page();
		$psize = $this->get_page_size();
		$fsql .= ' LIMIT ' . ($page * $psize) . ', ' . $psize;
		// var_dump($fsql);
		$results = $wpdb->get_results($fsql);
		// var_dump(count($results));

		foreach($results as &$row) {
			if($row->entity_type == 'event') {
				$row = $meetings[$row->entity_pk];
			}
			else if($row->entity_type == 'decision') {
				$row = $interim->decisions[$row->entity_pk];
			}
			else if($row->entity_type == 'treaty') {
				$row = $interim->treaties[$row->entity_pk];
			} else {
				die("He's dead, Jim!");
			}
		}
		return new InformeaSearchResult2($results, $count, $this);
	}

	/**
	 * Build objects hierarchy from the Solr search results, as:
	 * + Treaties
	 *     +--> articles
	 *           +--> paragraphs
	 *     +--> decisions
	 */
	function build_hierarchy($solr_results) {
		$ret = new StdClass();
		$ret->treaties = array();
		$ret->decisions = array();
		$ret->events = $solr_results->events; // Put events to avoid propagation of $solr_results

		$start = microtime_float();
		// Step 1. Build the cache structures from database
		$paragraphs1 = $this->cache_hashmap('SELECT a.`id` AS `id_paragraph`, a.`id_treaty_article` AS `id_article` FROM `ai_treaty_article_paragraph` a ',
					array_merge(array(-1), array_keys($solr_results->treaties_articles_paragraphs)), 'id_paragraph', 'id_article');
		$articles1 = $this->cache_hashmap('SELECT a.`id` AS `id_article`, a.`id_treaty` FROM `ai_treaty_article` a ',
					array_merge(array(-1), array_merge(array_keys($solr_results->treaties_articles), array_values($paragraphs1))), 'id_article', 'id_treaty');
		$decisions1 = $this->cache_hashmap('SELECT a.`id` AS id_decision, a.`id_treaty` FROM `ai_decision` a ',
					array_merge(array(-1), array_keys($solr_results->decisions)), 'id_decision', 'id_treaty');
		$documents1 = $this->cache_hashmap('SELECT a.`id` AS `id_document`, a.`id_decision` FROM `ai_document` a ',
					array_merge(array(-1), array_keys($solr_results->decisions_documents)), 'id_document', 'id_decision');

		$id_treaties = array_merge(array_keys($solr_results->treaties), $articles1, $decisions1);
		$id_treaties = array_intersect($this->get_treaties(), $id_treaties); // Remove disabled treaties (WHERE ai_treaty.`enabled` <> 1)
		$id_treaties = array_merge(array(-1), $id_treaties);
		$treaties = $this->cache_table('SELECT a.* FROM `ai_treaty` a WHERE a.`enabled` = 1', $id_treaties); // [id_treaty] = [StdClass(treaty)]

		$articles = array(); // [id_article] = [id_treaty]
		$paragraphs = array(); // [id_paragraph] = [id_article]
		$decisions = array(); // [id_decision] = [id_treaty]
		$documents = array(); // [id_document] = [id_decision]
		$decisions_paragraphs = array(); // [id_paragraph] = [id_decision]

		// Step 2. Filter out junk -
		// TODO: This step may be removed when we ensure that Solr contains only valid data (not disabled treaties etc.). See ticket: http://redmine.eaudeweb.ro/issues/show/732)
		$treaties_keys = array_keys($treaties);
		foreach($articles1 as $id_article => $id_treaty) {
			if(in_array($id_treaty, $treaties_keys)) {
				$articles[$id_article] = $id_treaty;
			}
		}
		unset($articles1);

		$articles_keys = array_keys($articles);
		foreach($paragraphs1 as $id_paragraph => $id_article) {
			if(in_array($id_article, $articles_keys)) {
				$paragraphs[$id_paragraph] = $id_article;
			}
		}
		unset($paragraphs1);

		foreach($decisions1 as $id_decision => $id_treaty) {
			if(in_array($id_treaty, $treaties_keys)) {
				$decisions[$id_decision] = $id_treaty;
			}
		}
		unset($decisions1);

		$decisions_keys = array_keys($decisions);
		foreach($documents1 as $id_document => $id_decision) {
			if(in_array($id_decision, $decisions_keys)) {
				$documents[$id_document] = $id_decision;
			}
		}
		unset($documents1);

		// Step 3. Hit the database and load the remaining objects used for response
		$paragraphs_ob = $this->cache_table('SELECT a.* FROM `ai_treaty_article_paragraph` a WHERE 1 = 1 ', array_keys($paragraphs)); // [id_paragraph] = [StdClass(id_paragraph)]
		$articles_ob = $this->cache_table('SELECT a.* FROM `ai_treaty_article` a WHERE 1 = 1 ', array_keys($articles)); // [id_article] = [StdClass(id_article)]
		$decisions_ob = $this->cache_table('SELECT a.*, b.`short_title` as `treaty` FROM `ai_decision` a INNER JOIN `ai_treaty` b ON b.`id` = a.`id_treaty` WHERE 1 = 1 ', array_keys($decisions)); // [id_decision] = [StdClass(id_decision)]
		$documents_ob = $this->cache_table('SELECT a.* FROM `ai_document` a WHERE 1 = 1 ', array_keys($documents)); // [id_document] = [StdClass(id_document)]

		// Additionally cache treaty for each paragraph
		$paragraphs_treaty = $this->cache_hashmap('SELECT a.`id` AS `id_paragraph`, b.`id_treaty` AS `id_treaty` FROM `ai_treaty_article_paragraph` a
					INNER JOIN `ai_treaty_article` b ON a.`id_treaty_article` = b.`id` ', array_keys($paragraphs), 'id_paragraph', 'id_treaty'); // [id_paragraph] = [id_treaty]

		// Step 4. Build the hierarchical relationship between entities
		$roots = array();
		// 4.1 Put treaties into output
		$tmp_tk = array_keys($solr_results->treaties);
		foreach($treaties as $id_treaty => $db_treaty) {
			$treaty = new TreatySearchResult2($id_treaty, $this, $db_treaty, in_array($id_treaty, $tmp_tk));
			$roots[$id_treaty] = $treaty;
		}
		// 4.2 Put articles into treaties
		$tmp_tak = array_keys($solr_results->treaties_articles);
		foreach($articles as $id_article => $id_treaty) {
			$article = new TreatyArticleSearchResult2($id_article, $this, $articles_ob[$id_article], in_array($id_article, $tmp_tak));
			$treaty = $roots[$id_treaty];
			$treaty->articles[$id_article] = $article;
		}
		// 4.3 Put paragraphs into treaty articles
		foreach($paragraphs as $id_paragraph => $id_article) {
			$id_treaty = $paragraphs_treaty[$id_paragraph];
			$treaty = $roots[$id_treaty];
			$article = $treaty->articles[$id_article];
			$paragraph = new TreatyParagraphSearchResult2($id_paragraph, $this, $paragraphs_ob[$id_paragraph], true);
			$article->paragraphs[$id_paragraph] = $paragraph;
		}

		// 4.4 Put decisions into treaties
		$tmp_dk = array_keys($solr_results->decisions);
		$root_decisions = array();
		foreach($decisions as $id_decision => $id_treaty) {
			$treaty = $roots[$id_treaty];
			$decision = new DecisionSearchResult2($id_decision, $this, $decisions_ob[$id_decision], in_array($id_decision, $tmp_dk));
			$treaty->decisions[$id_decision] = $decision;
			$root_decisions[$id_decision] = $decision;
		}
		$ret->treaties = $roots;
		$ret->decisions = $root_decisions;

		// 4.5 Put documents into decisions
		foreach($documents as $id_document => $id_decision) {
			$id_treaty = $decisions[$id_decision];
			$treaty = $roots[$id_treaty];
			$decision = $treaty->decisions[$id_decision];
			$document = new DecisionDocumentSearchResult2($id_document, $this, $documents_ob[$id_document], true);
			$decision->documents[$id_document] = $document;
		}

		// TODO decision paragraphs
		microtime_float($start, 'build_hierarchy took');
		// var_dump($ret);
		return $ret;
	}


	/**
	 * Cache a SQL query in the form "SELECT a.key, b.value FROM ...."
	 * @return Associative array in the form ret[intval(key)] = intval(value)
	 */
	function cache_hashmap($sql, $filter = array(), $key, $value) {
		global $wpdb;
		$ret = array();
		if(!empty($filter)) {
			$sql .= ' WHERE a.`id` IN (' . implode(',', array_unique($filter)) . ')';
		}
		$rows = $wpdb->get_results($sql);
		foreach($rows as $ob) {
			$ret[intval($ob->$key)] = intval($ob->$value);
		}
		return $ret;
	}


	/**
	 * Cache a SQL query in the form "SELECT * FROM ...."
	 * @return Associative array in the form $ret[intval($row->$key)] = StdClass(db_row)
	 */
	function cache_table($sql, $filter = array(), $key = 'id', $intkey = true) {
		global $wpdb;
		$ret = array();
		if(!empty($filter)) {
			$sql .= ' AND a.`id` IN (' . implode(',', array_unique($filter)) . ')';
		}
		$rows = $wpdb->get_results($sql);
		foreach($rows as $ob) {
			if($intkey) {
				$ret[intval($ob->$key)] = $ob;
			} else {
				$ret[$ob->$key] = $ob;
			}
		}
		return $ret;
	}


	/**
	 * If we have dirty search, select only matching hits that are tagged
	 * If we don't have dirty search, return all tagged items.
	 * Strange, but live with it :-)
	 */
	function handle_tagged_items(&$interim) {
		$terms = $this->get_terms();
		if($this->is_dirty_search()) {
			if(!empty($terms)) {
				$interim = $this->remove_untagged_items($interim);
			}
		} else {
			$interim = $this->add_tagged_items($interim);
		}
		return $interim;
	}


	/**
	 * Remove items that are resulted from Solr, but are not tagged with tags
	 */
	function remove_untagged_items(&$interim) {
		$terms = $this->get_terms();

		$tagged_decisions_paragraphs_obs = $this->find_tagged_decisions_paragraphs();
		$tagged_decisions_paragraphs_ids = array();
		foreach($tagged_decisions_paragraphs_obs as $ob) { $tagged_decisions_paragraphs_ids[] = $ob->id; }

		$tagged_decisions_obs = $this->find_tagged_decisions();
		$tagged_decisions_ids = array();
		foreach($tagged_decisions_obs as $ob) { $tagged_decisions_ids[] = $ob->id; }

		foreach($interim->treaties as $treaty) {
			foreach($treaty->decisions as $decision) {
				//TODO: decision paragraphs
				foreach($terms as $id_term) {
					if(!in_array($decision->id, $tagged_decisions_ids)) {
						unset($treaty->decisions[$decision->id]);
						unset($interim->decisions[$decision->id]);
					}
				}
			}
		}

		$tagged_treaty_paragraphs_obs = $this->find_tagged_treaty_paragraphs();
		$tagged_treaty_paragraphs_ids = array();
		foreach($tagged_treaty_paragraphs_obs as $ob) { $tagged_treaty_paragraphs_ids[] = $ob->id; }

		$tagged_treaty_articles_obs = $this->find_tagged_treaty_articles();
		$tagged_treaty_articles_ids = array();
		foreach($tagged_treaty_articles_obs as $ob) { $tagged_treaty_articles_ids[] = $ob->id; }

		$tagged_treaties_obs = $this->find_tagged_treaties();
		$tagged_treaties_ids = array();
		foreach($tagged_treaties_obs as $ob) { $tagged_treaties_ids[] = $ob->id; }

		foreach($interim->treaties as $treaty) {
			foreach($treaty->articles as $article) {
				foreach($article->paragraphs as $paragraph) {
					foreach($terms as $id_term) {
						if(!in_array($paragraph->id, $tagged_treaty_paragraphs_ids)) {
							unset($article->paragraphs[$paragraph->id]);
						}
					}
				}
				if(empty($article->paragraphs)) { // If the article has no paragraphs, look for article tags
					$atagged = false;
					foreach($terms as $id_term) {
						if(in_array($article->id, $tagged_treaty_articles_ids)) {
							$atagged = true;
						}
					}
					if(!$atagged) { unset($treaty->articles[$article->id]); } // Remove, article is not tagged
				}
			}
			if(empty($treaty->articles)) { // If the treaty has no articles, look for treaty tags
				$ttagged = false;
				foreach($terms as $id_term) {
					if(in_array($treaty->id, $tagged_treaties_ids)) {
						$ttagged = true;
					}
				}
				if(!$ttagged && empty($treaty->decisions)) { // Treaty has no paragraphs, is not tagged, has no decisions. Remove.
					unset($interim->treaties[$treaty->id]);
				}
			}
		}
		return $interim;
	}


	/**
	 * This function adds the tagged items to the resulted pages
	 */
	function add_tagged_items(&$interim) {
		$terms = $this->get_terms();
		if(empty($terms)) {
			return $interim;
		}
		// Do some caching
		$cache_ai_treaties = $this->cache_table('SELECT a.* FROM `ai_treaty` a WHERE a.`enabled` = 1 ORDER BY a.short_title', array()); // [id_treaty] = [StdClass(treaty)]
		$cache_ai_treaty_articles = $this->cache_table('SELECT a.* FROM `ai_treaty_article` a ', array()); // [id_treaty] = [StdClass(treaty)]
		$cache_ai_decisions = $this->cache_table('SELECT a.* FROM `ai_decision` a ', array()); // [id_treaty] = [StdClass(treaty)]

		$allowed_treaties = $this->get_treaties();

		// Handle the treaties tagging
		$treaties = $this->find_tagged_treaties(/* TODO: IMPROVE BY FILTERING ALSO THE TREATIES ALREADY */);
		foreach($treaties as $treaty) {
			if(in_array($treaty->id, $allowed_treaties)) {
				if(!array_key_exists($treaty->id, $interim->treaties)) {
					$db_treaty = $cache_ai_treaties[$treaty->id];
					$treaty = new TreatySearchResult2($treaty->id, $this, $db_treaty, true);
					$treaty->set_from_tagging(true);
					$interim->treaties[$treaty->id] = $treaty;
					imea_debug(sprintf('Treaty added by tagging engine: treaty => %s', $treaty->db->short_title));
				}
			}
		}

		// Handle the treaty articles tagging
		$treaty_articles = $this->find_tagged_treaty_articles(/* TODO: IMPROVE BY FILTERING ALSO THE TREATIES ALREADY */);
		foreach($treaty_articles as $article) {
			if(in_array($article->id_treaty, $allowed_treaties)) {
				if(array_key_exists($article->id_treaty, $interim->treaties)) {
					$treaty = $interim->treaties[$article->id_treaty];
				} else {
					$db_treaty = $cache_ai_treaties[$article->id_treaty];
					$treaty = new TreatySearchResult2($article->id_treaty, $this, $db_treaty, false);
					$treaty->set_from_tagging(true);
					$interim->treaties[$article->id_treaty] = $treaty;
				}
				if(!array_key_exists($article->id, $treaty->articles)) {
					$db_treaty_article = $cache_ai_treaty_articles[$article->id];
					$article = new TreatyArticleSearchResult2($article->id, $this, $db_treaty_article, true);
					$article->set_from_tagging(true);
					$treaty->articles[$article->id] = $article;
					imea_debug(sprintf('Article added by tagging engine: treaty => %s, article => %s', $treaty->db->short_title, $article->db->title));
				}
			}
		}

		// Handle the treaty paragraphs tagging
		$treaty_paragraphs = $this->find_tagged_treaty_paragraphs(/* TODO: IMPROVE BY FILTERING ALSO THE TREATIES ALREADY */);
		foreach($treaty_paragraphs as $paragraph) {
			// Check if treaty already exists
			if(in_array($paragraph->id_treaty, $allowed_treaties)) {
				if(array_key_exists($paragraph->id_treaty, $interim->treaties)) {
					$treaty = $interim->treaties[$paragraph->id_treaty];
				} else {
					$db_treaty = $cache_ai_treaties[$paragraph->id_treaty];
					$treaty = new TreatySearchResult2($paragraph->id_treaty, $this, $db_treaty, false);
					$treaty->set_from_tagging(true);
					$interim->treaties[$paragraph->id_treaty] = $treaty;
				}
				if(array_key_exists($paragraph->id_article, $treaty->articles)) {
					$article = $treaty->articles[$paragraph->id_article];
				} else {
					$db_treaty_article = $cache_ai_treaty_articles[$paragraph->id_article];
					$article = new TreatyArticleSearchResult2($paragraph->id_article, $this, $db_treaty_article, false);
					$treaty->articles[$paragraph->id_article] = $article;
				}
				if(!array_key_exists($paragraph->id, $article->paragraphs)) {
					$paragraph = new TreatyParagraphSearchResult2($paragraph->id, $this, $paragraph, true);
					$paragraph->set_from_tagging(true);
					$article->paragraphs[$paragraph->id] = $paragraph;
					imea_debug(sprintf('Paragraph added by tagging engine: treaty => %s, article => %s, paragraph id - %d', $treaty->db->short_title, $article->db->title, $paragraph->id));
				}
			}
		}

		// Handle treaty decisions
		$decisions = $this->find_tagged_decisions(/* TODO: IMPROVE BY FILTERING ALSO THE TREATIES ALREADY */);
		foreach($decisions as $decision) {
			if(in_array($decision->id_treaty, $allowed_treaties)) {
				if(array_key_exists($decision->id_treaty, $interim->treaties)) {
					$treaty = $interim->treaties[$decision->id_treaty];
				} else {
					$db_treaty = $cache_ai_treaties[$decision->id_treaty];
					$treaty = new TreatySearchResult2($decision->id_treaty, $this, $db_treaty, false);
					$treaty->set_from_tagging(true);
					$interim->treaties[$decision->id_treaty] = $treaty;
				}
				if(!array_key_exists($decision->id, $treaty->decisions)) {
					$db_decision = $cache_ai_decisions[$decision->id];
					$decision = new DecisionSearchResult2($decision->id, $this, $db_decision, true);
					$decision->set_from_tagging(true);
					$treaty->decisions[$decision->id] = $decision;
					$interim->decisions[$decision->id] = $decision;
					imea_debug(sprintf('Decision added by tagging engine: treaty => %s, decision => %s', $treaty->db->short_title, $decision->db->short_title));
				}
			}
		}

		// Handle treaty decisions paragraphs
		$decision_paragraphs = $this->find_tagged_decisions_paragraphs(/* TODO: IMPROVE BY FILTERING ALSO THE TREATIES ALREADY */);
		foreach($decision_paragraphs as $paragraph) {
			if(in_array($paragraph->id_treaty, $allowed_treaties)) {
				if(array_key_exists($paragraph->id_treaty, $interim->treaties)) {
					$treaty = $interim->treaties[$paragraph->id_treaty];
				} else {
					$db_treaty = $cache_ai_treaties[$paragraph->id_treaty];
					$treaty = new TreatySearchResult2($paragraph->id_treaty, $this, $db_treaty, false);
					$treaty->set_from_tagging(true);
					$interim->treaties[$paragraph->id_treaty] = $treaty;
				}
				if(array_key_exists($paragraph->id_decision, $treaty->decisions)) {
					$decision = $treaty->decisions[$paragraph->id_decision];
				} else {
					$db_decision = $cache_ai_decisions[$paragraph->id_decision];
					$decision = new DecisionSearchResult2($paragraph->id_decision, $this, $db_decision, false);
					$decision->set_from_tagging(true);
					$treaty->decisions[$paragraph->id_decision] = $decision;
					$interim->decisions[$paragraph->id_decision] = $decision;
				}
				if(!array_key_exists($paragraph->id, $decision->paragraphs)) {
					// We don't need DB cache
					$decision->paragraphs[$paragraph->id] = $paragraph;
					imea_debug(sprintf('Decision paragraph added by tagging engine: treaty => %s, decision => %s', $treaty->db->short_title, $decision->db->short_title));
				}
			}
		}
		return $interim;
	}


	private function find_tagged_decisions_paragraphs() {
		global $wpdb;
		$terms = $this->get_terms();
		$is_terms_or = $this->is_terms_or();
		$ret = array();
		if(count($terms)) {
			if($is_terms_or) {
				$sql = 'SELECT C.`id_treaty`, B.* FROM ai_decision_paragraph_vocabulary A
							INNER JOIN ai_decision_paragraph B ON A.`id_decision_paragraph` = B.`id`
							INNER JOIN ai_decision C ON B.`id_decision` = C.`id`
							WHERE A.id_concept = ' . implode(' OR A.id_concept = ', $terms);
				$sql .= ' GROUP BY A.id_decision_paragraph ';
			} else {
				$sql = 'SELECT C.`id_treaty`, B.* FROM ai_decision_paragraph_vocabulary A
							INNER JOIN ai_decision_paragraph B ON A.`id_decision_paragraph` = B.`id`
							INNER JOIN ai_decision C ON B.`id_decision` = C.`id`
							WHERE A.id_concept IN (' . implode(',', $terms) . ')';
				$sql .= ' GROUP BY A.id_decision_paragraph ';
				$sql .= ' HAVING COUNT(*) >= ' . count($terms);
			}
		}
		return $wpdb->get_results($sql);
	}

	private function find_tagged_decisions() {
		global $wpdb;
		$terms = $this->get_terms();
		$is_terms_or = $this->is_terms_or();
		$ret = array();
		if(count($terms)) {
			if($is_terms_or) {
				$sql = 'SELECT B.* FROM ai_decision_vocabulary A
							INNER JOIN ai_decision B ON A.`id_decision` = B.`id`
							WHERE A.id_concept = ' . implode(' OR A.id_concept = ', $terms);
				$sql .= ' GROUP BY A.id_decision ';
			} else {
				$sql = 'SELECT B.* FROM ai_decision_vocabulary A
							INNER JOIN ai_decision B ON A.`id_decision` = B.`id`
							WHERE A.id_concept IN (' . implode(',', $terms) . ')';
				$sql .= ' GROUP BY A.id_decision ';
				$sql .= ' HAVING COUNT(*) >= ' . count($terms);
			}
		}
		return $wpdb->get_results($sql);
	}


	private function find_tagged_treaties() {
		global $wpdb;
		$terms = $this->get_terms();
		$is_terms_or = $this->is_terms_or();
		$ret = array();
		if(count($terms)) {
			if($is_terms_or) {
				$sql = 'SELECT B.* FROM ai_treaty_vocabulary A
							INNER JOIN ai_treaty B ON B.id = A.id_treaty
							WHERE A.id_concept = ' . implode(' OR A.id_concept = ', $terms);
				$sql .= ' GROUP BY A.id_treaty ORDER BY B.short_title ';
			} else {
				$sql = 'SELECT B.* FROM ai_treaty_vocabulary A
							INNER JOIN ai_treaty B ON B.id = A.id_treaty
							WHERE A.id_concept IN (' . implode(',', $terms) . ')';
				$sql .= ' GROUP BY A.id_treaty ';
				$sql .= ' HAVING COUNT(*) >= ' . count($terms) . ' ORDER BY B.short_title';
			}
		}
		return $wpdb->get_results($sql);
	}


	private function find_tagged_treaty_articles() {
		global $wpdb;
		$terms = $this->get_terms();
		$is_terms_or = $this->is_terms_or();
		$ret = array();
		if(count($terms)) {
			if($is_terms_or) {
				$sql = 'SELECT B.* FROM ai_treaty_article_vocabulary A
							INNER JOIN ai_treaty_article B ON B.id = A.id_treaty_article
							WHERE A.id_concept = ' . implode(' OR A.id_concept = ', $terms);
				$sql .= ' GROUP BY A.id_treaty_article ORDER BY B.`order`';
			} else {
				$sql = 'SELECT B.* FROM ai_treaty_article_vocabulary A
							INNER JOIN ai_treaty_article B ON B.id = A.id_treaty_article
							WHERE A.id_concept IN (' . implode(',', $terms) . ')';
				$sql .= ' GROUP BY A.id_treaty_article ';
				$sql .= ' HAVING COUNT(*) >= ' . count($terms) . ' ORDER BY B.`order`';
			}
		}
		return $wpdb->get_results($sql);
	}


	private function find_tagged_treaty_paragraphs() {
		global $wpdb;
		$terms = $this->get_terms();
		$is_terms_or = $this->is_terms_or();
		$ret = array();
		if(count($terms)) {
			if($is_terms_or) {
				$sql = 'SELECT B.id AS `id_paragraph`, C.`id_treaty`, C.`id` AS `id_article`, B.*
							FROM ai_treaty_article_paragraph_vocabulary A
								INNER JOIN ai_treaty_article_paragraph B ON B.id = A.id_treaty_article_paragraph
								INNER JOIN ai_treaty_article C ON C.id = B.id_treaty_article
							WHERE A.id_concept = ' . implode(' OR A.id_concept = ', $terms);
				$sql .= ' GROUP BY A.id_treaty_article_paragraph ORDER BY B.`order`';
			} else {
				$sql = 'SELECT B.id AS `id_paragraph`, C.`id_treaty`, C.`id` AS `id_article`, B.*
							FROM ai_treaty_article_paragraph_vocabulary A
								INNER JOIN ai_treaty_article_paragraph B ON B.id = A.id_treaty_article_paragraph
								INNER JOIN ai_treaty_article C ON C.id = B.id_treaty_article
							WHERE A.id_concept IN (' . implode(',', $terms) . ')';
				$sql .= ' GROUP BY A.id_treaty_article_paragraph ';
				$sql .= ' HAVING COUNT(*) >= ' . count($terms) . ' ORDER BY B.`order`';
			}
		}
		return $wpdb->get_results($sql);
	}
}
?>
