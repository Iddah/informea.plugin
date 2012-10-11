<?php
require_once(dirname(__FILE__) . '/../InformeaSearch3.php');

/**
 * @group search3
 */
class InformeaSearch3Test extends WP_UnitTestCase {

    function test_search_database_decision() {
        $ob = new InformeaSearch3(array(
            'q_term' => array('580')
        ));
        $results = $ob->search();
        $this->assertEquals(1, count($results));
        $this->assertEquals(array(46), array_keys($results));
        $this->assertEquals(array('articles' => array(), 'decisions' => array(10303 => array())), $results[46]);
    }

    function test_search_database_treaty() {
        $ob = new InformeaSearch3(array(
            'q_term' => array('581')
        ));
        $results = $ob->search();
        $this->assertEquals(1, count($results));
        $this->assertEquals(array(46), array_keys($results));
        $this->assertEquals(array('articles' => array(1405 => array(7715)), 'decisions' => array()), $results[46]);
    }

    function test_search_database_both() {
        $ob = new InformeaSearch3(array(
            'q_term' => array('579')
        ));
        $results = $ob->search();
        $this->assertEquals(1, count($results));
        $this->assertEquals(array(46), array_keys($results));
        $this->assertEquals(array('articles' => array(), 'decisions' => array(10303 => array(35))), $results[46]);
    }
}