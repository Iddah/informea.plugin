<?php
require_once(dirname(__FILE__) . '/../InformeaSearch3.php');

class InformeaSearch3TestImpl extends InformeaSearch3 {

    public function __construct($request, $solr_config = array()) {
        parent::__construct($request, $solr_config);
    }

    public function solr_entity_filter() {
        return parent::solr_entity_filter();
    }
}


class InformeaSearch3Test extends WP_UnitTestCase {

    function test_search_database_decision() {
        $ob = new InformeaSearch3(array(
            'q_term' => array('580')
        ));
        $results = $ob->search();
        $this->assertEquals(1, count($results['treaties']));
        $this->assertEquals(array(46), array_keys($results['treaties']));
        $this->assertEquals(array(
            'articles' => array(),
            'decisions' => array(10303 => array('paragraphs' => array(), 'documents' => array()))), $results['treaties'][46]);
    }

    function test_search_database_treaty() {
        $ob = new InformeaSearch3(array(
            'q_term' => array('581')
        ));
        $results = $ob->search();
        $this->assertEquals(1, count($results['treaties']));
        $this->assertEquals(array(46), array_keys($results['treaties']));
        $this->assertEquals(array(
            'articles' => array(
                1405 => array(7715)
            ),
            'decisions' => array()
        ), $results['treaties'][46]);
    }

    function test_search_database_both() {
        $ob = new InformeaSearch3(array(
            'q_term' => array('579')
        ));
        $results = $ob->search();
        $this->assertEquals(1, count($results['treaties']));
        $this->assertEquals(array(46), array_keys($results['treaties']));
        $this->assertEquals(array(
            'articles' => array(),
            'decisions' => array(
                10303 => array(
                    'paragraphs' => array(35), 'documents' => array()
                )
            )
        ), $results['treaties'][46]);
    }


    function test_search_solr_treaty() {
        $ob = new InformeaSearch3(array(
            'q_use_treaties' => 1,
            'q_freetext' => '"The test treaty title"'
        ));
        $results = $ob->search();
        $treaties = $results['treaties'];
        $this->assertEquals(1, count($treaties));
        $this->assertEquals(array('articles' => array(), 'decisions' => array()), $treaties[46]);
    }

    function test_search_solr_article() {
        $ob = new InformeaSearch3(array(
            'q_use_treaties' => 1,
            'q_freetext' => '"First article"'
        ));
        $results = $ob->search();
        $treaties = $results['treaties'];
        $this->assertEquals(1, count($treaties));
        $this->assertEquals(array('articles' => array(1405 => array(7715, 7716)), 'decisions' => array()), $treaties[46]);
    }


    function test_search_solr_treaty_paragraph() {
        $ob = new InformeaSearch3(array(
            'q_use_treaties' => 1,
            'q_freetext' => '"First paragraph of the first article"'
        ));
        $results = $ob->search();
        $treaties = $results['treaties'];
        $this->assertEquals(1, count($treaties));
        $this->assertEquals(array('articles' => array(1405 => array(7715)), 'decisions' => array()), $treaties[46]);
    }

    function test_search_solr_decision() {
        $ob = new InformeaSearch3(array(
            'q_use_decisions' => 1,
            'q_freetext' => '"Test decision for test treaty"'
        ));
        $results = $ob->search();
        $treaties = $results['treaties'];
        $this->assertEquals(1, count($treaties));
        $this->assertEquals(array(
            'articles' => array(),
            'decisions' => array(
                10303 => array(
                    'paragraphs' => array(), 'documents' => array()
                )
            )
        ), $treaties[46]);
    }

    function test_search_solr_decision_paragraph_and_document() {
        $ob = new InformeaSearch3(array(
            'q_use_decisions' => 1,
            'q_freetext' => '"TEST OF DOCUMENT003 FOR DECISION"'
        ));
        $results = $ob->search();
        $treaties = $results['treaties'];
        $this->assertEquals(1, count($treaties));
        $this->assertEquals(array(
            'articles' => array(),
            'decisions' => array(
                10303 => array(
                    'paragraphs' => array(35), 'documents' => array(4970)
                )
            )
        ), $treaties[46]);
    }


    function test_search_solr_event() {
        $ob = new InformeaSearch3(array(
            'q_use_meetings' => 1,
            'q_freetext' => '"First Meeting"'
        ));
        $results = $ob->search();
        $events = $results['events'];
        $this->assertEquals(1, count($events));
        $this->assertEquals(array(2481), $events);
    }


    function test_search_solr_wrong_use_meetings() {
        $ob = new InformeaSearch3(array(
            'q_use_meetings' => 1,
            'q_freetext' => 'TEST OF DOCUMENT003 FOR DECISION'
        ));
        $results = $ob->search();
        $this->assertEquals(0, count($results['treaties']));
        $this->assertEquals(0, count($results['events']));

    }

    function test_solr_entity_filter() {
        $ob = new InformeaSearch3TestImpl(array());
        $this->assertEquals('(entity_type:dummy_yield_zero_results)', $ob->solr_entity_filter());

        $ob1 = new InformeaSearch3TestImpl(array('q_use_decisions' => 1));
        $this->assertEquals('(entity_type:decision OR entity_type:decision_paragraph OR entity_type:decision_document)', $ob1->solr_entity_filter());

        $ob2 = new InformeaSearch3TestImpl(array('q_use_decisions' => 1, 'q_use_treaties' => 1));
        $this->assertEquals('(entity_type:decision OR entity_type:decision_paragraph OR entity_type:decision_document OR entity_type:treaty OR entity_type:treaty_article OR entity_type:treaty_article_paragraph)', $ob2->solr_entity_filter());

        $ob3 = new InformeaSearch3TestImpl(array('q_use_decisions' => 1, 'q_use_treaties' => 1, 'q_use_meetings' => 1));
        $this->assertEquals('(entity_type:decision OR entity_type:decision_paragraph OR entity_type:decision_document OR entity_type:event OR entity_type:treaty OR entity_type:treaty_article OR entity_type:treaty_article_paragraph)', $ob3->solr_entity_filter());
    }


    function test_search_combined_with_tags() {
        $ob = new InformeaSearch3(array(
            'q_term' => 1,
            'q_use_decisions' => 1,
            'q_freetext' => 'test',
            'q_term' => array('581')
        ));
        $results = $ob->search();
        #var_dump($results);
        $treaty = $results['treaties'][46];
        $this->assertEquals(35, $treaty['decisions'][10303]['paragraphs'][0]);
        $this->assertEquals(4970, $treaty['decisions'][10303]['documents'][0]);
        $this->assertEquals(7715, $treaty['articles'][1405][0]);

    }


    function test_solr_highlight_treaty_paragraph() {
        $ob = new InformeaSearch3(array('q_freetext' => 'article'));
        $ret = $ob->solr_highlight('7715', 'treaty_article_paragraph');
        $this->assertEquals(1, count($ret));
        $this->assertEquals('First paragraph of the first <strong>article</strong>', $ret[0]);
    }

    function test_solr_highlight_decision_document() {
        $ob = new InformeaSearch3(array('q_freetext' => 'test'));
        $ret = $ob->solr_highlight('4970', 'decision_document');
        $this->assertEquals(1, count($ret));
        $this->assertEquals('<strong>TEST</strong> OF DOCUMENT003 FOR DECISION 1 IN ODT.', $ret[0]);
    }
}