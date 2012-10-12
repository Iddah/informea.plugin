<?php

require_once(dirname(__FILE__) . '/../CacheManager.php');

class CacheManagerTestImpl extends CacheManager {

    public static function get_cache($cache_name) {
        return self::$cache[$cache_name];
    }
}

/**
 * @group cache
 */
class CacheManagerTest extends WP_UnitTestCase {


    function test_get_treaty_for_treaty_paragraph_laziness() {

        CacheManagerTestImpl::clear();

        $cache = CacheManagerTestImpl::get_cache(CacheManager::$TREATY_TREATYPARAGRAPH);
        $this->assertEquals(array(), $cache);

        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_treaty_paragraph('7715'));

        $cache1 = CacheManagerTestImpl::get_cache(CacheManager::$TREATY_TREATYPARAGRAPH);
        $this->assertNotEquals(array(), $cache1);
    }


    function test_get_treaty_for_treaty_paragraph() {
        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_treaty_paragraph('7715'));
    }

    ///

    function test_get_treaty_for_decision_paragraph_laziness() {

        CacheManagerTestImpl::clear();

        $cache = CacheManagerTestImpl::get_cache(CacheManager::$TREATY_DECISIONPARAGRAPH);
        $this->assertEquals(array(), $cache);

        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_decision_paragraph('35'));

        $cache1 = CacheManagerTestImpl::get_cache(CacheManager::$TREATY_DECISIONPARAGRAPH);
        $this->assertNotEquals(array(), $cache1);
    }


    function test_get_treaty_for_decision_paragraph() {
        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_decision_paragraph('35'));
    }

    ///

    function test_get_treaty_for_article_laziness() {

        CacheManagerTestImpl::clear();

        $cache = CacheManagerTestImpl::get_cache(CacheManager::$ARTICLE_TREATY);
        $this->assertEquals(array(), $cache);

        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_article('1405'));

        $cache1 = CacheManagerTestImpl::get_cache(CacheManager::$ARTICLE_TREATY);
        $this->assertNotEquals(array(), $cache1);
    }


    function test_get_treaty_for_article() {
        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_article('1405'));
    }

    ///

    function test_get_treaty_for_decision_laziness() {

        CacheManagerTestImpl::clear();

        $cache = CacheManagerTestImpl::get_cache(CacheManager::$DECISION_TREATY);
        $this->assertEquals(array(), $cache);

        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_decision('10303'));

        $cache1 = CacheManagerTestImpl::get_cache(CacheManager::$DECISION_TREATY);
        $this->assertNotEquals(array(), $cache1);
    }


    function test_get_treaty_for_decision() {
        $this->assertEquals(46, CacheManagerTestImpl::get_treaty_for_decision('10303'));
    }
}