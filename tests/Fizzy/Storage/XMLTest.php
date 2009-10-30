<?php

// check if this is the main testfile or it is run from a suite
if (!defined("MAIN")) {
    set_include_path(get_include_path() . PATH_SEPARATOR . 'library/');
    define("MAIN", true);
}

require_once 'TestModel.php';
require_once 'Fizzy/Storage/XML.php';
require_once 'Fizzy/Storage/XML/Document.php';

/**
 * Description of XMLTest
 *
 * @author jeroen
 */
class XMLTest extends PHPUnit_Framework_TestCase
{

    protected $_storage = null;

    function setUp()
    {
        $this->_storage = new Fizzy_Storage_XML('xml:tests/data');
    }

    /**
     * Test the persist function twice. Once without XML and once with XML
     */
    function testPersist()
    {
        // check insert in new file
        if (is_file('tests/data/tests.xml'))
        {
            unlink('tests/data/tests.xml');
        }

        $model = new TestModel('test title', 'test body');

        $model = $this->_storage->persist($model);

        $this->assertNotNull($model->getId());

        // check insert in existing file

        $model = new TestModel('test title', 'test body');

        $model = $this->_storage->persist($model);

        $this->assertNotNull($model->getId());

        $xml = new Fizzy_Storage_XML_Document('tests/data/tests.xml');
        $root = $xml->firstChild;
        $this->assertEquals(2, $root->childNodes->length, "Should find 2 Child nodes");

        // check insert with fresh storge and no cache

        $this->_storage = null;
        $this->setUp();

        $model = new TestModel('test title', 'test body');

        $model = $this->_storage->persist($model);

        $this->assertNotNull($model->getId());

        $xml = new Fizzy_Storage_XML_Document('tests/data/tests.xml');
        $root = $xml->firstChild;
        $this->assertEquals(3, $root->childNodes->length, "Should find 3 Child nodes");

        // check update

        $model->setTitle('new test title');

        $this->_storage->persist($model);

        $xml = new Fizzy_Storage_XML_Document('tests/data/tests.xml');
        $root = $xml->getElementByUid('root');
        $this->assertEquals(3, $root->childNodes->length, "Should find 3 Child nodes");
        /*
        $this->assertEquals(
            'new test title',
            $root->childNodes->item(2)->childNodes->item(1)->wholeText,
            "XML not properly updated"
        );*/

    }

    function testFetchOne()
    {
        copy('tests/data/master.xml', 'tests/data/tests.xml');

        $model = $this->_storage->fetchOne('test', 123);
        
        $this->assertEquals(array('id' => '123', 'title' => 'Howdy', 'body' => 'Dowdy'), $model, "Wrong data");
    }

    function testRemove()
    {
        copy('tests/data/master.xml', 'tests/data/tests.xml');

        $array = $this->_storage->fetchOne('test', 123);

        $model = new TestModel();
        $model->populate($array);

        $this->_storage->remove($model);

        $xml = new Fizzy_Storage_XML_Document('tests/data/tests.xml');
        $root = $xml->getElementByUid('root');
        $this->assertEquals(1, $root->childNodes->length, "Should find 1 Child nodes");
    }

    function testFetchAll()
    {
        copy('tests/data/master.xml', 'tests/data/tests.xml');

        $results = $this->_storage->fetchAll('test');

        $this->assertEquals(
            array(
                array('id' => '123', 'title' => 'Howdy', 'body' => 'Dowdy'),
                array('id' => '456', 'title' => 'Howdy #2', 'body' => 'Dowdy #2')
            ),
            $results,
            "Results array didn't match."
        );
    }
}
