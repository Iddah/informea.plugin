<?php

require_once(dirname(__FILE__) . '/../CacheManager.php');

class CacheManagerTestImpl extends CacheManager {

    public static function get_cache($cache_name) {
        return self::$cache[$cache_name];
    }


    public static function get($cache_name, $key) {
        return parent::get($cache_name, $key);
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


    function test_get() {
        $ob1 = CacheManagerTestImpl::get('treaty', '46');
        $this->assertNotNull($ob1);

        $ob2 = CacheManagerTestImpl::get('decision', '10303');
        $this->assertNotNull($ob2);

        $ob3 = CacheManagerTestImpl::get('event', '2481');
        $this->assertNotNull($ob3);

        $ob4 = CacheManagerTestImpl::get('document', '4969');
        $this->assertNotNull($ob4);
    }


    function test_load_entity_treaty() {
        CacheManagerTestImpl::clear();

        $ob = new stdClass();
        $ob->type = 'treaty';
        $ob->id_entity = '46';

        $ob1 = CacheManagerTestImpl::load_entity($ob);
        $this->assertEquals('The test treaty title', $ob1->short_title);
    }

    function test_load_entity_decision() {
        CacheManagerTestImpl::clear();

        $ob = new stdClass();
        $ob->type = 'decision';
        $ob->id_entity = '10303';

        $ob1 = CacheManagerTestImpl::load_entity($ob);
        $this->assertEquals('Test decision for test treaty xxxx', $ob1->short_title);
        $this->assertEquals('http://informea.localhost/wp-content/uploads/2012/10/pivotal_tracker-256-copy.png', $ob1->logo_medium);
    }

    function test_load_entity_event() {
        CacheManagerTestImpl::clear();

        $ob = new stdClass();
        $ob->type = 'event';
        $ob->id_entity = '2481';

        $ob1 = CacheManagerTestImpl::load_entity($ob);
        $this->assertEquals('First Meeting', $ob1->title);
        $this->assertEquals('http://informea.localhost/wp-content/uploads/2012/10/pivotal_tracker-256-copy.png', $ob1->logo_medium);
    }

    function test_load_entity_document() {
        CacheManagerTestImpl::clear();

        $ob = new stdClass();
        $ob->type = 'document';
        $ob->id_entity = '4969';

        $ob1 = CacheManagerTestImpl::load_entity($ob);
        $this->assertEquals('Paulo Coelho - Al cincilea munte.pdf', $ob1->filename);
    }

    function test_load_entities() {
        CacheManagerTestImpl::clear();

        $ob1 = new stdClass();
        $ob1->type = 'treaty';
        $ob1->id_entity = '46';

        $ob2 = new stdClass();
        $ob2->type = 'document';
        $ob2->id_entity = '4969';

        $ob3 = new stdClass();
        $ob3->type = 'unknown';
        $ob3->id_entity = 'xxx';

        $ob4 = null;

        $results = CacheManagerTestImpl::load_entities(array($ob1, $ob2, $ob3, $ob4));
        $this->assertEquals(4, count($results));
        $this->assertEquals('The test treaty title', $results[0]->short_title);
        $this->assertEquals('Paulo Coelho - Al cincilea munte.pdf', $results[1]->filename);
        $this->assertNull($results[2]);
        $this->assertNull($results[3]);
    }


    function test_load_treaty_hierarchy() {
        $data = array(
            'articles' => array( 1405 => array( 7715, 7716 ) ),
            'decisions' => array( 10303 => array(
                'paragraphs' => array(35),
                'documents' => array(4969, 4970)
            ))
        );
        $ob = CacheManagerTestImpl::load_treaty_hierarchy('46', $data);
        $this->assertNotNull($ob);
        $this->assertEquals('The test treaty title', $ob->short_title);
        $this->assertEquals(1, count($ob->articles));
        $this->assertEquals('First article', $ob->articles[0]->title);
        $this->assertEquals(2, count($ob->articles[0]->paragraphs));
        $this->assertEquals('First paragraph of the first article', $ob->articles[0]->paragraphs[0]->content);

        $this->assertEquals(1, count($ob->decisions));
        $this->assertEquals('Test decision for test treaty xxxx', $ob->decisions[0]->short_title);
        $this->assertEquals(1, count($ob->decisions[0]->paragraphs));
        $this->assertEquals('TEST OF DOCUMENT003 FOR DECISION 1', $ob->decisions[0]->paragraphs[0]->content);

        $this->assertEquals(2, count($ob->decisions[0]->documents));
        $this->assertEquals('Paulo Coelho - Al cincilea munte.pdf', $ob->decisions[0]->documents[0]->filename);
        $this->assertEquals('Dictionar Proverbe.pdf', $ob->decisions[0]->documents[1]->filename);
    }
}