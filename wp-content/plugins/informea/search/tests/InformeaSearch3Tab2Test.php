<?php
require_once(dirname(__FILE__) . '/../InformeaSearch3.php');

/**
 * @group i2
 */
class InformeaSearch3Tab2Test extends WP_UnitTestCase {

    function test_search() {
        $ob = new InformeaSearch3Tab2(array('q_freetext' => 'test'));
        $results = $ob->search();
        $this->assertEquals(1, count($results));

        $this->assertEquals('The test treaty title', $results[46]->short_title);
        $this->assertTrue(empty($results[46]->articles));
        $this->assertEquals(0, count($results[46]->decisions));

        $this->assertEquals(0, count($results[46]->decisions));
    }
}