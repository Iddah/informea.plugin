<?php
require_once(dirname(__FILE__) . '/../InformeaSearch3.php');

class InformeaSearch3Tab1TestImpl extends InformeaSearch3Tab1 {

    public function __construct($request) {
        parent::__construct($request);
    }


    public function sort_and_paginate($treaties, $decisions, $meetings, $full = FALSE) {
        return parent::sort_and_paginate($treaties, $decisions, $meetings, $full);
    }
}

class InformeaSearch3Tab1Test extends WP_UnitTestCase {

    function test_sort_and_paginate_noresults() {
        $ob = new InformeaSearch3Tab1TestImpl(
            array('q_page' => 0, 'q_page_size' => 10)
        );
        $treaties = array();
        $decisions = array();
        $meetings = array();
        $results = $ob->sort_and_paginate($treaties, $decisions, $meetings);
        $this->assertTrue(empty($results));
    }

    function test_sort_and_paginate_treaty() {
        $ob = new InformeaSearch3Tab1TestImpl(array('q_page' => 0, 'q_page_size' => 10));
        $treaties = array(46);
        $decisions = array();
        $meetings = array();
        $results = $ob->sort_and_paginate($treaties, $decisions, $meetings);
        $this->assertEquals(1, count($results));
        $this->assertEquals(46, $results[0]->id_entity);
        $this->assertEquals('treaty', $results[0]->type);
    }

    function test_sort_and_paginate_decision() {
        $ob = new InformeaSearch3Tab1TestImpl(array('q_page' => 0, 'q_page_size' => 10));
        $treaties = array();
        $decisions = array(10303);
        $meetings = array();
        $results = $ob->sort_and_paginate($treaties, $decisions, $meetings);
        $this->assertEquals(1, count($results));
        $this->assertEquals(10303, $results[0]->id_entity);
        $this->assertEquals('decision', $results[0]->type);
    }

    function test_sort_and_paginate_event() {
        $ob = new InformeaSearch3Tab1TestImpl(array('q_page' => 0, 'q_page_size' => 10));
        $treaties = array();
        $decisions = array();
        $meetings = array(2481, 2482, 2483);
        $results = $ob->sort_and_paginate($treaties, $decisions, $meetings);
        $this->assertEquals(3, count($results));
        $this->assertEquals(2483, $results[0]->id_entity);
        $this->assertEquals('event', $results[0]->type);
    }

    function test_sort_and_paginate_pagesize() {
        $ob = new InformeaSearch3Tab1TestImpl(array('q_page' => 0, 'q_page_size' => 2));
        $treaties = array(46);
        $decisions = array(10303);
        $meetings = array(2481, 2482, 2483);
        $results = $ob->sort_and_paginate($treaties, $decisions, $meetings);
        $this->assertEquals(2, count($results));
        $this->assertEquals(2483, $results[0]->id_entity);
        $this->assertEquals(2482, $results[1]->id_entity);
    }

    function test_sort_and_paginate_second_page_2() {
        $ob = new InformeaSearch3Tab1TestImpl(array('q_page' => 1, 'q_page_size' => 2));
        $treaties = array(46);
        $decisions = array(10303);
        $meetings = array(2481, 2482, 2483);
        $results = $ob->sort_and_paginate($treaties, $decisions, $meetings);
        $this->assertEquals(2, count($results));
        $this->assertEquals(10303, $results[0]->id_entity);
        $this->assertEquals(2481, $results[1]->id_entity);
    }

    function test_sort_and_paginate_second_sort() {
        $ob = new InformeaSearch3Tab1TestImpl(array('q_page' => 0, 'q_page_size' => 3, 'q_sort_direction' => 'ASC'));
        $treaties = array();
        $decisions = array();
        $meetings = array(2481, 2482, 2483);
        $results = $ob->sort_and_paginate($treaties, $decisions, $meetings);
        $this->assertEquals(3, count($results));
        $this->assertEquals(2481, $results[0]->id_entity);
        $this->assertEquals(2482, $results[1]->id_entity);
    }

    function test_sort_and_paginate_full() {
        $ob = new InformeaSearch3Tab1TestImpl(array());
        $treaties = array(46);
        $decisions = array(10303);
        $meetings = array(2481, 2482, 2483, 2484, 2485, 2486, 2487, 2488, 2489, 2490, 2491);
        $results = $ob->sort_and_paginate($treaties, $decisions, $meetings, true);
        $this->assertEquals(13, count($results));
    }


    function test_search_decisions() {
        $ob = new InformeaSearch3Tab1(array(
            'q_freetext' => 'Test decision for test treaty xxxx',
            'q_use_decisions' => 1,
        ));
        $results = $ob->search(true);
        $this->assertEquals(1, count($results));
        $this->assertEquals('Test decision for test treaty xxxx', $results[0]->short_title);
        $this->assertEquals('decision', $results[0]->entity_type);
    }

    function test_search_treaties() {
        $ob = new InformeaSearch3Tab1(array(
            'q_freetext' => 'Test',
            'q_use_treaties' => 1,
        ));
        $results = $ob->search(true);
        $this->assertEquals(1, count($results));
        $this->assertEquals('The test treaty title', $results[0]->short_title);
        $this->assertEquals('treaty', $results[0]->entity_type);
    }

    function test_search_meetings() {
        $ob = new InformeaSearch3Tab1(array(
            'q_freetext' => 'Meeting',
            'q_use_meetings' => 1,
        ));
        $results = $ob->search(true);
        $this->assertEquals(11, count($results));
        $this->assertEquals('Meeting 3', $results[0]->title);
        $this->assertEquals('event', $results[0]->entity_type);
    }
}
