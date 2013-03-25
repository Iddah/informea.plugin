<?php

add_action('wp_ajax_nopriv_get_decision_paragraph_tags', 'informea_get_decision_paragraph_tags');
add_action('wp_ajax_get_decision_paragraph_tags', 'informea_get_decision_paragraph_tags');


/**
 * Generate JSON object with paragraph tags based on paragraph id
 * @param @id_paragraph paragraph id from query string
 * @return JSON object
 */
function informea_get_decision_paragraph_tags() {
    $id_paragraph = get_request_int('id_paragraph', 0);
    if ($id_paragraph > 0) {
        $arr = array();
        $ob = new informea_decisions();
        $tags = $ob->get_paragraph_tags($id_paragraph);
        foreach ($tags as $tag) {
            $arr[] = array('id' => $tag->id, 'term' => $tag->term);
        }
        header('Content-Type:application/json');
        echo json_encode($arr);
    }
    die();
}


class informea_treaties extends imea_treaties_page {

    public $expand;

    function __construct($id_treaty = NULL, $arr_parameters = array()) {
        parent::__construct($id_treaty, $arr_parameters);
    }

    function get_treaty_from_request() {
        $ret = NULL;
        $this->id_treaty = get_request_variable('id_treaty', 1);
        if (empty($this->id_treaty)) {
            $odata_name = get_request_variable('treaty');
            $ret = $this->get_treaty_by_odata_name($odata_name);
            $this->id_treaty = $ret->id;
        }
        $this->init();
        return $ret;
    }

    function handle_view() {
        global $expand, $id_treay;
        // Global
        $this->expand = get_request_variable('expand', 'str', 'treaty');
        $this->id_treaty = get_request_variable('id_treaty', 0);

        if ($this->is_index()) {
            get_template_part('imea_pages/treaties/page');
            exit(0);
        }

        // Administrative
        if (current_user_can('manage_options')) {
            if ($this->get_action() == 'delete_paragraph') {
                $id_paragraph = get_request_int('id_paragraph');
                $this->delete_paragraph($id_paragraph);
            }
            if ($this->get_action() == 'delete_article') {
                $id_article = get_request_int('id_article');
                $this->delete_article($id_article);
            }
        }
        // Print treaty page
        if ($this->expand == 'print') {
            get_template_part('imea_pages/treaties/page', 'treaty-print');
            exit(0);
        }
    }

    function get_treaty_by_odata_name($odata_name) {
        global $wpdb;
        return $wpdb->get_row("SELECT * FROM ai_treaty WHERE odata_name = '$odata_name' AND use_informea=1");
    }


    /**
     * Return number of treaties from a region
     * @param $region to check
     * @return number of treaties
     */
    function region_has_treaties($region) {
        global $wpdb;
        return $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM ai_treaty WHERE region = %s AND use_informea=1", $region));
    }


    /**
     * Retrieve active treaties all or by title
     * @param $title Treaty title
     * @return WP SQL result object
     */
    function get_treaties($title = null) {
        global $wpdb;
        if ($title) {
            return $wpdb->get_results("SELECT * FROM ai_treaty WHERE enabled = 1 AND use_informea=1 AND (short_title LIKE '%$title%' OR long_title LIKE '%$title%') ORDER BY short_title");
        } else {
            return $wpdb->get_results("SELECT * FROM ai_treaty WHERE enabled = 1 AND use_informea=1 ORDER BY short_title");
        }
    }


    /**
     * Retrieve treaties list by theme based on region
     * @param $region region to get treaties from
     * @return array with WP SQL result objects grouped by theme
     */
    function get_treaties_by_region_by_theme($region = '') {
        global $wpdb;
        if (strtolower($region) == 'global') {
            $region = '';
        }
        $data = $wpdb->get_results($wpdb->prepare("SELECT a.*, b.depository AS depository
			FROM ai_treaty a
			INNER JOIN ai_organization b ON a.id_organization = b.id
			WHERE a.enabled = 1 AND a.use_informea=1 AND a.region = %s ORDER BY a.`theme`, a.`order`", $region));
        $ret = array();
        foreach ($data as &$row) {
            if (!isset($ret[$row->theme])) {
                $ret[$row->theme] = array();
            }
            $ret[$row->theme][] = $row;
        }
        return $ret;
    }


    function get_cloud_terms_for_treaty_page($limit = 20) {
        $ret = array();
        if (!empty($this->treaty->id)) {
            $ret = $this->get_popular_terms($this->treaty->id, $limit);
        }
        if (!empty($this->tags)) {
            foreach ($this->tags as $tag) {
                $ob = new StdClass();
                $ob->text = $tag->term;
                $ob->url = get_bloginfo('url') . '/terms/' . $tag->id;
                $ob->popularity = 0;
                $ret[] = $ob;
            }
        }
        if (!empty($this->other_agreements)) {
            foreach ($this->other_agreements as $tag) {
                $ob = new StdClass();
                $ob->text = $tag->term;
                $ob->url = get_bloginfo('url') . '/terms/' . $tag->id;
                $ob->popularity = 0;
                $ret[] = $ob;
            }
        }

        if ($this->agreement) {
            $ob = new StdClass();
            $ob->text = $this->agreement->short_title;
            $ob->url = get_permalink() . '/' . $this->agreement->id;
            $ob->popularity = 0;
            $ret[] = $ob;
        }
        return $ret;
    }


    function tab_decisions_meeting_title($meeting) {
        return !empty($meeting->abbreviation) ? $meeting->abbreviation : $meeting->title;
    }

    function tab_decisions_with_paragraph_ids() {
        global $wpdb;
        return $wpdb->get_col('SELECT DISTINCT(a.id) FROM ai_decision a INNER JOIN ai_decision_paragraph b ON a.id = b.id_decision');
    }

    function page_decisions_overview_decision_link($decision, $treaty) {
        static $tagged = NULL;
        if($tagged === NULL) {
            $tagged = $this->tab_decisions_with_paragraph_ids();
        }
        if(!in_array($decision->id, $tagged)) {
            return $decision->number;
        } else {
            $no = $decision->number;
            $url = sprintf('%s/treaties/%s/decisions/%d', get_bloginfo('url'), $treaty->odata_name, $decision->id);
            return sprintf('<a name="decision-%d" target="_blank" href="%s">%s</a>', $no, $url, $no);
        }
    }
}


class informea_decisions extends imea_decisions_page {

    function __construct($arr_parameters = array()) {
        parent::__construct($arr_parameters);
    }


    /**
     * Retrieve the list of treaties
     */
    function get_treaties_list() {
        global $wpdb;
        $ret = array();
        // Get the themes
        $sql = "SELECT DISTINCT a.theme FROM ai_treaty a INNER JOIN ai_decision b ON b.id_treaty = a.id WHERE a.enabled = 1 AND a.use_informea=1 ORDER BY a.theme";
        $rows = $wpdb->get_results($sql);
        foreach ($rows as $row) {
            $ret[$row->theme] = array();
        }

        $sql = "SELECT a.*, a.logo_medium, a.theme FROM ai_treaty a INNER JOIN ai_decision c ON c.id_treaty = a.id WHERE enabled = 1 AND use_informea=1 GROUP BY a.id ORDER BY a.order";
        $rows = $wpdb->get_results($sql);
        foreach ($rows as $row) {
            $ret[$row->theme][] = $row;
        }
        return $ret;
    }

    function get_paragraph_tags($id_paragraph) {
        global $wpdb;
        return $wpdb->get_results($wpdb->prepare('SELECT b.* FROM ai_decision_paragraph_vocabulary a INNER JOIN voc_concept b ON a.id_concept = b.id WHERE a.id_decision_paragraph=%d', $id_paragraph));
    }
}


class informea_countries extends imea_countries_page {

    function __construct($id_country = NULL, $arr_parameters = array()) {
        parent::__construct($id_country, $arr_parameters);
    }


    function get_treaties_with_membership() {
        global $wpdb;
        return $wpdb->get_results('SELECT b.* FROM ai_treaty_country a
				INNER JOIN ai_treaty b ON b.`id` = a.`id_treaty` WHERE b.use_informea=1
				GROUP BY b.`id` ORDER BY b.`short_title`');
    }


    /**
     * Access ai_country
     * @return Rows from the table
     */
    function _get_country() {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT * FROM ai_country WHERE id = '%s'", $this->id_country);
        $this->country = $wpdb->get_row($sql);
        if ($this->country) {

            $sql = $wpdb->prepare('SELECT a.*, b.year FROM ai_treaty a
									JOIN ai_treaty_country b ON b.id_treaty = a.id
									WHERE a.enabled = TRUE AND b.id_country = %d AND a.use_informea=1 ORDER BY a.short_title', $this->id_country);
            $this->mea_membership = $wpdb->get_results($sql);
        }
    }


    /**
     * Retrieve the list of national focal points grouped by treaty
     * @param integer $id_country Country ID. If NULL, internal ID is used
     * @return array Array of treaty objects having set property focal_points as array of National Focal Points.
     * @global $wpdb WordPress database
     */
    function get_focal_points_by_treaty($id_country = NULL) {
        global $wpdb;

        $id_country = !empty($id_country) ? $id_country : $this->id_country;
        $treaties = $wpdb->get_results(
            $wpdb->prepare(
                'SELECT * FROM ai_treaty WHERE use_informea=1 AND id IN (SELECT DISTINCT(id_treaty) FROM view_people_treaty WHERE id_country=%d GROUP BY id_treaty)', $id_country
            ), OBJECT_K
        );
        $rows = $wpdb->get_results(
            $wpdb->prepare('SELECT * FROM view_people_treaty WHERE id_country=%d ORDER BY country_name, first_name, last_name', $id_country)
        );
        foreach ($rows as $row) {
            $treaty = $treaties[$row->id_treaty];
            if (!isset($treaty->focal_points)) {
                $treaty->focal_points = array();
            }
            $treaty->focal_points[] = $row;
        }
        return $treaties;
    }


    /**
     * Retrieve national reports for a country, group by treaty
     *
     * @param integer $id_country Country ID
     * @return array Array of treaties having property national_reports array with ai_country_report objects
     */
    function get_national_reports($id_country = NULL) {
        global $wpdb;

        $id_country = !empty($id_country) ? $id_country : $this->id_country;
        $treaties = $wpdb->get_results(
            $wpdb->prepare("SELECT b.* FROM ai_country_report a INNER JOIN ai_treaty b ON a.id_treaty = b.id WHERE b.enabled = TRUE AND b.use_informea=1 AND a.id_country=%d GROUP BY b.id", $id_country)
            , OBJECT_K
        );
        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT a.*, b.title AS meeting_title FROM ai_country_report a LEFT JOIN ai_event b ON a.id_event = b.id WHERE a.id_country=%d ORDER BY a.submission DESC", $id_country)
        );
        foreach ($rows as $row) {
            $treaty = $treaties[$row->id_treaty];
            if (!isset($treaty->national_reports)) {
                $treaty->national_reports = array();
            }
            $treaty->national_reports[] = $row;
        }
        return $treaties;
    }


    /**
     * Retrieve national plans for a country, group by treaty
     *
     * @param integer $id_country Country ID
     * @return array Array of treaties having property national_plans array with ai_country_plan objects
     */
    function get_national_plans($id_country = NULL) {
        global $wpdb;

        $id_country = !empty($id_country) ? $id_country : $this->id_country;
        $treaties = $wpdb->get_results(
            $wpdb->prepare("SELECT b.* FROM ai_country_plan a INNER JOIN ai_treaty b ON a.id_treaty = b.id WHERE b.enabled = TRUE AND b.use_informea=1 AND a.id_country=%d GROUP BY b.id", $id_country)
            , OBJECT_K
        );

        $rows = $wpdb->get_results(
            $wpdb->prepare("SELECT a.*, b.title AS meeting_title FROM ai_country_plan a LEFT JOIN ai_event b ON a.id_event = b.id WHERE a.id_country=%d ORDER BY a.submission DESC", $id_country)
        );
        foreach ($rows as $row) {
            $treaty = $treaties[$row->id_treaty];
            if (!isset($treaty->national_plans)) {
                $treaty->national_plans = array();
            }
            $treaty->national_plans[] = $row;
        }
        return $treaties;
    }
}


class informea_events extends imea_events_page {


    /**
     * Retrieve the list of conventions that have events
     * @return List of ai_treaty
     */
    function get_treaties() {
        global $wpdb;
        $sql = "SELECT a.* FROM ai_treaty a
			INNER JOIN ai_event b ON b.id_treaty = a.id
			WHERE (a.enabled=1 AND a.use_informea=1) OR odata_name='unep' GROUP BY a.id ORDER BY a.short_title
		";
        $rows = $wpdb->get_results($sql);
        $ret = array();
        foreach ($rows as $row) {
            $ret[$row->id] = $row;
        }
        return $ret;
    }
}