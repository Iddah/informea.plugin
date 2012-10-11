<?php

require_once(dirname(__FILE__) . '/../CacheManager.php');

class CacheManagerTestImpl extends CacheManager {


    public static function get_cache_treaty_treatyparagraph() {
        return parent::$cache_treaty_treatyparagraph;
    }


    public static function get_cache_treaty_decisionparagraph() {
        return parent::$cache_treaty_decisionparagraph;
    }

    public static function get_cache_treaty_article() {
        return parent::$cache_article_treaty;
    }

    public static function get_cache_treaty_decision() {
        return parent::$cache_decision_treaty;
    }
}

class CacheManagerTest extends WP_UnitTestCase {


    function test_get_treaty_for_treaty_paragraph_laziness() {

        CacheManagerTestImpl::clear();

        $cache = CacheManagerTestImpl::get_cache_treaty_treatyparagraph();
        $this->assertEquals(array(), $cache);

        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_treaty_paragraph('7715'));

        $cache1 = CacheManagerTestImpl::get_cache_treaty_treatyparagraph();
        $this->assertNotEquals(array(), $cache1);
    }


    function test_get_treaty_for_treaty_paragraph() {
        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_treaty_paragraph('7715'));
    }

    ///

    function test_get_treaty_for_decision_paragraph_laziness() {

        CacheManagerTestImpl::clear();

        $cache = CacheManagerTestImpl::get_cache_treaty_decisionparagraph();
        $this->assertEquals(array(), $cache);

        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_decision_paragraph('35'));

        $cache1 = CacheManagerTestImpl::get_cache_treaty_decisionparagraph();
        $this->assertNotEquals(array(), $cache1);
    }


    function test_get_treaty_for_decision_paragraph() {
        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_decision_paragraph('35'));
    }

    ///

    function test_get_treaty_for_article_laziness() {

        CacheManagerTestImpl::clear();

        $cache = CacheManagerTestImpl::get_cache_treaty_article();
        $this->assertEquals(array(), $cache);

        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_article('1405'));

        $cache1 = CacheManagerTestImpl::get_cache_treaty_article();
        $this->assertNotEquals(array(), $cache1);
    }


    function test_get_treaty_for_article() {
        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_article('1405'));
    }

    ///

    function test_get_treaty_for_decision_laziness() {

        CacheManagerTestImpl::clear();

        $cache = CacheManagerTestImpl::get_cache_treaty_decision();
        $this->assertEquals(array(), $cache);

        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_decision('10303'));

        $cache1 = CacheManagerTestImpl::get_cache_treaty_decision();
        $this->assertNotEquals(array(), $cache1);
    }


    function test_get_treaty_for_decision() {
        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_decision('10303'));
    }
}