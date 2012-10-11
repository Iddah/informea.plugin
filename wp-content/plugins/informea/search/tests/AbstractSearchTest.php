<?php

require_once(dirname(__FILE__) . '/../AbstractSearch.php');

class AbstractSearchTestImpl extends AbstractSearch {}

class AbstractSearchTest extends WP_UnitTestCase {

    function setUp() {
        parent::setUp();
    }


    function test_is_dirty_search() {
        $ob = new AbstractSearchTestImpl(array('q_freetext' => 'test'));
        $this->assertTrue($ob->is_dirty_search());

        $ob1 = new AbstractSearchTestImpl(array('q_freetext' => ''));
        $this->assertFalse($ob1->is_dirty_search());

        $ob2 = new AbstractSearchTestImpl(array());
        $this->assertFalse($ob2->is_dirty_search());
    }


    function test_get_request_value() {
        $ob = new AbstractSearchTestImpl(
            array(
                    'param1' => 'value1',
                    'param2' => array('value1', 'value2'),
                    'param3' => '5',
                    'param4' => '2.3',
                    'param5' => ' test ',
                    'param6' => array(' test1 ', ' test2 ')
                )
           );
        $this->assertEquals('value1', $ob->get_request_value('param1'));
        $this->assertEquals(array('value1', 'value2'), $ob->get_request_value('param2'));
        $this->assertEquals(5, $ob->get_request_value('param3'));
        $this->assertEquals(2.3, $ob->get_request_value('param4'));

        $this->assertEquals('test', $ob->get_request_value('param5'));
        $this->assertEquals(' test ', $ob->get_request_value('param5', NULL, FALSE));

        $this->assertEquals(array('test1', 'test2'), $ob->get_request_value('param6'));
        $this->assertEquals(array(' test1 ', ' test2 '), $ob->get_request_value('param6', NULL, FALSE));
    }


    function test_get_request_int() {
        $ob = new AbstractSearchTestImpl(
            array(
                    'param1' => 'str',
                    'param2' => '2',
                    'param3' => '2.5'
                )
           );
        $this->assertEquals(0, $ob->get_request_int('param1'));
        $this->assertEquals(2, $ob->get_request_int('param2'));
        $this->assertEquals(2, $ob->get_request_int('param3'));
    }


    function test_get_treaties() {
        $ob = new AbstractSearchTestImpl(
            array('q_treaty' => array('1', '2'))
        );
        $this->assertEquals(array(1, 2), $ob->get_treaties());

        $ob1 = new AbstractSearchTestImpl(array('q_treaty' => array()));
        $this->assertEquals(array(), $ob1->get_treaties());

        $ob2 = new AbstractSearchTestImpl(array());
        $this->assertEquals(array(), $ob2->get_treaties());

        $ob3 = new AbstractSearchTestImpl(
            array('q_treaty' => array('invalid', '2'))
        );
        $this->assertEquals(array(0, 2), $ob3->get_treaties());
    }


    function test_is_terms_or() {
        $ob = new AbstractSearchTestImpl(array('q_term_or' => 'or'));
        $this->assertTrue($ob->is_terms_or());

        $ob1 = new AbstractSearchTestImpl(array('q_term_or' => '1'));
        $this->assertFalse($ob1->is_terms_or());

        $ob2 = new AbstractSearchTestImpl(array());
        $this->assertFalse($ob2->is_terms_or());
    }


    function test_get_terms() {
        $ob = new AbstractSearchTestImpl(
            array('q_term' => array('1', '2'))
        );
        $this->assertEquals(array(1, 2), $ob->get_terms());

        $ob1 = new AbstractSearchTestImpl(array('q_term' => array()));
        $this->assertEquals(array(), $ob1->get_terms());

        $ob2 = new AbstractSearchTestImpl(array());
        $this->assertEquals(array(), $ob2->get_terms());

        $ob3 = new AbstractSearchTestImpl(
            array('q_term' => array('invalid', '2'))
        );
        $this->assertEquals(array(0, 2), $ob3->get_terms());
    }


    function test_is_using_terms() {
        $ob = new AbstractSearchTestImpl(array('q_term' => array(1)));
        $this->assertTrue($ob->is_using_terms());

        $ob1 = new AbstractSearchTestImpl(array());
        $this->assertFalse($ob1->is_using_terms());
    }


    function test_get_freetext() {
        $ob = new AbstractSearchTestImpl(array('q_freetext' => 'test'));
        $this->assertEquals('test', $ob->get_freetext());

        $ob1 = new AbstractSearchTestImpl(array());
        $this->assertEquals('', $ob1->get_freetext());
    }


    function test_get_start_date() {
        $ob = new AbstractSearchTestImpl(
            array(
                'q_start_month' => '2',
                'q_start_year' => '2012'
            )
        );
        $this->assertEquals('2012-02-01', $ob->get_start_date());
        $this->assertEquals('2012-02-01T00:00:00Z', $ob->get_start_date(true));

        $ob1 = new AbstractSearchTestImpl(
            array(
                'q_start_month' => '2',
            )
        );
        $this->assertEquals(NULL, $ob1->get_start_date());
        $this->assertEquals(NULL, $ob1->get_start_date(true));

        $ob2 = new AbstractSearchTestImpl(
            array(
                'q_start_year' => '2012',
            )
        );
        $this->assertEquals(NULL, $ob2->get_start_date());
        $this->assertEquals(NULL, $ob2->get_start_date(true));

        $ob3 = new AbstractSearchTestImpl(
            array(
                'q_start_month' => 'a',
                'q_start_year' => 'b'
            )
        );
        $this->assertEquals(NULL, $ob3->get_start_date());
        $this->assertEquals(NULL, $ob3->get_start_date(true));

        $ob4 = new AbstractSearchTestImpl(
            array(
                'q_start_month' => '13',
                'q_start_year' => '2012'
            )
        );
        $this->assertEquals(NULL, $ob4->get_start_date());
        $this->assertEquals(NULL, $ob4->get_start_date(true));
    }


    function test_get_end_date() {
        $ob = new AbstractSearchTestImpl(
            array(
                'q_end_month' => '2',
                'q_end_year' => '2012'
            )
        );
        $this->assertEquals('2012-02-31', $ob->get_end_date());
        $this->assertEquals('2012-02-31T00:00:00Z', $ob->get_end_date(true));

        $ob1 = new AbstractSearchTestImpl(
            array(
                'q_end_month' => '2',
            )
        );
        $this->assertEquals(NULL, $ob1->get_end_date());
        $this->assertEquals(NULL, $ob1->get_end_date(true));

        $ob2 = new AbstractSearchTestImpl(
            array(
                'q_end_year' => '2012',
            )
        );
        $this->assertEquals(NULL, $ob2->get_end_date());
        $this->assertEquals(NULL, $ob2->get_end_date(true));

        $ob3 = new AbstractSearchTestImpl(
            array(
                'q_end_month' => 'a',
                'q_end_year' => 'b'
            )
        );
        $this->assertEquals(NULL, $ob3->get_end_date());
        $this->assertEquals(NULL, $ob3->get_end_date(true));

        $ob4 = new AbstractSearchTestImpl(
            array(
                'q_end_month' => '13',
                'q_end_year' => '2012'
            )
        );
        $this->assertEquals(NULL, $ob4->get_end_date());
        $this->assertEquals(NULL, $ob4->get_end_date(true));
    }


    function test_is_use_treaties() {
        $ob = new AbstractSearchTestImpl(array('q_use_treaties' => '1'));
        $this->assertTrue($ob->is_use_treaties());

        $ob1 = new AbstractSearchTestImpl(array('q_use_treaties' => '0'));
        $this->assertTrue($ob1->is_use_treaties());

        $ob2 = new AbstractSearchTestImpl(array('q_use_treaties' => 'a'));
        $this->assertTrue($ob2->is_use_treaties());

        $ob3 = new AbstractSearchTestImpl(array());
        $this->assertFalse($ob3->is_use_treaties());
    }


    function test_is_use_decisions() {
        $ob = new AbstractSearchTestImpl(array('q_use_decisions' => '1'));
        $this->assertTrue($ob->is_use_decisions());

        $ob1 = new AbstractSearchTestImpl(array('q_use_decisions' => '0'));
        $this->assertTrue($ob1->is_use_decisions());

        $ob2 = new AbstractSearchTestImpl(array('q_use_decisions' => 'a'));
        $this->assertTrue($ob2->is_use_decisions());

        $ob3 = new AbstractSearchTestImpl(array());
        $this->assertFalse($ob3->is_use_decisions());
    }


    function test_is_use_meetings() {
        $ob = new AbstractSearchTestImpl(array('q_use_meetings' => '1'));
        $this->assertTrue($ob->is_use_meetings());

        $ob1 = new AbstractSearchTestImpl(array('q_use_meetings' => '0'));
        $this->assertTrue($ob1->is_use_meetings());

        $ob2 = new AbstractSearchTestImpl(array('q_use_meetings' => 'a'));
        $this->assertTrue($ob2->is_use_meetings());

        $ob3 = new AbstractSearchTestImpl(array());
        $this->assertFalse($ob3->is_use_meetings());
    }


    function test_get_q_tab() {
        $ob = new AbstractSearchTestImpl(array('q_tab' => '2'));
        $this->assertEquals(2, $ob->get_q_tab());

        $ob1 = new AbstractSearchTestImpl(array());
        $this->assertEquals(1, $ob1->get_q_tab());

        $ob2 = new AbstractSearchTestImpl(array('q_tab' => 'xxx'));
        $this->assertEquals(1, $ob2->get_q_tab());
    }


    function test_get_page() {
        $ob = new AbstractSearchTestImpl(array('q_page' => '3'));
        $this->assertEquals(3, $ob->get_page());

        $ob1 = new AbstractSearchTestImpl(array());
        $this->assertEquals(0, $ob1->get_page());

        $ob2 = new AbstractSearchTestImpl(array('q_page' => 'xxx'));
        $this->assertEquals(0, $ob2->get_page());
    }


    function test_get_page_size() {
        $ob = new AbstractSearchTestImpl(array('q_page_size' => '3'));
        $this->assertEquals(3, $ob->get_page_size());

        $ob1 = new AbstractSearchTestImpl(array());
        $this->assertEquals(10, $ob1->get_page_size());

        $ob2 = new AbstractSearchTestImpl(array('q_page_size' => 'xxx'));
        $this->assertEquals(10, $ob2->get_page_size());
    }


    function test_get_sort_direction() {
        $ob = new AbstractSearchTestImpl(array('q_sort_direction' => 'ASC'));
        $this->assertEquals('ASC', $ob->get_sort_direction());

        $ob1 = new AbstractSearchTestImpl(array());
        $this->assertEquals('DESC', $ob1->get_sort_direction());

        $ob2 = new AbstractSearchTestImpl(array('q_sort_direction' => 'xxx'));
        $this->assertEquals('DESC', $ob2->get_sort_direction());
    }


}
