<?php

/**
 *
 * START ===> FREETEXT? === [yes] ===> SOLR:solr ===> KEYWORDSEARCH? === [yes] ===> DB:db ===> MERGE(solr, db): results ===> ...
 *                    \                                   /       \                              /
 *                     \========= [no] ==================          ================ NO ==========
 */
class InformeaSearch3 extends AbstractSearch {

	public function __construct($request) {
		parent::__construct($request);
	}

	/**
	 * @return dictionary with search results. Primary entities are
	 * keys: treaties, events, decisions.
	 */
	public function search() {
	}


    protected function db_search() {
        $ret = array();
        if(!$this->is_using_terms()) {
            return $ret;
        }
        global $wpdb;
        $terms = $this->get_terms();
        $sql_filter = sprintf(' WHERE a.id IN (%s)', explode(",", $terms));
        $sql = sprintf("
            SELECT a.id as id_term, 'treaty_paragraph' as `type`, b.id_treaty_article_paragraph as `id_entity` FROM voc_concept a
            INNER JOIN ai_treaty_article_paragraph_vocabulary b ON a.id = b.id_concept %s
            UNION
            SELECT a.id AS id_term, 'article' as `type`, b.id_treaty_article as `id_entity` FROM voc_concept a
            INNER JOIN ai_treaty_article_vocabulary b ON a.id = b.id_concept %s
            UNION
            SELECT a.id AS id_term, 'treaty' as `type`, b.id_treaty as `id_entity` FROM voc_concept a
            INNER JOIN ai_treaty_vocabulary b ON a.id = b.id_concept %s
            UNION
            SELECT a.id AS id_term, 'decision_paragraph' as `type`, b.id_decision_paragraph as `id_entity` FROM voc_concept a
            INNER JOIN ai_decision_paragraph_vocabulary b ON a.id = b.id_concept %s
            UNION
            SELECT a.id AS id_term, 'decision' as `type`, b.id_decision as `id_entity` FROM voc_concept a
            INNER JOIN ai_decision_vocabulary b ON a.id = b.id_concept %s
        ", $sql_filter, $sql_filter, $sql_filter, $sql_filter);
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            $id_entity = $row->id_entity;
            switch($row->type) {
                case 'treaty':
                    break;
                case 'article':
                    break;
                case 'treaty_paragraph':
                    break;
                case 'decision':
                    break;
                case 'decision_paragraph':
                    break;
            }
        }

    }

    protected function solr_search() {

    }
}
