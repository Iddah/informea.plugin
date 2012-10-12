<?php

/**
 * @group cache
 */
class CacheManager {
    protected static $cache = array();

    public static $TREATY_TREATYPARAGRAPH = 'treaty_treatyparagraph';
    public static $TREATY_DECISIONPARAGRAPH = 'treaty_decisionparagraph';
    public static $ARTICLE_TREATY = 'article_treaty';
    public static $DECISION_TREATY = 'decision_treaty';
    public static $DECISIONDOCUMENT_TREATY = 'decisiondocument_treaty';

    /**
     * Cannot be instantiated
     */
    private function __construct() {}


    /**
     * Clear the cache
     */
    static function clear() {
        self::$cache = array(
            self::$TREATY_TREATYPARAGRAPH => array(),
            self::$TREATY_DECISIONPARAGRAPH => array(),
            self::$ARTICLE_TREATY => array(),
            self::$DECISION_TREATY => array(),
            self::$DECISIONDOCUMENT_TREATY => array()
        );
    }


    protected static function get($cache_name, $key) {
        if(empty(self::$cache[$cache_name])) {
            call_user_func('self::load_cache_' . $cache_name);
        }
        return array_key_exists($key, self::$cache[$cache_name]) ? self::$cache[$cache_name][$key] : null;
    }


    /**
     * Find treaty for a treaty paragraph
     * @param integer $id_paragraph ID of the treaty's paragraph to retrieve
     * @return integer ID of the treaty for this paragraph
     */
	static function get_treaty_for_treaty_paragraph($id_paragraph) {
        $idx = intval($id_paragraph);
        return self::get(self::$TREATY_TREATYPARAGRAPH, $idx);
	}

    /**
     * Find treaty for a decision paragraph
     * @param integer $id_paragraph ID of the decision's paragraph to retrieve
     * @return integer ID of the treaty for this paragraph
     */
	static function get_treaty_for_decision_paragraph($id_paragraph) {
        $idx = intval($id_paragraph);
        return self::get(self::$TREATY_DECISIONPARAGRAPH, $idx);
	}


    /**
     * Find the treaty for an article
     * @param integer $id_article
     * @return integer ID of the treaty for this article
     */
    static function get_treaty_for_article($id_article) {
        $idx = intval($id_article);
        return self::get(self::$ARTICLE_TREATY, $idx);
    }


    /**
     * Find the treaty for an decision
     * @param integer $id_decision
     * @return integer ID of the treaty for this decision
     */
    static function get_treaty_for_decision($id_decision) {
        $idx = intval($id_decision);
        return self::get(self::$DECISION_TREATY, $idx);
    }


    protected static function load_cache_treaty_treatyparagraph() {
        global $wpdb;
        $sql = 'SELECT c.id as id_paragraph, a.id as id_treaty FROM ai_treaty a
                    INNER JOIN ai_treaty_article b ON a.id = b.id_treaty
                    INNER JOIN ai_treaty_article_paragraph c ON b.id = c.id_treaty_article';
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache['treaty_treatyparagraph'][intval($row->id_paragraph)] = intval($row->id_treaty);
        }
    }

    protected static function load_cache_treaty_decisionparagraph() {
        global $wpdb;
        $sql = 'SELECT c.id as id_paragraph, a.id as id_treaty FROM ai_treaty a
                    INNER JOIN ai_decision b ON a.id = b.id_treaty
                    INNER JOIN ai_decision_paragraph c ON b.id = c.id_decision';
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache['treaty_decisionparagraph'][intval($row->id_paragraph)] = intval($row->id_treaty);
        }
    }

    protected static function load_cache_article_treaty() {
        global $wpdb;
        $sql = 'SELECT id as id_article, id_treaty FROM ai_treaty_article';
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache['article_treaty'][intval($row->id_article)] = intval($row->id_treaty);
        }
    }

    protected static function load_cache_decision_treaty() {
        global $wpdb;
        $sql = 'SELECT id as id_decision, id_treaty FROM ai_decision';
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache['decision_treaty'][intval($row->id_decision)] = intval($row->id_treaty);
        }
    }
}
