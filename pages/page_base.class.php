<?php
if(!class_exists( 'InforMEAException')) {
class InforMEAException extends Exception {

	public function __construct($message, $code = 0, Exception $previous = null) {
		parent::__construct($message, $code, $previous);
	}
}
}

if(!class_exists( 'imea_page_base_page')) {
class imea_page_base_page {

	/** Parameters accepted by the page - Used by the paginator */
	protected $req_parameters = array();
	/** Paginator that provides the results */
	protected $paginator = NULL;

	/** Variables used inside administration area */
	public $actioned = FALSE;
	public $action = FALSE;
	public $success = FALSE;
	public $errors = array();
	public $insert_id = NULL;
	/** End */


	function get_action() {
		return get_request_variable('action');
	}


	/**
	 * Retrieve request POST parameter
	 * @param name of the parameter
	 * @return parameter value or empty string if not set
	 */
	function get_value($name, $strip_slashes = FALSE) {
		$ret = isset($_POST[$name]) ? $_POST[$name] : NULL;
		if($strip_slashes) {
			$ret = stripslashes($ret);
		}
		return $ret;
	}

	/**
	 * Echo the request parameter
	 * @param name of the parameter
	 * @return Nothing
	 */
	function get_value_e($name, $strip_slashes = FALSE) {
		echo $this->get_value($name, $strip_slashes);
	}


	/**
	 * Construct new object
	 * @param arr_parameters HTTP GET parameters specific to this page. They are used to reconstruct the URL for pagination links.
	 */
	function __construct($arr_parameters = array()) {
		$this->req_parameters = $arr_parameters;
	}

	/**
	 * Retrieve the data paginator object.
	 * @see paginated_query class
	 * @return paginated_query object
	 */
	function get_paginator() {
		return $this->paginator;
	}

	/**
	 * This controller method specifies if we are on index page of the section or not.
	 * Override on extending classes.
	 * @return True if index page.
	 */
	function is_index() {
		return true;
	}

	/**
	 */
	function is_404() {
		return FALSE;
	}

	/**
	 * Retrieve GET parameter or NULL
	 */
	function req_get($name, $default = NULL) {
		$ret = $default;
		if( isset($_GET[$name]) && $_GET[$name] != '') {
			$ret = $_GET[$name];
		}
		return $ret;
	}

	/**
	 * Retrieve POST parameter or NULL
	 */
	function req_post($name, $default = NULL) {
		$ret = $default;
		if( isset($_POST[$name]) && $_POST[$name] != '') {
			$ret = $_POST[$name];
		}
		return $ret;
	}

	/**
	 * This function gets the meetings from all MEA nodes. Two upcoming events from each MEA recorded in the events table
	 * @returnList of ai_event objects
	 */
	function get_meetings($id_treaty = NULL) {
		global $wpdb;
		$ret = array();
		if(!empty($id_treaty)) {
			$ret = $wpdb->get_results("SELECT a.*, b.short_title as treaty_short_title, b.logo_medium FROM ai_event a
				INNER JOIN ai_treaty b ON a.id_treaty = b.id WHERE a.id_treaty = $id_treaty AND a.start >= CURRENT_DATE() ORDER BY a.start ASC LIMIT 5");
		} else {
			$treaties = $wpdb->get_col('SELECT DISTINCt(id_treaty) FROM ai_event');
			foreach($treaties as $id_treaty) {
				$events = $wpdb->get_results("SELECT a.*, b.short_title as treaty_short_title, b.logo_medium FROM ai_event a
					INNER JOIN ai_treaty b ON a.id_treaty = b.id
					WHERE a.id_treaty = $id_treaty AND a.start >= CURRENT_DATE() ORDER BY a.start ASC LIMIT 2");
				$ret = array_merge($ret, $events);
			}
		}
		return $ret;
	}

	function get_meetings_for_ids($ids) {
		global $wpdb;
		$ret = array();
		$ret = $wpdb->get_results("SELECT a.*, b.short_title as treaty_short_title, b.logo_medium FROM ai_event a INNER JOIN ai_treaty b ON a.id_treaty = b.id WHERE a.id_treaty in ($ids) AND a.start >= CURRENT_DATE() ORDER BY a.start ASC LIMIT 5");
		return $ret;
	}


	function get_popular_terms($id_treaty = NULL, $limit = 10) {
		global $wpdb;
		$sql = "SELECT * FROM voc_concept ORDER BY popularity DESC LIMIT $limit";
		if($id_treaty !== NULL) {
			$sql = "SELECT * FROM voc_concept WHERE id IN (
						SELECT distinct(id_concept) FROM ai_treaty_vocabulary WHERE id_treaty = $id_treaty
							UNION
						SELECT distinct(id_concept) FROM ai_treaty_article_paragraph_vocabulary a
								INNER JOIN ai_treaty_article b ON a.id_treaty_article_paragraph = b.id
								WHERE b.id_treaty = $id_treaty
							UNION
						SELECT distinct(id_concept) FROM ai_treaty_article_vocabulary a
								INNER JOIN ai_treaty_article b ON a.id_treaty_article = b.id
								WHERE b.id_treaty = $id_treaty
					) ORDER BY popularity DESC LIMIT " . $limit * 4 . "";
		}
		$terms = $wpdb->get_results($sql);
		$terms = array_slice($terms, 0, $limit);
		return $terms;
	}

	function get_popular_terms_for_ids($ids, $limit = 10) {
		global $wpdb;
		$sql = "SELECT * FROM voc_concept ORDER BY popularity DESC LIMIT $limit";
		if($ids !== NULL) {
			$sql = "SELECT * FROM voc_concept WHERE id IN (
						SELECT distinct(id_concept) FROM ai_treaty_vocabulary WHERE id_treaty in($ids)
							UNION
						SELECT distinct(id_concept) FROM ai_treaty_article_paragraph_vocabulary a
								INNER JOIN ai_treaty_article b ON a.id_treaty_article_paragraph = b.id
								WHERE b.id_treaty in($ids)
							UNION
						SELECT distinct(id_concept) FROM ai_treaty_article_vocabulary a
								INNER JOIN ai_treaty_article b ON a.id_treaty_article = b.id
								WHERE b.id_treaty in($ids)
					) ORDER BY popularity DESC LIMIT " . $limit * 4 . "";
		}
		$terms = $wpdb->get_results($sql);
		$terms = array_slice($terms, 0, $limit);
		return $terms;
	}

	/**
	 * Compute the popularity based on a 1 - 10 scale
	 * @return array Terms with popularity fixed
	 */
	function compute_popularity($terms) {
		if(!empty($terms)) {
			$greatest = 0;
			foreach($terms as $term) {
				if($term->popularity > $greatest) {
					$greatest = $term->popularity;
				}
			}
			if($greatest == 0) {
				$greatest = 1;
			}
			foreach($terms as &$term) {
				$term->popularity = ceil($term->popularity * 10 / $greatest);
				if($term->popularity == 0) {
					$term->popularity = 1;
				}
			}
		}
		return $terms;
	}


	function array_unique_terms($terms) {
		$ret = array();
		$added = array();
		foreach($terms as $term) {
			if(!in_array($term->id, $added)) {
				$ret[] = $term;
				$added[] = $term->id;
			}
		}
		return $ret;
	}


	function get_decision_documents($id_decision) {
		global $wpdb;
		$ret = array();
		$sql = "SELECT * FROM ai_document WHERE id_decision = $id_decision";
		$docs = $wpdb->get_results($sql);
		foreach($docs as $doc) {
			$ob = new StdClass();
			$ob->id = $doc->id;
			$url = get_bloginfo('template_directory') . '/images/';
			if($doc->mime == 'pdf') {
				$url .= 'pdf.png';
			}
			else if($doc->mime == 'doc') {
				$url .= 'doc.png';
			} else if($doc->mime == 'xls') {
				$url .= 'xls.png';
			} else {
				$url .= 'file.png';
			}
			$ob->icon_url = $url;
			$ob->url = $doc->url;
			$ob->language = $doc->language;

			// File size
			$file = $_SERVER['DOCUMENT_ROOT'] . '/' . $doc->path;
			$file_size = 'TODO KB';
			$ob->file_size = $file_size;

			$ret[] = $ob;
		}

		return $ret;
	}

	function get_decision_tags($id_decision) {
		global $wpdb;
		$sql = "SELECT a.* FROM voc_concept a
					INNER JOIN ai_decision_vocabulary b ON a.id = b.id_concept
					WHERE id_decision = $id_decision
				UNION
				SELECT a.* FROM voc_concept a
					INNER JOIN ai_decision_paragraph_vocabulary b ON a.id = b.id_concept
					INNER JOIN ai_decision_paragraph c ON b.id_decision_paragraph = c.id
					WHERE c.id_decision = $id_decision";
		return $wpdb->get_results($sql);
	}

	function get_month_events($month, $year) {
		global $wpdb;
		$sql = "SELECT a.*, c.id as id_treaty, c.logo_medium, c.short_title AS treaty_short_title
					FROM ai_event a
					INNER JOIN ai_treaty c ON a.id_treaty = c.id
				WHERE a.end >= STR_TO_DATE('$month/1/$year', '%m/%d/%Y')
					AND a.start <= STR_TO_DATE('$month/31/$year', '%m/%d/%Y')";
		return $wpdb->get_results($sql);
	}

	function get_contact_for_id($id) {
		global $wpdb;
		$ret = array();
		$sql = "SELECT * FROM view_people_treaty WHERE id = {$id}";
		return $wpdb->get_row($sql);
	}


	protected function check_db_error() {
		global $wpdb;
		if(strlen($wpdb->last_error) > 0) {
			$trace = '';
			try {
				throw new Exception('dummy');
			} catch(Exception $e) {
				$btrace = debug_backtrace();
				$trace = $btrace[0];
				$trace = print_r($trace, true);
			}
			throw new InforMEAException('<div class="sql-error">SQL statement failed: <pre>' . $wpdb->last_error . '(' . $wpdb->last_query . ')</pre><br /><pre>' . $trace . '</pre></div>');
		}
	}

	/** !!!!!!!!!!!!!!!!!!!!!! ADMINISTRATION AREA SPECIFIC !!!!!!!!!!!!!!!!!!!!!! */

	/**
	 * Do the security check for forms and echo unauthorized message if not correct
	 * @param $nonce_field Nonce field, see Wordpress nonce definition, http://codex.wordpress.org/WordPress_Nonces
	 * @return TRUE if security is OK
	 */
	function security_check($nonce_field) {
		if(!check_admin_referer($nonce_field)) {
			echo('<p>You are not authorized to access this page</p>');
			return FALSE;
		}
		return TRUE;
	}


	/**
	* Insert a record inside ai_activity_log table.
	* @param string $operation Possible values for operation (insert, update or delete)
	* @param string $section Section affected by the user, for example: vocabulary, treaty, decision etc. Do not invent new sections if some already exist. Look first.
	* @param string $username User that created the action (WordPress username)
	* @param string $description Description of the operation, for example: "Added tags a, b, c to article 'Article 2', paragraph 4". Send something meaningful and readable.
	* @param string $link Link to online version of the affected entity (if available).
	* @return boolean TRUE if successs, FALSE otherwise
	*/
	function add_activity_log($operation, $section, $description, $username = NULL, $link = NULL) {
		global $wpdb;
		global $current_user;

		if($username !== NULL) {
			$user = $username;
		} else {
			$user = $current_user->user_login;
		}
		$wpdb->insert('ai_activity_log', array(
				'operation' => $operation,
				'section' => $section,
				'username' => $user,
				'description' => $description,
				'url' => $link
			)
		);
	}

}
}


if(!class_exists('EcolexParser')) {

/**
 * Parse content from Ecolex results page
 * i.e. http://www.ecolex.org/ecolex/ledge/view/SearchResults?screen=Literature&index=literature&keyword=%22Biological%20Diversity%22&sortField=searchDate
 * and removes header/footer, processes the links and returns the html to be included inside page
 */
class EcolexParser {
	private $url = null;
	private $curl_timeout = 10;
	private $page_url = null;

	private $html = null;
	private $parsed = false;
	private $doc = null;

	private $WWW_ECOLEX_ORG = 'http://ecolex.org';

	/**
	 * @param string $url - URL from Ecolex to parse
	 * @param string $page_url - URL to modify links to point to (links from paginator and sorter)
	 */
	public function __construct($url, $page_url) {
		$this->url = $url;
		$this->page_url = $page_url;
	}

	protected function get_remote_html() {
		//echo "Retrieving the HTML content from {$this->url}\n";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->curl_timeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15 InforMEA") );
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		$this->html = curl_exec($ch);
		curl_close($ch);

		//@todo: check for errors or empty results before going further.
		// ""

		// Remove <meta name="keywords" as contains non UTF-8 characters
		$this->html = preg_replace('/\<meta name="keywords" content=".*">/', '', $this->html);

		// Replace & with &amp; (only when not followed by n (&nbsp;) or a (&amp;)
		$this->html = preg_replace('/&(![n,a])/', '&amp;', $this->html);

		// Remove the "clear" div
		$this->html = preg_replace('/<div class="clear"><\/div>/', '', $this->html);

		return $this->html;
	}

	protected function parse_html() {
		if(!$this->parsed) {
			$this->parsed = true;
			$this->get_remote_html();
			$d = new DOMDocument();
			$d->strictErrorChecking = false;
			$d->recover = true;
			//echo "    * Parsing the HTML content\n";

			libxml_use_internal_errors(true);
			$d->loadHTML($this->html);
			libxml_use_internal_errors(false);

			$this->doc = $d;
		}
	}

	protected function get_content_div() {
		$ret = null;
		$this->parse_html();
		$cn = $this->doc->getElementById('content');
		if($cn) {
			$newdoc = new DOMDocument();
			$cloned = $cn->cloneNode(true);
			$this->fix_url_informea($cloned);

			$newdoc->appendChild($newdoc->importNode($cloned, true));
			//echo "    * Extract the content div\n";
			$ret = $newdoc->saveHTML();
		}
		return $ret;
	}

	protected function fix_url_informea(&$node) {
		// Fix the links from sorter table
		$tables = $node->getElementsByTagName('table');
		if($tables->length > 0) {
			//echo "    * Found <table>, now fixing links inside\n";
			$table = $tables->item(0);
			foreach($table->getElementsByTagName('a') as $a) {
				$a->setAttribute('href', $this->page_url . '?next=' . urlencode(urlencode($this->WWW_ECOLEX_ORG . $a->getAttribute('href'))));
			}
		}

		// Fix the links from pager paginator
		$spans = $node->getElementsByTagName('span');
		foreach($spans as $span) {
			if($span->getAttribute('class') == 'table-pager') {
				//echo "    * Found <span class='table-pager'>, now fixing links inside\n";
				foreach($span->getElementsByTagName('a') as $a) {
					$a->setAttribute('href', $this->page_url . '?next=' . urlencode(urlencode($this->WWW_ECOLEX_ORG . $a->getAttribute('href'))));
				}
			}
		}

		// Fix the links from results to open in new tab/window
		$uls = $node->getElementsByTagName('ul');
		if($uls->length > 0) {
			//echo "    * Found <ul>, now fixing links inside\n";
			$ul = $uls->item(0);
			foreach($ul->getElementsByTagName('a') as $a) {
				$a->setAttribute('target', '_blank');
				$attr = $a->getAttribute('href');
				if(strpos($attr, 'http') === false) {
					$a->setAttribute('href', $this->WWW_ECOLEX_ORG . $a->getAttribute('href'));
				}
			}
		}
	}

	/**
	 * Returns the HTML after being processed. May return NULL if parsing fails
	 * @return string Content as HTML
	 */
	public function get_content() {
		$ret = $this->get_content_div();
		$ret = preg_replace('/\<div id="content"\>/', '<div id="ecolex-content">', $ret);
		return $ret;
	}

	/**
	 * Returns the raw HTML as was loaded from Ecolex website
	 */
	public function get_raw_html() {
		$this->parse_html();
		return $this->html;
	}
}
}



if(!class_exists('UNDataWebsiteParser')) {

/**
 * Parse content from UN Data Website results page
 * i.e. http://data.un.org/CountryProfile.aspx?crName=Romania
 */
class UNDataWebsiteParser {
	private $url = null;
	private $curl_timeout = 10;

	private $id_country = null;
	private $country_name = null;

	private $html = null;
	private $doc = null;

	private $WWW_URL = 'http://data.un.org/CountryProfile.aspx?crName=';
	private $WWW_IMG_URL = 'http://data.un.org/';

	private $img = null;
	private $environment = null;
	private $parsed = false;

	/**
	 * @param string $id_country - Country internal ID
	 * @param string $country_name - Country name
	 */
	public function __construct($id_country, $country_name) {
		$this->id_country = $id_country;
		$this->country_name = $country_name;

		$this->url = $this->WWW_URL . $country_name;
		// Check cache
		$this->check_cache($id_country, $country_name);
	}

	protected function check_cache() {
		global $wpdb;
		// Purge old records from cache
		$wpdb->query('DELETE FROM ai_cache WHERE `created` < DATE_SUB(NOW(), INTERVAL 14 DAY)');
		// Look for our data
		$img = $wpdb->get_row($wpdb->prepare('SELECT * FROM ai_cache WHERE id=%s AND domain=%s', $this->id_country, 'un-img-country'));
		if($img != null) { $this->img = $img->value; }

		$environment = $wpdb->get_row($wpdb->prepare('SELECT * FROM ai_cache WHERE id=%s AND domain=%s', $this->id_country, 'un-img-environment'));
		if($environment != null) { $this->environment = $environment->value; }
	}

	protected function get_remote_dom() {
		//echo "Retrieving the HTML content from {$this->url}\n";
		if($this->parsed) { return $this->doc; }
		$ch = curl_init();
		$this->parsed = true;
		curl_setopt($ch, CURLOPT_URL, $this->url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->curl_timeout);
		curl_setopt($ch, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15 InforMEA") );
		curl_setopt($ch, CURLOPT_NOBODY, 0);
		$this->html = curl_exec($ch);
		curl_close($ch);

		//echo "    * Parsing the HTML content\n";
		if(!empty($this->html)) {
			$d = new DOMDocument();
			$d->strictErrorChecking = false;
			$d->recover = true;
			libxml_use_internal_errors(true);
			$d->loadHTML($this->html);
			libxml_use_internal_errors(false);
			$this->doc = $d;
			return $d;
		}
		return null;
	}


	/**
	 * Returns the country map image url.
	 * @return string URL
	 */
	public function get_map_image() {
		if(!empty($this->img)) {
			return $this->img;
		}
		$ret = null;
		$doc = $this->get_remote_dom();
		if($doc) {
			$cn = $this->doc->getElementById('ctl00_main_MapSection');
			if($cn) {
				$imgs = $cn->getElementsByTagName('img');
				if($imgs->length > 0) {
					$ret = $imgs->item(0);
					$ret = $this->WWW_IMG_URL . $ret->getAttribute('src');
					$this->img = $ret;

					// Populate the cache
					global $wpdb;
					$wpdb->insert('ai_cache', array('id' => $this->id_country, 'domain' => 'un-img-country', 'value' => $ret));
				}
			}
		}
		return $ret;
	}

	/**
	 * Returns the Environment section from country profile
	 * @return string HTML
	 */
	public function get_environmental_data() {
		if(!empty($this->environment)) {
			return $this->environment;
		}
		$ret = null;
		$doc = $this->get_remote_dom();
		if($doc) {
			$cn = $this->doc->getElementById('Environment');
			if($cn) {
				$table = $cn->nextSibling->nextSibling;
				if($table && $table->nodeName == 'table') {
					$newdoc = new DOMDocument();
					$cloned = $table->cloneNode(true);
					$newdoc->appendChild($newdoc->importNode($cloned, true));
					$ret = $newdoc->saveHTML();
					$this->environment = $ret;

					// Populate the cache
					global $wpdb;
					$wpdb->insert('ai_cache', array('id' => $this->id_country, 'domain' => 'un-img-environment', 'value' => $ret));
				}
			}
		}
		return $ret;
	}

	/**
	 * Returns the raw HTML as was loaded from Ecolex website
	 */
	public function get_raw_html() {
		$this->parse_html();
		return $this->html;
	}
}
}
?>
