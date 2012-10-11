<?php

class CacheManager {
	protected static $cache_treaty_treatyparagraph = array();
	protected static $cache_treaty_decisionparagraph = array();
	protected static $cache_article_treaty = array();
	protected static $cache_decision_treaty = array();
	protected static $cache_decisiondocument_treaty = array();


    /**
     * Cannot be instantiated
     */
    private function __construct() {}


    /**
     * Clear the cache
     */
    static function clear() {
        self::$cache_treaty_treatyparagraph = array();
        self::$cache_treaty_decisionparagraph = array();
        self::$cache_article_treaty = array();
        self::$cache_decision_treaty = array();
        self::$cache_decisiondocument_treaty = array();
    }


    /**
     * Find treaty for a treaty paragraph
     * @param integer $id_paragraph ID of the treaty's paragraph to retrieve
     * @return integer ID of the treaty for this paragraph
     */
	static function get_treaty_for_treaty_paragraph($id_paragraph) {
        $idx = intval($id_paragraph);
        if($idx > 0) {
            // Lazy load the cache
            if(empty(self::$cache_treaty_treatyparagraph)) {
                CacheManager::cache_treaties_for_treaties_paragraphs();
            }
            return array_key_exists($idx, self::$cache_treaty_treatyparagraph) ? self::$cache_treaty_treatyparagraph[$idx] : null;
        }
	}

    /**
     * Find treaty for a decision paragraph
     * @param integer $id_paragraph ID of the decision's paragraph to retrieve
     * @return integer ID of the treaty for this paragraph
     */
	static function get_treaty_for_decision_paragraph($id_paragraph) {
        $idx = intval($id_paragraph);
        if($idx > 0) {
            // Lazy load the cache
            if(empty(self::$cache_treaty_decisionparagraph)) {
                CacheManager::cache_treaties_for_decisions_paragraphs();
            }
            return array_key_exists($idx, self::$cache_treaty_decisionparagraph) ? self::$cache_treaty_decisionparagraph[$idx] : null;
        }
	}


    /**
     * Find the treaty for an article
     * @param integer $id_article
     * @return integer ID of the treaty for this article
     */
    static function get_treaty_for_article($id_article) {
        $idx = intval($id_article);
        if($idx > 0) {
            // Lazy load the cache
            if(empty(self::$cache_article_treaty)) {
                CacheManager::cache_treaties_for_articles();
            }
            return array_key_exists($idx, self::$cache_article_treaty) ? self::$cache_article_treaty[$idx] : null;
        }
    }


    /**
     * Find the treaty for an decision
     * @param integer $id_decision
     * @return integer ID of the treaty for this decision
     */
    static function get_treaty_for_decision($id_decision) {
        $idx = intval($id_decision);
        if($idx > 0) {
            // Lazy load the cache
            if(empty(self::$cache_decision_treaty)) {
                CacheManager::cache_treaties_for_decisions();
            }
            return array_key_exists($idx, self::$cache_decision_treaty) ? self::$cache_decision_treaty[$idx] : null;
        }
    }


    private static function cache_treaties_for_treaties_paragraphs() {
        global $wpdb;
        $sql = 'SELECT c.id as id_paragraph, a.id as id_treaty FROM ai_treaty a
                    INNER JOIN ai_treaty_article b ON a.id = b.id_treaty
                    INNER JOIN ai_treaty_article_paragraph c ON b.id = c.id_treaty_article';
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache_treaty_treatyparagraph[intval($row->id_paragraph)] = intval($row->id_treaty);
        }
    }

    private static function cache_treaties_for_decisions_paragraphs() {
        global $wpdb;
        $sql = 'SELECT c.id as id_paragraph, a.id as id_treaty FROM ai_treaty a
                    INNER JOIN ai_decision b ON a.id = b.id_treaty
                    INNER JOIN ai_decision_paragraph c ON b.id = c.id_decision';
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache_treaty_decisionparagraph[intval($row->id_paragraph)] = intval($row->id_treaty);
        }
    }

    private static function cache_treaties_for_articles() {
        global $wpdb;
        $sql = 'SELECT id as id_article, id_treaty FROM ai_treaty_article';
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache_article_treaty[intval($row->id_article)] = intval($row->id_treaty);
        }
    }

    private static function cache_treaties_for_decisions() {
        global $wpdb;
        $sql = 'SELECT id as id_decision, id_treaty FROM ai_decision';
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache_decision_treaty[intval($row->id_decision)] = intval($row->id_treaty);
        }
    }
}
