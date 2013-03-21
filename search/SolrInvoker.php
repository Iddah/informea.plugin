<?php
class InformeaSolrInvoker {
    private $request;
    private $solr;
    private $search_ui;


    public function __construct($request, $solr_server_cfg) {
        $this->request = $request;
        $this->solr = new SolrClient($solr_server_cfg);
        $this->search_ui = new AbstractSearch($request);
    }


    public function solr_entity_type() {
        $arr = array();
        $tab = $this->search_ui->get_q_tab();
        if ($this->search_ui->is_use_decisions() && $tab != '3') {
            $arr[] = 'entity_type:decision';
            $arr[] = 'entity_type:decision_paragraph';
            $arr[] = 'entity_type:decision_document';
        }
        if ($this->search_ui->is_use_meetings() && $tab != '3' && $tab != '4') {
            $arr[] = 'entity_type:event';
        }
        if ($this->search_ui->is_use_treaties() && $tab != '4') {
            $arr[] = 'entity_type:treaty';
            $arr[] = 'entity_type:treaty_article';
            $arr[] = 'entity_type:treaty_article_paragraph';
        }
        if (!count($arr)) {
            $arr[] = 'entity_type:dummy_yield_zero_results';
        }
        return '(' . implode(' OR ', $arr) . ')';
    }


    /**
     * @return array Array of StdClass objects having id and entity_type fields
     */
    public function search($debug) {
        $start = microtime_float();
        $ret = new StdClass();
        $ret->events = array();
        $ret->decisions = array();
        $ret->decisions_documents = array();
        $ret->decisions_paragraphs = array();
        // $ret->decisions_paragraphs = array(); - requirements have changed
        $ret->treaties = array();
        $ret->treaties_articles = array();
        $ret->treaties_articles_paragraphs = array();

        // Handle special case where search string is empty - return empty results
        if (!$this->search_ui->is_dirty_search()) {
            return $ret;
        }

        $query = new SolrQuery($this->search_ui->get_solr_query());
        // Set the fields returned by the query
        $query->addField('id')->addField('entity_type')->addField('decision_id')->addField('treaty_article_id')->addField('treaty_id');
        // Set the entities returned by the query
        $query->addFilterQuery($this->solr_entity_type());
        // Get a very large number of results to ensure we get all the hits
        $query->setRows(99999);
        $q_resp = null;
        try {
            $q_resp = $this->solr->query($query);
            $q_resp->setParseMode(SolrQueryResponse::PARSE_SOLR_DOC);
            $resp = $q_resp->getResponse();
            if (!empty($resp->response->docs)) {
                foreach ($resp->response->docs as $doc) {
                    $ob = new StdClass();
                    $ob->id = intval($doc->getField('id')->values[0]);
                    $ob->type = $doc->getField('entity_type')->values[0];
                    $ob->decision_id = null;
                    // Inject object-specific properties
                    if (isset($doc->decision_id->values[0])) {
                        $ob->decision_id = $doc->decision_id->values[0];
                    }
                    if (isset($doc->treaty_article_id->values[0])) {
                        $ob->treaty_article_id = $doc->treaty_article_id->values[0];
                    }
                    if (isset($doc->treaty_id->values[0])) {
                        $ob->treaty_id = $doc->treaty_id->values[0];
                    }
                    $ob_id = intval($ob->id);
                    // Sort properties by type
                    switch ($ob->type) {
                        case 'event':
                            $ret->events[$ob_id] = $ob;
                            break;
                        case 'treaty';
                            $ret->treaties[$ob_id] = $ob;
                            break;
                        case 'treaty_article';
                            $ret->treaties_articles[$ob_id] = $ob;
                            break;
                        case 'treaty_article_paragraph';
                            $ret->treaties_articles_paragraphs[$ob_id] = $ob;
                            break;
                        case 'decision';
                            $ret->decisions[$ob_id] = $ob;
                            break;
                        case 'decision_document';
                            $ret->decisions[$ob->decision_id] = $ob;
                            $ret->decisions_documents[$ob_id] = $ob;
                            break;
                        case 'decision_paragraph';
                            $ret->decisions[$ob->decision_id] = $ob;
                            $ret->decisions_paragraphs[$ob_id] = $ob;
                            break;
                        default:
                            throw new Exception('Unknown entity type:' . $ob->type);
                    }
                }
            }
        } catch (Exception $e) {
            imea_log('Failed Solr query');
            imea_log($e);
        }
        microtime_float($start, '    Solr search');
        return $ret;
    }


    public function highlight($id, $entity_type, $size = 500) {
        $excerpt = '';
        $goptions = get_option('informea_options');
        $options = array(
            'hostname' => $goptions['solr_server'],
            'path' => $goptions['solr_path'],
            'port' => $goptions['solr_port']
        );
        $solr = new SolrClient($options);
        $query_string = $this->search_ui->get_solr_query();
        if (empty($query_string)) {
            return '';
        }
        if (!empty($query_string)) {
            $q = new SolrQuery($query_string);
            $uid = "$id $entity_type";
            $q->addFilterQuery("unique_id:\"$uid\"");
            $q->addField('unique_id');
            $q->setHighlight(true);
            $q->addHighlightField('text');
            $q->setHighlightSnippets(5);
            $q->setHighlightSimplePre('$$$$$');
            $q->setHighlightSimplePost('#####');
            $q->setHighlightFragsize($size);
            $q->setHighlightMaxAnalyzedChars(200000); // To analyse entire document
            $q_resp = $solr->query($q);
            $q_resp->setParseMode(SolrQueryResponse::PARSE_SOLR_DOC);
            $resp = $q_resp->getResponse();
            if (isset($resp->response->docs[0])) {
                if (isset($resp->highlighting)) {
                    if ($resp->highlighting->offsetExists($uid)) {
                        $field = $resp->highlighting->offsetGet($uid);
                        $field = $field->offsetGet('text');
                        if ($field) {
                            foreach ($field as $snippet) {
                                $excerpt .= '<p class="highlight_snippet"> ... ' . htmlspecialchars(strip_tags($snippet)) . '... </p>';
                            }
                            $excerpt = str_replace('$$$$$', '<strong class="search_highlight">', $excerpt);
                            $excerpt = str_replace('#####', '</strong>', $excerpt);
                            $excerpt = str_replace('&nbsp;', ' ', $excerpt);
                        }
                    }
                }
            }
        }
        return $excerpt;
    }
}
?>
