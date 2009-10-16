<?php
// check if this is the main testfile or it is run from a suite
if (!defined("MAIN")) {
    set_include_path(get_include_path() . PATH_SEPARATOR . 'library/');
    define("MAIN", true);
}

require_once 'Storage/TestModel.php';
require_once 'Fizzy/Storage.php';

/**
 * Description of StorageTest
 *
 * @author Jeroen Tietema <jeroen@voidwalkers.nl>
 */
class StorageTest extends PHPUnit_Framework_TestCase
{
    protected $storage = null;

    function setUp()
    {
        // copy the test database
        copy('tests/data/master.db', 'tests/data/test.db');

        $this->storage = new Fizzy_Storage('sqlite:./tests/data/test.db');
    }

    function tearDown()
    {
        // remove the used copy of the test database
        $this->storage = null;

        //unlink('tests/data/test.db');
    }

    function testSQLiteFetchOne()
    {
        // test what happens if none is found
        $model = $this->storage->fetchOne('test',1111);

        $this->assertEquals(1111, $model->getId());
        $this->assertEquals(
            array('title' => 'test title', 'body' => 'test body'),
            $model->toArray()
        );
    }

    function testSQLiteFetchAll()
    {
        // test what happens if none is found
        $array = $this->storage->fetchAll('test');

        $this->assertEquals(1, count($array),
            "More results than expected, added something to the master.db?");
        
        $this->assertEquals(
            array('title' => 'test title', 'body' => 'test body'),
            $array[0]->toArray(),
            "Contents to returned model not as expected, did the content of db change?"
        );

        $this->assertEquals(1111, $array[0]->getId(), "Unexpected id");
    }


    function testSQLiteInsert()
    {
        // test insert in existing database
        $model = new TestModel('test', 'hoi');
        $model = $this->storage->persist($model);
        
        $this->assertNotNull($model->getId());

        $array = $this->storage->fetchAll('test');

        $this->assertEquals(2, count($array),
            "Row not inserted in db or data added to db.");
    }

    function testSQLiteInsertCreateTable()
    {
        // test insert in not existing database
        // eg. don't use the test database copy

        $storage = new Fizzy_Storage('sqlite:tests/data/new.db');

        $model = new TestModel('test', 'hoi');
        $model = $storage->persist($model);

        $this->assertNotNull($model->getId());

        $array = $storage->fetchAll('test');

        $this->assertEquals(1, count($array));

        unlink('tests/data/new.db');
    }

    function testSQLiteUpdate()
    {
        $model = $this->storage->fetchOne('test', 1111);
        
        $this->storage->persist($model);

        $array = $this->storage->fetchAll('test');

        $this->assertEquals(1, count($array), "Row inserted instead of update.");

        $this->assertEquals(
            array('title' => 'test', 'body' => 'hoi'),
            $array[0]->toArray(),
            "Contents did not match, model not updated correct."
        );
    }

    function testSQLiteRemove()
    {
        // test what happens if we remove twice
        $model = new TestModel();
        $model->setId('1111');

        $this->storage->remove($model);

        $array = $this->storage->fetchAll('test');

        $this->assertEquals(0, count($array), "DB not empty item not deleted?");
    }
}
