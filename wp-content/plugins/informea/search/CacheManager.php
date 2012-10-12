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
    public static $TREATY_ARTICLE = 'treaty_article';
    public static $TREATY_PARAGRAPH = 'treaty_paragraph';
    public static $DECISION_PARAGRAPH = 'decision_paragraph';

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
            self::$DOCUMENT => array(),
            self::$TREATY_ARTICLE => array(),
            self::$TREATY_PARAGRAPH => array(),
            self::$DECISION_PARAGRAPH => array()
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
            $ret = self::get($skeleton->type, intval($skeleton->id_entity));
            if($ret) {
                $ret->entity_type = $skeleton->type;
            }
        }
        return $ret;
    }


    /**
     * Load a single treaty object
     * @param mixed $id Treaty Id
     * @return object Treaty object or null
     */
    static function load_treaty($id) {
        return self::get(self::$TREATY, intval($id));
    }


    /**
     * Load a single treaty article object
     * @param mixed $id Article Id
     * @return object Treaty article object or null
     */
    static function load_treaty_article($id) {
        $ob = new stdClass();
        $ob->type = self::$TREATY_ARTICLE;
        $ob->id_entity = $id;
        return self::load_entity($ob);
    }


    /**
     * Load a single treaty paragraph object
     * @param mixed $id Paragraph Id
     * @return object Treaty paragraph object or null
     */
    static function load_treaty_paragraph($id) {
        $ob = new stdClass();
        $ob->type = self::$TREATY_PARAGRAPH;
        $ob->id_entity = $id;
        return self::load_entity($ob);
    }


    /**
     * Load a single decision
     * @param mixed $id Decision Id
     * @return object Decision or null
     */
    static function load_decision($id) {
        $ob = new stdClass();
        $ob->type = self::$DECISION;
        $ob->id_entity = $id;
        return self::load_entity($ob);
    }


    /**
     * Load a single decision paragraph
     * @param mixed $id Decision Id
     * @return object Decision paragraph or null
     */
    static function load_decision_paragraph($id) {
        $ob = new stdClass();
        $ob->type = self::$DECISION_PARAGRAPH;
        $ob->id_entity = $id;
        return self::load_entity($ob);
    }


    /**
     * Load a single decision document
     * @param mixed $id Document Id
     * @return object Decision or null
     */
    static function load_document($id) {
        $ob = new stdClass();
        $ob->type = self::$DOCUMENT;
        $ob->id_entity = $id;
        return self::load_entity($ob);
    }


    /**
     * Load a single event
     * @param mixed $id Event Id
     * @return object Decision or null
     */
    static function load_event($id) {
        $ob = new stdClass();
        $ob->type = self::$EVENT;
        $ob->id_entity = $id;
        return self::load_entity($ob);
    }


    /**
     * Load a treaty tree of objects from database
     * @param integer $id Treaty ID
     * @param array $data Data array('articles' => array(...), 'decisions' => array(...))
     * @return Object with properties
     * TREATY
     *   -> articles => array( ARTICLE -> paragraphs = array())
     *   -> decisions => array( DECISION -> paragraphs = array())
     */
    static function load_treaty_hierarchy($id, $data) {
        $treaty = self::load_treaty($id);
        $treaty->articles = array();
        $treaty->decisions = array();
        $treaty->events = array();

        foreach($data['articles'] as $id_article => $paragraphs) {
            $article = self::load_treaty_article($id_article);
            $article->paragraphs = array();
            $treaty->articles[] = $article;
            foreach($paragraphs as $id_paragraph) {
                $paragraph = self::load_treaty_paragraph($id_paragraph);
                $article->paragraphs[] = $paragraph;
            }
        }
        foreach($data['decisions'] as $id_decision => $inner) {
            $decision = self::load_decision($id_decision);
            $treaty->decisions[] = $decision;

            $decision->paragraphs = array();
            foreach($inner['paragraphs'] as $id_paragraph) {
                $paragraph = self::load_decision_paragraph($id_paragraph);
                $decision->paragraphs[] = $paragraph;
            }

            $decision->documents = array();
            foreach($inner['documents'] as $id_document) {
                $document = self::load_document($id_document);
                $decision->documents[] = $document;
            }
        }
        return $treaty;
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
            $method = 'load_cache_' . $cache_name;
            if(method_exists('CacheManager', $method)) {
                call_user_func('self::' . $method);
            } else {
                error_log("Cannot load cache $cache_name");
            }
        }
        return isset(self::$cache[$cache_name]) && array_key_exists($key, self::$cache[$cache_name]) ? self::$cache[$cache_name][$key] : null;
    }


    // Cache loading functions in format load_cache_CACHE_NAME

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


    protected static function load_cache_treaty_article() {
        global $wpdb;
        $sql = "SELECT a.* FROM ai_treaty_article a";
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache[self::$TREATY_ARTICLE][intval($row->id)] = $row;
        }
    }


    protected static function load_cache_treaty_paragraph() {
        global $wpdb;
        $sql = "SELECT a.* FROM ai_treaty_article_paragraph a";
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache[self::$TREATY_PARAGRAPH][intval($row->id)] = $row;
        }
    }


    protected static function load_cache_decision_paragraph() {
        global $wpdb;
        $sql = "SELECT a.* FROM ai_decision_paragraph a";
        $rows = $wpdb->get_results($sql);
        foreach($rows as $row) {
            self::$cache[self::$DECISION_PARAGRAPH][intval($row->id)] = $row;
        }
    }
}
