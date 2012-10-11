<?php

require_once(dirname(__FILE__) . '/../CacheManager.php');

class CacheManagerTestImpl extends CacheManager {


    public static function get_cache_treaty_treatyparagraph() {
        return parent::$cache_treaty_treatyparagraph;
    }


    public static function get_cache_treaty_decisionparagraph() {
        return parent::$cache_treaty_decisionparagraph;
    }
}

class CacheManagerTest extends WP_UnitTestCase {


    function test_get_treaty_for_treaty_paragraph_laziness() {
        $cache = CacheManagerTestImpl::get_cache_treaty_treatyparagraph();
        $this->assertEquals(array(), $cache);

        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_treaty_paragraph('7715'));

        $cache1 = CacheManagerTestImpl::get_cache_treaty_treatyparagraph();
        $this->assertNotEquals(array(), $cache1);
    }


    function test_get_treaty_for_treaty_paragraph() {
        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_treaty_paragraph('7715'));
    }


    function test_get_treaty_for_decision_paragraph_laziness() {
        $cache = CacheManagerTestImpl::get_cache_treaty_decisionparagraph();
        $this->assertEquals(array(), $cache);

        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_decision_paragraph('35'));

        $cache1 = CacheManagerTestImpl::get_cache_treaty_decisionparagraph();
        $this->assertNotEquals(array(), $cache1);
    }


    function test_get_treaty_for_decision_paragraph() {
        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_decision_paragraph('35'));
    }
}