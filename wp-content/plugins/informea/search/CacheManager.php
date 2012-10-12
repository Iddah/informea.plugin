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
    public static $TREATY = 'treaty';
    public static $DECISION = 'decision';
    public static $EVENT = 'event';
    public static $DOCUMENT = 'document';

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
            self::$DECISIONDOCUMENT_TREATY => array(),
            self::$TREATY => array(),
            self::$DECISION => array(),
            self::$EVENT => array(),
            self::$DOCUMENT => array()
        );
    }


    /**
     * Load actual object from database
     * @param object $skeleton Skeleton object with property 'type' and 'id_entity'
     * @return Object loaded or null
     */
    static function load_entity($skeleton) {
        $ret = null;
        if($skeleton) {
            switch($skeleton->type) {
                case 'treaty':
                    $ret = self::get(self::$TREATY, intval($skeleton->id_entity));
                    if($ret) {
                        $ret->entity_type = 'treaty';
                    }
                    break;
                case 'decision':
                    $ret = self::get(self::$DECISION, intval($skeleton->id_entity));
                    if($ret) {
                        $ret->entity_type = 'decision';
                    }
                    break;
                case 'event':
                    $ret = self::get(self::$EVENT, intval($skeleton->id_entity));
                    if($ret) {
                        $ret->entity_type = 'event';
                    }
                    break;
                case 'document':
                    $ret = self::get(self::$DOCUMENT, intval($skeleton->id_entity));
                    if($ret) {
                        $ret->entity_type = 'document';
                    }
                    break;
            }
        }
        return $ret;
    }


    /**
     * Load multiple objects at once.
     * @param object $array_skeletons Array of skeleton objects
     * @return array Array of objects loaded
     */
    static function load_entities($array_skeletons) {
        $ret = array();
        foreach($array_skeletons as $skeleton) {
            $ret[] = self::load_entity($skeleton);
        }
        return $ret;
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


    protected static function get($cache_name, $key) {
        if(empty(self::$cache[$cache_name])) {
            call_user_func('self::load_cache_' . $cache_name);
        }
        return array_key_exists($key, self::$cache[$cache_name]) ? self::$cache[$cache_name][$key] : null;
    }


    protected static function load_cache_treaty_treatyparagraph() {
        global $wpdb;
        $sql = 'SELECT c.id as id_paragraph, a.id as id_treaty FROM ai_treaty a
                    INNER JOIN ai_treaty_article b ON a.id = b.id_treaty
                    INNER JOIN ai_treaty_article_paragraph c ON b.id = c.id_treaty_article';
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache[self::$TREATY_TREATYPARAGRAPH][intval($row->id_paragraph)] = intval($row->id_treaty);
        }
    }


    protected static function load_cache_treaty_decisionparagraph() {
        global $wpdb;
        $sql = 'SELECT c.id as id_paragraph, a.id as id_treaty FROM ai_treaty a
                    INNER JOIN ai_decision b ON a.id = b.id_treaty
                    INNER JOIN ai_decision_paragraph c ON b.id = c.id_decision';
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache[self::$TREATY_DECISIONPARAGRAPH][intval($row->id_paragraph)] = intval($row->id_treaty);
        }
    }


    protected static function load_cache_article_treaty() {
        global $wpdb;
        $sql = 'SELECT id as id_article, id_treaty FROM ai_treaty_article';
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache[self::$ARTICLE_TREATY][intval($row->id_article)] = intval($row->id_treaty);
        }
    }


    protected static function load_cache_decision_treaty() {
        global $wpdb;
        $sql = 'SELECT id as id_decision, id_treaty FROM ai_decision';
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache[self::$DECISION_TREATY][intval($row->id_decision)] = intval($row->id_treaty);
        }
    }


    protected static function load_cache_treaty() {
        global $wpdb;
        $sql = 'SELECT id, short_title, year, start, url, regional, logo_medium, odata_name, region FROM ai_treaty ORDER by `order`';
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache[self::$TREATY][intval($row->id)] = $row;
        }
    }


    protected static function load_cache_decision() {
        global $wpdb;
        $sql = "
            SELECT a.id, a.link, a.short_title, a.`type`, a.`status`, a.number, a.id_treaty, a.published, a.id_meeting, a.display_order,
                b.logo_medium, b.short_title as treaty_title
            FROM ai_decision a
            INNER JOIN ai_treaty b ON a.id_treaty = b.id
            ORDER BY `display_order`";
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache[self::$DECISION][intval($row->id)] = $row;
        }
    }


    protected static function load_cache_event() {
        global $wpdb;
        $sql = "
            SELECT a.id, a.id_treaty, a.title, a.description, a.`start`, a.`end`, a.repetition, a.kind, a.`type`,
                a.access, a.`status`, a.image, a.image_copyright, a.id_country, b.`name` as country, a.latitude, a.longitude,
                c.short_title as treaty_title, c.logo_medium
            FROM ai_event a
            LEFT JOIN ai_country b ON a.id_country = b.id
            INNER JOIN ai_treaty c ON a.id_treaty = c.id";
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache[self::$EVENT][intval($row->id)] = $row;
        }
    }


    protected static function load_cache_document() {
        global $wpdb;
        $sql = "
            SELECT a.id, a.mime, a.url, a.path, a.`language`, a.size, a.filename, a.id_decision
            FROM ai_document a";
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache[self::$DOCUMENT][intval($row->id)] = $row;
        }
    }
}
